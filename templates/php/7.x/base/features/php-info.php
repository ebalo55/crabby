<?php

// section.constants
$PHP_INFO = "__FEAT_PHP_INFO__";
// section.constants.end

// section.functions
/**
 * Create the example page
 *
 * @param $page_content string The page content container
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 * @param $page string The current page
 * @param $css string The CSS of the page
 */
function __PREFIX__makePhpInfoPage(&$page_content, $features, $page, $css) {
    ob_start();
    phpinfo();
    $php_info     = ob_get_clean();
    $page_content = __PREFIX__makePage(
        $features,
        $css,
        $page,
        [
            __PREFIX__makePageHeader(
                $features[$page]["title"],
                $features[$page]["description"]
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
                            " . __PREFIX__listEnabledExtensions() . "
                        </div>
                    </div>",
        ]
    );
}

/**
 * List all enabled extensions
 *
 * @return string
 */
function __PREFIX__listEnabledExtensions() {
    $extensions = get_loaded_extensions();
    $content    = __PREFIX__openCommandOutputScreen(
        true,
        "max-h-96 overflow-y-scroll mb-8",
        "Enabled extensions",
        true,
        true
    );
    foreach ($extensions as $extension) {
        $content .= "- $extension\n";
    }
    $content .= __PREFIX__closeCommandOutputScreen(true);
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
function __PREFIX__phpInfoHooksFeatures(&$features) {
    global $PHP_INFO;

    $features[] = [
        "title"       => "PHP Info",
        "description" => "Display PHP information.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
</svg>',
        "op"          => $PHP_INFO,
    ];
}

// section.functions.end

// section.hooks
add_hook("features", "__PREFIX__phpInfoHooksFeatures");
add_named_hook("GET_page", $PHP_INFO, "__PREFIX__makePhpInfoPage");
// section.hooks.end