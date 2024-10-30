=== Plugin Name ===
Contributors: MTR Design
Donate link: http://mtr-design.com/en/
Tags: podcast, audio, voice, recorder, player, XSPF, MTR Design
Requires at least: 2.3
Tested up to: 2.7
Stable tag: 0.7.4

MTR Podcast Recorder offers real-time recording (via a Java applet) that is saved into MP3 format on the same server where WordPress is installed.

== Description ==

MTR Podcast Recorder offers real-time recording that is saved on the same server where WordPress is installed. All recordings will be saved into the MP3 format using the so famous LAME encoder. In addition to all this, you can manage your playlists and recordings from the WordPress administration. You can easily add a Podcast Player widget to any of your sidebars, posts, or pages if you want to promote a specific recording to your readers.

For more information, please visit the official plugin page at <http://mtr-design.com/en/projects/mtr-podcast-recorder/>.

Features

* Podcast recording into MP3 files. WAV files used as a fallback format if errors occur.
* Podcast organizing into playlists. Easy and intuitive administration of playlists and their contents.
* Podcast playing: a single file or an entire playlist.
* Sidebar widget available for podcast playing.
* Makes adding a podcast to a post or page very simple.
* Control over where the player will display within your post or page.
* Supports unlimited number of MP3 files.
* We use XSPF Web Music Player, which is Open Source Software and can be used, modified and custom styled by anyone, including for commercial purposes.

Plugin related Links:

* Author Homepage - <http://mtr-design.com/en/>
* XSPF Web Music Player - <http://musicplayer.sourceforge.net/>
* LAME encoder - <http://lame.sourceforge.net/index.php>


== Installation ==

Prerequisites and performance

1. Sun's Java Runtime Environment (JRE) version 1.4.2 or higher.
1. Adobe Flash Player plug-in for your browser - version 7 or higher.
1. WordPress 2.3 or higher.
1. PHP5.
1. Windows operating system (the next version of the plugin will work on MacOS and on all popular Linux distros).

Basic installation steps

1. Extract the archive into wp-content/plugins/ inside your WordPress installation. Make sure that the directory with the plugin files is named "mtr-podcast-recorder".
1. Login to the Site Admin and go to Plugins and activate the Podcast Recorder.
1. Then it can be configured from the Settings menu. It should be pretty straight forward.
1. You can create playlists and recordings via the Manage -> MTR Podcast Recorder section of your WordPress administration.


== Frequently Asked Questions ==

= How do I add a Podcast Player to a post or page? =

This is true assuming that you have the MTR Podcast Recorder activated from your "Plugins" page. When creating a post or page, you will notice that there is a new button added to the built-in WordPress editor's toolbar which will enable you to insert image placeholders of the places where a podcast player should appear when someone is reading the post or page. When you click it, you will be prompted to select a playlist and a recording (the last one is optional). Then just confirm your selection, and the placeholder of the podcast player will be inserted into the editor. Once you are over with your article, publish it and check out the resulting post/page.

= How do I perform custom styling of the MTR Podcast Recorder? =

One way will be to study the default CSS file located in directory of the plug-in inside the "wp-content/plugins" of your WordPress installation and modify the classes. You can achieve the same thing if you write your own CSS rules based on the classes and ID-s used in the default CSS file of the MTR Podcast Recorder and then save them in the podcast recorder settings under the "Settings" menu of the administration. These rules should overwrite any in the default CSS file.

= Can I have a custom design for my blog podcasts player? =

Sure, you can. The plugin uses the open source XSPF Web Music Player (<http://musicplayer.sourceforge.net/>), so you can download the application (all XSPF Web Music Player versions come with source FLA files and are fully editable) and create a custom skin for your blog.

= How do I create a playlist? =

Assuming that you are on the page with the podcast recorder, just find the button that says "Create New Playlist" and click it. A text box appears requesting for the name of the new playlist to be created. Once you enter it, click "Save" and it will be created. If you change your mind, then click "Cancel".
Note: The rules for names of playlists are the same as rules for file names on any operating system, with the exception of the SPACE character, which is converted into an underscore character.

= How do I create a recording and save to it a specific playlist? =

Click on the "Record" button of the podcast recorder. After you finish recording, select a playlist from the drop-down list. Then type a name for the recording in the provided text box, and click the "Save" button on the podcast recorder's interface.
Note: The rules for names of recordings are the same as rules for file names on any operating system, with the exception of the SPACE character, which is converted into an underscore character.

= How do I listen to a recording from a playlist? =

Well, this depends on whether you are in the administration area or just reading a post. In the admin area, just click on the "Play" button from the action buttons on the right side of each recording. If you are reading a post, then click on the "Play" button of the flash player.

= How do I sort recordings in a playlist? =

Sorting the recordings in a playlist is done one step at a time via the up-down arrows located in the actions section on the right side of each recording.

= How do I delete a recording from a playlist? =

Just like with playlists, there is a "Delete" button next to each recording, so click it, confirm your action and you are off to go.


== Licence ==

The MTR Podcast Recorder is distributed as a freeware under the GNU General Public License (GPL) license, so you can use it free of charge on your blog. It is a new project under active development, so new features will be added constantly.

