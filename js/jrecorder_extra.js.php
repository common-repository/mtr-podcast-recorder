<?php
// Include configuration file
if (!function_exists("add_action"))
    require_once("../../../../wp-config.php");

global $jRecorderPhpProcessorPath, $jRecorderAjaxProcessorPath, $jRecorderImagesPath,
       $jRecorderStorageUrl, $jRecorderStoragePath;
?>

var $$ = jQuery.noConflict();

var jRecorderCurrentPlaylist = "";

$$(document).ready(
    function()
    {
        var $$ = jQuery.noConflict();

        //$$("#jRecPlaylistsWrapper, #jRecSongsWrapper, #jRecPlaylistsSongsWrapper").equalizeCols();

        <?php // get list with existing playlists ?>
        $$.post("<?php echo $jRecorderPhpProcessorPath; ?>", {action : 'getPlaylists'},
            function(response)
            {
                var playlist = "";

                redoPlaylists(response);

                <?php // Load the recordings from the first playlist by default ?>
                loadPlaylist();

                <?php // Add functionality to the Create New Playlist button ?>
                $$("#jRecBtnNewPlaylist").click(
                    function()
                    {
                        $$(this).hide();

                        $$(this).after('\
                            <div id="jRecRemoveMe" style="float: left;">\
                                <input type="text" id="jRecNewPlaylistName" name="jRecNewPlaylistName" style="width: 145px; height: 20px; float: left; border: 0;" />\
                                <button class="jRecSaveBtn button-secondary" style="float: left; margin: 2px 3px 0 3px;">Save</button>\
                                <button class="jRecCancelBtn button-secondary" style="float: left; margin: 2px 3px 0 3px;">Cancel</button>\
                            </div>\
                        ');

                        //$$("#jRecPlaylistsWrapper, #jRecSongsWrapper, #jRecPlaylistsSongsWrapper").equalizeCols();

                        // Insert actions for the buttons created above
                        $$("#jRecRemoveMe .jRecSaveBtn").click(function() {
                            $$.post("<?php echo $jRecorderAjaxProcessorPath; ?>", { action : 'createPlaylist', playlist : $$("#jRecNewPlaylistName").val() },
                                function(response)
                                {
                                    // Get an updated sorted list of the playlists
                                    $$.post("<?php echo $jRecorderPhpProcessorPath; ?>", { action : 'getPlaylists' },
                                        function(response)
                                        {
                                            // Remove the inserted elements
                                            $$("#jRecPlaylists li").remove();

                                            redoPlaylists(response);

                                            $$("#jRecRemoveMe").remove();
                                            $$("#jRecBtnNewPlaylist").show();
                                        }
                                    );
                                }
                            ); // end $$.post
                        });

                        $$("#jRecRemoveMe .jRecCancelBtn").click(function() {
                            $$("#jRecRemoveMe").remove();
                            $$("#jRecBtnNewPlaylist").show();
                        });

                        $$("#jRecRemoveMe #jRecNewPlaylistName").blur(function() {
                            $$(this).val( slugify( $$(this).val() ) );
                        });

                        return false;
                    }
                ); // end .click

                // Add some more functionality to the recorder's save button
                $$("#jRecSaveButton").click(function() {
                    var obj = $$("#jRecPlaylist").get(0);

                    var selectedPlaylistValue = obj.options[obj.selectedIndex].value;

                    // If we are recording to the playlist we are also looking at,
                    // auto update the list with recordings with the new one
                    if (jRecorderCurrentPlaylist == selectedPlaylistValue) {
                        loadPlaylist();
                    }

                    return false;
                });

            } // end function(response)
        ); // end $$.post
    } // end document ready inline function
); // end document ready function


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

function fixZebra(tableName)
{
    var $$ = jQuery.noConflict();

    // Fix the zebra coloring
    $$("#" + tableName + " tr:nth-child(odd)").addClass("odd");
    $$("#" + tableName + " tr:even").removeClass("odd");
    $$("#" + tableName + " tr:even").addClass("even");
}

