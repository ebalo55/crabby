use std::error::Error;
use std::fs;
use std::fs::File;
use std::io::copy;

pub fn download_templates() {
	println!("Cloning templates...");
	download_file(
		"https://github.com/ebalo55/crabby/archive/refs/heads/main.zip",
		"crabby.zip",
	).unwrap();
	unzip_templates("crabby.zip", ".").unwrap();
	fs::remove_file("crabby.zip").unwrap();
}

/// Function to download a file from a given URL to a given path
fn download_file(url: &str, file_path: &str) -> Result<(), Box<dyn Error>> {
	// Send a GET request to the URL
	let response = reqwest::blocking::get(url)?;

	// Check if the request was successful
	if response.status().is_success() {
		// Create a new file at the specified file path
		let mut file = File::create(file_path)?;

		// Copy the content of the response body to the file
		copy(&mut response.bytes().unwrap().as_ref(), &mut file)?;

		println!("File downloaded successfully!");
		Ok(())
	} else {
		// If the request was not successful, extract the error message and return it
		let status = response.status();
		let message = response.text()?;
		Err(format!("Request failed with status {}: {}", status, message).into())
	}
}

/// Function to unzip an archive to a given path
fn unzip_templates(archive_path: &str, extract_to: &str) -> Result<(), Box<dyn Error>> {
	// Open the ZIP archive
	let file = File::open(archive_path)?;
	let mut archive = zip::ZipArchive::new(file)?;
	archive.extract(extract_to)?;

	fs::rename("crabby-main/templates", "templates")?;
	fs::remove_dir_all("crabby-main")?;

	println!("Archive extracted successfully!");
	Ok(())
}