use crate::actions::generate_php::generate_php_webshell;
use crate::cli_arguments::{CliGenerateArguments, CliGenerateCommand};

pub fn generate_webshell(args: &CliGenerateArguments) -> anyhow::Result<()> {
	match &args.template {
		CliGenerateCommand::Php(php_args) => generate_php_webshell(args, php_args),
	}
}