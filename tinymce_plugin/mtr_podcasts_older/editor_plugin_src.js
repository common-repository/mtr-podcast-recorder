
/* Import plugin specific language pack */
tinyMCE.importPluginLanguagePack('mtr_podcasts');

var TinyMCE_MTRPodcastsPlugin = {
	/**
	 * Returns information about the plugin as a name/value array.
	 * The current keys are longname, author, authorurl, infourl and version.
	 *
	 * @returns Name/value array containing information about the plugin.
	 * @type Array
	 */
	getInfo : function() {
		return {
			longname : 'MTR Podcast Recorder plugin',
			author : 'MTR Design',
			authorurl : 'http://mtr-design.com',
			infourl : 'http://wiki.mtr-design.com',
			version : "1.0"
		};
	},

	/**
	 * Gets executed when a TinyMCE editor instance is initialized.
	 *
	 * @param {TinyMCE_Control} Initialized TinyMCE editor control instance.
	 */
	initInstance : function(inst) {
		// You can take out plugin specific parameters
		//alert("Initialization parameter:" + tinyMCE.getParam("somename_someparam", false));

		// Register custom keyboard shortcut
		//inst.addShortcut('ctrl', 'r', 'lang_jrecorder_desc', 'mceSomeCommand');
	},

	/**
	 * Gets executed when a TinyMCE editor instance is removed.
	 *
	 * @param {TinyMCE_Control} Removed TinyMCE editor control instance.
	 */
	removeInstance : function(inst) {
		// Cleanup instance resources
	},

	/**
	 * Gets executed when a TinyMCE editor instance is displayed using for example mceToggleEditor command.
	 *
	 * @param {TinyMCE_Control} Visible TinyMCE editor control instance.
	 */
	showInstance : function(inst) {
		// Show instance resources
	},

	/**
	 * Gets executed when a TinyMCE editor instance is hidden using for example mceToggleEditor command.
	 *
	 * @param {TinyMCE_Control} Hidden TinyMCE editor control instance.
	 */
	hideInstance : function(inst) {
		// Hide instance resources
	},

	/**
	 * Returns the HTML code for a specific control or empty string if this plugin doesn't have that control.
	 * A control can be a button, select list or any other HTML item to present in the TinyMCE user interface.
	 * The variable {$editor_id} will be replaced with the current editor instance id and {$pluginurl} will be replaced
	 * with the URL of the plugin. Language variables such as {$lang_somekey} will also be replaced with contents from
	 * the language packs.
	 *
	 * @param {string} cn Editor control/button name to get HTML for.
	 * @return HTML code for a specific control or empty string.
	 * @type string
	 */
	getControlHTML : function(cn) {
		switch (cn) {
			case "mtr_podcasts":
				return tinyMCE.getButtonHTML(cn, 'lang_mtr_podcasts_desc', '{$pluginurl}/images/jrecorder.gif', 'mceInsertJRecorder');
		}

		return "";
	},

	/**
	 * Executes a specific command, this function handles plugin commands.
	 *
	 * @param {string} editor_id TinyMCE editor instance id that issued the command.
	 * @param {HTMLElement} element Body or root element for the editor instance.
	 * @param {string} command Command name to be executed.
	 * @param {string} user_interface True/false if a user interface should be presented.
	 * @param {mixed} value Custom value argument, can be anything.
	 * @return true/false if the command was executed by this plugin or not.
	 * @type
	 */
	execCommand : function(editor_id, element, command, user_interface, value) {
		// Handle commands
		switch (command) {
			// Remember to have the "mce" prefix for commands so they don't intersect with built in ones in the browser.
			case "mceInsertJRecorder":
				// Do your custom command logic here.
				var template = new Array();

                template['file'] = '../../../../../wp-content/plugins/mtr-podcast-recorder/tinymce_plugin/mtr_podcasts_older/dialog.php';
                template['width'] = 320;
                template['height'] = 190;

                // "mce_replacevariables : false" below wont' replace any lang variables, but it will make the elements events WORK!!! (stupid old tinymce)
                tinyMCE.openWindow(template, { editor_id : editor_id, inline : "yes", scrollbars : "yes", mce_replacevariables : false } );
                tinyMCE.triggerNodeChange(false);

				return true;
		}

		// Pass to next handler in chain
		return false;
	},

	/**
	 * Gets called ones the cursor/selection in a TinyMCE instance changes. This is useful to enable/disable
	 * button controls depending on where the user are and what they have selected. This method gets executed
	 * alot and should be as performance tuned as possible.
	 *
	 * @param {string} editor_id TinyMCE editor instance id that was changed.
	 * @param {HTMLNode} node Current node location, where the cursor is in the DOM tree.
	 * @param {int} undo_index The current undo index, if this is -1 custom undo/redo is disabled.
	 * @param {int} undo_levels The current undo levels, if this is -1 custom undo/redo is disabled.
	 * @param {boolean} visual_aid Is visual aids enabled/disabled ex: dotted lines on tables.
	 * @param {boolean} any_selection Is there any selection at all or is there only a cursor.
	 */
	handleNodeChange : function(editor_id, node, undo_index, undo_levels, visual_aid, any_selection) {
	    if (node == null)
			return;

        tinyMCE.switchClass(editor_id + '_mtr_podcasts', 'mceButtonNormal');

        do {
			if (node.nodeName == "IMG" && tinyMCE.getAttrib(node, 'class').indexOf('mceItemWidgetJRecorder') == 0) {
				tinyMCE.switchClass(editor_id + '_mtr_podcasts', 'mceButtonSelected');
				return true;
			}
		} while ((node = node.parentNode));

        tinyMCE.switchClass(editor_id + '_mtr_podcasts', 'mceButtonNormal');

        return true;
	},

	/**
	 * Gets called when a TinyMCE editor instance gets filled with content on startup.
	 *
	 * @param {string} editor_id TinyMCE editor instance id that was filled with content.
	 * @param {HTMLElement} body HTML body element of editor instance.
	 * @param {HTMLDocument} doc HTML document instance.
	 */
	setupContent : function(editor_id, body, doc) {
	},

	/**
	 * Gets called when the contents of a TinyMCE area is modified, in other words when a undo level is
	 * added.
	 *
	 * @param {TinyMCE_Control} inst TinyMCE editor area control instance that got modified.
	 */
	onChange : function(inst) {
	},

	/**
	 * Gets called when TinyMCE handles events such as keydown, mousedown etc. TinyMCE
	 * doesn't listen on all types of events so custom event handling may be required for
	 * some purposes.
	 *
	 * @param {Event} e HTML editor event reference.
	 * @return true - pass to next handler in chain, false - stop chain execution
	 * @type boolean
	 */
	handleEvent : function(e) {
	    // Display event type in statusbar
		top.status = "template plugin event: " + e.type;

		return true;
	},

	/**
	 * Gets called when HTML contents is inserted or retrived from a TinyMCE editor instance.
	 * The type parameter contains what type of event that was performed and what format the content is in.
	 * Possible valuses for type is get_from_editor, insert_to_editor, get_from_editor_dom, insert_to_editor_dom.
	 *
	 * @param {string} type Cleanup event type.
	 * @param {mixed} content Editor contents that gets inserted/extracted can be a string or DOM element.
	 * @param {TinyMCE_Control} inst TinyMCE editor instance control that performes the cleanup.
	 * @return New content or the input content depending on action.
	 * @type string
	 */
	cleanup : function(type, content, inst) {

	    switch (type) {
			case "get_from_editor":
				//alert("[FROM] Value HTML string: " + content);

				break;

			case "insert_to_editor":
				//alert("[TO] Value HTML string: " + content);

				break;

			case "get_from_editor_dom":
				//alert("[FROM] Value DOM Element " + content.innerHTML);

				break;

			case "insert_to_editor_dom":
				//alert("[TO] Value DOM Element: " + content.innerHTML);

				break;
		}

		return content;
	},

	// Private plugin internal methods

	/**
	 * This is just a internal plugin method, prefix all internal methods with a _ character.
	 * The prefix is needed so they doesn't collide with future TinyMCE callback functions.
	 *
	 * @param {string} a Some arg1.
	 * @param {string} b Some arg2.
	 * @return Some return.
	 * @type string
	 */
	_someInternalFunction : function(a, b) {
		return 1;
	}
};

// Adds the plugin class to the list of available TinyMCE plugins
tinyMCE.addPlugin("mtr_podcasts", TinyMCE_MTRPodcastsPlugin);
