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