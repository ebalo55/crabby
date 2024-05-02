# Project Documentation

This project is a PHP-based web application that provides various functionalities for server operations.
It is designed to be stealthy, meaning that the operations should not be visible to the user or any monitoring system
without raising any uncommon process tree or disk operation.

## Docker Compose Configuration

The project uses Docker Compose for setting up the development environment. The `docker-compose.yml` file includes
configurations for different PHP versions (5.x, 7.x, 8.x) with Apache, WordPress with a webshell plugin, MariaDB, and
OpenLDAP.

## Webshell Plugin

The webshell plugin is a PHP script that provides various functionalities once activated.

### Unique features

The WordPress webshell includes all the features of the template webshell, plus:

- [x] Added stealthiness - hides itself from the plugin list once activated
- [ ] Deactivation bypass - hooks into the WordPress deactivation process to prevent the plugin from being deactivated
- [ ] Persistence - hooks into the WordPress uninstall process to ensure the plugin cannot be removed

## Webshell Template

The `template.php` file is a PHP script that can be used to perform various operations on a server. The operations
include:

- [x] File extraction (preview and download as base64 encoded string)
- [x] Directory listing using common `ls -lah` result format
- [x] Exfiltration of files from the server or connected shares (with folder extraction and extension filtering
  capabilities)
- [x] Port scanning
- [x] Writing files
- [x] Running commands
- [x] PHP info and loaded extensions
- [x] Querying databases based on the available PHP extensions and drivers
- [x] Querying LDAP (authenticated and anonymously)
- [x] Running BloodHound-like queries (supported but untested)
- [ ] Php code evaluation

## Login

The application includes a login system. The username and password are hashed using SHA512 and a salt. The login system
is session-based even if at the current stage the session is practically unused within the webshell itself.

### !!! WARNING Notice !!!

Never directly use the `template.php` file in a production environment as it contains a **development only** backdoor
allowing to bypass the login system.

The backdoor is a simple `if` statement that checks if the `dev` parameter is set in the URL.

The backdoor gets automatically removed by the generator when building the final webshell.

## Database Operations

The application supports querying various types of databases including:

- [x] MySQL
- [x] PostgreSQL
- [x] SQLite
- [x] SQL Server
- [x] Oracle
- [x] MongoDB
- [x] IBM DB2
- [x] Firebird
- [x] ODBC
- [x] Informix
- [x] Sybase

## LDAP Operations

The application supports querying LDAP servers. The LDAP operations include:

- [x] Anonymous bind
- [x] Authenticated bind
- [x] Querying the LDAP server
- [x] Run BloodHound-like queries (supported but untested)

## Exfiltration

The application supports exfiltrating data from the server. The exfiltration includes:

- [x] File exfiltration
- [x] Folder exfiltration
- [x] Extension filtering (optional)
- [x] Recursive exfiltration of files and folders within a given path (optional)
- [x] Stacking multiple exfiltration operations
- [x] Stacking of multiple optional features within the same path

## Port Scanning

The application supports scanning ports on a given IP address. The port scanning includes:

- [x] Scanning a range of ports

## File Writing

The application supports writing files on the server. The file writing includes:

- [x] Writing files with a given content
- [x] Writing binary files decoded from base64 encoded strings

## PHP Info

The application can display the PHP info and loaded extensions.

## Directory Listing

The application supports listing directories on the server or connected shares. The directory listing includes:

- [x] Listing directories using common `ls -lah` result format
- [x] Listing directories with a given path
- [x] Recursive listing of directories within a given path using a given depth

## File Extraction

The application supports extracting files and previewing them from the server or connected shares.

## Stealthiness

The application is designed to be stealthy, meaning that the operations should not be visible to the user or any
monitoring system without raising any uncommon process tree or disk operation. The possibility to get discovered always
exists, but the goal is to make it as hard as possible.
