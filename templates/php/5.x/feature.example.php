<?php

// section.constants
$EXAMPLE = "__FEAT_EXAMPLE__";
// section.constants.end

// section.functions
/**
 * Create the example page
 *
 * @param $page_content string The page content container
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features container
 * @param $page string The current page
 * @param $css string The CSS of the page
 */
function __PREFIX__makeExamplePage(&$page_content, $features, $page, $css) {
    $page_content = __PREFIX__makePage(
        $features,
        $page,
        $css,
        array(/* Add your content here */)
    );
}

/**
 * Handle the login operation
 *
 * @param $operation string The operation to handle
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features container
 *
 * @return void
 */
function __PREFIX__handleExample($operation, $features) {
    //...
}

/**
 * Hook the page generation to check if the user is authenticated
 *
 * @return void
 */
function __PREFIX__exampleHooksPageGeneration() {
    global $EXAMPLE;

    // ...
}

/**
 * Hook the isolated operations to add the current operation
 *
 * @param $isolated_ops array The isolated operations container
 *
 * @return void
 */
function __PREFIX__exampleHooksIsolatedOps(&$isolated_ops) {
    global $EXAMPLE;

    $isolated_ops[] = $EXAMPLE;
}

/**
 * Hook the features to add the login feature
 *
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function __PREFIX__exampleHooksFeatures(&$features) {
    global $EXAMPLE;

    $features[] = array(
        "title"       => "Example",
        "description" => "Example feature",
        "svg"         => 'INSERT RAW SVG HERE',
        "op"          => $EXAMPLE,
    );
}

// section.functions.end

// section.hooks
add_hook("page_generation", "__PREFIX__exampleHooksPageGeneration");
add_hook("isolated_ops", "__PREFIX__exampleHooksIsolatedOps");
add_hook("features", "__PREFIX__exampleHooksFeatures");
add_named_hook("GET_page", $EXAMPLE, "__PREFIX__makeExamplePage");
add_named_hook("POST_operation", $EXAMPLE, "__PREFIX__handleExample");
// section.hooks.end