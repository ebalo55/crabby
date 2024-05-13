/// Pretty print a file size in bytes to a human-readable string.
pub fn pretty_print_filesize(size: u64) -> String {
	const KB: f64 = 1024.0;
	const MB: f64 = KB * KB;
	const GB: f64 = MB * KB;
	const TB: f64 = GB * KB;

	if size < KB as u64 {
		format!("{} B", size)
	} else if size < MB as u64 {
		format!("{:.2} KB", size as f64 / KB)
	} else if size < GB as u64 {
		format!("{:.2} MB", size as f64 / MB)
	} else if size < TB as u64 {
		format!("{:.2} GB", size as f64 / GB)
	} else {
		format!("{:.2} TB", size as f64 / TB)
	}
}