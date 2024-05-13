use std::path::PathBuf;
use anyhow::Context;
use fancy_regex::Regex;

/// A feature of the PHP template
#[derive(Debug)]
pub struct PhpFeature {
	/// The path of the feature
	pub path: PathBuf,
	/// The name of the feature
	pub name: String,
	/// The code section representing the constants of the feature
	pub constants: String,
	/// The code section representing the functions of the feature
	pub functions: String,
	/// The code section representing the hooks of the feature
	pub hooks: String,
}

impl PhpFeature {
	/// Generate the code for the feature
	pub fn new(feature: PathBuf) -> Self {
		let code = std::fs::read_to_string(&feature)
			.with_context(|| format!("Could not read the feature file at '{}'", feature.display()))
			.unwrap();

		let name = Self::compute_feature_name(&feature);

		Self {
			path: feature,
			name,
			constants: Self::extract_constants(&code),
			functions: Self::extract_functions(&code),
			hooks: Self::extract_hooks(&code),
		}
	}

	/// Extract the constants section from the code
	fn extract_constants(code: &str) -> String {
		Self::extract(code, "constants", "end")
	}

	/// Extract the functions section from the code
	fn extract_functions(code: &str) -> String {
		Self::extract(code, "functions", "end")
	}

	/// Extract the hooks section from the code
	fn extract_hooks(code: &str) -> String {
		Self::extract(code, "hooks", "end")
	}

	/// Extract a section from the code
	fn extract(code: &str, prefix: &str, suffix: &str) -> String {
		let regex = Regex::new(format!(r#"\/\/ section\.{}((?>\s|.)*?)\/\/ section\.{}\.{}"#, prefix, prefix, suffix).as_str()).unwrap();
		let captures = regex.captures(code);

		if captures.is_err() {
			return String::new();
		}

		let captures = captures.unwrap();

		match captures {
			Some(captures) => captures.get(1).unwrap().as_str().to_string(),
			None => String::new(),
		}
	}

	/// Compute the name of the feature from the path
	fn compute_feature_name(feature_path: &PathBuf) -> String {
		let level_1_parent = feature_path.parent().unwrap();
		let level_2_parent = level_1_parent.parent().unwrap();

		if level_1_parent.file_name().unwrap().to_str().unwrap() == "features" {
			// main feature found, the feature name is the file name (without the extension)
			// e.g. "feature"
			feature_path.file_stem().unwrap().to_str().unwrap().to_string()
		} else {
			// grouped feature found, the feature name is the parent folder name + the file name (without the extension)
			// e.g. "grouped/feature"
			format!(
				"{}/{}",
				level_2_parent.file_name().unwrap().to_str().unwrap(),
				feature_path.file_stem().unwrap().to_str().unwrap()
			)
		}
	}
}