function redoPlaylists(response)
{
    var $$ = jQuery.noConflict();

    // Repopulate the playlists list
    $$("#jRecPlaylists").html("");
    var playlists = response.split("\n");
    for (var i = 0; i < playlists.length; i++)
    {
        var playlist = $$.trim(playlists[i]);
        if (playlist.length == 0) {
            playlists.splice(i--, 1);
            continue;
        }

        if (jRecorderCurrentPlaylist == "" && i == 0)
            jRecorderCurrentPlaylist = playlist;

        var trClass = (i % 2 == 0) ? ' even ' : ' odd ';

        var aClass = (playlist == jRecorderCurrentPlaylist) ? ' class="current" ' : '';

        var html = ' \
            <tr id="' + playlist + '" class="' + trClass + '"> \
                <td class="text"> \
                    <a href="#" title="' + playlist + '"' + aClass + '>' + playlist + '</a> \
                </td> \
                <td class="actions"> \
                    <div class="jRecPlaylistEdit"></div> \
                    <div class="jRecPlaylistDelete"></div> \
                </td> \
            <tr>';
        $$("#jRecPlaylists").append(html);
    }

    // Needed this line here for Safari
    //$$("#jRecPlaylistsWrapper, #jRecSongsWrapper, #jRecPlaylistsSongsWrapper").equalizeCols();

    <?php // Setup the click event for each playlist entry ?>
    $$("#jRecPlaylists a").click(function() {
        jRecorderCurrentPlaylist = $$(this).text();
        loadPlaylist();

        $$("#jRecPlaylists a").removeClass("current");
        $$(this).addClass("current");

        return false;
    });

    // EDIT
    $$(".jRecPlaylistEdit").click(function() {
        var old = $$(this).parent().parent().attr("id");

        var obj = $$(this).parent().parent().find(".text");
        obj.find("a").hide();
        obj.append('<input type="text" id="jRecEditedPlaylist" name="jRecEditedPlaylist" value="' + old + '" style="width: 95%; height: 15px !important; border: 1px solid blue;" />');
        $$("#jRecEditedPlaylist").focus(); // for some reason this line is ignored unless .select() is added or a second line
        $$("#jRecEditedPlaylist").focus();

        $$("#jRecEditedPlaylist").blur( function() {
            var r = confirm("!!! WARNING !!!\n\r\n\rIf you have used any recordings from this playlist in a post or in the sidebar widget, \n\ryou will have to modify the playlist name there as well, or the recording there will not be available for playback.");
            if (!r)
            {
                $$("#jRecEditedPlaylist").remove();
                obj.find("a").show();

                return;
            }

            var newPlaylist = slugify($$(this).val());

            $$.post("<?php echo $jRecorderAjaxProcessorPath; ?>", { action : 'editPlaylist', oldName : old, newName : newPlaylist },
                function(response)
                {
                    if (response != "OK")
                    {
                        alert(response);
                        return false;
                    }

                    $$("#jRecEditedPlaylist").remove();
                    obj.find("a").text(newPlaylist);
                    obj.find("a").show();

                    if (jRecorderCurrentPlaylist == old) {
                        jRecorderCurrentPlaylist = newPlaylist;
                    }

                    jRecorderLoadPlaylists();
                }
            );
        });

        return false;
    });

    // DELETE
    $$(".jRecPlaylistDelete").click(function() {
        var r = confirm("!!! WARNING !!!\n\r\n\rIf you delete this playlist, all recordings in it will be deleted as well.\n\r\n\rDo you want to proceed?");
        if (!r) return;

        var pl = $$(this).parent().parent().attr("id");

        var obj = $$(this).parent().parent();

        $$.post("<?php echo $jRecorderAjaxProcessorPath; ?>", { action : 'deletePlaylist', playlist : pl },
            function(response)
            {
                if (response != "OK")
                {
                    alert(response);
                    return false;
                }

                obj.remove();

                if (pl == jRecorderCurrentPlaylist)
                {
                    jRecorderCurrentPlaylist = $$("#jRecPlaylists tr:first").find(".text a").text();
                    $$("#jRecPlaylists tr:first").find(".text a").addClass("current");
                    loadPlaylist();
                }

                jRecorderLoadPlaylists();
            }
        );

        return false;
    });


    // (bug fix) IE 7 seems to add empty LI elements, so remove them
    $$("#jRecPlaylists li").each(
        function(i)
        {
            if ($$(this).text() == "") {
                $$(this).remove();
            }
        }
    );

    //$$("#jRecPlaylistsWrapper, #jRecSongsWrapper, #jRecPlaylistsSongsWrapper").equalizeCols();

    // Reload the playlists in the applet
    jRecorderLoadPlaylists();
}

