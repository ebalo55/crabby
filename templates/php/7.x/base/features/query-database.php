<?php

// section.constants
$QUERY_DATABASES = "__FEAT_QUERY_DATABASES__";
// section.constants.end

// section.functions
/**
 * Create the example page
 *
 * @param $page_content string The page content container
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 * @param $page string The current page
 * @param $css string The CSS of the page
 */
function __PREFIX__makeQueryDatabasePage(&$page_content, $features, $page, $css) {
    $feature = array_values(array_filter($features, function ($feature) use ($page) {
        return $feature["op"] === $page;
    }));

    $page_content = __PREFIX__makePage(
        $features,
        $css,
        $page,
        [__PREFIX__makePageHeader(
            $feature[0]["title"],
            $feature[0]["description"]
        ), __PREFIX__makeForm(
            $page,
            $_SERVER["REQUEST_URI"],
            [__PREFIX__makeSelect(
                "Database",
                "__PARAM_1__",
                [[
                    "value"    => "mysql",
                    "label"    => "MySQL",
                    "disabled" => !extension_loaded("mysql") &&
                                  !extension_loaded("mysqli") &&
                                  !extension_loaded("pdo_mysql"),
                ], [
                    "value"    => "cubrid",
                    "label"    => "CUBRID",
                    "disabled" => !extension_loaded("cubrid") &&
                                  !extension_loaded("pdo_cubrid"),
                ], [
                    "value"    => "pgsql",
                    "label"    => "PostgreSQL",
                    "disabled" => !extension_loaded("pgsql") &&
                                  !extension_loaded("pdo_pgsql"),
                ], [
                    "value"    => "sqlite",
                    "label"    => "SQLite",
                    "disabled" => !extension_loaded("sqlite3") &&
                                  !extension_loaded("pdo_sqlite"),
                ], [
                    "value"    => "sqlsrv",
                    "label"    => "SQL Server",
                    "disabled" => !extension_loaded("sqlsrv") &&
                                  !extension_loaded("pdo_sqlsrv"),
                ], [
                    "value"    => "oci",
                    "label"    => "Oracle",
                    "disabled" => !extension_loaded("oci8") &&
                                  !extension_loaded("pdo_oci"),
                ], [
                    "value"    => "mongodb",
                    "label"    => "MongoDB",
                    "disabled" => !extension_loaded("mongo") &&
                                  !extension_loaded("mongodb"),
                ], [
                    "value"    => "ibm",
                    "label"    => "IBM DB2",
                    "disabled" => !extension_loaded("ibm_db2") &&
                                  !extension_loaded("pdo_ibm"),
                ], [
                    "value"    => "firebird",
                    "label"    => "Firebird/Interbase",
                    "disabled" => !extension_loaded("interbase") &&
                                  !extension_loaded("pdo_firebird"),
                ], [
                    "value"    => "odbc",
                    "label"    => "ODBC",
                    "disabled" => !extension_loaded("odbc") &&
                                  !extension_loaded("pdo_odbc"),
                ], [
                    "value"    => "informix",
                    "label"    => "Informix",
                    "disabled" => !extension_loaded("pdo_informix"),
                ], [
                    "value"    => "sybase",
                    "label"    => "Sybase",
                    "disabled" => !extension_loaded("sybase") &&
                                  !extension_loaded("mssql") &&
                                  !extension_loaded("pdo_dblib"),
                ], [
                    "value"    => "raw",
                    "label"    => "Raw PDO connection string",
                    "disabled" => !extension_loaded("pdo"),
                    "selected" => true,
                ]],
                true,
                "Database driver not available."
            ), __PREFIX__makeInput(
                "text",
                "Host",
                "__PARAM_2__",
                "localhost",
                "The host to connect to (default: localhost)"
            ), __PREFIX__makeInput(
                "number",
                "Port",
                "__PARAM_3__",
                "3306",
                "
                                    The port to connect to, default depend on the database
                                    <ul class='text-sm text-zinc-500 list-disc list-inside'>
                                        <li>MySQL (default: 3306)</li>
                                        <li>CUBRID (default: 30000)</li>
                                        <li>PostgreSQL (default: 5432)</li>
                                        <li>SQLite (default: None)</li>
                                        <li>SQL Server (default: 1433)</li>
                                        <li>Oracle (default: 1521)</li>
                                        <li>MongoDB (default: 27017)</li>
                                        <li>IBM DB2 (default: 50000)</li>
                                        <li>Firebird/Interbase (default: 3050)</li>
                                        <li>ODBC (default: None)</li>
                                        <li>Informix (default: 9800)</li>
                                        <li>Sybase (default: 5000)</li>
                                    </ul>"
            ), __PREFIX__makeInput(
                "text",
                "Username",
                "__PARAM_4__",
                "admin",
                "The username to connect with.",
                true
            ), __PREFIX__makeInput(
                "password",
                "Password",
                "__PARAM_5__",
                "&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;",
                "The password to connect with.",
                true
            ), __PREFIX__makeInput(
                "text",
                "Database",
                "__PARAM_6__",
                "ExampleDB",
                "The database to connect to."
            ), __PREFIX__makeInput(
                "text",
                "Charset",
                "__PARAM_7__",
                "utf8",
                "The charset to use for the connection."
            ), __PREFIX__makeInput(
                "text",
                "Service name",
                "__PARAM_8__",
                "orcl",
                "The service name to use for the connection."
            ), __PREFIX__makeInput(
                "text",
                "SID",
                "__PARAM_9__",
                "orcl",
                "The SID to use for the connection."
            ), __PREFIX__makeInput(
                "text",
                "Options",
                "__PARAM_10__",
                "ssl=true",
                "The options to use for the connection."
            ), __PREFIX__makeInput(
                "text",
                "Role",
                "__PARAM_11__",
                "SYSDBA",
                "The role to use for the connection."
            ), __PREFIX__makeInput(
                "text",
                "Dialect",
                "__PARAM_12__",
                "3",
                "The dialect to use for the connection."
            ), __PREFIX__makeInput(
                "text",
                "Protocol",
                "__PARAM_13__",
                "onsoctcp",
                "The protocol to use for the connection."
            ), __PREFIX__makeCheckbox(
                "__PARAM_14__",
                "Enable scrollable cursors",
                "Enable scrollable cursors for the connection.",
                true,
                "1"
            ), __PREFIX__makeInput(
                "text",
                "ODBC driver",
                "__PARAM_15__",
                "ODBC Driver 17 for SQL Server",
                "The ODBC driver to use for the connection."
            ), __PREFIX__makeInput(
                "text",
                "Raw connection string",
                "__PARAM_16__",
                "mysql:host=localhost;port=3306;dbname=ExampleDB;charset=utf8",
                "The raw connection string to use for the connection."
            ), __PREFIX__makeInput(
                "text",
                "Server",
                "__PARAM_17__",
                "ol_informix1170",
                "The Informix server name to use for the connection."
            ), __PREFIX__makeInput(
                "textarea",
                "Query",
                "__PARAM_18__",
                "SHOW DATABASES",
                "The query to run against the database. Leave empty to perform a connection test."
            ), __PREFIX__makeInput(
                "text",
                "Collection",
                "__PARAM_19__",
                "users",
                "The collection to query against for MongoDB."
            ), '<script>
                        function __PREFIX__hideAll() {
                            for (let i = 2; i <= 17; i++) {
                                document.getElementById(`__PARAM_${i}__-container`).classList.add(`hidden`);
                            }

                            document.getElementById(`__PARAM_19__-container`).classList.add(`hidden`);
                        }

                        function __PREFIX__showRange(start, end) {
                            for (let i = start; i <= end; i++) {
                                document.getElementById(`__PARAM_${i}__-container`).classList.remove(`hidden`);
                            }
                        }

                        hideAll();
                        showRange(16, 16);
                        const select = document.getElementById(`__PARAM_1__`);
                        select.addEventListener(`change`, (event) => {
                           const value = event.target.value;
                           hideAll()

                           switch (value) {
                                case `raw`:
                                    showRange(16, 16);
                                    break;
                                case `mysql`:
                                case `cubrid`:
                                    showRange(2, 7);
                                    break;
                                case `pgsql`:
                                case `sqlsrv`:
                                case `ibm`:
                                case `sybase`:
                                    showRange(2, 6);
                                    break;
                                case `sqlite`:
                                    showRange(2, 2);
                                    showRange(4, 5);
                                    break;
                                case `oci`:
                                    showRange(2, 5);
                                    showRange(8, 9);
                                    break;
                                case `mongodb`:
                                    showRange(2, 6);
                                    showRange(10, 10);
                                    showRange(19, 19);
                                    break;
                                case `firebird`:
                                    showRange(2, 7);
                                    showRange(11, 12);
                                    break;
                                case `odbc`:
                                    showRange(2, 6);
                                    showRange(15, 15);
                                    break;
                                case `informix`:
                                    showRange(2, 6);
                                    showRange(13, 14);
                                    showRange(17, 17);
                                    break;
                           }
                        });
                    </script>']
        )]
    );
}

/**
 * Handle the login operation
 *
 * @param $operation string The operation to handle
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *
 * @return void
 */
function __PREFIX__handleQueryDatabase($operation, $features) {
    __PREFIX__connectAndQueryDatabase(
        $_POST["__PARAM_1__"],
        $_POST["__PARAM_4__"],
        $_POST["__PARAM_5__"],
        !empty($_POST["__PARAM_2__"]) ? $_POST["__PARAM_2__"] : "localhost",
        !empty($_POST["__PARAM_3__"]) ? intval($_POST["__PARAM_3__"]) : null,
        $_POST["__PARAM_8__"],
        $_POST["__PARAM_9__"],
        $_POST["__PARAM_6__"],
        $_POST["__PARAM_7__"],
        $_POST["__PARAM_10__"],
        $_POST["__PARAM_11__"],
        $_POST["__PARAM_12__"],
        $_POST["__PARAM_15__"],
        $_POST["__PARAM_17__"],
        !empty($_POST["__PARAM_13__"]) ? $_POST["__PARAM_13__"] : "onsoctcp",
        $_POST["__PARAM_14__"],
        $_POST["__PARAM_16__"],
        $_POST["__PARAM_18__"],
        $_POST["__PARAM_19__"]
    );
}

/**
 * Run a PDO query and output the results
 *
 * @param $pdo PDO PDO connection to use
 * @param $query string Query to run
 *
 * @return void
 */
function __PREFIX__runPDOQuery($pdo, $query) {
    $stmt = $pdo->query($query);
    if ($stmt) {
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($result) {
            echo "[Driver: PDO] Query executed successfully.\n";
            __PREFIX__printAsciiTable($result);
        }
        else {
            echo "[Driver: PDO] Query failed: " . json_encode($pdo->errorInfo()) . "\n";
        }
    }
    else {
        echo "[Driver: PDO] Query failed: " . json_encode($pdo->errorInfo()) . "\n";
    }
}

/**
 * Connect to a database using the given credentials and return the connection
 *
 * @param $db_type string Database type
 * @param $username string Username to connect with
 * @param $password string Password to connect with
 * @param $host string Host to connect to
 * @param $port int|null Port to connect to
 * @param $service_name string|null Service name to use for connection
 * @param $sid string|null SID to use for connection
 * @param $database string|null Database to connect to
 * @param $charset string|null Charset to use for connection
 * @param $options string|null Options to use for connection
 * @param $role string|null Role to use for connection
 * @param $dialect string|null Dialect to use for connection
 * @param $odbc_driver string|null ODBC driver to use for connection
 * @param $server string|null Informix server name
 * @param $protocol string Protocol to use for connection
 * @param $enableScrollableCursors string|null Whether to enable scrollable cursors
 * @param $raw_connection_string string Raw connection string to use for connection
 * @param $query string|null Query to run
 * @param $collection string|null Collection to use for connection
 *
 * @return void
 */
function __PREFIX__connectAndQueryDatabase(
    $db_type,
    $username,
    $password,
    $host = 'localhost',
    $port = null,
    $service_name = null,
    $sid = null,
    $database = null,
    $charset = null,
    $options = null,
    $role = null,
    $dialect = null,
    $odbc_driver = null,
    $server = null,
    $protocol = "onsoctcp",
    $enableScrollableCursors = null,
    $raw_connection_string = "",
    $query = null,
    $collection = null
) {
    if ($db_type === 'mysql') {
        $port = $port ?: 3306;

        // Check if the MySQL extension is loaded
        if (extension_loaded("mysql")) {
            $connection = mysql_connect("$host:$port", $username, $password);

            if (!$connection) {
                echo "[Driver: mysql] Connection failed: " . htmlentities(mysql_error());
            }
            else {
                echo "[Driver: mysql] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;

                if (!empty($query)) {
                    $result = mysql_query($query, $connection);
                    echo "[Driver: mysql] Query executed successfully.\n";
                    if ($result) {
                        $rows = [];
                        while ($row = mysql_fetch_assoc($result)) {
                            $rows[] = $row;
                        }
                        __PREFIX__printAsciiTable($rows);
                    }
                    else {
                        echo "[Driver: mysql] Query failed: " . htmlentities(mysql_error());
                    }
                }
            }
        }
        // Check if the MySQLi extension is loaded
        elseif (extension_loaded("mysqli")) {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

            try {
                $connection = mysqli_connect($host, $username, $password, $database, $port);

                if (!$connection) {
                    echo "[Driver: mysqli] Connection failed: " . htmlentities(mysqli_connect_error());
                }
                else {
                    echo "[Driver: mysqli] Connected successfully using " .
                         htmlentities($username) .
                         ":" .
                         htmlentities($password) .
                         PHP_EOL;

                    if (!empty($query)) {
                        $result = mysqli_query($connection, $query);
                        if ($result) {
                            echo "[Driver: mysqli] Query executed successfully.\n";
                            $rows = [];
                            while ($row = mysqli_fetch_assoc($result)) {
                                $rows[] = $row;
                            }
                            __PREFIX__printAsciiTable($rows);
                        }
                        else {
                            echo "[Driver: mysql] Query failed: " . htmlentities(mysqli_error($connection));
                        }
                    }
                }
            }
            catch (mysqli_sql_exception $e) {
                echo "[Driver: mysqli] Connection failed: " . htmlentities($e->getMessage());
            }
        }
        // Check if the PDO MySQL extension is loaded
        elseif (extension_loaded("pdo_mysql")) {
            try {
                $dsn = "mysql:host=$host;port=$port" .
                       (!empty($database) ? ";dbname=$database" : "") .
                       (!empty($charset) ? ";charset=$charset" : "");

                $pdo = new PDO($dsn, $username, $password);
                echo "[Driver: pdo_mysql] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;

                if (!empty($query)) {
                    __PREFIX__runPDOQuery($pdo, $query);
                }
            }
            catch (PDOException $e) {
                echo "[Driver: pdo_mysql] Connection failed: " . htmlentities($e->getMessage());
            }
        }
        // Check if the PDO extension is loaded but the PDO MySQL driver is not installed
        elseif (extension_loaded("pdo")) {
            echo "[Driver: PDO] PDO extension is loaded but PDO MySQL driver is not installed.\n";
        }
        else {
            echo "[Driver: none] MySQL extension is not loaded.\n";
        }
    }
    elseif ($db_type === 'cubrid') {
        $port = $port ?: 30000;

        // Check if the CUBRID PDO extension is loaded
        if (extension_loaded("pdo_cubrid")) {
            try {
                $dsn = "cubrid:host=$host;port=$port" .
                       (!empty($database) ? ";dbname=$database" : "") .
                       (!empty($charset) ? ";charset=$charset" : "");

                $pdo = new PDO($dsn, $username, $password);
                echo "[Driver: pdo_cubrid] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;;

                if (!empty($query)) {
                    __PREFIX__runPDOQuery($pdo, $query);
                }
            }
            catch (PDOException $e) {
                echo "[Driver: pdo_cubrid] Connection failed: " . htmlentities($e->getMessage());
            }
        }
        // Check if the CUBRID extension is loaded
        elseif (extension_loaded("cubrid")) {
            $connection = cubrid_connect($host, $port, $database, $username, $password);

            if (!$connection) {
                echo "[Driver: cubrid] Connection failed: " . htmlentities(cubrid_error_msg());
            }
            else {
                echo "[Driver: cubrid] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;;

                if (!empty($query)) {
                    $result = cubrid_query($query, $connection);
                    if ($result) {
                        echo "[Driver: cubrid] Query executed successfully.\n";
                        $rows = [];
                        while ($row = cubrid_fetch_assoc($result)) {
                            $rows[] = $row;
                        }
                        __PREFIX__printAsciiTable($rows);
                    }
                    else {
                        echo "[Driver: cubrid] Query failed: " . htmlentities(cubrid_error($connection));
                    }
                }
            }
        }
        else {
            echo "[Driver: none] CUBRID extension is not loaded.\n";
        }
    }
    elseif ($db_type === 'pgsql') {
        $port = $port ?: 5432;

        // Check if the PostgreSQL PDO extension is loaded
        if (extension_loaded("pdo_pgsql")) {
            try {
                $dsn = "pgsql:host=$host;port=$port" . (!empty($database) ? ";dbname=$database" : "");

                $pdo = new PDO($dsn, $username, $password);
                echo "[Driver: pdo_pgsql] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;;

                if (!empty($query)) {
                    __PREFIX__runPDOQuery($pdo, $query);
                }
            }
            catch (PDOException $e) {
                echo "[Driver: pdo_pgsql] Connection failed: " . htmlentities($e->getMessage());
            }
        }
        // Check if the PostgreSQL extension is loaded
        elseif (extension_loaded("pgsql")) {
            $connection = pg_connect("host=$host port=$port dbname=$database user=$username password=$password");

            if (!$connection) {
                echo "[Driver: pgsql] Connection failed: " . htmlentities(pg_last_error());
            }
            else {
                echo "[Driver: pgsql] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;;

                if (!empty($query)) {
                    $result = pg_query($connection, $query);
                    if ($result) {
                        echo "[Driver: pgsql] Query executed successfully.\n";
                        $rows = [];
                        while ($row = pg_fetch_assoc($result)) {
                            $rows[] = $row;
                        }
                        __PREFIX__printAsciiTable($rows);
                    }
                    else {
                        echo "[Driver: pgsql] Query failed: " . htmlentities(pg_last_error($connection));
                    }
                }
            }
        }
        else {
            echo "[Driver: none] PostgreSQL extension is not loaded.\n";
        }
    }
    elseif ($db_type === 'sqlite') {
        // Check if the SQLite PDO extension is loaded
        if (extension_loaded("pdo_sqlite")) {
            try {
                $dsn = "sqlite:$host";

                $pdo = new PDO($dsn);
                echo "[Driver: pdo_sqlite] Connected successfully using " . htmlentities($host) . PHP_EOL;

                if (!empty($query)) {
                    __PREFIX__runPDOQuery($pdo, $query);
                }
            }
            catch (PDOException $e) {
                echo "[Driver: pdo_sqlite] Connection failed: " . htmlentities($e->getMessage());
            }
        }
        // Check if the SQLite extension is loaded
        elseif (extension_loaded("sqlite3")) {
            $connection = sqlite_open($host, 0666, $error);

            if (!$connection) {
                echo "[Driver: sqlite3] Connection failed: " . htmlentities($error);
            }
            else {
                echo "[Driver: sqlite3] Connected successfully using " . htmlentities($host) . PHP_EOL;

                if (!empty($query)) {
                    $result = sqlite_query($connection, $query);
                    if ($result) {
                        echo "[Driver: sqlite3] Query executed successfully.\n";
                        $rows = [];
                        while ($row = sqlite_fetch_array($result)) {
                            $rows[] = $row;
                        }
                        __PREFIX__printAsciiTable($rows);
                    }
                    else {
                        echo "[Driver: sqlite3] Query failed: " .
                             htmlentities(sqlite_error_string(sqlite_last_error($connection)));
                    }
                }
            }
        }
        else {
            echo "[Driver: none] SQLite extension is not loaded.\n";
        }
    }
    elseif ($db_type === 'sqlsrv') {
        $port = $port ?: 1433;

        // Check if the SQL Server PDO extension is loaded
        if (extension_loaded("pdo_sqlsrv")) {
            try {
                $dsn = "sqlsrv:Server=$host,$port" . (!empty($database) ? ";Database=$database" : "");

                $pdo = new PDO($dsn, $username, $password);
                echo "[Driver: pdo_sqlsrv] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;;

                if (!empty($query)) {
                    __PREFIX__runPDOQuery($pdo, $query);
                }
            }
            catch (PDOException $e) {
                echo "[Driver: pdo_sqlsrv] Connection failed: " . htmlentities($e->getMessage());
            }
        }
        // Check if the SQL Server extension is loaded
        elseif (extension_loaded("sqlsrv")) {
            echo "Connecting to $host with default instance specification ...\n";
            $connection = sqlsrv_connect($host, ["Database" => $database, "UID" => $username, "PWD" => $password]);

            if (!$connection) {
                echo "[Driver: sqlsrv] Connection failed: " . htmlentities(sqlsrv_errors());
                echo "[Driver: sqlsrv] Trying to connect to " .
                     htmlentities($host) .
                     "," .
                     htmlentities($port) .
                     "...\n";

                $connection = sqlsrv_connect(
                    "$host,$port",
                    ["Database" => $database, "UID" => $username, "PWD" => $password]
                );

                if (!$connection) {
                    echo "[Driver: sqlsrv] Connection failed: " . htmlentities(sqlsrv_errors());
                }
                else {
                    echo "[Driver: sqlsrv] Connected successfully using " .
                         htmlentities($username) .
                         ":" .
                         htmlentities($password) .
                         " (host,port).\n";
                }
            }
            else {
                echo "[Driver: sqlsrv] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     " (host only).\n";
            }

            if (!empty($query) && $connection) {
                $result = sqlsrv_query($connection, $query);
                if ($result) {
                    echo "[Driver: sqlsrv] Query executed successfully.\n";
                    $rows = [];
                    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                        $rows[] = $row;
                    }
                    __PREFIX__printAsciiTable($rows);
                }
                else {
                    echo "[Driver: sqlsrv] Query failed: " . htmlentities(sqlsrv_errors());
                }
            }
        }
        else {
            echo "[Driver: none] SQL Server extension is not loaded.\n";
        }
    }
    elseif ($db_type === 'oci') {
        $port = $port ?: 1521;

        // Check if the Oracle PDO extension is loaded
        if (extension_loaded("pdo_oci")) {
            try {
                if (!empty($sid)) {
                    $tns = "(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)(HOST = $host)(PORT = $port))(CONNECT_DATA = (SID = $sid)))";
                }
                else {
                    $tns = "(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)(HOST = $host)(PORT = $port))(CONNECT_DATA = (SERVICE_NAME = $service_name)))";
                }
                $dsn = "oci:dbname=$tns";

                $pdo = new PDO($dsn, $username, $password);
                echo "[Driver: pdo_oci] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;;

                if (!empty($query)) {
                    __PREFIX__runPDOQuery($pdo, $query);
                }
            }
            catch (PDOException $e) {
                echo "[Driver: pdo_oci] Connection failed: " . htmlentities($e->getMessage());
            }
        }
        // Check if the Oracle extension is loaded
        elseif (extension_loaded("oci8")) {
            $connection = oci_connect($username, $password, "$host:$port/$service_name");

            if (!$connection) {
                echo "[Driver: oci8] Connection failed: " . htmlentities(oci_error());
            }
            else {
                echo "[Driver: oci8] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;;

                if (!empty($query)) {
                    $statement = oci_parse($connection, $query);
                    if ($statement) {
                        if (oci_execute($statement)) {
                            echo "[Driver: oci8] Query executed successfully.\n";
                            $rows = [];
                            while ($row = oci_fetch_assoc($statement)) {
                                $rows[] = $row;
                            }
                            __PREFIX__printAsciiTable($rows);
                        }
                        else {
                            echo "[Driver: oci8] Query failed: " . htmlentities(oci_error($statement));
                        }
                    }
                    else {
                        echo "[Driver: oci8] Query failed: " . htmlentities(oci_error($connection));
                    }
                }
            }
        }
        else {
            echo "[Driver: none] Oracle extension is not loaded.\n";
        }
    }
    elseif ($db_type === 'mongodb') {
        $port = $port ?: 27017;
        $dsn  = "mongodb://$username:$password@$host:$port/$database";

        // Check if the MongoDB extension is loaded
        if (extension_loaded("mongodb")) {
            try {
                $connection = new MongoDB\Driver\Manager($dsn, explode("&", $options));
                echo "[Driver: mongodb] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;;

                if (!empty($query)) {
                    $query  = new MongoDB\Driver\Query([]);
                    $cursor = $connection->executeQuery("$database.$collection", $query);

                    $rows = [];
                    foreach ($cursor as $row) {
                        $rows[] = $row;
                    }
                    __PREFIX__printAsciiTable($rows);
                }
            }
            catch (MongoDB\Driver\Exception\Exception $e) {
                echo "[Driver: mongodb] Connection failed: " . htmlentities($e->getMessage());
            }
        }
        // Check if the Mongo extension is loaded
        elseif (extension_loaded("mongo")) {
            try {
                $connection = new Mongo($dsn, array_merge(["connect" => true], explode("&", $options)));
                echo "[Driver: mongo] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;;

                if (!empty($query)) {
                    $collection = $connection->selectCollection($database, $collection);
                    $cursor     = $collection->find();

                    $rows = [];
                    foreach ($cursor as $row) {
                        $rows[] = $row;
                    }
                    __PREFIX__printAsciiTable($rows);
                }
            }
            catch (MongoConnectionException $e) {
                echo "[Driver: mongo] Connection failed: " . htmlentities($e->getMessage());
            }
            catch (Exception $e) {
                echo "[Driver: mongo] Connection failed: " . htmlentities($e->getMessage());
            }
        }
        else {
            echo "[Driver: none] MongoDB extension is not loaded.\n";
        }
    }
    elseif ($db_type === 'ibm') {
        $port = $port ?: 50000;
        $dsn  = "ibm:DRIVER={IBM DB2 ODBC DRIVER};DATABASE=$database;HOSTNAME=$host;PORT=$port;PROTOCOL=TCPIP;UID=$username;PWD=$password;";

        // Check if the IBM PDO extension is loaded
        if (extension_loaded("pdo_ibm")) {
            try {
                $pdo = new PDO($dsn);
                echo "[Driver: pdo_ibm] Connected successfully using $" .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;;

                if (!empty($query)) {
                    __PREFIX__runPDOQuery($pdo, $query);
                }
            }
            catch (PDOException $e) {
                echo "[Driver: pdo_ibm] Connection failed: " . htmlentities($e->getMessage());
            }
        }
        // Check if the IBM extension is loaded
        elseif (extension_loaded("ibm")) {
            $connection = db2_connect($dsn, $username, $password);

            if (!$connection) {
                echo "[Driver: ibm] Connection failed: " . htmlentities(db2_conn_error());
            }
            else {
                echo "[Driver: ibm] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;;

                if (!empty($query)) {
                    $result = db2_exec($connection, $query);
                    if ($result) {
                        echo "[Driver: ibm] Query executed successfully.\n";
                        $rows = [];
                        while ($row = db2_fetch_assoc($result)) {
                            $rows[] = $row;
                        }
                        __PREFIX__printAsciiTable($rows);
                    }
                    else {
                        echo "[Driver: ibm] Query failed: " . htmlentities(db2_conn_error());
                    }
                }
            }
        }
        else {
            echo "[Driver: none] IBM extension is not loaded.\n";
        }
    }
    elseif ($db_type === 'firebird') {
        $port = $port ?: 3050;
        $dsn  = "firebird:dbname=$host/$port:$database" .
                (!empty($charset) ? ";charset=$charset" : "") .
                (!empty($role) ? ";role=$role" : "") .
                (!empty($dialect) ? ";dialect=$dialect" : "");

        // Check if the Firebird PDO extension is loaded
        if (extension_loaded("pdo_firebird")) {
            try {
                $pdo = new PDO($dsn, $username, $password);
                echo "[Driver: pdo_firebird] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;;

                if (!empty($query)) {
                    __PREFIX__runPDOQuery($pdo, $query);
                }
            }
            catch (PDOException $e) {
                echo "[Driver: pdo_firebird] Connection failed: " . htmlentities($e->getMessage());
            }
        }
        // Check if the Firebird extension is loaded
        elseif (extension_loaded("interbase")) {
            echo "Connecting to $host/$port:$database (TCP/IP on custom port) ...\n";
            $connection = ibase_connect($host . "/" . $port . ":" . $database, $username, $password);

            if (!$connection) {
                echo "[Driver: interbase] Connection failed: " . htmlentities(ibase_errmsg());
                echo "[Driver: interbase] Trying to connect to " .
                     htmlentities($host) .
                     ":" .
                     htmlentities($database) .
                     " (TCP/IP implicit port) ...\n";

                $connection = ibase_connect($host . ":" . $database, $username, $password);

                if (!$connection) {
                    echo "[Driver: interbase] Connection failed: " . htmlentities(ibase_errmsg());
                    echo "[Driver: interbase] Trying to connect to //" .
                         htmlentities($host) .
                         "/" .
                         htmlentities($database) .
                         " (NetBEUI) ...\n";

                    $connection = ibase_connect("//" . $host . "/" . $database, $username, $password);

                    if (!$connection) {
                        echo "[Driver: interbase] Connection failed: " . htmlentities(ibase_errmsg());
                    }
                    else {
                        echo "[Driver: interbase] Connected successfully using " .
                             htmlentities($username) .
                             ":" .
                             htmlentities($password) .
                             " (//host/database aka NetBEUI).\n";
                    }
                }
                else {
                    echo "[Driver: interbase] Connected successfully using " .
                         htmlentities($username) .
                         ":" .
                         htmlentities($password) .
                         " (host:database).\n";
                }
            }
            else {
                echo "[Driver: interbase] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     " (host/port:database).\n";
            }

            if (!empty($query) && $connection) {
                $result = ibase_query($connection, $query);
                if ($result) {
                    echo "[Driver: interbase] Query executed successfully.\n";
                    $rows = [];
                    while ($row = ibase_fetch_assoc($result)) {
                        $rows[] = $row;
                    }
                    __PREFIX__printAsciiTable($rows);
                }
                else {
                    echo "[Driver: interbase] Query failed: " . htmlentities(ibase_errmsg());
                }
            }
        }
        else {
            echo "[Driver: none] Firebird extension is not loaded.\n";
        }
    }
    elseif ($db_type === 'odbc') {
        $dsn = "odbc:Driver=$odbc_driver;Server=$host,$port;Database=$database;Uid=$username;Pwd=$password;";

        // Check if the ODBC PDO extension is loaded
        if (extension_loaded("pdo_odbc")) {
            try {
                $pdo = new PDO($dsn);
                echo "[Driver: pdo_odbc] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;;

                if (!empty($query)) {
                    __PREFIX__runPDOQuery($pdo, $query);
                }
            }
            catch (PDOException $e) {
                echo "[Driver: pdo_odbc] Connection failed: " . htmlentities($e->getMessage());
            }
        }
        // Check if the ODBC extension is loaded
        elseif (extension_loaded("odbc")) {
            $connection = odbc_connect($dsn, $username, $password);

            if (!$connection) {
                echo "[Driver: odbc] Connection failed: " . htmlentities(odbc_errormsg());
            }
            else {
                echo "[Driver: odbc] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;;

                if (!empty($query)) {
                    $result = odbc_exec($connection, $query);
                    if ($result) {
                        echo "[Driver: odbc] Query executed successfully.\n";
                        $rows = [];
                        while ($row = odbc_fetch_array($result)) {
                            $rows[] = $row;
                        }
                        __PREFIX__printAsciiTable($rows);
                    }
                    else {
                        echo "[Driver: odbc] Query failed: " . htmlentities(odbc_errormsg());
                    }
                }
            }
        }
        else {
            echo "[Driver: none] ODBC extension is not loaded.\n";
        }
    }
    elseif ($db_type === 'informix') {
        $port = $port ?: 9800;
        $dsn  = "informix:host=$host;service=$port;database=$database;server=$server;protocol=$protocol;EnableScrollableCursors=$enableScrollableCursors";

        // Check if the Informix PDO extension is loaded
        if (extension_loaded("pdo_informix")) {
            try {
                $pdo = new PDO($dsn, $username, $password);
                echo "[Driver: pdo_informix] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;;

                if (!empty($query)) {
                    __PREFIX__runPDOQuery($pdo, $query);
                }
            }
            catch (PDOException $e) {
                echo "[Driver: pdo_informix] Connection failed: " . htmlentities($e->getMessage());
            }
        }
        else {
            echo "[Driver: none] Informix extension is not loaded.\n";
        }
    }
    elseif ($db_type === 'sybase') {
        $port = $port ?: 5000;
        $dsn  = "sybase:host=$host:$port" . (!empty($database) ? ";dbname=$database" : "");

        // Check if the Sybase PDO extension is loaded
        if (extension_loaded("pdo_dblib")) {
            try {
                $pdo = new PDO($dsn, $username, $password);
                echo "[Driver: pdo_dblib] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;;

                if (!empty($query)) {
                    __PREFIX__runPDOQuery($pdo, $query);
                }
            }
            catch (PDOException $e) {
                echo "[Driver: pdo_dblib] Connection failed: " . htmlentities($e->getMessage());
            }
        }
        // Check if the Sybase extension is loaded
        elseif (extension_loaded("sybase")) {
            $connection = sybase_connect($host, $username, $password);

            if (!$connection) {
                echo "[Driver: sybase] Connection failed: " . htmlentities(sybase_get_last_message());
            }
            else {
                echo "[Driver: sybase] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;;

                if (!empty($query)) {
                    $result = sybase_query($query, $connection);
                    if ($result) {
                        echo "[Driver: sybase] Query executed successfully.\n";
                        $rows = [];
                        while ($row = sybase_fetch_assoc($result)) {
                            $rows[] = $row;
                        }
                        __PREFIX__printAsciiTable($rows);
                    }
                    else {
                        echo "[Driver: sybase] Query failed: " . htmlentities(sybase_get_last_message());
                    }
                }
            }
        }
        // Check if the FreeTDS extension is loaded
        elseif (extension_loaded("mssql")) {
            $connection = mssql_connect($host, $username, $password);

            if (!$connection) {
                echo "[Driver: mssql] Connection failed: " . htmlentities(mssql_get_last_message());
            }
            else {
                echo "[Driver: mssql] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;;

                if (!empty($query)) {
                    $result = mssql_query($query, $connection);
                    if ($result) {
                        echo "Query executed successfully.\n";
                        while ($row = mssql_fetch_assoc($result)) {
                            echo json_encode($row);
                        }
                    }
                    else {
                        echo "Query failed: " . htmlentities(mssql_get_last_message());
                    }
                }
            }
        }
        else {
            echo "[Driver: none] Sybase extension is not loaded.\n";
        }
    }
    elseif ($db_type === 'raw') {
        $dsn = $raw_connection_string;

        // Check if the PDO extension is loaded
        if (extension_loaded("pdo")) {
            try {
                $pdo = new PDO($dsn, $username, $password);
                echo "[Driver: PDO] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;;

                if (!empty($query)) {
                    __PREFIX__runPDOQuery($pdo, $query);
                }
            }
            catch (PDOException $e) {
                echo "[Driver: PDO] Connection failed: " . htmlentities($e->getMessage());
            }
        }
        else {
            echo "[Driver: PDO] PDO extension is not loaded.\n";
        }
    }
    else {
        echo "[Driver: none] Unsupported database type: " . htmlentities($db_type) . PHP_EOL;
    }
}

/**
 * Hook the features to add the login feature
 *
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function __PREFIX__queryDatabaseHooksFeatures(&$features) {
    global $QUERY_DATABASES;

    $features[] = ["title"       => "Query databases", "description" => "Query databases using the provided credentials.", "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
</svg>', "op"          => $QUERY_DATABASES];
}

// section.functions.end

// section.hooks
add_hook("features", "__PREFIX__queryDatabaseHooksFeatures");
add_named_hook("GET_page", $QUERY_DATABASES, "__PREFIX__makeQueryDatabasePage");
add_named_hook("POST_operation", $QUERY_DATABASES, "__PREFIX__handleQueryDatabase");
// section.hooks.end