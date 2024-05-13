<?php

/**
 * Check if the request method is POST
 */
function __PREFIX__isPost(): bool {
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
    return $_GET['page'] ?? $features[0]["op"];
}

/**
 * Render the page content by calling the named hook for the current page
 *
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 * @param $page string The page to render
 */
function __PREFIX__renderPage($features, $page): void {
    global $css;
    // Load the page content by calling the named hook for the current page
    $content = "";
    $args    = [&$content, $features, $page, $css];
    call_named_hook("GET_page", $page, $args);

    // Render the page content
    echo $content;
}

/**
 * Check if the operation is in the isolated operations list
 *
 * @param $operation string The operation to check
 * @param $isolated_ops array The isolated operations list
 */
function __PREFIX__isIsolatedOperation($operation, $isolated_ops): bool {
    return in_array($operation, $isolated_ops);
}

/**
 * Open the command output screen where output can be freely written
 *
 * @param bool $capture_output Whether to capture the output or not
 * @param string $classes Classes to apply to the command output screen
 * @param string $title Title of the command output screen
 * @param bool $no_margin Whether to remove the margin from the command output screen
 * @param bool $no_padding Whether to remove the padding from the command output screen
 *
 * @return void|string Returns the output if $capture_output is true
 */
function __PREFIX__openCommandOutputScreen(
    $capture_output = false,
    $classes = "",
    $title = "Command output",
    $no_margin = false,
    $no_padding = false
): string | false {
    if ($capture_output) {
        ob_start();
    }

    ?>
    <div class="<?php
    echo $no_margin ? "" : "ml-72 ";
    echo $no_padding ? "" : "py-10 ";
    ?>">
    <div class="container px-16">
    <div class="bg-zinc-900 font-mono text-sm overflow-auto rounded shadow-md text-white px-6 py-3">
    <h3 class="border-b border-zinc-700 text-sm font-semibold leading-8">
        <?= htmlentities($title) ?>
    </h3>
    <pre class="p-2 mt-2 <?= htmlentities($classes) ?>"><?php

    if ($capture_output) {
        return ob_get_clean();
    }
}

/**
 * Closes the command output screen
 *
 * @param bool $capture_output Whether to capture the output or not
 *
 * @return void|string Returns the output if $capture_output is true
 */
