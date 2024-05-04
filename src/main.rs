#[macro_use]
extern crate log;

use std::cmp::PartialEq;
use anyhow::Context;
use clap::Parser;

use folder_exists::folder_exists;

use crate::cli_arguments::{CliArguments, CliCommand, CliGenerateCommand};

mod enums;
mod actions;
mod folder_exists;
mod generate_random_string;
mod cli_arguments;
mod extract_unique_strings;

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
	trace!("Parsed arguments: {:?}", args);

	match args.command {
		CliCommand::Clone => {
			actions::download_templates::download_templates();
		}
		CliCommand::Generate(mut args) => {
			// Check if the templates folder exists, and download them if it doesn't
			if !folder_exists("templates").unwrap() {
				actions::download_templates::download_templates();
			}

			// Set the default username, password, and salt if not provided
			if !args.security.username.is_some() {
				args.security.username = Some(generate_random_string::generate_password(args.security.username_length).unwrap());
			}
			if !args.security.password.is_some() {
				args.security.password = Some(generate_random_string::generate_password(args.security.password_length).unwrap());
			}
			if !args.security.salt.is_some() {
				args.security.salt = Some(generate_random_string::generate_password(args.security.salt_length).unwrap());
			}

			debug!("Random salt: {}", args.security.salt.as_ref().unwrap());

			// Generate the webshell
			return actions::generate_webshell::generate_webshell(&args);
		}
	}

	Ok(())
}
