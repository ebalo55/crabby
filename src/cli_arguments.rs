use clap::{Args, Parser, Subcommand};
use crate::enums::php::{PhpCms, PhpVersion};

/// CLI arguments for the webshell generator
#[derive(Parser, Debug)]
#[command(author, version, long_about)]
pub struct CliArguments {
	/// Enable debug mode
	///
	/// Pass multiple times to increase verbosity
	#[arg(short, long, action = clap::ArgAction::Count, default_value = "0")]
	pub debug: u8,

	/// Command to execute
	#[command(subcommand)]
	pub command: CliCommand,
}

/// CLI main subcommands
#[derive(Subcommand, Debug, PartialEq)]
pub enum CliCommand {
	/// Clone templates and exit.
	///
	/// This will clone the templates from the repository and exit, useful for updating the templates or customizing them.
	Clone,

	/// Generate a webshell.
	///
	/// This will generate a webshell based on the provided arguments.
	Generate(CliGenerateArguments),
}

/// CLI arguments for the webshell generator (generate command)
#[derive(Debug, Args, PartialEq)]
pub struct CliGenerateArguments {
	/// Obfuscation options, flattened in cli
	#[command(flatten)]
	pub obfuscation: CliGenerateObfuscation,

	/// Security options, flattened in cli
	#[command(flatten)]
	pub security: CliGenerateSecurity,

	/// Which template language to use
	///
	/// This will determine the language of the generated webshell
	#[command(subcommand)]
	pub template: CliGenerateCommand,

	/// Output filename
	///
	/// If not provided, the webshell will be written to "shell.[template-extension]" in the current directory
	#[arg(short, long)]
	pub output: Option<String>,
}

/// CLI subcommands (template definition) for the webshell generator (generate command)
#[derive(Subcommand, Debug, PartialEq)]
#[command(subcommand_value_name = "TEMPLATE")]
pub enum CliGenerateCommand {
	/// Generate a PHP webshell
	Php(CliGeneratePhpTemplate),
}

/// CLI arguments for the PHP template generator
#[derive(Debug, Args, PartialEq)]
pub struct CliGeneratePhpTemplate {
	/// Define the language version to use for the template
	///
	/// This is useful for templates that have multiple versions available
	#[arg(long, default_value = "53")]
	pub template_version: PhpVersion,

	/// Define the CMS to generate the webshell for
	///
	/// This will generate a webshell that is compatible with the selected CMS
	#[arg(long)]
	pub cms: Option<PhpCms>,

	/// Enable or disable the plugin mode (generates a plugin archive)
	///
	/// This will generate a plugin archive that can be installed on the CMS.
	/// Note that if no CMS is selected, this will be ignored and the standalone mode will be used.
	#[arg(short, long, default_value = "true")]
	pub plugin: bool,

	/// Enable or disable the standalone mode (generates a standalone webshell)
	///
	/// This will generate a standalone webshell that can be uploaded to the server.
	/// Note that if no CMS is selected, this will be used as the default mode.
	#[arg(short, long, default_value = "false")]
	pub standalone: bool,

	/// Prefix to use for the WordPress functions
	///
	/// This will prefix all the functions with a generated string, required to avoid conflicts with other plugins
	/// in WordPress and other CMS.
	///
	///
	/// The pattern follows a regex-like syntax:
	///
	/// - `\d` - Random digit
	///
	/// - `\w` - Random word character (uppercase or lowercase letter)
	///
	/// - `\s` - Random special character
	///
	/// - `.` - Random character
	///
	/// - `\\` - Escape sequence
	///
	///
	/// Additionally the pattern can contain repetition fragments enclosed in curly braces:
	///
	/// - `{n}` - Repeat the previous character n times (ONLY IF it is a pattern character)
	///
	///
	/// Any other character is treated as a literal character.
	///
	/// Note: It is strongly un-suggested to provide special characters in the format as the possibility to break the
	///       code is high.
	#[arg(long, default_value = r"\w{2}\d")]
	pub functions_prefix: String,
}

/// CLI arguments for the obfuscation options, common to all templates
#[derive(Debug, Args, PartialEq)]
pub struct CliGenerateObfuscation {
	/// Obfuscate the generated code
	///
	/// This will make the code harder to read and understand also reducing the file size, it will also make the code
	/// harder to analyze and detect by antivirus software
	#[arg(long, default_value = "false")]
	pub obfuscate: bool,

	/// Minify the generated code
	///
	/// This will remove all comments and unnecessary whitespace from the code, drastically reducing the overall file size
	/// and making the code harder to read and understand
	#[arg(long, default_value = "false")]
	pub minify: bool,

	/// Format used to generate variable names if obfuscation is enabled
	///
	/// The pattern follows a regex-like syntax:
	///
	/// - `\d` - Random digit
	///
	/// - `\w` - Random word character (uppercase or lowercase letter)
	///
	/// - `\s` - Random special character
	///
	/// - `.` - Random character
	///
	/// - `\\` - Escape sequence
	///
	///
	/// Additionally the pattern can contain repetition fragments enclosed in curly braces:
	///
	/// - `{n}` - Repeat the previous character n times (ONLY IF it is a pattern character)
	///
	///
	/// Any other character is treated as a literal character.
	///
	/// Note: It is strongly un-suggested to provide special characters in the format as the possibility to break the
	///       code is high.
	#[arg(long, default_value = r"\w{3")]
	pub obfuscation_variable_format: String,

	/// Format used to generate variable names if obfuscation is enabled
	///
	/// The pattern follows a regex-like syntax:
	///
	/// - `\d` - Random digit
	///
	/// - `\w` - Random word character (uppercase or lowercase letter)
	///
	/// - `\s` - Random special character
	///
	/// - `.` - Random character
	///
	/// - `\\` - Escape sequence
	///
	///
	/// Additionally the pattern can contain repetition fragments enclosed in curly braces:
	///
	/// - `{n}` - Repeat the previous character n times (ONLY IF it is a pattern character)
	///
	///
	/// Any other character is treated as a literal character.
	///
	/// Note: It is strongly un-suggested to provide special characters in the format as the possibility to break the
	///       code is high.
	#[arg(long, default_value = r"\w{3}")]
	pub obfuscation_function_format: String,
}

/// CLI arguments for the security options, common to all templates
#[derive(Debug, Args, PartialEq)]
pub struct CliGenerateSecurity {
	/// Password used to authenticate to the webshell
	///
	/// Autogenerated if not provided
	#[arg(short, long)]
	pub password: Option<String>,

	/// Length of the password used to authenticate to the webshell
	///
	/// Only used if the password is autogenerated
	#[arg(long, default_value = "32")]
	pub password_length: u32,

	/// Username used to authenticate to the webshell
	///
	/// Autogenerated if not provided
	#[arg(short, long)]
	pub username: Option<String>,

	/// Length of the username used to authenticate to the webshell
	///
	/// Only used if the username is autogenerated
	#[arg(long, default_value = "16")]
	pub username_length: u32,

	/// Salt used to hash the password
	///
	/// Autogenerated if not provided
	#[arg(short, long)]
	pub salt: Option<String>,

	/// Length of the generated salt
	///
	/// Only used if the salt is autogenerated
	#[arg(long, default_value = "64")]
	pub salt_length: u32,
}