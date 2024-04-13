use std::fmt::Display;

use clap::ValueEnum;

#[derive(Copy, Clone, PartialEq, Eq, ValueEnum, Debug)]
pub enum ObfuscationMethod {
	/// Basic string obfuscation
	Basic,

	/// Control flow obfuscation
	ControlFlow,
}

impl Display for ObfuscationMethod {
	fn fmt(&self, f: &mut std::fmt::Formatter<'_>) -> std::fmt::Result {
		match self {
			ObfuscationMethod::Basic => write!(f, "Basic"),
			ObfuscationMethod::ControlFlow => write!(f, "ControlFlow"),
		}
	}
}
