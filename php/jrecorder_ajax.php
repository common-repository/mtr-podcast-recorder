<?php
if (!function_exists("add_action"))
    require_once("../../../../wp-config.php");

require_once("jrecorder_globals.php");
require_once("Playlist.php");

if (isset($jrecorder))
{
    if (!isset($_POST['action']))
        return;

    if ($_POST['action'] == "loadRecordings")
    {
        $playlist = $jRecorderStoragePath."/".$_POST['playlist'].".xspf";

        $xml = @simplexml_load_file($playlist);
        if (!$xml)
            return "ERROR: Cannot read playlist [".$_POST['playlist']."] - it appears to be corrupted!";

        $songs = array();
        foreach ($xml->trackList->children() as $track)
        {
            $songs[] = basename($track->location)."|".$track->annotation;
        }

        echo implode("\n", $songs);
        return;
    }

    if ($_POST['action'] == "deleteSong")
    {
        $playlist = $jRecorderStoragePath."/".$_POST['playlist'].".xspf";

        $id = @trim(@substr($_POST['id'], @strpos($_POST['id'], "_") + 1));

        if (!$id || $id == "")
            die("Bad recording name given! ");

        $file = $jRecorderStoragePath."/".$id;

        if (!file_exists($file))
            echo "Recording does not exist! [$file] ";

        if (!@unlink($file))
            echo "Cannot delete recording! ";

        $playlist = new Playlist($playlist);
        $playlist->deleteNode($file);

        echo "OK";
        return;
    }

    if ($_POST['action'] == "sortSong")
    {
        $direction = $_POST['direction'];
        if ($direction != "up" && $direction != "down")
            die("Invalid sorting order!");

        $playlist = $_POST['playlist'];
        if ($playlist == "")
            die("Unspecified playlist!");

        $id = @trim(@substr($_POST['id'], @strpos($_POST['id'], "_") + 1));
        if (!$id || $id == "")
            die("Bad recording name given!");

        require_once("Playlist.php");

        $recFilename = $jRecorderStoragePath."/".$id;
        $pfile = $jRecorderStoragePath."/".$playlist.".xspf";

        $playlist = new Playlist($pfile);
        $playlist->moveNode($recFilename, $direction);

        echo "OK";
        return;
    }

    if ($_POST['action'] == "setSongStatus")
    {
        $playlist = $jRecorderStoragePath."/".$_POST['playlist'].".xspf";
        $id = @trim(@substr($_POST['id'], @strpos($_POST['id'], "_") + 1));
        $status = $_POST['status'];

        if ($status != "on" && $status != "off")
            die("ERROR: Bad status code!");

        if (!file_exists($playlist))
            die("ERROR: The playlist [{$_POST['playlist']}] does not exist on the server!");

        require_once("Playlist.php");

        $recFilename = $jRecorderStoragePath."/".$id;
        $playlistObj = new Playlist($playlist);
        $playlistObj->setStatus($recFilename, $status);

        echo "OK";
        return;
    }

    if ($_POST['action'] == "createPlaylist")
    {
        $playlistName = $_POST['playlist'];

        require_once("Playlist.php");

        $file = $jRecorderStoragePath."/".$playlistName.".xspf";
        $playlist = new Playlist($file);
        if (!$playlist->createPlaylist($file))
            echo "ERROR: Cannot create the playlist: permission denied!";
        else
            echo "OK";

        return;
    }

    if ($_POST['action'] == "editPlaylist")
    {
        $newPlaylistName = $_POST['newName'];
        $oldPlaylistName = $_POST['oldName'];

        $tmp = rand(100000000, 999999999);

        $newPlaylistName = $jRecorderStoragePath."/".$newPlaylistName.".xspf";
        $oldPlaylistName = $jRecorderStoragePath."/".$oldPlaylistName.".xspf";
        $tmp = $jRecorderStoragePath."/".$tmp.".xspf";

        // To handle cases where only the case of a letter is different, we need to rename the file twice.
        // Otherwise, the case remains unaffected.
        if (!file_exists($oldPlaylistName))
            die("ERROR: No such playlist [$oldPlaylistName] found on the server!");

        if (rename($oldPlaylistName, $tmp)) {
            rename($tmp, $newPlaylistName);
        } else {
            die("ERROR: You don't have suffucient permissions to edit a playlist!");
        }

        echo "OK";
        return;
    }

    if ($_POST['action'] == "deletePlaylist")
    {
        $playlist = $jRecorderStoragePath."/".$_POST['playlist'].".xspf";

        $xml = @simplexml_load_file($playlist);
        if (!$xml)
            return "ERROR: Cannot read playlist [".$_POST['playlist']."] - it appears to be corrupted!";

        $errors = "";
        foreach ($xml->trackList->children() as $track)
        {
            $filename = $jRecorderStoragePath."/".basename(((string)$track->title));
            if (!unlink($filename))
            {
                $errors .= "ERROR: Cannot delete the following recording [$filename] due to lack of permissions on the server!\n".
                           "Before proceeding any further, please erase this file manually, then try to delete the playlist again.\n\n";
                break;
            }
        }

        // No errors, we can proceed with the playlist.
        if ($errors == "")
        {
            if (!unlink($playlist))
                $errors .= "ERROR: Cannot delete the following playlist [$playlist] due to lack of permissions on the server.\n".
                           "You will have to remove it manually! However, all recordings were successfully removed!\n\n";
        }

        if ($errors != "")
            echo $errors;
        else
            echo "OK";

        return;
    }

    // ... add more actions here ...
}
?>