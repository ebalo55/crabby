<?php

// section.constants
$LOGIN    = "__FEAT_LOGIN__";
$USERNAME = "__USERNAME__";
$PASSWORD = "__PASSWORD__";
$SALT     = '__SALT__';
// section.constants.end

// section.functions
/**
 * Create the login page
 */
function __PREFIX__makeExamplePage(&$page_content, $page, $css) {
    $username = !empty($_GET["username"]) ? htmlentities($_GET["username"]) : false;
    $error    = !empty($_GET["error"]) ? htmlentities($_GET["error"]) : false;

    ob_start();
    // ...
    $page_content = ob_get_clean();
}

/**
 * Handle the login operation
 *
 * @param $operation string The operation to handle
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *
 * @return void
 */
function __PREFIX__handleExample($operation, $features) {
    global $SALT, $PASSWORD, $USERNAME;

    //...
}

/**
 * Hook the page generation to check if the user is authenticated
 *
 * @return void
 */
function __PREFIX__example_hooks_page_generation() {
    global $LOGIN;

    // ...
}

/**
 * Hook the isolated operations to add the current operation
 *
 * @param $isolated_ops array The isolated operations container
 *
 * @return void
 */
function __PREFIX__example_hooks_isolated_ops(&$isolated_ops) {
    global $LOGIN;

    $isolated_ops[] = $LOGIN;
}

/**
 * Hook the features to add the login feature
 *
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function __PREFIX__example_hooks_features(&$features) {
    global $LOGIN;

    // ...
}

// section.functions.end

// section.hooks
add_hook("page_generation", "__PREFIX__login_hooks_page_generation");
add_hook("isolated_ops", "__PREFIX__login_hooks_isolated_ops");
add_hook("features", "__PREFIX__login_hooks_features");
add_named_hook("GET_page", $LOGIN, "__PREFIX__makeLoginPage");
add_named_hook("POST_operation", $LOGIN, "__PREFIX__handleLogin");
// section.hooks.end