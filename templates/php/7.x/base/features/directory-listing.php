<?php

// section.constants
$DIRECTORY_LISTING = "__FEAT_DIRECTORY_LISTING__";
// section.constants.end

// section.functions
/**
 * Create the login page
 *
 * @param $page_content string The page content container
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 * @param $page string The current page
 * @param $css string The CSS of the page
 */
function __PREFIX__makeDirectoryListingPage(&$page_content, $features, $page, $css) {
    $feature = array_values(array_filter($features, function ($feature) use ($page) {
        return $feature["op"] === $page;
    }));

    $page_content = __PREFIX__makePage(
        $features,
        $css,
        $page,
        [__PREFIX__makePageHeader(
            $feature[0]["title"],
            $feature[0]["description"]
        ), __PREFIX__makeForm(
            $page,
            $_SERVER["REQUEST_URI"],
            [__PREFIX__makeInput(
                "text",
                "Path",
                "__PARAM_1__",
                "C://path/to/directory or \\\\network\\path\\to\\directory",
                "Fully qualified path to the directory to list.",
                true
            ), __PREFIX__makeInput(
                "text",
                "Depth",
                "__PARAM_2__",
                "5",
                "How many levels deep to list, where " . __PREFIX__makeCodeHighlight(0) .
                " is the current directory and " . __PREFIX__makeCodeHighlight("inf") .
                " means to list all.",
                true
            )]
        )]
    );
}

/**
 * Handle the directory listing operation
 *
 * @param $operation string The operation to handle
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features container
 *
 * @return void
 */
function __PREFIX__handleDirectoryListing($operation, $features) {
    $max_depth = strtolower($_POST['__PARAM_2__']) === "inf" ? INF : intval($_POST['__PARAM_2__']);

    __PREFIX__listFilesRecursive($_POST['__PARAM_1__'], $max_depth);
}

/**
 * Get the permissions string for a file or directory (unix like `ls -l` output)
 *
 * @param $path string Path to get permissions for
 *
 * @return string
 */
function __PREFIX__getPermissionsString($path) {
    if (!file_exists($path)) {
        return "----------";
    }

    $perms = fileperms($path);

    // Determine file type
    $type = '';
    if (is_dir($path)) {
        $type = 'd';
    }
    elseif (is_file($path)) {
        $type = '-';
    }

    // Owner permissions
    $owner_perms = ($perms & 0x0100) ? 'r' : '-';
    $owner_perms .= ($perms & 0x0080) ? 'w' : '-';
    $owner_perms .= ($perms & 0x0040) ? (($perms & 0x0800) ? 's' : 'x') : (($perms & 0x0800) ? 'S' : '-');

    // Group permissions
    $group_perms = ($perms & 0x0020) ? 'r' : '-';
    $group_perms .= ($perms & 0x0010) ? 'w' : '-';
    $group_perms .= ($perms & 0x0008) ? (($perms & 0x0400) ? 's' : 'x') : (($perms & 0x0400) ? 'S' : '-');

    // Other permissions
    $other_perms = ($perms & 0x0004) ? 'r' : '-';
    $other_perms .= ($perms & 0x0002) ? 'w' : '-';
    $other_perms .= ($perms & 0x0001) ? (($perms & 0x0200) ? 't' : 'x') : (($perms & 0x0200) ? 'T' : '-');

    return $type . $owner_perms . $group_perms . $other_perms;
}

/**
 * Get the stat for the current path and print information
 *
 * @param $path string Path to get stat for
 *
 * @return array
 */
function __PREFIX__getStatForCurrentPath($path) {
    $stat = stat($path);

    // Print information for current path
    $perm = __PREFIX__getPermissionsString($path);

    // Output `ls -lah` like format
    echo "$perm " .
         __PREFIX__pad_right("" . $stat["nlink"], 3) .
         " " .
         __PREFIX__pad_right("" . $stat["uid"], 5) . // always 0 on windows
         " " .
         __PREFIX__pad_right("" . $stat["gid"], 5) . // always 0 on windows
         " " .
         __PREFIX__formatBytes($stat["size"]) .
         " " .
         __PREFIX__convertUnixTimestampToDate($stat["mtime"]) .
         " " . htmlentities($path) . PHP_EOL;

    return [$stat, $perm];
}

/**
 * List files recursively
 *
 * @param $path string Path to list
 * @param $max_depth int Maximum depth to list
 * @param $depth int Current depth
 * @param $show_line_split bool Whether to show a line split between entries
 *
 * @return void
 */
function __PREFIX__listFilesRecursive($path, $max_depth, $depth = 0, $show_line_split = true) {
    // Get stat for current path
    __PREFIX__getStatForCurrentPath($path);

    if ($show_line_split) {
        echo "----------------\n";
    }

    // Check if path is a directory and is readable
    if (is_dir($path) && is_readable($path)) {

        // Open directory handle
        $dir_handle = opendir($path);

        // Loop through directory contents
        while (($file = readdir($dir_handle)) !== false) {

            // Ignore '.' and '..'
            if ($file !== '.' && $file !== '..') {
                $sub_path = "$path/$file";

                // Recursively list files if depth is less than max depth
                if ($depth < $max_depth) {
                    __PREFIX__listFilesRecursive($sub_path, $max_depth, $depth + 1, false);
                }
                else {
                    // Print information for files beyond max depth
                    __PREFIX__getStatForCurrentPath($sub_path);
                }
            }
        }
        // Close directory handle
        closedir($dir_handle);
    }
}

/**
 * Hook the features to add the login feature
 *
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function __PREFIX__directoryListingHooksFeatures(&$features) {
    global $DIRECTORY_LISTING;

    $features[] = ["title"       => "Directory listing", "description" => "List all files and folders in a directory and optionally its subdirectories.", "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" />
</svg>', "op"          => $DIRECTORY_LISTING];
}
// section.functions.end

// section.hooks
add_hook("features", "__PREFIX__directoryListingHooksFeatures");
add_named_hook("GET_page", $DIRECTORY_LISTING, "__PREFIX__makeDirectoryListingPage");
add_named_hook("POST_operation", $DIRECTORY_LISTING, "__PREFIX__handleDirectoryListing");
// section.hooks.end