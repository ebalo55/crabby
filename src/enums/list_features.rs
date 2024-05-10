use std::fmt::Display;
use clap::{ValueEnum};

#[derive(Copy, Clone, PartialEq, Eq, ValueEnum, Debug, Default)]
pub enum ListFeatures {
	/// List features for the PHP template language.
	#[default]
	Php,
}

impl Display for ListFeatures {
	fn fmt(&self, f: &mut std::fmt::Formatter<'_>) -> std::fmt::Result {
		match self {
			Self::Php => write!(f, "php"),
		}
	}
}