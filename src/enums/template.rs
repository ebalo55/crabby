use std::fmt::Display;

use clap::ValueEnum;

#[derive(Copy, Clone, PartialEq, Eq, ValueEnum, Debug)]
pub enum Template {
	/// PHP template
	Php,
}

impl Display for Template {
	fn fmt(&self, f: &mut std::fmt::Formatter<'_>) -> std::fmt::Result {
		match self {
			Template::Php => write!(f, "PHP"),
		}
	}
}