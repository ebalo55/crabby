use std::collections::HashSet;
use regex::Regex;

/// Extract unique strings from the input using the provided pattern
pub fn extract_unique_strings(input: &str, pattern: &str) -> Vec<String> {
	let re = Regex::new(pattern).unwrap();
	let mut unique_strings = HashSet::new();

	for mat in re.find_iter(input) {
		unique_strings.insert(mat.as_str().to_string());
	}

	unique_strings.into_iter().collect()
}