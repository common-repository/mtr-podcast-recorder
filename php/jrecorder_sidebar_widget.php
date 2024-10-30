<?php
    global $jRecorderStorageUrl;

    // Collect our widget's options.
    $options = get_option('widget_jrecorder');

    $swfParams = 'playlist_url='.$jRecorderStorageUrl.$options['playlist'].".xspf";
    if (trim($options['recording']) != "")
        $swfParams = 'song_url='.$jRecorderStorageUrl.$options['recording'].'&song_title='.$options['recording'].'&';

    $path = get_bloginfo('wpurl').'/wp-content/plugins/mtr-podcast-recorder/flash/xspf_player_slim.swf?'.$swfParams;

?>
<div id="player">
    <object width="200" height="15">
        <param name="movie" value="<?php echo $path; ?>" />
        <param name="quality" value="high" />
        <param name="menu" value="false" />
        <param name="wmode" value="transparent" />
        <embed src="<?php echo $path; ?>" wmode="transparent" quality="high" menu="false" type="application/x-shockwave-flash" width="200" height="15"></embed>
    </object>

    <?php /* or the tag below
    <object type="application/x-shockwave-flash" data="<?php echo $path; ?>" width="200" height="15">
        <param name="movie" value="<?php echo $path; ?>" />
        <embed src="<?php echo $path; ?>" wmode="transparent" quality="high" menu="false" type="application/x-shockwave-flash" width="200" height="15"></embed>
    </object>
    */ ?>
</div>