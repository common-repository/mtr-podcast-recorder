<?php
//******************************************************************************
// Find where wp-config.php is located by scanning the URL
//******************************************************************************
$jRecorderWpConfigPath = $_SERVER['DOCUMENT_ROOT'];
$arr = explode("/", $_SERVER['REQUEST_URI']);
foreach ($arr as $one)
{
    if ($one == "") continue;
    if ($one == "wp-content") break;
    if ($one == "wp-admin") break;
    if ($one == "wp-includes") break;

    $jRecorderWpConfigPath .= "/".$one;
}

if (!function_exists("add_action"))
{
//    if ($jRecorderWpConfigPath != $_SERVER['DOCUMENT_ROOT'])
        require_once($jRecorderWpConfigPath."/wp-config.php");
    // If wp-config.php not found through the "scanning" process above, assume
    // it is 5 levels beneath the script calling it (accurate 98% of the time)
//    else
//        require_once("../../../../../wp-config.php");
}
//******************************************************************************
// Done with wp-config.php
//******************************************************************************

//******************************************************************************
// Define global variables for easy portability and setup. Another set of similar
// variables exists in the JavaScript file jrecorder.js.php, so be sure to
// modify that one as well.
//******************************************************************************
global $jRecorderPhpProcessorPath;
global $jRecorderImagesPath;
global $jRecorderStoragePath;
global $jRecorderStorageUrl;
global $jRecorderAjaxProcessorPath;

if (!isset($jRecorderPhpProcessorPath))
    $jRecorderPhpProcessorPath = get_bloginfo('wpurl').'/wp-content/plugins/mtr-podcast-recorder/php/mtrPodcastsProcessor.php';

if (!isset($jRecorderImagesPath))
    $jRecorderImagesPath = get_bloginfo('wpurl').'/wp-content/plugins/mtr-podcast-recorder/images/';

if (!isset($jRecorderStoragePath))
{
    // The next line is not working for some reason, even though it is true.
    // So I replaced it with the ugly code below it.
    //$jRecorderStoragePath = realpath('../wp-content/mtr-podcasts');

    $jRecorderStoragePath = $_SERVER['DOCUMENT_ROOT'];
    $arr = explode("/", $_SERVER['REQUEST_URI']);
    foreach ($arr as $one)
    {
        if ($one == "") continue;
        if ($one == "wp-content") break;
        $jRecorderStoragePath .= "/".$one;
    }
    $jRecorderStoragePath .= "/"."wp-content/mtr-podcasts";
}

if (!isset($jRecorderStorageUrl))
    $jRecorderStorageUrl = get_bloginfo('wpurl').'/wp-content/mtr-podcasts/';

if (!isset($jRecorderAjaxProcessorPath))
    $jRecorderAjaxProcessorPath = get_bloginfo('wpurl').'/wp-content/plugins/mtr-podcast-recorder/php/jrecorder_ajax.php';

?>