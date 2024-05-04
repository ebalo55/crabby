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
			PhpCms::Wordpress => write!(f, "WordPress"),
			PhpCms::Joomla => write!(f, "Joomla"),
			PhpCms::Drupal => write!(f, "Drupal"),
		}
	}
}

#[derive(Copy, Clone, PartialEq, Eq, ValueEnum, Debug)]
pub enum PhpVersion {
	/// Generate webshell for PHP >= 5.3 & < 7.0
	_53,
	/// Generate webshell for PHP >= 7.0 & < 8.0
	_70,
	/// Generate webshell for PHP >= 8.0
	_80,
}

impl Display for PhpVersion {
	fn fmt(&self, f: &mut std::fmt::Formatter<'_>) -> std::fmt::Result {
		match self {
			PhpVersion::_53 => write!(f, "PHP >= 5.3 & < 7.0"),
			PhpVersion::_70 => write!(f, "PHP >= 7.0 & < 8.0"),
			PhpVersion::_80 => write!(f, "PHP >= 8.0"),
		}
	}
}