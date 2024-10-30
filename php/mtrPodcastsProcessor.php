<?php
    // Include configuration file
    if (!function_exists("add_action"))
        require_once("../../../../wp-config.php");

    require_once("jrecorder_globals.php");

    /***************************************************************************
     * USER-DEFINED VARIABLES THAT MUST BE SETUP PRIOR TO USING THIS RECORDER
     * FOR THE FIRST TIME
     **************************************************************************/
    $appletPath = '../applet/';

    // Set directory where to save recordings and playlists
    $path = '../../../mtr-podcasts';
    if (file_exists($path) === false)
    {
        if (!mkdir($path, 0775))
        {
            echo "ERROR: You don't have enough write permissions to the server in the WordPress wp-content directory! \n\n";
            echo "Please resolve this issue before proceeding any further!";
            exit;
        }
        @chown($path, "root");
        @chmod($path, 0775);
    }
    $path = realpath($path);

    //**************************************************************************
    // END OF USER DEFINED VARIABLES
    //**************************************************************************

    //==========================================================================
    //--------------------------------------------------------------------------
    //
    //
    // DO NOT MODIFY PAST THIS LINE
    //
    //
    //--------------------------------------------------------------------------
    //==========================================================================

    /***************************************************************************
     * Functions declarations begin
     **************************************************************************/

    /**
     * Process a file upload error
     */
    function processFileUploadError($error, $level)
    {
        switch ($error)
        {
            // No error
            case "0":
                echo "OK";
                break;

            // The uploaded file exceeds the upload_max_filesize directive in php.ini
            case "1":
                echo "ERROR: The uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;

            // The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form
            case "2":
                echo "ERROR: The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;

            // The uploaded file was only partially uploaded
            case "3":
                echo "ERROR: The uploaded file was only partially uploaded";
                break;

            // No file was uploaded
            case "4":
                echo "ERROR: No file was uploaded";
                break;

            // Missing a temporary folder
            case "6":
                echo "ERROR: Missing a temporary folder";
                break;

            // Failed to write file to disk
            case "7":
                echo "ERROR: Failed to write file to disk";
                break;

            // File upload stopped by extension
            case "8":
                echo "ERROR: File upload stopped by extension";
                break;

            // Unknown error or custom errors
            default:
                switch ($error)
                {
                    // Lack of READ/WRITE permission to the server
                    case "100":
                        echo "ERROR: Failed to save the entire audio file to the server! Check if your file name is valid and if you have proper READ/WRITE permission to the server!";
                        break;

                    // Lack of READ permissions on the server
                    case "101":
                        echo "ERROR: Cannot upload the audio file to the server due to lack of READ permissions!";
                        break;

                    // Lack of WRITE permissions on the server
                    case "102":
                        echo "ERROR: Cannot upload the audio file to the server due to lack of WRITE permissions!";
                        break;

                    // If no matches are found, assume this is an error message
                    default:
                        echo $error;
                }
        }

        $level = strtoupper($level);
        if ($level == "SEVERE")
            exit($error);

        return;
    }

    /***************************************************************************
     * End of functions declarations
     **************************************************************************/

    // Allow GET variables only when debugging/testing the code
    if (isset($_GET['debug']))
        $_POST = array_merge($_POST, $_GET);

    /**
     * Process any applet non-file-upload requests here; if uploading a file
     * $_POST['action'] must not be used; choose a different name for the variable
     */
    if (isset($_POST['action']))
    {
        $playlists[] = "";
        if ($_POST['action'] == "getPlaylists")
        {
            $dh = opendir($path);
            $i = 0;
            while (($file = readdir($dh)) !== false)
            {
                if (is_file($path."/".$file) && $file != "." && $file != "..")
                {
                    require_once("Playlist.php");
                    $playlist = new Playlist($path."/".$file);

                    $ext = strtolower(end(explode(".", $file)));
                    if ($ext == "xspf" && $playlist->isPlaylist($path."/".$file))
                    {
                        $playlists[] = substr($file, 0, -1 * strlen($ext) - 1);
                        $i++;
                    }
                }
            }

            if ($i == 0)
            {
                $playlist = "default_playlist.xspf";

                require_once("Playlist.php");
                $pl = new Playlist($path."/".$playlist);
                $pl->createPlaylist($path."/".$playlist);
                @chown($path."/".$playlist, "root");
                @chmod($path."/".$playlist, 0777);

                $ext = strtolower(end(explode(".", $playlist)));
                $playlists[] = substr($playlist, 0, -1 * strlen($ext) - 1);
            }
            sort($playlists);
            echo implode("\n", $playlists);

            exit;
        }

        if ($_POST['action'] == "getLame")
        {
            $os = strtolower($_POST['os']);

            if ($_POST['subaction'] == "getLameExe")
            {
                $file = $appletPath."lame.exe";

                if (strpos($os, "windows") !== false)
                    $file = $appletPath."lame.exe";
                else if (strpos($os, "mac") !== false)
                    $file = $appletPath."lame";
                else if (strpos($os, "linux") !== false)
                    $file = $appletPath."lame";
                else if (strpos($os, "irix") !== false)
                    $file = $appletPath."lame";
                else if (strpos($os, "freebsd") !== false)
                    $file = $appletPath."lame";
            }
            else if ($_POST['subaction'] == "getLameEncDll")
            {
                $file = $appletPath."lame_enc.dll";

                if (strpos($os, "windows") !== false)
                    $file = $appletPath."lame_enc.dll";
                else if (strpos($os, "mac") !== false)
                    $file = $appletPath."libmp3lame.a";
                else if (strpos($os, "linux") !== false)
                    $file = $appletPath."libmp3lame.a";
                else if (strpos($os, "irix") !== false)
                    $file = $appletPath."libmp3lame.a";
                else if (strpos($os, "freebsd") !== false)
                    $file = $appletPath."libmp3lame.a";
            }

            $fp = fopen($file, "rb");
            $buf = fread($fp, filesize($file));
            fclose($fp);
            echo base64_encode($buf);

            exit;
        }

        exit;
    }

