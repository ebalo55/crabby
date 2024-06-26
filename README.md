<p align="center">
<img src=".assets/crab.png" width="432">
</p>
<h1 align="center">
    Crabby
</h1>
<h3 align="center">
    WebShells for Red Teams, just easily
</h3>

## What is Crabby?

Crabby is a tool developed to generate webshells written in - insert your desired webshell language -.
It is designed to be used by red teams to aid in lateral movement, privilege escalation, and data exfiltration.

## Features

- Web shell generation in multiple languages, see [Supported Web Shells](#supported-web-shells) for more information.
- Customizable web shell templates.
- Stealthy web shell generation by default.
- Pluggable features templates.

## Supported Web Shells

Refer to the list below for the supported web shells and their current status, more information are available in the
associated template folder.

- [x] PHP (5.5+, 7.x, 8.x) - Most of the php 5.5+ code is compatible down to 5.3 but some features have not been tested.

## Installation

### Precompiled Binaries

Get the latest version of Crabby by downloading a precompiled binary from the
[releases page](https://github.com/ebalo55/crabby/releases). Precompiled binaries are available for Linux and Windows.

### Building from Source

To build Crabby from source, you will need to have Rust installed on your system. You can install Rust by following the
instructions on the [official website](https://www.rust-lang.org/tools/install).

```bash
git clone https://github.com/ebalo55/crabby.git
cd crabby
cargo build --release --bins
```

The compiled binary will be available in the `target/release` directory.

### Install via Cargo

You can also install Crabby using Cargo, the Rust package manager.

```bash
cargo install crabby-webshell-generator
```