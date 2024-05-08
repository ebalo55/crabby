<?php

/**
 * Check if the request method is POST
 *
 * @return bool
 */
function __PREFIX__isPost() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Load the page or get the fallback page
 *
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return mixed
 */
function __PREFIX__loadPageOrDefault($features) {
    return isset($_GET['page']) ? $_GET['page'] : $features[0]["op"];
}

/**
 * Render the page content by calling the named hook for the current page
 *
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features container
 * @param $page string The page to render
 *
 * @return void
 */
function __PREFIX__renderPage($features, $page) {
    global $css;
    // Load the page content by calling the named hook for the current page
    $content = "";
    $args = array(&$content, $features, $page, $css);
    call_named_hook("GET_page", $page, $args);

    // Render the page content
    echo $content;
}

/**
 * Check if the operation is in the isolated operations list
 *
 * @param $operation string The operation to check
 * @param $isolated_ops array The isolated operations list
 *
 * @return bool
 */
function __PREFIX__isIsolatedOperation($operation, $isolated_ops) {
    return in_array($operation, $isolated_ops);
}

/**
 * Open the command output screen where output can be freely written
 *
 * @param string $classes Classes to apply to the command output screen
 * @param string $title Title of the command output screen
 * @param bool $no_margin Whether to remove the margin from the command output screen
 * @param bool $no_padding Whether to remove the padding from the command output screen
 *
 * @return void
 */
function __PREFIX__openCommandOutputScreen(
    $classes = "",
    $title = "Command output",
    $no_margin = false,
    $no_padding = false
) {
    ?>
    <div class="<?php
    echo $no_margin ? "" : "ml-72 ";
    echo $no_padding ? "" : "py-10 ";
    ?>">
    <div class="container px-16">
    <div class="bg-zinc-900 font-mono text-sm overflow-auto rounded shadow-md text-white px-6 py-3">
    <h3 class="border-b border-zinc-700 text-sm font-semibold leading-8">
        <?php echo $title ?>
    </h3>
    <pre class="p-2 mt-2 <?php echo $classes ?>"><?php
}

/**
 * Closes the command output screen
 *
 * @return void
 */
function __PREFIX__closeCommandOutputScreen() {
    ?></pre>
    </div>
    </div>
    </div>
    <?php
}

/**
 * Create a page with the given elements
 *
 * @param $enabled_features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The
 *     features container
 * @param $css string CSS to include in the page
 * @param $current_page string Current page to highlight in the navigation
 * @param $elements string[] Elements to include in the page
 *
 * @return string
 */