//print_r($_POST);
//print_r($_FILES);

    $uploadAction = strtoupper(trim($_POST['uploadAction']));
    if ($uploadAction != "CREATE" && $uploadAction != "APPEND")
        return;

    $playlist = $path."/".trim($_POST['playlist']).".xspf";
    $targetFile = str_replace("\\", "/", $path) . "/" . basename( $_FILES['audioFile']['name']);
    $httpFile = $jRecorderStorageUrl.basename($targetFile);

    $result = is_uploaded_file($_FILES['audioFile']['tmp_name']);
    if ($result === false)
        processFileUploadError($_FILES["audioFile"]["error"], "SEVERE");

    // This "if" is true only on the very first chunk of bytes that come
    // from the MTRPodcasts applet; then the "else" is executed
    if ($uploadAction == "CREATE")
    {
        $result = move_uploaded_file($_FILES['audioFile']['tmp_name'], $targetFile);
        if ($result === false)
            processFileUploadError("100", "SEVERE");

        require_once("Playlist.php");
        $objPlaylist = new Playlist($playlist);

        // After audio file is created, add it to the desired playlist.
        // If the playlist does not exist, create it.
        if (!file_exists($playlist))
            $objPlaylist->createPlaylist($playlist);

        $res = $objPlaylist->addToPlaylist($playlist, basename($targetFile), $httpFile);
        if ($res !== true && $res !== false)
            processFileUploadError($res, "");
    }
    else if ($uploadAction == "APPEND")
    {
        // Read uploaded chunk of the audio file
        $buf = file_get_contents($_FILES['audioFile']['tmp_name']);
        if ($buf === false)
            processFileUploadError("101", "SEVERE");

        // Append the uploaded chunk of bytes to the existing audio file
        $res = file_put_contents($targetFile, $buf, FILE_APPEND);
        if ($res === false)
            processFileUploadError("102", "SEVERE");
    }

    echo "OK";             // Send this to browser if no errors were encountered
?>