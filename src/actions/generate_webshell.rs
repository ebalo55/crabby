use crate::actions::generate_php::generate_php_webshell;
use crate::cli_arguments::{CliGenerateArguments, CliGenerateCommand};

pub fn generate_webshell(args: &CliGenerateArguments) -> anyhow::Result<()> {
	match &args.template {
		CliGenerateCommand::Php(php_args) => generate_php_webshell(args, php_args),
	}

	/*

	if args.obfuscate {
		info!("Obfuscating the generated code");
		code = minify(&code, args);
	}

	let output_path = PathBuf::from(args.output.as_ref().unwrap());
	std::fs::write(&output_path, code)
		.with_context(|| format!("Could not write the output file: {}", output_path.display()))?;

	info!("Webshell generated successfully: {}", output_path.display());
	info!("Username: {}", args.username.as_ref().unwrap());
	info!("Password: {}", args.password.as_ref().unwrap());*/
}