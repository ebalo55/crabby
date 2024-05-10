<?php

// section.constants
$QUERY_LDAP = "__FEAT_QUERY_LDAP__";
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
function __PREFIX__makeQueryLDAPPage(&$page_content, $features, $page, $css) {
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
                        "Domain controller",
                        "__PARAM_1__",
                        "hostname or IP address",
                        "The domain controller to connect to.",
                        true
                    ),
                    __PREFIX__makeInput(
                        "text",
                        "LDAP port",
                        "__PARAM_2__",
                        "389",
                        "The port to connect to."
                    ),
                    __PREFIX__makeInput(
                        "text",
                        "Domain",
                        "__PARAM_3__",
                        "example.com",
                        "The domain to connect to.",
                        true
                    ),
                    __PREFIX__makeInput(
                        "text",
                        "Username",
                        "__PARAM_4__",
                        "admin",
                        "The username to connect with."
                    ),
                    __PREFIX__makeInput(
                        "password",
                        "Password",
                        "__PARAM_5__",
                        "&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;",
                        "The password to connect with."
                    ),
                    __PREFIX__makeInput(
                        "textarea",
                        "Query",
                        "__PARAM_6__",
                        "(&(objectClass=user)(sAMAccountName=*))",
                        "The LDAP query to run against the domain controller.",
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
function __PREFIX__handleQueryLDAP($operation, $features) {
    __PREFIX__runLDAPQuery(
        $_POST["__PARAM_1__"],
        !empty($_POST["__PARAM_2__"]) ? intval($_POST["__PARAM_2__"]) : null,
        !empty($_POST["__PARAM_4__"]) ? $_POST["__PARAM_4__"] : null,
        !empty($_POST["__PARAM_5__"]) ? $_POST["__PARAM_5__"] : null,
        $_POST["__PARAM_3__"],
        $_POST["__PARAM_6__"]
    );
}

/**
 * @param $server string LDAP server
 * @param $port int|null LDAP port
 * @param $username string|null LDAP username
 * @param $password string|null LDAP password
 * @param $domain string LDAP domain
 * @param $query string LDAP query
 *
 * @return void
 */
function __PREFIX__runLDAPQuery($server, $port, $username, $password, $domain, $query) {
    $port = $port ?: 389;

    // Connect to LDAP server
    $ldap_conn = ldap_connect("ldap://$server", $port);

    if (!$ldap_conn) {
        echo "Connection failed: " . ldap_error($ldap_conn);
        return;
    }

    $base_dn = "DC=" . implode(",DC=", explode(".", $domain));
    echo "Connected successfully to LDAP server $server:$port.\n";
    echo "Base DN: $base_dn\n";

    // Set LDAP options
    ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 1);
    ldap_set_option($ldap_conn, LDAP_DEREF_ALWAYS, 1);

    // Bind to LDAP server
    if (!empty($username) && !empty($password)) {
        $username = "CN=$username,$base_dn";
        echo "Binding with username: $username\n";

        // Bind with username and password (authenticating)
        $ldap_bind = ldap_bind($ldap_conn, $username, $password);
    }
    else {
        echo "Binding anonymously\n";
        $ldap_bind = ldap_bind($ldap_conn);
    }

    if (!$ldap_bind) {
        echo "Bind failed: " . ldap_error($ldap_conn);
        return;
    }

    // Perform LDAP search
    $ldap_search = ldap_search($ldap_conn, $base_dn, trim($query), array("*"), 0, 0);

    if (!$ldap_search) {
        echo "Search failed: " . ldap_error($ldap_conn);
        return;
    }

    // Get search result entries
    $ldap_entries = ldap_get_entries($ldap_conn, $ldap_search);

    if (!$ldap_entries) {
        echo "Search failed: " . ldap_error($ldap_conn);
        return;
    }

    echo "Query executed successfully (Query: $query).\n";
    echo json_encode($ldap_entries);

    // Close LDAP connection
    ldap_unbind($ldap_conn);
}

/**
 * Hook the features to add the login feature
 *
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function __PREFIX__query_ldap_hooks_features(&$features) {
    global $QUERY_LDAP;

    $features[] = array(
        "title"       => "Query LDAP",
        "description" => "Query LDAP using the provided credentials.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
</svg>',
        "op"          => $QUERY_LDAP,
    );
}

// section.functions.end

// section.hooks
add_hook("features", "__PREFIX__query_ldap_hooks_features");
add_named_hook("GET_page", $QUERY_LDAP, "__PREFIX__makeQueryLDAPPage");
add_named_hook("POST_operation", $QUERY_LDAP, "__PREFIX__handleQueryLDAP");
// section.hooks.end