use std::iter::Peekable;
use std::str::Chars;

use anyhow::Result;
use rand::prelude::*;
use rand::thread_rng;

/// Generate a random string based on a pattern
///
/// The pattern follows a regex-like syntax:
/// - `\d` - Random digit
/// - `\w` - Random word character (uppercase or lowercase letter)
/// - `\s` - Random special character
/// - `.` - Random character
/// - `\\` - Escape sequence
///
/// Additionally the pattern can contain repetition fragments enclosed in curly braces:
/// - `{m}` - Repeat the previous character m times (ONLY IF it is a pattern character)
///
/// Any other character is treated as a literal character.
///
/// # Arguments
///
/// - `pattern` - The pattern to generate the random string from
///
/// # Returns
///
/// The generated random string
pub fn generate_random_string(pattern: &str) -> Result<String> {
    let mut rng = thread_rng();
    let mut result = String::new();

    let mut pattern_chars = pattern.chars().peekable();

    while let Some(c) = pattern_chars.next() {
        match c {
            // escape sequence initiated
            '\\' => {
                // check if there is a next character
                if let Some(next_char) = pattern_chars.next() {
                    match next_char {
                        // generate random digit
                        'd' => {
                            let repetitions = parse_repetition(&mut pattern_chars);

                            // generate the random digits
                            for _ in 0..repetitions {
                                result.push(random_digit(&mut rng));
                            }
                        }
                        // generate random word character
                        'w' => {
                            let repetitions = parse_repetition(&mut pattern_chars);

                            // generate the random word characters
                            for _ in 0..repetitions {
                                let random_char = match rng.gen_range(0..2) {
                                    0 => random_lowercase_letter(&mut rng),
                                    _ => random_uppercase_letter(&mut rng),
                                };
                                result.push(random_char);
                            }
                        }
                        // generate random special character
                        's' => {
                            // check if there is a repetition start character ('{')
                            let repetitions = parse_repetition(&mut pattern_chars);

                            // generate the random special characters
                            for _ in 0..repetitions {
                                result.push(random_special_character(&mut rng));
                            }
                        }
                        // any unparsed character
                        _ => {
                            result.push(next_char);
                        }
                    }
                }
                // if there is no next character, just add the backslash
                else {
                    result.push('\\');
                }
            }
            // completely random character
            '.' => {
                let repetitions = parse_repetition(&mut pattern_chars);

                // generate the random characters and add them to the result
                for _ in 0..repetitions {
                    let random_char = match rng.gen_range(0..3) {
                        0 => random_digit(&mut rng),
                        1 => match rng.gen_range(0..2) {
                            0 => random_uppercase_letter(&mut rng),
                            _ => random_lowercase_letter(&mut rng),
                        },
                        _ => random_special_character(&mut rng),
                    };

                    result.push(random_char);
                }
            }
            // any unparsed character
            unparsed_char => {
                result.push(unparsed_char);
            }
        }
    }

    Ok(result)
}

/// Parse a repetition fragment
///
/// Defined as a number of repetitions enclosed in curly braces
///
/// # Example
///
/// ```
/// let pattern = "A{3}";
/// let result = parse_repetition(&mut pattern.chars().peekable());
/// assert_eq!(result, 3);
/// ```
///
/// # Arguments
///
/// - `pattern_chars` - The pattern characters iterator
///
/// # Returns
///
/// The number of repetitions
fn parse_repetition(pattern_chars: &mut Peekable<Chars>) -> usize {
    if pattern_chars.peek() != Some(&'{') {
        return 1;
    }

    let mut repeat_str = String::new();

    // read the number of repetitions
    while let Some(&next) = pattern_chars.peek() {
        // if the next character is '}', break the loop as the repetition fragment has been closed
        if next == '}' {
            pattern_chars.next();
            break;
        }
        if next == '{' {
            pattern_chars.next();
            continue;
        }

        // add the next character to the repeat string
        repeat_str.push(next.clone());
        pattern_chars.next();
    }

    // parse the repeat string into a number
    let count = repeat_str.parse::<usize>().unwrap_or(1);

    count
}

/// Generate a random digit
///
/// # Arguments
///
/// - `rng` - The random number generator
///
/// # Returns
///
/// The random digit as a character
fn random_digit(rng: &mut ThreadRng) -> char {
    char::from_u32(rng.gen_range(('0' as u32)..=('9' as u32))).unwrap()
}

/// Generate a random uppercase letter
///
/// # Arguments
///
/// - `rng` - The random number generator
///
/// # Returns
///
/// The random uppercase letter as a character
fn random_uppercase_letter(rng: &mut ThreadRng) -> char {
    char::from_u32(rng.gen_range(('A' as u32)..=('Z' as u32))).unwrap()
}

/// Generate a random lowercase letter
///
/// # Arguments
///
/// - `rng` - The random number generator
///
/// # Returns
///
/// The random lowercase letter as a character
fn random_lowercase_letter(rng: &mut ThreadRng) -> char {
    char::from_u32(rng.gen_range(('a' as u32)..=('z' as u32))).unwrap()
}

/// Generate a random special character
///
/// # Arguments
///
/// - `rng` - The random number generator
/// - `char_map` - The special character map to choose from
///
/// # Returns
///
/// The random special character as a character
fn random_special_character(rng: &mut ThreadRng) -> char {
    let default_char_map = "!#$%&()*+,-/:;?<=>@[]^_{}~.";
    default_char_map.chars().choose(rng).unwrap()
}


/// Generate a random password of a given length
///
/// - `length` - The length of the password to generate
pub fn generate_password(length: u32) -> Result<String> {
    let result = generate_random_string(format!(".{{{length}}}").as_str())?;
    Ok(result)
}

#[cfg(test)]
mod tests {
    use super::*;

    #[test]
    fn test_generate_random_string() -> Result<(), Box<dyn std::error::Error>> {
        let pattern = r"\d{3}\w{3}\s{3}.{3}\{sample\}";
        let result = generate_random_string(pattern)?;
        println!("Generated random string: {}", result);
        assert_eq!(result.len(), 20);
        assert_eq!(result.chars().nth(0).unwrap().is_ascii_digit(), true);
        assert_eq!(result.chars().nth(1).unwrap().is_ascii_digit(), true);
        assert_eq!(result.chars().nth(2).unwrap().is_ascii_digit(), true);
        assert_eq!(result.chars().nth(3).unwrap().is_ascii_alphabetic(), true);
        assert_eq!(result.chars().nth(4).unwrap().is_ascii_alphabetic(), true);
        assert_eq!(result.chars().nth(5).unwrap().is_ascii_alphabetic(), true);
        assert_eq!(result.ends_with("{sample}"), true);

        let pattern = r"\w\d{2}";
        let result = generate_random_string(pattern)?;
        println!("Generated random string: {}", result);
        assert_eq!(result.len(), 3);
        assert_eq!(result.chars().nth(0).unwrap().is_ascii_alphabetic(), true);
        assert_eq!(result.chars().nth(1).unwrap().is_ascii_digit(), true);
        assert_eq!(result.chars().nth(2).unwrap().is_ascii_digit(), true);

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