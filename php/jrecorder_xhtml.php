<?php global $jRecorderPhpProcessorPath, $jRecorderImagesPath; ?>

<div class="wrap">

    <h2 style="border: 0;">MTR Podcast Recorder</h2>
    <p>
		MTR Podcast Recorder offers real-time recording that is saved on the same server where WordPress is installed. All recordings will be saved into the MP3 format using the so famous LAME encoder. In addition to all this, you can manage your playlists and recordings from the WordPress administration. You can easily add a Podcast Player widget to any of your sidebars, posts, or pages if you want to promote a specific recording to your readers.<br />
		For more information, please visit the official plugin page at <a href="http://mtr-design.com/en/projects/mtr-podcast-recorder/">http://mtr-design.com/en/projects/mtr-podcast-recorder/</a>.
    </p>
    <hr />

    <div id="divJRecorder" class="jRecorder">
        <!--[if !IE]>-->
        <object id="objJRecorder" classid="java:org.mtr.jrecorder.JRecorder.class" archive="MTRPodcasts.jar" width="380" height="71" type="application/x-java-applet" style="width: 1px; height: 1px; margin: auto;">
            <param name="scriptable" value="true" />
            <param name="mayscript" value="true" />
            <!--
                Codebase is optional if the applet's JAR file is in the same directory as the XHTML page;
                Otherwise, it must contain the directory name where the main class is located
            -->
            <param name="codebase" value="../wp-content/plugins/mtr-podcast-recorder/applet/" />
            <!-- Konqueror browser needs the following param -->
            <param name="archive" value="MTRPodcasts.jar" />
            <!-- The following three are used by Java Plug-in 1.6.0_10 for Firefox 3 at least -->
            <param name="java_arguments" value="-Xmx512m" />
            <param name="separate_jvm" value="true" />
            <param name="classloader_cache" value="false" />
            <!-- Custom parameters for the applet -->
            <param name="processor" value="<?php echo $jRecorderPhpProcessorPath; ?>" />
        <!--<![endif]-->
        <object id="objJRecorder" classid="clsid:8AD9C840-044E-11D1-B3E9-00805F499D93" width="380" height="71" style="width: 1px; height: 1px; margin: auto;">
            <param name="code" value="org.mtr.jrecorder.JRecorder.class" />
            <param name="archive" value="MTRPodcasts.jar" />
            <param name="scriptable" value="true" />
            <param name="mayscript" value="true" />
            <param name="codebase" value="../wp-content/plugins/mtr-podcast-recorder/applet/" />
            <!-- The following three are used by Java Plug-in 1.6.0_10 -->
            <param name="java_arguments" value="-Xmx512m" />
            <param name="separate_jvm" value="true" />
            <param name="classloader_cache" value="false" />
            <!-- Custom parameters for the applet -->
            <param name="processor" value="<?php echo $jRecorderPhpProcessorPath; ?>" />

            <strong>
                This browser does not have a Java Plug-in. <br />
                <a href="http://java.sun.com/products/plugin/downloads/index.html">
                    Get the latest Java Plug-in here.
                </a>
            </strong>
        </object>
        <!--[if !IE]>-->
        </object>
        <!--<![endif]-->

        <div id="jRecorderLoading" class="jRecorderLoading"><?php echo $adminOptions['loading_message']; ?></div>
        <div id="jRecorderWrapper" class="jRecorderWrapper">
            <div id="jRecPauseButton" class="jRecPauseButton" title="Pause recording" onclick="jRecorderEventHandler(this);"></div>
            <div id="jRecRecordButton" class="jRecRecordButton" title="Start recording" onclick="jRecorderEventHandler(this);"></div>

            <select id="jRecPlaylist" name="jRecPlaylist" onchange="jRecorder.setSelectedPlaylist(this.options[this.selectedIndex].value);">
                <option>Loading playlists...</option>
            </select>

            <input type="text" name="jRecFilename" value="" onblur="this.value=slugify(this.value); jRecorder.setFilename(this.value);" onclick="this.select();" />
            <a id="jRecSaveButton" class="jRecSaveButton button-secondary" href="#" onclick="jRecorderEventHandler(this);">Save to Server</a>
        </div>
    </div>

</div> <!-- end div.wrap -->
