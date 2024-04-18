use anyhow::anyhow;
use rand::prelude::*;
use rand::thread_rng;

/// Generate a random string based on a pattern
///
/// - `pattern` - The pattern to use for generating the random string (A: uppercase/lowercase letter, 0: digit, !: special character)
pub fn generate_random_string(pattern: &str) -> Result<String, Box<dyn std::error::Error>> {
	let mut rng = thread_rng();
	let mut result = String::new();

	for c in pattern.chars() {
		match c {
			'A' => result.push(
				"ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz"
					.chars()
					.choose(&mut rng)
					.ok_or(anyhow!("[!] Cannot generate random character"))?
			),
			'0' => result.push(
				"0123456789"
					.chars()
					.choose(&mut rng)
					.ok_or(anyhow!("[!] Cannot generate random character"))?
			),
			'!' => result.push(
				"!@#$%^&*()_+-=[]{}|;:,.<>?"
					.chars()
					.choose(&mut rng)
					.ok_or(anyhow!("[!] Cannot generate random character"))?
			),
			_ => result.push(c),
		}
	}

	Ok(result)
}

/// Generate a random password of a given length
///
/// - `length` - The length of the password to generate
pub fn generate_password(length: u32) -> Result<String, Box<dyn std::error::Error>> {
	let mut rng = thread_rng();
	let mut pattern = String::new();

	for _ in 0..length {
		pattern.push("A0!".chars().choose(&mut rng).unwrap());
	}

	let result = generate_random_string(pattern.as_str())?;
	Ok(result)
}

#[cfg(test)]
mod tests {
	use super::*;

	#[test]
	fn test_generate_random_string() -> Result<(), Box<dyn std::error::Error>> {
		let pattern = "A0!";
		let result = generate_random_string(pattern)?;
		println!("Generated random string: {}", result);
		assert_eq!(result.len(), 3);

		Ok(())
	}

	#[test]
	fn test_generate_password() -> Result<(), Box<dyn std::error::Error>> {
		let length = 8;
		let result = generate_password(length)?;
		println!("Generated 8 char password: {}", result);
		assert_eq!(result.len(), length as usize);

		Ok(())
	}
}