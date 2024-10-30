<div class="wrap">
   <form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
       <h2>Podcast Recorder Settings</h2>
       <h3>Custom CSS skin for the podcast recorder</h3>
       <textarea id="jRecorderCss" name="jRecorderCss" style="width: 80%; height: 100px;"><?php _e(apply_filters('format_to_edit',$adminOptions['css']),'JRecorder') ?></textarea>

       <h3>Custom loading message</h3>
       <p>Leaving this field black will yield the following message: 'Podcast Recorder is loading...'</p>
       <p>
           <label for="jRecorderLoadingMessage">
               <input type="test" id="jRecorderLoadingMessage" name="jRecorderLoadingMessage" style="width: 80%;" value="<?php _e($adminOptions['loading_message'], 'JRecorder'); ?>" />
           </label>
       </p>

       <div class="submit">
           <input type="submit" id="update_JRecorderSettings" name="update_JRecorderSettings" value="<?php _e('Update Settings', 'JRecorder'); ?>" />
       </div>
    </form>
</div>