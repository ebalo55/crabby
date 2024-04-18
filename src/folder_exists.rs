use std::{env, io};
use std::path::Path;

/// Check if a folder exists
///
/// # Arguments
/// - folder_name: &str - The name of the folder to check
pub fn folder_exists(folder_name: &str) -> io::Result<bool> {
	let mut path = env::current_dir()?;
	path.push(folder_name);

	Ok(Path::new(&path).exists())
}