function __PREFIX__makePage($enabled_features, $css, $current_page, $elements) {
    $args = array(&$elements, $current_page, $css);
    call_hook("page_generation", $args);

    ob_start();
    ?>
    <html lang="en">
    <head>
        <title>__TITLE__</title>
        <style><?= $css ?></style>
        <script>__JS__</script>
    </head>
    <body class="bg-white">
    <div class="fixed inset-y-0 z-50 w-72 flex flex-col">
        <div class="flex flex-grow flex-col gap-y-5 overflow-y-auto bg-zinc-900 px-6 pb-4">
            <div class="flex items-center h-16 shrink-0">
                <h1 class="text-2xl text-white">__TITLE__</h1>
            </div>
            <nav class="flex flex-1 flex-col">
                <ul role="list" class="flex flex-1 flex-col gap-y-7">
                    <li>
                        <ul role="list" class="-mx-2">
                            <?php
                            foreach ($enabled_features as $feature => $definition) {
                                echo __PREFIX__makeNavLink($feature, $current_page, $definition);
                            }
                            ?>
                        </ul>
                    </li>
                </ul>
            </nav>
        </div>
    </div>
    <div class="ml-72">
        <main class="py-10">
            <div class="container px-16">
                <?php
                foreach ($elements as $element) {
                    echo $element;
                }
                ?>
            </div>
        </main>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Create a navigation link
 *
 * @param $page string Page to link to
 * @param $current_page string Current page to highlight
 * @param $definition array{title: string, description: string, svg: string, hidden?: bool, op: string} Nav item
 *     definition
 *
 * @return string
 */
function __PREFIX__makeNavLink($page, $current_page, $definition) {
    if ($definition["hidden"]) {
        return "";
    }

    ob_start();
    ?>
    <li>
        <a href="?page=<?= urlencode($page) ?>"
           class="flex gap-x-3 rounded p-2 text-sm font-semibold leading-6
           <?= __PREFIX__htmlHighlightActivePage($current_page, $page) ?>
           "
           id="nav-<?= $page ?>"
        >
            <div class="flex items-center justify-center">
                <?= $definition["svg"]; ?>
            </div>
            <?= htmlentities($definition["title"]) ?>
        </a>
    </li>
    <?php
    return ob_get_clean();
}

/**
 * Returns the classes to apply to the navigation item highlighted because it's the current page
 *
 * @param $current_page string Current page
 * @param $checking_page string Page to check if it's the current page
 *
 * @return string
 */
function __PREFIX__htmlHighlightActivePage($current_page, $checking_page) {
    if ($current_page === $checking_page) {
        return "bg-zinc-800 text-white";
    }
    return "text-zinc-400";
}

/**
 * Format bytes to human-readable format
 *
 * @param $bytes array|int|float Bytes to format
 *
 * @return string
 */
function __PREFIX__formatBytes($bytes) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $bytes = max($bytes, 0);
    $pow   = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow   = min($pow, count($units) - 1);

    // Calculate size in the chosen unit
    $bytes /= pow(1024, $pow);

    // Format with three-point precision
    return __PREFIX__pad_right(round($bytes, 3) . ' ' . $units[$pow]);
}

/**
 * Download a file in chunks
 *
 * @param $filepath string Path to the file to download
 * @param $filesize int Size of the file
 * @param $filename string|null Name of the file to download or null to use the original filename
 *
 * @return void
 */
function __PREFIX__chunkedDownload($filepath, $filesize, $filename = null) {
    $chunk_size = 4096; // Adjust chunk size as needed

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header(
        'Content-Disposition: attachment; filename="' .
        (!empty($filename) ? $filename : basename($filepath)) // Use the original filename if not provided
        . '"'
    );
    header('Content-Transfer-Encoding: chunked');
    header('Content-Length: ' . $filesize); // Set content length for progress tracking

    $file_handle = fopen($filepath, 'rb');

    while (!feof($file_handle)) {
        $chunk = fread($file_handle, $chunk_size);
        echo $chunk;
        flush();     // Flush output buffer after sending each chunk
    }

    fclose($file_handle);
}

/**
 * Create a code highlight element
 *
 * @param $code float|int|string Code to highlight
 *
 * @return string
 */
function __PREFIX__makeCodeHighlight($code) {
    ob_start();
    ?>
    <code class="font-mono bg-zinc-100 text-zinc-900 text-sm px-2 py-1 rounded mx-1 select-all"><?= htmlentities(
            $code
        ) ?></code>
    <?php
    return ob_get_clean();
}

/**
 * Pad a string to the left with spaces
 *
 * @param $str string String to pad
 * @param $pad_length int Length to pad to
 *
 * @return string
 */
function __PREFIX__pad_right($str, $pad_length = 10) {
    // Ensure string and pad length are valid
    if (!is_string($str) || !is_int($pad_length) || $pad_length <= 0) {
        return $str; // Return unmodified string for invalid input
    }

    // Pad the string with spaces using str_pad
    return str_pad($str, $pad_length);
}

/**
 * Convert a Unix timestamp to a date string
 *
 * @param $timestamp int Unix timestamp
 *
 * @return string
 */
function __PREFIX__convertUnixTimestampToDate($timestamp) {
    return date('Y-m-d H:i:s', $timestamp);
}