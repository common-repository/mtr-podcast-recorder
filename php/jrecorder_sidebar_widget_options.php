<div>
    <input type="hidden" id="jrecorder-widget-saved-playlist" name="jrecorder-widget-saved-playlist" value="<?php echo $playlist; ?>" />
    <label for="jrecorder-widget-playlist" style="line-height:35px;display:block;">
        Select playlist:
    </label>
    <select id="jrecorder-widget-playlist" name="jrecorder-widget-playlist" style="width: 100%;">
        <option value=""></option>
    </select>

    <input type="hidden" id="jrecorder-widget-saved-recording" name="jrecorder-widget-saved-recording" value="<?php echo $recording; ?>" />
    <label for="jrecorder-widget-recording" style="line-height:35px;display:block;">
        Select recording:
    </label>
    <select id="jrecorder-widget-recording" name="jrecorder-widget-recording" style="width: 100%;">
        <option></option>
    </select>

    <input type="hidden" name="jrecorder-widget-submit" id="jrecorder-widget-submit" value="1" />

    <div class="spacer"></div>
    <br />
</div>
<script type="text/javascript">
    $$("#jrecorder-widget-playlist").change(
        function()
        {
            jRecorderWidgetPlaylistOnChange();
        }
    );
</script>