use std::collections::HashSet;
use std::path::{Path, PathBuf};

use anyhow::Context;
use regex::Regex;
use sha2::{Digest, Sha512};

use crate::cli_arguments::CliArguments;
use crate::generate_random_string::{generate_password, generate_random_string};

pub fn generate_webshell(args: &CliArguments) -> anyhow::Result<()> {
	let mut template_path = PathBuf::from("templates");
	template_path.push(args.template.to_string().to_lowercase());
	if args.template_version.is_some() {
		template_path.push(args.template_version.as_ref().unwrap());
	}

	let code_path = template_path.join(format!("template.{}", args.template.to_string().to_lowercase()));
	let css_path = template_path.join("compiled.css");
	let js_path = template_path.join("compiled.js");

	if !Path::exists(&code_path) {
		error!("The template code file does not exist: {}", code_path.display());
		std::process::exit(1);
	}

	let mut css = String::new();
	if !Path::exists(&css_path) {
		warn!("The template CSS file does not exist: {}. Continuing without CSS.", css_path.display());
	} else {
		let css_result = std::fs::read_to_string(&css_path);

		match css_result {
			Ok(css_content) => {
				css = css_content;
			}
			Err(e) => {
				warn!("Could not read the template CSS file: {}. Continuing without CSS.", css_path.display());
			}
		}
	}

	let mut js = String::new();
	if !Path::exists(&js_path) {
		warn!("The template JS file does not exist: {}. Continuing without JS.", js_path.display());
	} else {
		let js_result = std::fs::read_to_string(&js_path);

		match js_result {
			Ok(js_content) => {
				js = js_content;
			}
			Err(e) => {
				warn!("Could not read the template JS file: {}. Continuing without JS.", js_path.display());
			}
		}
	}

	let mut code = std::fs::read_to_string(&code_path)
		.with_context(|| format!("Could not read the template code file: {}", code_path.display()))?;

	let unique_strings = extract_unique_strings(&code, r#"__[0-9a-zA-Z_]+__"#);
	debug!("Template placeholders: {:?}", unique_strings);

	let mut buffer_username = [0u8; 128];
	let mut buffer_password = [0u8; 128];
	let hashed_username = base16ct::lower::encode_str(
		Sha512::digest(
			format!("{}{}", args.username.as_ref().unwrap(), args.salt.as_ref().unwrap())
		).as_slice(),
		&mut buffer_username,
	).unwrap();

	let hashed_password = base16ct::lower::encode_str(
		Sha512::digest(
			format!("{}{}", args.password.as_ref().unwrap(), args.salt.as_ref().unwrap())
		).as_slice(),
		&mut buffer_password,
	).unwrap();

	for placeholder in unique_strings.iter() {
		let value = match placeholder.as_str() {
			"__CSS__" => css.clone(),
			"__JS__" => js.clone(),
			"__USERNAME__" => hashed_username.to_string(),
			"__PASSWORD__" => hashed_password.to_string(),
			"__SALT__" => args.salt.as_ref().unwrap().to_string(),
			name => {
				if name.starts_with("__FEAT_") || name.starts_with("__PARAM_") {
					generate_random_string("A00")?
				} else {
					generate_password(5)?
				}
			}
		};

		code = code.replace(placeholder, &value);
	}
	code = minify(&code);

	let output_path = PathBuf::from(args.output.as_ref().unwrap());
	std::fs::write(&output_path, code)
		.with_context(|| format!("Could not write the output file: {}", output_path.display()))?;

	info!("Webshell generated successfully: {}", output_path.display());

	Ok(())
}

/// Extract unique strings from the input using the provided pattern
fn extract_unique_strings(input: &str, pattern: &str) -> Vec<String> {
	let re = Regex::new(pattern).unwrap();
	let mut unique_strings = HashSet::new();

	for mat in re.find_iter(input) {
		unique_strings.insert(mat.as_str().to_string());
	}

	unique_strings.into_iter().collect()
}

/// Remove comments from the file content
fn minify(file_content: &str) -> String {
	// Regex patterns for line comments (// ...) and multiline comments (/* ... */)
	let comments_patters = Regex::new(r#"([^:]//.*|/\*(.|\s)*?\*/)"#).unwrap();

	// Remove comments
	let content_without_comments = comments_patters.replace_all(file_content, "");

	let php_opening_tag = Regex::new(r#"<\?php\s*"#).unwrap();
	let php_fixed = php_opening_tag.replace_all(&content_without_comments, "<?php ");

	let minified_content = php_fixed.replace("\n", "")
	                                .replace("\r", "")
	                                .replace("\t", "")
	                                .replace("  ", "");
	minified_content
}