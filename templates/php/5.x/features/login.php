<?php

// section.constants
$LOGIN    = "__FEAT_LOGIN__";
$USERNAME = "__USERNAME__";
$PASSWORD = "__PASSWORD__";
$SALT     = '__SALT__';
// section.constants.end

// section.functions
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
                <div class="bg-red-500 p-3 rounded-md text-white text-center">
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

// section.functions.end

// section.hooks
session_start();
add_hook("page_generation", "__PREFIX__login_hooks_page_generation");
add_hook("isolated_ops", "__PREFIX__login_hooks_isolated_ops");
add_hook("features", "__PREFIX__login_hooks_features");
add_named_hook("GET_page", $LOGIN, "__PREFIX__makeLoginPage");
add_named_hook("POST_operation", $LOGIN, "__PREFIX__handleLogin");
// section.hooks.end