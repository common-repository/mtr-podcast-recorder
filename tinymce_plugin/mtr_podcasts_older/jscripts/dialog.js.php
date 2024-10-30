<?php
    require_once("../../../php/jrecorder_globals.php");
?>
var $$ = jQuery.noConflict();

tinyMCE.importPluginLanguagePack('mtr_podcasts');

var JRecorderDialog = {
	init : function() {
	    var inst = tinyMCE.selectedInstance;

	    // Playlist select control onchange event
        $$("#tinymceJRecorderPlaylist").change(
            function()
            {
                var currentPlaylist = $$(this).val();

                $$.post("<?php echo $jRecorderAjaxProcessorPath; ?>", { action : 'loadRecordings', playlist : currentPlaylist },
                    function(response)
                    {
                        // Remove any previous options
                        $$("#tinymceJRecorderRecording").html("");
                        $$("#tinymceJRecorderRecording").append('<option value=""></option>');

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

                            var html = '<option value="' + songName + '">' + songName + '</option>';

                            $$("#tinymceJRecorderRecording").append(html);

                            // Those two are needed, or some presentational bug appears
                            $$("#tinymceJRecorderRecording").css("width", "auto");
                            $$("#tinymceJRecorderRecording").width("100%");
                        } // end for
                    }
                ); // end $$.post
            }
        ); // end $$.change

		this.loadPlaylists();
	},

	insert : function() {
		// Insert the contents from the input into the document

		var text = "";

        var pl = $$.trim( $$("#tinymceJRecorderPlaylist").val() );
        var rec = $$.trim( $$("#tinymceJRecorderRecording").val() );

        if (pl == '') {
            tinyMCEPopup.close();
            return;
        }
        text = pl;

        if (rec != '') {
            text += ' | ' + rec;
        }

        <?php
        // This image is never visialized in the posts/pages/comments. It will
        // be filtered out and replaced before those are displayed to the user.
        //
        // class = used only as a selector when filtering the post
        // alt = "the name of the playlist" | "the name of the recording"
        ?>
        var img = '<img src="<?php echo get_bloginfo('wpurl'); ?>/wp-content/plugins/mtr-podcast-recorder/tinymce_plugin/mtr_podcasts_older/images/jrecorderPlayer.gif" \
                        alt="' + text + '" \
                        title="Podcast Player Plugin Placeholder" \
                        style="border: 0; display: inline; vertical-align: middle;" \
                        class="mceItemWidgetJRecorder jRecorderPlayerPlaceholder" \
                   />\n';

		tinyMCEPopup.execCommand('mceInsertContent', false, img);
		tinyMCE.triggerNodeChange(false);

		tinyMCEPopup.close();
	},

	loadPlaylists : function() {
        // Load the playlists
        $$.post("<?php echo $jRecorderPhpProcessorPath; ?>", { action : 'getPlaylists' },
            function(response)
            {
                var playlists = response.split("\n");
                for (var i = 0; i < playlists.length; i++)
                {
                    var playlist = $$.trim(playlists[i]);
                    if (playlist.length == 0) {
                        playlists.splice(i--, 1);
                        continue;
                    }

                    var html = '<option value="' + playlist + '">' + playlist + '</option>';

                    $$("#tinymceJRecorderPlaylist").append(html);
                }

                $$("#tinymceJRecorderPlaylist").width("100%");

                // Force loading of list with recordings
                $$("#tinymceJRecorderPlaylist").change();
            }
        ); // end $$.post
	}
};

jQuery(document).ready(
    function()
    {
        tinyMCEPopup.executeOnLoad('JRecorderDialog.init();');
    }
);