function __PREFIX__closeCommandOutputScreen($capture_output = false): string | false {
    if ($capture_output) {
        ob_start();
    }

    ?></pre>
    </div>
    </div>
    </div>
    <?php

    if ($capture_output) {
        return ob_get_clean();
    }
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
function __PREFIX__makePage($enabled_features, $css, $current_page, $elements): string | false {
    $args = [&$elements, $current_page, $css];
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
function __PREFIX__makeNavLink($page, $current_page, array $definition): string | false {
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
                <?= $definition["svg"] ?>
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
 */
function __PREFIX__htmlHighlightActivePage($current_page, $checking_page): string {
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
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];

    $bytes = max($bytes, 0);
    $pow   = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow   = min($pow, count($units) - 1);

    // Calculate size in the chosen unit
    $bytes /= 1024 ** $pow;

    // Format with three-point precision
    return __PREFIX__pad_right(round($bytes, 3) . ' ' . $units[$pow]);
}

/**
 * Download a file in chunks
 *
 * @param $filepath string Path to the file to download
 * @param $filesize int Size of the file
 * @param $filename string|null Name of the file to download or null to use the original filename
 */
function __PREFIX__chunkedDownload($filepath, string $filesize, $filename = null): void {
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
function __PREFIX__makeCodeHighlight($code): string | false {
    ob_start();
    ?>
    <code class="font-mono bg-zinc-100 text-zinc-900 text-sm px-2 py-1 rounded mx-1 select-all">
        <?= htmlentities($code) ?>
    </code>
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
 */
function __PREFIX__convertUnixTimestampToDate($timestamp): string {
    return date('Y-m-d H:i:s', $timestamp);
}

/**
 * Create a page header
 *
 * @param $title string Title of the page
 * @param $description string Description of the page
 *
 * @return string
 */
function __PREFIX__makePageHeader($title, $description): string | false {
    ob_start();
    ?>
    <div class="lg:flex lg:items-center lg:justify-between">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-zinc-900 sm:truncate sm:text-3xl sm:tracking-tight">
                <?= htmlentities($title) ?>
            </h2>
            <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
                <div class="mt-2 flex items-center text-sm text-zinc-500">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="1.5" stroke="currentColor"
                         class="mr-1.5 h-5 w-5 flex-shrink-0 text-zinc-400">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/>
                    </svg>
                    <?= $description ?>
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Create a form with the given elements
 *
 * @param $operation string Operation to perform when the form is submitted
 * @param $action string Action to perform when the form is submitted
 * @param $elements string[] Elements to include in the form
 * @param $method string Method to use for the form, default is "post"
 *
 * @return string
 */
function __PREFIX__makeForm(
    $operation,
    $action,
    $elements,
    $method = "post",
    $submit_label = "Run operation",
    $classes = "flex flex-col gap-y-6 max-w-xl mt-8"
): string | false {
    ob_start();
    ?>
    <form action="<?= $action ?>" method="<?= htmlentities($method) ?>"
          class="<?= htmlentities($classes) ?>">
        <input type="hidden" name="__OPERATION__" value="<?= htmlentities($operation) ?>"/>
        <?php
        foreach ($elements as $element) {
            echo $element;
        }
        ?>
        <button type="submit"
                class="rounded px-3 py-2 text-sm font-semibold text-white shadow bg-zinc-800 flex-grow-0 ml-auto
                       hover:bg-zinc-700 transition-all duration-300">
            <?= htmlentities($submit_label) ?>
        </button>
    </form>
    <?php
    return ob_get_clean();
}

/**
 * Create an input field
 *
 * @param $type string Type of the input
 * @param $label string Label for the input
 * @param $name string Name of the input
 * @param $placeholder string Placeholder for the input
 * @param $description string Description of the input
 * @param $required bool Whether is the input required
 *
 * @return string
 */
function __PREFIX__makeInput(
    $type,
    $label,
    $name,
    $placeholder,
    $description,
    $required = false,
    $query_param = null,
    $value = null
): string | false {
    $name = htmlentities($name);

    ob_start();
    if ($type !== "textarea") {
        ?>
        <div class="flex flex-col gap-y-2 <?= $type === "hidden" ? "hidden" : ""; ?>"
             id="<?= $name ?>-container">
            <label for="<?= $name ?>" class="block text-sm font-medium leading-6 text-zinc-900">
                <?= htmlentities($label) ?>
                <?php
                if ($required) {
                    echo "<sup class='text-red-500'>*</sup>";
                }
                ?>
            </label>
            <input type="<?= htmlentities($type) ?>"
                   id="<?= $name ?>"
                   name="<?= $name ?>"
                   placeholder="<?= htmlentities($placeholder) ?>"
                <?php
                if ($required) {
                    echo "required ";
                }
                if (!empty($query_param)) {
                    echo "value=\"" . htmlentities($_GET[$query_param]) . "\" ";
                }
                elseif (!empty($value)) {
                    echo "value=\"" . htmlentities($value) . "\" ";
                }
                ?>
                   class="block w-full border-0 rounded py-1.5 text-zinc-900 shadow ring-1 ring-inset ring-zinc-300 focus:ring-indigo-600 placeholder-zinc-400">
            <p class="text-sm text-zinc-500">
                <?= $description ?>
            </p>
        </div>
        <?php
    }
    else {
        ?>
        <div class="flex flex-col gap-y-2" id="<?= $name ?>-container">
            <label for="<?= $name ?>" class="block text-sm font-medium leading-6 text-zinc-900">
                <?= $label ?>
                <?php
                if ($required) {
                    echo "<sup class='text-red-500'>*</sup>";
                }
                ?>
            </label>
            <textarea id="<?= $name ?>"
                      name="<?= $name ?>"
                      placeholder="<?= htmlentities($placeholder) ?>"
                <?php
                if ($required) {
                    echo "required";
                }
                ?>
                      class="block w-full border-0 rounded py-1.5 text-zinc-900 shadow ring-1 ring-inset ring-zinc-300 focus:ring-indigo-600 placeholder-zinc-400"
                      rows="5"
            ><?php
                if (!empty($value)) {
                    echo htmlentities($value);
                }
                ?></textarea>
            <p class="text-sm text-zinc-500">
                <?= $description ?>
            </p>
        </div>
        <?php
    }
    return ob_get_clean();
}

/**
 * Create a select field
 *
 * @param $label string Label for the select
 * @param $name string Name of the select
 * @param $options array<{label: string, value: string, disabled?: bool, selected?: bool}> Options for the select
 * @param $required bool Whether the select is required
 * @param $disable_reason string|null Reason for the option to be disabled, if any
 *
 * @return string
 */
function __PREFIX__makeSelect($label, $name, $options, $required = false, $disable_reason = null): string | false {
    $name = htmlentities($name);

    ob_start();
    ?>
    <div id="<?= $name ?>-container">
        <label for="<?= $name ?>" class="block text-sm font-medium leading-6 text-gray-900">
            <?= $label ?>
            <?php
            if ($required) {
                echo "<sup class='text-red-500'>*</sup>";
            }
            ?>
        </label>
        <select id="<?= $name ?>" name="<?= $name ?>"
                class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset
                ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6"
            <?php
            if ($required) {
                echo "required ";
            }
            ?>
        >
            <?php
            foreach ($options as $option) {
                echo "<option value='" . htmlentities($option["value"]) . "' " .
                     ($option["disabled"] ? "disabled " : "") .
                     ($option["selected"] ? "selected " : "") .
                     ">" .
                     htmlentities($option["label"]) .
                     ($option["disabled"] && !empty($disable_reason) ? " - " . htmlentities($disable_reason) : "") .
                     "</option>";
            }
            ?>
        </select>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Create a checkbox field
 *
 * @param $name string Name of the checkbox
 * @param $label string Label for the checkbox
 * @param $description string Description of the checkbox
 * @param $is_checked bool Whether the checkbox is checked
 * @param $value string Value of the checkbox, default is "y"
 * @param $onclick string|null OnClick event for the checkbox
 *
 * @return string
 */
function __PREFIX__makeCheckbox(
    $name,
    $label,
    $description,
    $is_checked = false,
    $value = "y",
    $onclick = null
): string | false {
    $name = htmlentities($name);

    ob_start();
    ?>
    <div class="relative flex items-start" id="<?= $name ?>-container">
        <div class="flex h-6 items-center">
            <input id="<?= $name ?>" name="<?= $name ?>" type="checkbox"
                   class="h-4 w-4 text-indigo-600 border-zinc-300 rounded focus:ring-indigo-600 "
                   value="<?= htmlentities($value) ?>"
                <?php
                if ($is_checked) {
                    echo "checked ";
                }
                if ($onclick !== null) {
                    echo "onclick=\"$onclick\" ";
                }
                ?>
            >
        </div>
        <div class="ml-3 text-sm leading-6 flex flex-col select-none">
            <label for="<?= $name ?>" class="font-medium text-zinc-900 w-full cursor-pointer">
                <?= htmlentities($label) ?>
            </label>
            <p class="text-zinc-500 cursor-pointer" onclick="document.getElementById('<?= $name ?>').click()">
                <?= $description ?>
            </p>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Check if the checkbox is active
 *
 * @param $name string Name of the checkbox
 */
function __PREFIX__isCheckboxActive($name): bool {
    return isset($_POST[$name]) && $_POST[$name] === "y";
}

/**
 * Array column function __PREFIX__for PHP < 5.5
 *
 * @param $array array Array to extract the column from
 * @param $column_name string Column name to extract
 *
 * @return array Extracted column
 */
function __PREFIX__array_column($array, $column_name): array {
    return array_map(fn($element) => $element[$column_name], $array);
}

/**
 * Print an ASCII table from the given data
 *
 * @param $data array[] Data to print
 */
function __PREFIX__printAsciiTable($data): void {
    // Get column headers
    $headers = array_keys($data[0]);

    // Calculate column widths
    $columnWidths = [];
    foreach ($headers as $header) {
        $columnWidths[$header] = max(array_map('strlen', __PREFIX__array_column($data, $header))) + 2;
    }

    // Print top row
    echo "+";
    foreach ($headers as $header) {
        echo str_repeat("-", $columnWidths[$header]);
        echo "+";
    }
    echo PHP_EOL;

    // Print header row
    echo "|";
    foreach ($headers as $header) {
        printf("%-{$columnWidths[$header]}s|", htmlentities($header));
    }
    echo PHP_EOL;

    // Print divider row
    echo "+";
    foreach ($headers as $header) {
        echo str_repeat("-", $columnWidths[$header]);
        echo "+";
    }
    echo PHP_EOL;

    // Print table rows
    foreach ($data as $row) {
        echo "|";
        foreach ($row as $key => $value) {
            printf("%-{$columnWidths[$key]}s|", htmlentities($value));
        }
        echo PHP_EOL;
    }

    // Print bottom row
    echo "+";
    foreach ($headers as $header) {
        echo str_repeat("-", $columnWidths[$header]);
        echo "+";
    }
    echo PHP_EOL;
}

/**
 * Create an alert element on the page
 *
 * @param $title string Title of the alert box
 * @param $message string Message of the alert
 *
 * @return string
 */
function __PREFIX__makeAlert($title, $message): string | false {
    ob_start();
    ?>
    <div class="bg-yellow-100 border-l-4 border-yellow-500 p-4 rounded mt-4 text-zinc-900 flex gap-x-4">
        <div class="flex items-start justify-center text-yellow-600">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                 stroke="currentColor" class="w-8 h-8">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z"/>
            </svg>
        </div>
        <div class="flex flex-col">
            <h3 class="text-sm leading-7 font-semibold">
                <?= htmlentities($title) ?>
            </h3>
            <p class="text-sm">
                <?= $message ?>
            </p>
        </div>
    </div>
    <?php
    return ob_get_clean();
}