<?php
// Include configuration file
//if (!function_exists("add_action"))
//    require_once("../../../../wp-config.php");

require_once("../php/jrecorder_globals.php");

global $jRecorderPhpProcessorPath, $jRecorderAjaxProcessorPath, $jRecorderImagesPath;
?>
var $$ = jQuery.noConflict();

function jRecorderWidgetPlaylistOnChange()
{
    var currentPlaylist = $$("#jrecorder-widget-playlist").val();
    var currentRecording = $$("#jrecorder-widget-saved-recording").val();

    $$.post("<?php echo $jRecorderAjaxProcessorPath; ?>", { action : 'loadRecordings', playlist : currentPlaylist },
        function(response)
        {
            // Remove any previous options
            $$("#jrecorder-widget-recording").html("<option value=''>&nbsp;</option>");

            var songs = response.split("\n");

            for (var i = 0; i < songs.length; i++)
            {
                // Separate the song name from its status (pipe separated)
                var arr2 = songs[i].split("|");
                var songName = arr2[0];
                var songStatus = arr2[1];
                if ($$.trim(songStatus) == "") songStatus = "on";

                // Skip inactive recordings
                if (songStatus != "on") continue;

                var isSelected = (songName == currentRecording) ? ' selected ' : '';

                var html = '<option value="' + songName + '"' + isSelected + '>' + songName + '</option>';

                $$("#jrecorder-widget-recording").append(html);

                // Those two are needed, or some presentational bug appears
                $$("#jrecorder-widget-recording").css("width", "auto");
                $$("#jrecorder-widget-recording").width("100%");
            } // end for
        }
    ); // end $$.post
}

$$(document).ready(
    function()
    {
        var $$ = jQuery.noConflict();

        // When playlist selection changes, reload the list with recordings
        $$("#jrecorder-widget-playlist").change(
            function()
            {
                jRecorderWidgetPlaylistOnChange();
            }
        ); // end $$.change

        // Load all playlists
        $$.post("<?php echo $jRecorderPhpProcessorPath; ?>", { action : 'getPlaylists' },
            function(response)
            {
                if (response.indexOf("ERROR") > -1)
                {
                    response = response.replace(/\<.*?\>/g, "");
                    alert(response);
                    return;
                }

                var currentPlaylist = $$("#jrecorder-widget-saved-playlist").val();

                var playlists = response.split("\n");
                for (var i = 0; i < playlists.length; i++)
                {
                    var playlist = $$.trim(playlists[i]);
                    if (playlist.length == 0) {
                        playlists.splice(i--, 1);
                        continue;
                    }

                    var isSelected = (playlist == currentPlaylist) ? ' selected ' : '';

                    var html = '<option value="' + playlist + '"' + isSelected + '>' + playlist + '</option>';

                    $$("#jrecorder-widget-playlist").append(html);
                }

                // Force loading of list with recordings
                $$("#jrecorder-widget-playlist").change();
            }
        ); // end $$.post
    }
);