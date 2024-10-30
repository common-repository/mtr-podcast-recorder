<?php
/*
    Plugin Name: MTR Podcast Recorder
    Plugin URI: http://mtr-design.com/en/projects/mtr-podcast-recorder/
    Description: MTR Podcast Recorder offers real-time recording (via a Java applet) that is saved on the same server where WordPress is installed. All recordings will be saved into the MP3 format using the so famous LAME encoder. When you have your podcasts recorded, you can easily add a Podcast Player widget to any of your sidebars, posts, or pages if you want to promote a specific recording to your readers.
    Author: MTR Design.
    Version: 0.7.4
    Author URI: http://mtr-design.com/en/
*/

require_once("php/jrecorder_globals.php");

//******************************************************************************
// Create a JRecorder class for the plugin
//******************************************************************************
if (!class_exists("JRecorder"))
{
    class JRecorder
    {
		private $adminOptionsName = 'JRecorderAdminOptions';

        function __construct()
        {}

        function __destruct()
        {}

        //----------------------------------------------------------------------
        // JRecorder install function
        //----------------------------------------------------------------------
        function install()
        {
            global $wpdb;

            // Set up default admin panel options
            $this->getAdminOptions();
        }

        //----------------------------------------------------------------------
        // Retrieve JRecorder options from the admin panel
        //----------------------------------------------------------------------
        function getAdminOptions()
        {
            // Default values for the admin panel options
            $adminOptions = array(
                'css' => '',
                'loading_message' => 'MTR Podcast Recorder is loading...'
            );

            // Load options saved in the database; override default values
            $jrecOptions = get_option($this->adminOptionsName);
            if (!empty($jrecOptions)) {
                foreach ($jrecOptions as $key => $value)
                    $adminOptions[$key] = $value;
            }

            // Store the admin panel options back to the WordPress database
            update_option($this->adminOptionsName, $adminOptions);

            // Return the options to the user for whatever he needs them
            return $adminOptions;
        }

        //----------------------------------------------------------------------
        // Adds stuff to the head of the document.
        //----------------------------------------------------------------------
        function addToHeadTag()
        {
            $adminOptions = $this->getAdminOptions();

            // Include the default CSS stylesheet
            echo '<link type="text/css" rel="stylesheet" href="'.get_bloginfo('wpurl').'/wp-content/plugins/mtr-podcast-recorder/css/jrecorder.css" />';
            echo "\n";
            echo '<link type="text/css" rel="stylesheet" href="'.get_bloginfo('wpurl').'/wp-content/plugins/mtr-podcast-recorder/css/jrecorder_extra.css" />';
            echo "\n";

            // Overwrite default CSS rules with custom CSS rules
            _e('<style type="text/css">'.
                   $adminOptions['css'].
               '</style>');
            echo "\n";

            // WP 2.0 ?
            if (!function_exists('wp_enqueue_script'))
            {
                echo '<script type="text/javascript" src="'.get_bloginfo('wpurl').'/wp-content/plugins/mtr-podcast-recorder/js/jquery-1.2.6.pack.js"></script>';
                echo "\n";
                echo '<script type="text/javascript" src="'.get_bloginfo('wpurl').'/wp-content/plugins/mtr-podcast-recorder/js/jquery.equalizecols.js"></script>';
                echo "\n";
                echo '<script type="text/javascript" src="'.get_bloginfo('wpurl').'/wp-content/plugins/mtr-podcast-recorder/js/jquery.preloadCssImages-v3.js"></script>';
                echo "\n";

                echo '<script type="text/javascript" src="'.get_bloginfo('wpurl').'/wp-content/plugins/mtr-podcast-recorder/js/jrecorder.js.php"></script>';
                echo "\n";
                echo '<script type="text/javascript" src="'.get_bloginfo('wpurl').'/wp-content/plugins/mtr-podcast-recorder/js/jrecorder_extra.js.php"></script>';
                echo "\n";
                echo '<script type="text/javascript" src="'.get_bloginfo('wpurl').'/wp-content/plugins/mtr-podcast-recorder/js/jrecorder_sidebar_widget.js.php"></script>';
                echo "\n";
            }
        }

        //----------------------------------------------------------------------
        // Register stuff. E.g. JavaScript files. This must be in an action like
        // "init", which gets called for each page. "wp_head" is called after
        // any necessary file have been queued up for processing.
        //----------------------------------------------------------------------
        function registerStuff()
        {
            // Include JavaScript functions; For old WP installations like 2.0,
            // this code is located in $this->addToHeadTag(); Please make sure
            // that both pieces of code stay in sync.
            if (function_exists('wp_enqueue_script'))
            {
                wp_enqueue_script('jquery');
                wp_enqueue_script('jquery_latest', get_bloginfo('wpurl').'/wp-content/plugins/mtr-podcast-recorder/js/jquery-1.2.6.pack.js');
                wp_enqueue_script('jquery_equal_cols', get_bloginfo('wpurl').'/wp-content/plugins/mtr-podcast-recorder/js/jquery.equalizecols.js');
                wp_enqueue_script('jquery_preload_css_images', get_bloginfo('wpurl').'/wp-content/plugins/mtr-podcast-recorder/js/jquery.preloadCssImages-v3.js');

                wp_enqueue_script('jrecorder', get_bloginfo('wpurl').'/wp-content/plugins/mtr-podcast-recorder/js/jrecorder.js.php');
                wp_enqueue_script('jrecorder_extra', get_bloginfo('wpurl').'/wp-content/plugins/mtr-podcast-recorder/js/jrecorder_extra.js.php');
                wp_enqueue_script('jrecorder_sidebar_widget', get_bloginfo('wpurl').'/wp-content/plugins/mtr-podcast-recorder/js/jrecorder_sidebar_widget.js.php');
            }

            if (function_exists('register_sidebar_widget') && function_exists('register_widget_control'))
            {
                //$widgetOptions = array('classname' => 'JRecorder::sidebarWidget', 'description' => 'A podcast recording of your choice on your blog');
                register_sidebar_widget('MTR Podcast Player', 'JRecorder::sidebarWidget');

                // This registers the (optional!) widget control form.
                register_widget_control('MTR Podcast Player', 'JRecorder::sidebarWidgetControl');
            }

            // TinyMCE plugin initialization
            $this->tinymceAddButtons();
        }

        //----------------------------------------------------------------------
        // Displays the admin page
        //----------------------------------------------------------------------
        function printAdminPage()
        {
            $adminOptions = $this->getAdminOptions();

            if (isset($_POST['update_JRecorderSettings']))
            {
                if (isset($_POST['jRecorderCss']))
                    $adminOptions['css'] = $_POST['jRecorderCssFile'];

                if (isset($_POST['jRecorderLoadingMessage']))
                    $adminOptions['loading_message'] = $_POST['jRecorderLoadingMessage'];

                // Add more settings options here...

                update_option($this->adminOptionsName, $adminOptions);

                echo '<div class="updated">'.
                         '<p>'.
                             '<strong>';
                                 _e("Settings updated.", "JRecorder");
                echo         '</strong>';
                         '</p>'.
                     '</div>';
            }

            require_once("php/jrecorder_admin_settings.php");
        }

        function displayRecorder()
        {
            $adminOptions = $this->getAdminOptions();

            require_once("php/jrecorder_xhtml.php");
            require_once("php/jrecorder_playlists.php");
        }

        public static function sidebarWidget($args)
        {
            extract($args);

            echo $before_widget;
            echo $before_title;
            echo '<p>MTR Podcast Player</p>';    // widget title
            echo $after_title;

            require("php/jrecorder_sidebar_widget.php");

            echo $after_widget;
        }

        public static function sidebarWidgetControl()
        {
            // Collect our widget's options.
            $options = get_option('widget_jrecorder');

            // This is for handing the control form submission.
            if ( $_POST['jrecorder-widget-submit'] ) {
                // Clean up control form submission options
                $newoptions['playlist'] = strip_tags(stripslashes($_POST['jrecorder-widget-playlist']));
                $newoptions['recording'] = strip_tags(stripslashes($_POST['jrecorder-widget-recording']));

                // If original widget options do not match control form
                // submission options, update them.
                if ( $options != $newoptions ) {
                    $options = $newoptions;
                    update_option('widget_jrecorder', $options);
                }
            }

            // Format options as valid HTML. Hey, why not.
            $playlist = htmlspecialchars($options['playlist'], ENT_QUOTES);
            $recording = htmlspecialchars($options['recording'], ENT_QUOTES);

            require("php/jrecorder_sidebar_widget_options.php");
        }

        function generatePodcastPlayerHtml($data)
        {
            global $jRecorderStorageUrl, $jRecorderStoragePath;

            $dataArr = explode("|", $data);

            $playlist = $jRecorderStoragePath."/".trim($dataArr[0]).".xspf";
            $swfUrl = "playlist_url=".$jRecorderStorageUrl.basename($playlist);

            if (sizeof($dataArr) > 1)
            {
                $recording = $jRecorderStoragePath."/".trim($dataArr[1]);

                require_once("php/Playlist.php");
                $pl = new Playlist($playlist);
                $rdata = $pl->getRecordingData($recording);

                $swfUrl = "song_url=".$jRecorderStorageUrl.basename($rdata['location'])."&song_title=".basename($rdata['location'])."&";
            }
            // Replace it with a flash player
            //$path = get_bloginfo('wpurl').'/wp-content/plugins/mtr-podcast-recorder/flash/xspf_player_slim.swf?&song_url='.$jRecorderStorageUrl.basename($rdata['location']).'&song_title='.basename($rdata['location']).'&';
            $path = get_bloginfo('wpurl').'/wp-content/plugins/mtr-podcast-recorder/flash/xspf_player_slim.swf?'.$swfUrl;
            $playerHtml =
                '<object width="200" height="15">'.
                    '<param name="movie" value="'.$path.'" />'.
                    '<param name="quality" value="high" />'.
                    '<param name="menu" value="false" />'.
                    '<param name="wmode" value="transparent" />'.
                    '<embed src="'.$path.'" wmode="transparent" quality="high" menu="false" type="application/x-shockwave-flash" width="200" height="15"></embed>'.
                '</object>';

            return $playerHtml;
        }

        /**
         * Filters a post/page/comment and replaces specific tags with a flash
         * player.
         *
         * There are two ways to have a flash player inserted for a specific
         * playlist and/or recording.
         *
         * 1) <mtrpodcast> playlist | recording.mp3 </mtrpodcast>
         *
         * 2) <img src="..."
         *         alt="playlist | recording.mp3"
         *         class="jRecorderPlayerPlaceholder"
         *         title="..." />
         *
         * where "..." are usually values inserted by tinyMCE. They only matter
         * to tinyMCE. As far as this filter is concerned, we only care for the
         * "alt" and "class" attributes to find the information we need.
         *
         * @param string $content
         * @return string
         */
        function filterPodcastTag($content = "")
        {
            global $jRecorderStoragePath, $jRecorderStorageUrl;

            //==================================================================
            // START THE FIRST ROUND OF FILTERING - THE CUSTOM TAGS
            //==================================================================

            $content = str_replace("&lt;mtrpodcast&gt;", "<mtrpodcast>", $content);
            $content = str_replace("&lt;/mtrpodcast&gt;", "</mtrpodcast>", $content);

            $str1 = "<mtrpodcast>";
            $str2 = "</mtrpodcast>";

            $pos1 = $pos2 = false;

            $pos1 = strpos($content, $str1);
            while ($pos1 !== false)
            {
                $pos2 = strpos($content, $str2, $pos1 + 1);

                if ($pos2 !== false)
                {
                    $data = substr($content, $pos1 + strlen($str1), $pos2 - $pos1 - strlen($str1));
                    $playerHtml = $this->generatePodcastPlayerHtml($data);

                    // Rebuild the content string
                    /*
                    $s1 = substr($content, 0, $pos1);
                    $s2 = substr($content, $pos2 + strlen($str2));
                    $content = $s1.$playerHtml.$s2;
                    */
                    $s = str_replace($str1.$data.$str2, $playerHtml, $content);
                    $content = $s;
                }

                // Find the next one
                $pos1 = strpos($content, $str1, $pos1 + strlen($playerHtml));
            }

            //==================================================================
            // START NEXT ROUND OF FILTERING - THE IMG TAG
            //==================================================================

            $str1 = "jRecorderPlayerPlaceholder";
            $str2 = "<img ";
            $str3 = ">";
            $str4 = " alt";

            // Find where "<img " is
            $pos1 = strpos($content, $str2);
            while ($pos1 != false)
            {
                // Find where ">" is
                $pos2 = strpos($content, $str3, $pos1 + 1);

                // Set $start and $length, and get entire img tag
                $start = $pos1;
                $length = $pos2 - $pos1 + strlen($str3);
                $imgStr = substr($content, $start, $length);

                // Check if this is "our" img tag; if not, start from beginning
                if (strpos($imgStr, $str1) === false) {
                    $pos1 = strpos($content, $str2, $start + strlen($imgStr));
                    continue;
                }

                // ... else let's filter it our

                // Get the "alt" attr
                $pos0 = strpos($imgStr, $str4);
                $pos1 = strpos($imgStr, "\"", $pos0);
                $pos2 = strpos($imgStr, "\"", $pos1 + 1);
                $altData = substr($imgStr, $pos1 + 1, $pos2 - $pos1 - 1);

                // Replace the data
                $data = $altData;
                $playerHtml = $this->generatePodcastPlayerHtml($data);

                // Rebuild the content string
                /*
                $s1 = substr($content, 0, $start);
                $s2 = substr($content, $start + $length);
                $content = $s1.$playerHtml.$s2;
                */
                $s = str_replace($imgStr, $playerHtml, $content);
                $content = $s;

                // Find next item to filter if any
                $pos1 = strpos($content, $str2, $start + strlen($playerHtml));
            }

            // DONE FILTERING; RETURN FILTERED CONTENT

            return $content;
        }

        //----------------------------------------------------------------------
        // tinyMCE related methods
        //----------------------------------------------------------------------
        function tinymceAddButtons()
        {
            // Don't bother doing this stuff if the current user lacks permissions
            if ( ! current_user_can('edit_posts') && ! current_user_can('edit_pages') )
                return;

             // Add only in Rich Editor mode
             if ( get_user_option('rich_editing') == 'true')
             {
                 // Load the tinyMCE plugin depending on the WP version.
                 // WP 2.5+ uses tinyMCE 3.0; older versions use tinyMCE 2.xx
                 $version = explode(".", get_bloginfo("version"));
                 if ( trim($version[0]) < 2 || (trim($version[0]) == 2 && trim($version[1]) < 5) )
                 {
                     // WP 2.0 to 2.5 (Excluding)
                     add_filter("mce_plugins", array(&$this, "tinymceLoadPlugin_before25"));
                     add_action("tinymce_before_init", array(&$this, "tinymceLoadPlugin_before25init"));
                 }
                 else
                 {
                     // WP 2.5 and up
                     add_filter("mce_external_plugins", array(&$this, "tinymceLoadPlugin"));
                 }

                 add_filter('mce_buttons', array(&$this, "tinymceRegisterButton"));
             }
        }

        function tinymceRegisterButton($buttons)
        {
            array_push($buttons, "separator", "mtr_podcasts");
            return $buttons;
        }

        // Load the TinyMCE plugin : editor_plugin.js (wp2.5)
        function tinymceLoadPlugin($plugin_array)
        {
            $path = get_bloginfo('wpurl').'/wp-content/plugins/mtr-podcast-recorder/tinymce_plugin/mtr_podcasts';
            $plugin_array['mtr_podcasts'] = $path.'/editor_plugin.js';
            return $plugin_array;
        }

        // Load the TinyMCE plugin : editor_plugin.js (wp before v2.5)
        function &tinymceLoadPlugin_before25(&$plugin_array)
        {
            $plugin_array[] = "-mtr_podcasts"; // the leading "-" means it's an external plugin
            return $plugin_array;
        }

        function tinymceLoadPlugin_before25init()
        {
            $path = get_bloginfo('wpurl').'/wp-content/plugins/mtr-podcast-recorder/tinymce_plugin/mtr_podcasts_older';
            //$path.'/editor_plugin.js';
            echo "tinyMCE.loadPlugin('mtr_podcasts', '".$path."');\n";
        }
        //----------------------------------------------------------------------
        // end of tinyMCE related methods
        //----------------------------------------------------------------------

        function wrongWPVersion()
        {
            $_GET['action'] = $_POST['action'] = "deactivate";
            $_GET['plugin'] = $_POST['plugin'] = "MTR Podcast Recorder";
            //echo "Sorry, but you need WordPress 2.2 at least to be able to activate this plugin!";
        }

        // ... more methods...
    }
}

