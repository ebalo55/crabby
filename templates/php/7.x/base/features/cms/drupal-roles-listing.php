<?php

// section.constants
$CHECK_DRUPAL_USER_ROLES = "__FEAT_CHECK_DRUPAL_USER_ROLES__";
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
function __PREFIX__makeCheckDrupalRolesPage(&$page_content, $features, $page, $css) {
    $feature = array_values(array_filter($features, function ($feature) use ($page) {
        return $feature["op"] === $page;
    }));

    $roles        = __PREFIX__getDrupalRoles();
    $page_content = __PREFIX__makePage(
        $features,
        $css,
        $page,
        [__PREFIX__makePageHeader(
            $feature[0]["title"],
            $feature[0]["description"]
        ), __PREFIX__makeTable(
            "Roles",
            "Drupal roles and their permissions",
            $roles,
            ["role"        => "Role", "permissions" => "Permissions"]
        )]
    );
}

/**
 * Get the list of Drupal roles
 *
 * @return array
 */
function __PREFIX__getDrupalRoles() {
    if(!class_exists('\Drupal\user\Entity\Role')) {
        return [];
    }

    $roles       = \Drupal\user\Entity\Role::loadMultiple();
    $permissions = \Drupal::service('user.permissions')
        ->getPermissions();

    $result = [];

    foreach ($roles as $role) {
        $role_permissions = [];

        foreach ($permissions as $permission => $permission_info) {
            if ($role->hasPermission($permission)) {
                $role_permissions[] = "<li>" . htmlentities($permission_info['title']) . "</li>";
            }
        }

        $result[] = ["id"          => $role->id(), "role"        => $role->label(), "permissions" => "<ul class='list-disc list-inside'>" . implode("", $role_permissions) . "</ul>"];
    }

    return $result;
}

/**
 * Hook the features to add the login feature
 *
 * @param $features array{title: string, description: string, svg: string, hidden?: bool, op: string}[] The features
 *     container
 *
 * @return void
 */
function __PREFIX__checkDrupalRolesHooksFeatures(&$features) {
    global $CHECK_DRUPAL_USER_ROLES;

    $features[] = ["title"       => "List Drupal roles", "description" => "List all Drupal roles and their permissions.", "svg"         => '<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
  <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 1 1 9 0v3.75M3.75 21.75h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H3.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
</svg>', "op"          => $CHECK_DRUPAL_USER_ROLES];
}

// section.functions.end

// section.hooks
add_hook("features", "__PREFIX__checkDrupalRolesHooksFeatures");
add_named_hook("GET_page", $CHECK_DRUPAL_USER_ROLES, "__PREFIX__makeCheckDrupalRolesPage");
// section.hooks.end