<?php
// Include configuration file
if (!function_exists("add_action"))
    require_once("../../../../wp-config.php");

echo 'var jRecorderImagesPath = "'.get_bloginfo('wpurl').'/wp-content/plugins/mtr-podcast-recorder/images/";';
?>

// Global object used to access the JRecorder applet
var jRecorder = null;

// This function must exist; it is called right after the JRecorder
// applet has finished loading and initializing
function jRecorderLoaded(objApplet)
{
    jRecorder = objApplet;

    jRecorderLoadPlaylists();

    // Get current value of "filename"
    var jRecFilenameObj = document.getElementsByName("jRecFilename")[0];
    jRecFilenameObj.value = jRecorder.getFilename();

    // Finished loading --- show JRecorder
    document.getElementById("objJRecorder").style.width = "0";
    document.getElementById("objJRecorder").style.height = "0";

    document.getElementById("jRecorderLoading").style.display = "none";
    document.getElementById("jRecorderWrapper").style.display = "block";
}

// This function (re)loads the playlists for the JRecorder XHTML control
function jRecorderLoadPlaylists()
{
    if (!jRecorder)
        return;

    var jRecPlaylistsStr = jRecorder.getPlaylists();
    var jRecPlaylistArr = jRecPlaylistsStr.split("\n");

    var jRecPlaylistObj = document.getElementsByName("jRecPlaylist")[0];
    jRecPlaylistObj.options.length = 0;

    for (var i = 0; i < jRecPlaylistArr.length; i++)
    {
        if (jRecPlaylistArr[i].length == 0) {
            jRecPlaylistArr.splice(i--, 1);
        } else {
            jRecPlaylistObj.options[i] = new Option(jRecPlaylistArr[i], jRecPlaylistArr[i]);
        }
    }
}

// JRecorder event handler function - controls the JRecorder applet
function jRecorderEventHandler(jrecElement)
{
    if (jRecorder == null)
        return;

    if (typeof jrecElement == "string")
        jrecElement = document.getElementById(jrecElement);

    var jrecButtonName = jrecElement.className.toLowerCase();

    if (jrecButtonName.indexOf("record") > -1)
    {
        jrecElement.className = "jRecStopButton";
        jrecElement.title = "Stop recording";
        jRecorder.jsRecord();
    }
    else if (jrecButtonName.indexOf("stop") > -1)
    {
        jrecElement.className = "jRecRecordButton";
        jrecElement.title = "Start recording";
        jRecorder.jsStop();
    }
    else if (jrecButtonName.indexOf("pause") > -1)
    {
        jrecElement.className = "jRecContinueButton";
        jrecElement.title = "Continue recording";
        jRecorder.jsPause();
    }
    else if (jrecButtonName.indexOf("continue") > -1)
    {
        jrecElement.className = "jRecPauseButton";
        jrecElement.title = "Pause recording";
        jRecorder.jsContinue();
    }
    else if (jrecButtonName.indexOf("save") > -1)
    {
        jRecorder.jsSave();
    }
    else
    {
        return false;
    }

    return true;
}

if (!window.slugify || typeof window.slugify != "function")
{
    function slugify(t)
    {
    	t = t.replace(/а/g, "a");	t = t.replace(/б/g, "b");	t = t.replace(/в/g, "v");	t = t.replace(/г/g, "g");	t = t.replace(/д/g, "d");	t = t.replace(/е/g, "e");	t = t.replace(/ж/g, "j");	t = t.replace(/з/g, "z");	t = t.replace(/и/g, "i");	t = t.replace(/й/g, "i");	t = t.replace(/к/g, "k");	t = t.replace(/л/g, "l");	t = t.replace(/м/g, "m");	t = t.replace(/н/g, "n");	t = t.replace(/о/g, "o");	t = t.replace(/п/g, "p");	t = t.replace(/р/g, "r");	t = t.replace(/с/g, "s");	t = t.replace(/т/g, "t");	t = t.replace(/у/g, "u");	t = t.replace(/ф/g, "f");	t = t.replace(/х/g, "h");	t = t.replace(/ц/g, "c");	t = t.replace(/ч/g, "ch");	t = t.replace(/ш/g, "sh");	t = t.replace(/щ/g, "sht");	t = t.replace(/ь/g, "i");	t = t.replace(/ъ/g, "y");	t = t.replace(/ю/g, "yu");	t = t.replace(/я/g, "ya");
    	t = t.replace(/А/g, "a");	t = t.replace(/Б/g, "b");	t = t.replace(/В/g, "v");	t = t.replace(/Г/g, "g");	t = t.replace(/Д/g, "d");	t = t.replace(/Е/g, "e");	t = t.replace(/Ж/g, "j");	t = t.replace(/З/g, "z");	t = t.replace(/И/g, "i");	t = t.replace(/Й/g, "i");	t = t.replace(/К/g, "k");	t = t.replace(/Л/g, "l");	t = t.replace(/М/g, "m");	t = t.replace(/Н/g, "n");	t = t.replace(/О/g, "o");	t = t.replace(/П/g, "p");	t = t.replace(/Р/g, "r");	t = t.replace(/С/g, "s");	t = t.replace(/Т/g, "t");	t = t.replace(/У/g, "u");	t = t.replace(/Ф/g, "f");	t = t.replace(/Х/g, "h");	t = t.replace(/Ц/g, "c");	t = t.replace(/Ч/g, "ch");	t = t.replace(/Ш/g, "sh");	t = t.replace(/Щ/g, "sht");	t = t.replace(/Ь/g, "i");	t = t.replace(/Ъ/g, "y");	t = t.replace(/Ю/g, "yu");	t = t.replace(/Я/g, "ya");
        t = t.replace(/[^a-zA-Z0-9\ _-]/g, "");

        //t = t.replace(/\s/g, "-");
        t = t.replace(/\s/g, "_");

        //t = t.replace(/-+/g, "-");
        t = t.replace(/-+/g, "_");

        t = t.replace(/_+/g, "_");
        t = t.replace(/[_-]*$/, "");

        //t = t.toLowerCase();

        return t;
    }
}