//******************************************************************************
// Instantiate the JRecorder plugin
//******************************************************************************
if (class_exists("JRecorder")) {
    $jrecorder = new JRecorder();
}

//******************************************************************************
// Initialize the admin panel
//******************************************************************************
if (!function_exists("jRecorderAddPanels"))
{
    function jRecorderAddPanels()
    {
        global $jrecorder;
        if (!isset($jrecorder))
            return;

        // Add an entry to the admin "Settings" menu
        if (function_exists("add_options_page"))
            add_options_page('MTR Podcast Recorder', 'MTR Podcast Recorder', 9, basename(__FILE__), array(&$jrecorder, 'printAdminPage'));

        // Add an entry to the "Manage" menu
        if (function_exists('add_management_page'))
            add_management_page('MTR Podcast Recorder', 'MTR Podcast Recorder', 5, basename(__FILE__), array(&$jrecorder, 'displayRecorder'));
    }
}

//******************************************************************************
// Actions and filters
//******************************************************************************
if (isset($jrecorder))
{
    $version = explode(".", get_bloginfo("version"));
    if ( trim($version[0]) > 2 || (trim($version[0]) == 2 && trim($version[1]) >= 2) )
    {
        register_activation_hook(__FILE__, array(&$jrecorder, 'install', 1));

        add_action('init', array(&$jrecorder, 'registerStuff'));
        add_action('admin_head', array(&$jrecorder, 'addToHeadTag'));
        add_action('admin_menu', 'jRecorderAddPanels');

        add_filter('the_content', array(&$jrecorder, 'filterPodcastTag'));
        add_filter('comment_text', array(&$jrecorder, 'filterPodcastTag'));
    }
    else
    {
        //echo "Sorry, but you need WordPress 2.2 at least to be able to activate this plugin!";
        add_action('init', array(&$jrecorder, 'wrongWPVersion'));
    }
}

?>