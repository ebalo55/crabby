#[macro_use]
extern crate log;

use anyhow::Context;
use clap::Parser;

use folder_exists::folder_exists;

use crate::cli_arguments::CliArguments;

mod enums;
mod actions;
mod folder_exists;
mod generate_random_string;
mod cli_arguments;

fn setup_logging(debug_level: u8) -> anyhow::Result<()> {
	let mut base_config = fern::Dispatch::new()
		.format(|out, message, record| {
			let level_padding = if record.level().to_string().len() < 5 {
				" ".repeat(5 - record.level().to_string().len() + 1).to_string()
			} else {
				" ".to_string()
			};

			let colors = fern::colors::ColoredLevelConfig::new()
				.info(fern::colors::Color::Green)
				.warn(fern::colors::Color::Yellow)
				.error(fern::colors::Color::Red)
				.debug(fern::colors::Color::Blue)
				.trace(fern::colors::Color::Magenta);

			let additional_info = if record.level() > log::LevelFilter::Debug {
				format!(" [{}:{}]", record.file().unwrap_or(""), record.line().unwrap_or(0))
			} else {
				"".to_string()
			};

			out.finish(format_args!(
				"[{}]{}[{}]{} {}",
				colors.color(record.level()),
				level_padding,
				humantime::format_rfc3339_seconds(std::time::SystemTime::now()),
				additional_info,
				message
			))
		})
		.level(log::LevelFilter::Trace)
		.chain(std::io::stdout());

	base_config = match debug_level {
		0 => base_config.level(log::LevelFilter::Info),
		1 => base_config.level(log::LevelFilter::Debug),
		2 => base_config.level(log::LevelFilter::Trace),
		_ => base_config.level(log::LevelFilter::Trace),
	};

	base_config.apply()?;
	Ok(())
}

fn main() -> anyhow::Result<()> {
	let mut args = CliArguments::parse();

	setup_logging(args.debug)?;

	// If the user wants to clone the templates and exit, do so
	if args.bare_clone {
		actions::download_templates::download_templates();
		return Ok(());
	}

	// Check if the templates folder exists, and download them if it doesn't
	if !folder_exists("templates").unwrap() {
		actions::download_templates::download_templates();
	}

	// Set the default output filename if not provided
	if !args.output.is_some() {
		args.output = Some(format!("shell.{}", args.template.to_string().to_lowercase()));
	}

	// Set the default username, password, and salt if not provided
	if !args.username.is_some() {
		args.username = Some(generate_random_string::generate_password(args.username_length).unwrap());
	}
	if !args.password.is_some() {
		args.password = Some(generate_random_string::generate_password(args.password_length).unwrap());
	}
	if !args.salt.is_some() {
		args.salt = Some(generate_random_string::generate_password(args.salt_length).unwrap());
	}

	debug!("Random salt: {}", args.salt.as_ref().unwrap());

	if !args.template_version.is_some() {
		args.template_version = Some(
			serde_json::from_str::<serde_json::Value>(
				&std::fs::read_to_string(
					format!(
						"templates/{}/meta.json",
						args.template.to_string().to_lowercase()
					)
				).with_context(|| "Could not read the meta.json file for the template")?
			).with_context(|| "Cannot parse json data from meta.json")?["default_version"]
				.as_str()
				.unwrap()
				.to_string()
		);
	}

	// Generate the webshell
	actions::generate_webshell::generate_webshell(&args)
}
