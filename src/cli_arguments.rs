use clap::{Args, Parser, Subcommand};
use crate::enums::list_features::ListFeatures;

use crate::enums::php::{PhpCms, PhpVersion, TemplateVariant};

/// CLI arguments for the webshell generator
#[derive(Parser, Debug, Default)]
#[command(author, version, long_about)]
pub struct CliArguments {
    /// Enable debug mode.
    ///
    /// Pass multiple times to increase verbosity.
    #[arg(short, long, global = true, action = clap::ArgAction::Count, default_value = "0", display_order = 0)]
    pub debug: u8,

    /// Command to execute
    #[command(subcommand)]
    pub command: CliCommand,
}

/// CLI main subcommands
#[derive(Subcommand, Debug, PartialEq, Default)]
pub enum CliCommand {
    /// Clone templates and exit.
    ///
    /// This will clone the templates from the repository and exit, useful for updating the templates or customizing them.
    #[default]
    Clone,

    /// List available features for a template language.
    FeatureList(CliFeatureListArguments),

    /// Generate a webshell.
    ///
    /// This will generate a webshell based on the provided arguments.
    Generate(CliGenerateArguments),
}

/// CLI arguments for the feature list command
#[derive(Debug, Args, PartialEq, Default)]
pub struct CliFeatureListArguments {
    /// Template language to list the features for.
    pub template: ListFeatures,
}

/// CLI arguments for the webshell generator (generate command)
#[derive(Debug, Args, PartialEq, Default)]
pub struct CliGenerateArguments {
    /// Obfuscation options, flattened in cli
    #[command(flatten)]
    pub obfuscation: CliGenerateObfuscation,

    /// Security options, flattened in cli
    #[command(flatten)]
    pub security: CliGenerateSecurity,

    /// Which template language to use.
    ///
    /// This will determine the language of the generated webshell.
    #[command(subcommand)]
    pub template: CliGenerateCommand,

    /// Output filename.
    ///
    /// If not provided, the webshell will be written to "shell.[template-extension]" in the current directory.
    #[arg(short, long, global = true, display_order = 0)]
    pub output: Option<String>,
}

/// CLI subcommands (template definition) for the webshell generator (generate command).
#[derive(Subcommand, Debug, PartialEq)]
#[command(subcommand_value_name = "TEMPLATE")]
pub enum CliGenerateCommand {
    /// Generate a PHP webshell.
    Php(CliGeneratePhpTemplate),
}

/// Default implementation for the webshell generator (generate command).
impl Default for CliGenerateCommand {
    fn default() -> Self {
        CliGenerateCommand::Php(CliGeneratePhpTemplate::default())
    }
}

/// CLI arguments for the PHP template generator
#[derive(Debug, Args, PartialEq, Default)]
pub struct CliGeneratePhpTemplate {
    /// Define the language version to use for the template.
    #[arg(short, long, default_value = "53", display_order = 0, value_enum)]
    pub template_version: PhpVersion,

    /// Define the template variant to use for the template.
    ///
    /// This will generate a webshell based on the chosen variant.
    #[arg(long, default_value = "base", display_order = 0, value_enum)]
    pub variant: TemplateVariant,

    /// Define the CMS to generate the webshell for.
    ///
    /// This will generate a webshell that is compatible with the selected CMS.
    #[arg(long, display_order = 2, value_enum)]
    pub cms: Option<PhpCms>,

    /// Define the CMS options to use for the template.
    #[arg(
    short,
    long,
    action = clap::ArgAction::Append,
    display_order = 2,
    long_help = r#"Define the CMS options to use for the template.

This will pass additional options to the CMS template to customize the generated webshell.
The options are passed as a key-value pair separated by an equal sign, multiple options can
be passed by repeating the flag multiple times.

Each option is useful to disguise the webshell during installation or into the plugin
details views (if self hiding is not available or enabled).

Note that the options are specific to the CMS template and may not be available in all
templates.

WordPress plugin options:
- __PLUGIN_NAME__: Identifies the plugin in the CMS
- __PLUGIN_DESCRIPTION__: Description of the plugin
- __PLUGIN_VERSION__: Version of the plugin
- __PLUGIN_AUTHOR__: Author of the plugin

Joomla plugin options:
Currently no options are available

Drupal plugin options:
Currently no options are available

Note:
- Options are not validated and are passed as-is to the template
- Options are not sanitized and may break the generated code if not correctly formatted, try
  not to use special characters please
- Options are not required and can be omitted if not needed, in that case random values will be used

Example: `--cms-option key1=value1 --cms-option key2=value`"#
    )]
    pub cms_option: Vec<String>,

    /// Disable the plugin mode (generates a plugin archive).
    ///
    /// No plugin archive will be generated if a CMS is selected.
    ///
    /// This enables the auto fallback to standalone mode.
    // Reference to the https://jwodder.github.io/kbits/posts/clap-bool-negate/ to know why the naming are inverted
    #[arg(long = "no-plugin", overrides_with = "_no_plugin", default_value = "true", action = clap::ArgAction::SetFalse, display_order = 1)]
    pub plugin: bool,

    /// Enable the plugin mode (generates a plugin archive).
    ///
    /// This will generate a plugin archive that can be installed on the CMS.
    ///
    /// Note that if no CMS is selected, this will be ignored and the standalone mode will be used.
    // Reference to the https://jwodder.github.io/kbits/posts/clap-bool-negate/ to know why the naming are inverted
    #[arg(short = 'p', long = "plugin", overrides_with = "plugin", display_order = 1)]
    pub _no_plugin: bool,

    /// Enable the standalone mode (generates a standalone webshell).
    ///
    /// This will generate a standalone webshell that can be uploaded to the server.
    ///
    /// Note that if no CMS is selected, this will be used as the default mode.
    #[arg(short, long, default_value = "false", display_order = 1)]
    pub standalone: bool,

    /// Prefix to use for the functions.
    #[arg(
    short,
    long,
    default_value = r"\w{2}\d",
    display_order = 1,
    long_help = r#"Prefix to use for the functions.

