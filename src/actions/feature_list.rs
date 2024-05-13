use std::fs;
use std::path::Path;

use colored::Colorize;
use glob::glob;

pub(crate) fn list_features(path: &str, extension: &str) {
	// Create the glob pattern for the root files
	let glob_pattern = format!("{}/**/*.{}", path, extension);

	// Search for files matching the pattern
	if let Ok(entries) = glob(&glob_pattern) {
		// Iterate over the matching files
		for entry in entries.filter_map(Result::ok) {
			// Check if the file is named "root.{extension}"
			if let Some(file_name) = entry.file_name() {
				if file_name.to_str().unwrap() == format!("root.{}", extension) {
					// Open a group separator of level 1
					println!("Variation: {}", entry.parent().unwrap().file_name().unwrap().to_str().unwrap().to_uppercase().bold());

					// Check if a "features" folder exists in the same path
					let features_path = entry.parent().unwrap().join("features");
					if let Ok(features_dir) = fs::read_dir(&features_path) {
						let mut folders = vec![];

						// List all files in the "features" folder
						for feature_entry in features_dir.filter_map(Result::ok) {
							if Path::is_file(&feature_entry.path()) {
								println!("   - {}", feature_entry.file_name().to_str().unwrap().replace(format!(".{}", extension).as_str(), ""));
							} else if Path::is_dir(&feature_entry.path()) {
								folders.push(feature_entry);
							}
						}

						// list all files in the nested folders making the new folder a group
						for folder in folders {
							if let Some(feature_name) = folder.file_name().to_str() {
								// Open a group separator of level 2
								println!("   Group: {}", feature_name.to_uppercase().bold());

								// List all files in the feature folder
								let feature_files = fs::read_dir(folder.path()).unwrap();
								for feature_file in feature_files.filter_map(Result::ok) {
									if (Path::is_file(&feature_file.path())) {
										println!(
											"      - {}/{}",
											folder.file_name().to_str().unwrap(),
											feature_file.file_name().to_str().unwrap().replace(format!(".{}", extension).as_str(), "")
										);
									}
								}
							}
						}
						println!("\n");
					}
				}
			}
		}
	}
}
