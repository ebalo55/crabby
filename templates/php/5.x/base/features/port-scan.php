<?php

// section.constants
$PORT_SCAN = "__FEAT_PORT_SCAN__";
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
function __PREFIX__makePortScanPage(&$page_content, $features, $page, $css) {
    $page_content = __PREFIX__makePage(
        $features,
        $css,
        $page,
        array(
            __PREFIX__makePageHeader(
                $features[$page]["title"],
                $features[$page]["description"]
            ),
            __PREFIX__makeForm(
                $page,
                $_SERVER["REQUEST_URI"],
                array(
                    __PREFIX__makeInput(
                        "text",
                        "Host",
                        "__PARAM_1__",
                        "localhost",
                        "The host to connect to",
                        true
                    ),
                    __PREFIX__makeInput(
                        "number",
                        "Starting port",
                        "__PARAM_2__",
                        "1",
                        "Starting port of the scan (included)",
                        true
                    ),
                    __PREFIX__makeInput(
                        "number",
                        "Ending port",
                        "__PARAM_3__",
                        "65535",
                        "Ending port of the scan (included)",
                        true
                    ),
                )
            ),
        )
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
function __PREFIX__handlePortScan($operation, $features) {
    $host      = $_POST['__PARAM_1__'];
    $startPort = intval($_POST['__PARAM_2__']);
    $endPort   = intval($_POST['__PARAM_3__']);

    echo "Scanning ports $startPort to $endPort on " . htmlentities($host) . "...\n";

    // Loop through the port range
    for ($port = $startPort; $port <= $endPort; $port++) {
        // Attempt to connect to the host on the current port
        $socket = @fsockopen($host, $port, $errno, $errstr, 1);

        // Check if the connection was successful
        if ($socket) {
            // The port is open
            fclose($socket);
            echo "Port $port: OPEN\n";
        }
        else {
            // The port is closed or unreachable
            echo "Port $port: CLOSED / UNREACHABLE (err: $errstr)\n";
        }
        flush();
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
function __PREFIX__portScanHooksFeatures(&$features) {
    global $PORT_SCAN;

    $features[] = array(
        "title"       => "Port scan",
        "description" => "Scan a given range of TCP ports using connect method.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.25-8.25-3.286Zm0 13.036h.008v.008H12v-.008Z" />
</svg>',
        "op"          => $PORT_SCAN,
    );
}

// section.functions.end

// section.hooks
add_hook("features", "__PREFIX__portScanHooksFeatures");
add_named_hook("GET_page", $PORT_SCAN, "__PREFIX__makePortScanPage");
add_named_hook("POST_operation", $PORT_SCAN, "__PREFIX__handlePortScan");
// section.hooks.end