// Loads the recordings inside a playlist and builds a table from the entries
function loadPlaylist()
{
    var $$ = jQuery.noConflict();

    $$.post("<?php echo $jRecorderAjaxProcessorPath; ?>", { action : 'loadRecordings', playlist : jRecorderCurrentPlaylist },
        function(response)
        {
            var songs = response.split("\n");

            $$("#jRecSongsTable tbody tr").remove();

            var anyFound = false;
            for (var i = 0; i < songs.length; i++)
            {
                if ($$.trim(songs[i]) == "")
                    continue;

                // Separate the song name from its status (pipe separated)
                var arr2 = songs[i].split("|");
                var songName = arr2[0];
                var songStatus = arr2[1];
                if ($$.trim(songStatus) == "") songStatus = "on";

                <?php $path = get_bloginfo('wpurl').'/wp-content/plugins/mtr-podcast-recorder/flash/xspf_player_button.swf?&song_url={song_url}&song_title={song_title}&'; ?>
                <?php echo 'var songUrl = "'.$path.'";'; ?>
                songUrl = songUrl.replace(/{song_url}/, '<?php echo $jRecorderStorageUrl; ?>' + songName);
                songUrl = songUrl.replace(/{song_title}/, songName);

                anyFound = true;

                $$("#jRecSongsTable tbody").append('\
                    <tr id="jRecSongRow_' + i + '_' + songName + '">\
                        <td style="width: 50px; text-align: center;">\
                            <div class="jRecSongStatus jRecSongStatus-' + songStatus + '"></div>\
                        </td>\
                        <td>' + songName + '</td>\
                        <td style="width: 125px;">\
                            <!--[if IE]><div style="float: left; margin-top: 1px;"><![endif]--> \
                            <object width="21" height="21" style="float: left; margin-top: 1px;"> \
                                <param name="movie" value="' + songUrl + '" /> \
                                <param name="quality" value="high" /> \
                                <param name="menu" value="false" /> \
                                <param name="wmode" value="transparent" /> \
                                <param name="swliveconnect" value="true" /> \
                                <embed src="' + songUrl + '" wmode="transparent" quality="high" menu="false" type="application/x-shockwave-flash" swliveconnect="true" width="21" height="21"></embed> \
                            </object> \
                            <!--[if IE]></div><![endif]--> \
                            <!--div class="jRecSongPlay"><span style="display: none;"><?php echo $jRecorderStorageUrl; ?>' + songName + '</span></div-->\
                            <div class="jRecSongDelete"></div>\
                            <div class="jRecSongDown"></div>\
                            <div class="jRecSongUp"></div>\
                        </td>\
                    </tr>\
                ');
            }

            if (!anyFound || songs.length == 0)
            {
                $$("#jRecSongsTable tbody").html('<tr><td colspan="3">Playlist is empty.</td></tr>');
                return;
            }

            // Make columns equally tall
            //$$("#jRecPlaylistsWrapper, #jRecSongsWrapper, #jRecPlaylistsSongsWrapper").equalizeCols();

            // Add zebra coloring
            fixZebra("jRecSongsTable");

            <?php // actions to button images on the page ?>
            // STATUS
            $$(".jRecSongStatus").click(
                function()
                {
                    var status = true;
                    var statusStr = "on";
                    if ($$(this).hasClass("jRecSongStatus-on")) {
                        status = false;
                        statusStr = "off";
                    }

                    var row = $$(this).parents("tr:first").get(0);

                    var url = "<?php echo $jRecorderAjaxProcessorPath; ?>";
                    $$.post(url, { action : 'setSongStatus', id : row.id.replace(/\D*/, ''), playlist : jRecorderCurrentPlaylist, status : statusStr }, function(response) {
                        if (response != "OK")
                        {
                            alert(response);
                            return false;
                        }
                    });

                    $$(this).removeClass("jRecSongStatus-on");
                    $$(this).removeClass("jRecSongStatus-off");
                    if (status) {
                        $$(this).addClass("jRecSongStatus-on");
                    } else {
                        $$(this).addClass("jRecSongStatus-off");
                    }

                    return false;
                }
            );

/********************** NEEDED IF FLASH PLAYER WILL BE CONTROLLED VIA JAVASCRIPT
            // PLAY
            $$(".jRecSongPlay").click(
                function()
                {
                    if ($$(this).hasClass("jRecSongStop")) {
                        $$(this).removeClass("jRecSongStop");
// TODO Stop playback

                        return;
                    }

                    $$(this).addClass("jRecSongStop");

// TODO Start playback
//                    var rec = $$(this).find("span").text();
//                    jRecorder.jsPlay(rec, "jRecRestorePlayButton");
                }
            );
**************************/

            // MOVE DOWN
            $$(".jRecSongDown").click(
                function()
                {
                    var ct = $$(this).parents("tr:first");

                    // stub class used as a selector
                    if (ct.next().hasClass("nosort")) {
                        return;
                    }

                    ct.before(ct.next());

            		var navID = ct.get(0).id.replace(/n/, '');

                    var url = "<?php echo $jRecorderAjaxProcessorPath; ?>";
                    $$.post(url, { action: 'sortSong', id: navID.replace(/\D*/, ''), direction: 'down', playlist : jRecorderCurrentPlaylist }, function() {

                    });

                    fixZebra("jRecSongsTable");

                    return false;
                }
            );

            // MOVE UP
            $$(".jRecSongUp").click(
                function()
                {
                    var ct = $$(this).parents("tr:first");

                    // stub class used as a selector
                    if (ct.prev().hasClass("nosort")) {
                        return;
                    }

                    ct.after(ct.prev());

            		var navID = ct.get(0).id.replace(/n/, '');

                    var url = "<?php echo $jRecorderAjaxProcessorPath; ?>";
                    $$.post(url, { action: 'sortSong', id: navID.replace(/\D*/, ''), direction: 'up', playlist : jRecorderCurrentPlaylist }, function() {

                    });

                    fixZebra("jRecSongsTable");

                    return false;
                }
            );

            // DELETE
            $$(".jRecSongDelete").click(
                function()
                {
                    r = confirm("Are you sure you want to delete this?");
                    if(!r) return false;

                    var row = $$(this).parents("tr:first").get(0);
                    var url = "<?php echo $jRecorderAjaxProcessorPath; ?>";
            		$$.post( url, { action: 'deleteSong', playlist: jRecorderCurrentPlaylist, id: row.id.replace(/\D*/, '') }, function(r) {
                        $$(row).remove();
                        fixZebra("jRecSongsTable");
                    });

                    return false;
                }
            );
        }
    );
}

function jRecRestorePlayButton()
{
    alert("restore play button");
}