<?php
    require_once("../../php/jrecorder_globals.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<title>{#mtr_podcasts_dlg.title}</title>

	<script type="text/javascript" src="<?php echo get_bloginfo('wpurl').'/wp-content/plugins/mtr-podcast-recorder/js/jquery-1.2.6.pack.js'; ?>"></script>
	<!--script type="text/javascript" src="../../tiny_mce_popup.js"></script-->
	<script type="text/javascript" src="<?php echo get_bloginfo('wpurl'); ?>/wp-includes/js/tinymce/tiny_mce_popup.js"></script>
	<script type="text/javascript" src="js/dialog.js.php"></script>
</head>
<body>

<form onsubmit="JRecorderDialog.insert(); return false;" action="#">
	<p>A playlist is required at a minimum in order to insert the Podcast Player.</p>
	<p>
	   Select playlist:
	   <select id="tinymceJRecorderPlaylist" name="tinymceJRecorderPlaylist" class="text">
	       <option></option>
	   </select>
	</p>
	<p>
	   Select recording:
	   <select id="tinymceJRecorderRecording" name="tinymceJRecorderRecording" class="text">
	       <option>select a playlist first</option>
	   </select>
   </p>

	<div class="mceActionPanel">
		<div style="float: left">
			<input type="button" id="insert" name="insert" value="{#insert}" onclick="JRecorderDialog.insert();" />
		</div>

		<div style="float: right">
			<input type="button" id="cancel" name="cancel" value="{#cancel}" onclick="tinyMCEPopup.close();" />
		</div>
	</div>
</form>

</body>
</html>
