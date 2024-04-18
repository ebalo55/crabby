use crate::cli_arguments::CliArguments;

pub fn generate_webshell(args: &CliArguments) -> anyhow::Result<()> {
	println!("{:?}", args);
	Ok(())
}