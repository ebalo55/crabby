use std::fmt::Display;

use clap::ValueEnum;

#[derive(Copy, Clone, PartialEq, Eq, ValueEnum, Debug)]
pub enum PhpCms {
	/// Generate webshell for the WordPress CMS
	Wordpress,
	/// Generate webshell for the Joomla CMS
	Joomla,
	/// Generate webshell for the Drupal CMS
	Drupal,
}

impl Display for PhpCms {
	fn fmt(&self, f: &mut std::fmt::Formatter<'_>) -> std::fmt::Result {
		match self {
			Self::Wordpress => write!(f, "WordPress"),
			Self::Joomla => write!(f, "Joomla"),
			Self::Drupal => write!(f, "Drupal"),
		}
	}
}

#[derive(Copy, Clone, PartialEq, Eq, ValueEnum, Debug, Default)]
pub enum PhpVersion {
	/// Generate webshell for PHP >= 5.3 & < 7.0
	#[default]
	_53,
	/// Generate webshell for PHP >= 7.0 & < 8.0
	_70,
	/// Generate webshell for PHP >= 8.0
	_80,
}

impl Display for PhpVersion {
	fn fmt(&self, f: &mut std::fmt::Formatter<'_>) -> std::fmt::Result {
		match self {
			Self::_53 => write!(f, "PHP >= 5.3 & < 7.0"),
			Self::_70 => write!(f, "PHP >= 7.0 & < 8.0"),
			Self::_80 => write!(f, "PHP >= 8.0"),
		}
	}
}

#[derive(Copy, Clone, PartialEq, Eq, ValueEnum, Debug, Default)]
pub enum TemplateVariant {
	/// Generate webshell using the base template
	#[default]
	Base,
	/// Generate webshell using the minimal template
	Minimal,
}

impl Display for TemplateVariant {
	fn fmt(&self, f: &mut std::fmt::Formatter<'_>) -> std::fmt::Result {
		match self {
			Self::Base => write!(f, "base"),
			Self::Minimal => write!(f, "minimal"),
		}
	}
}