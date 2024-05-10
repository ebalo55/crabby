use crate::cli_arguments::{CliGenerateArguments, CliGenerateCommand};

pub fn generate_webshell(args: &CliGenerateArguments) -> anyhow::Result<()> {
	match &args.template {
		CliGenerateCommand::Php(php_args) => crate::strategies::webshell::php::Generator::new(args, php_args).generate(),
	}
}