This will prefix all the functions with a generated string, required to avoid conflicts with other
plugins in WordPress and other CMS.

The pattern follows a regex-like syntax:
- `\d` - Random digit
- `\w` - Random word character (uppercase or lowercase letter)
- `\s` - Random special character
- `.` - Random character
- `\\` - Escape sequence

Additionally the pattern can contain repetition fragments enclosed in curly braces:
- `{m}` - Repeat the previous character m times (ONLY IF it is a pattern character)

Any other character is treated as a literal character.

Note: It is strongly un-suggested to provide special characters in the format as the possibility to
      break the code is high."#
    )]
    pub functions_prefix: String,

    /// Features to include in the generated webshell.
    #[arg(
    short,
    long,
    action = clap::ArgAction::Append,
    display_order = 2,
    long_help = r#"Features to include in the generated webshell.

This will include optional features in the generated webshell, each additional feature will add more code increasing its overall size,
complexity, detectability and functionality.

Note that the features are specific to the template and may not be available in all templates or cms.

To see the available features for the selected template, use the `feature-list [template-language]` command.
"#
    )]
    pub features: Vec<String>,
}

/// CLI arguments for the obfuscation options, common to all templates
#[derive(Debug, Args, PartialEq, Default)]
pub struct CliGenerateObfuscation {
    /// Obfuscate the generated code.
    ///
    /// This will make the code harder to read and understand also reducing the file size, it will
    /// also reduce the possibility of detection by antivirus software.
    #[arg(long, global = true, default_value = "false", display_order = 0)]
    pub obfuscate: bool,

    /// Minify the generated code.
    ///
    /// This will remove all comments and unnecessary whitespace from the code, drastically reducing
    /// the overall file size and making the code harder to read and understand.
    #[arg(long, global = true, default_value = "false", display_order = 0)]
    pub minify: bool,

    /// Format used to generate variable names if obfuscation is enabled.
    #[arg(
    long,
    global = true,
    default_value = r"\w{3}",
    display_order = 3,
    long_help = r#"Format used to generate variable names if obfuscation is enabled.

The pattern follows a regex-like syntax:
- `\d` - Random digit
- `\w` - Random word character (uppercase or lowercase letter)
- `\s` - Random special character
- `.` - Random character
- `\\` - Escape sequence

Additionally the pattern can contain repetition fragments enclosed in curly braces:
- `{m}` - Repeat the previous character m times (ONLY IF it is a pattern character)

Any other character is treated as a literal character.

Note: It is strongly un-suggested to provide special characters in the format as the possibility to
      break the code is high."#
    )]
    pub obfuscation_variable_format: String,

    /// Format used to generate function names if obfuscation is enabled.
    #[arg(
    long,
    global = true,
    default_value = r"\w{3}",
    display_order = 3,
    long_help = r#"Format used to generate function names if obfuscation is enabled.

The pattern follows a regex-like syntax:
- `\d` - Random digit
- `\w` - Random word character (uppercase or lowercase letter)
- `\s` - Random special character
- `.` - Random character
- `\\` - Escape sequence

Additionally the pattern can contain repetition fragments enclosed in curly braces:
- `{m}` - Repeat the previous character m times (ONLY IF it is a pattern character)

Any other character is treated as a literal character.

Note: It is strongly un-suggested to provide special characters in the format as the possibility to
      break the code is high."#
    )]
    pub obfuscation_function_format: String,
}

/// CLI arguments for the security options, common to all templates
#[derive(Debug, Args, PartialEq, Default)]
pub struct CliGenerateSecurity {
    /// Password used to authenticate to the webshell.
    ///
    /// Autogenerated if not provided.
    #[arg(long, global = true, display_order = 4)]
    pub password: Option<String>,

    /// Length of the password used to authenticate to the webshell.
    ///
    /// Only used if the password is autogenerated.
    #[arg(long, global = true, default_value = "32", display_order = 4)]
    pub password_length: u32,

    /// Username used to authenticate to the webshell.
    ///
    /// Autogenerated if not provided.
    #[arg(long, global = true, display_order = 4)]
    pub username: Option<String>,

    /// Length of the username used to authenticate to the webshell.
    ///
    /// Only used if the username is autogenerated.
    #[arg(long, global = true, default_value = "16", display_order = 4)]
    pub username_length: u32,

    /// Salt used to hash the password.
    ///
    /// Autogenerated if not provided.
    #[arg(long, global = true, display_order = 4)]
    pub salt: Option<String>,

    /// Length of the generated salt.
    ///
    /// Only used if the salt is autogenerated.
    #[arg(long, global = true, default_value = "64", display_order = 4)]
    pub salt_length: u32,
}