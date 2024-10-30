(function($) {
	/**
	 * equalizes the heights of all elements in a jQuery collection
	 * thanks to John Resig for optimizing this!
	 * usage: $("#col1, #col2, #col3").equalizeCols();
	 */

	$.fn.equalizeCols = function(){
		var height = 0;

		return this
			.css("height", "auto")
			.each(function() {
				height = Math.max(height, this.offsetHeight);
			})
			.css("height", height)
			.each(function() {
				var h = this.offsetHeight;
				if (h > height) {
					$(this).css("height", height - (h - height));
				};
			});

	};

})(jQuery);