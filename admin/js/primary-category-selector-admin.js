(function($) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	var taxonomies = primaryCategorySelector.taxonomies;

	/**
	 * Function to dynamically change the vaule of primary taxonomy/cat selector
	 * @param {*} taxonomyName
	 */
	function termCheckboxHandler ( taxonomyName, isGutenberg ) {
		return function() {
			console.log(taxonomyName);
			if (true === isGutenberg ) {
				var label = $("label[for='" + this.id + "']").text();
			} else {
				var label = this.nextSibling.data;
			}

			if (true === $(this).prop('checked')) {
				var primaryTaxSelect = $('select#primary-' + taxonomyName);
				primaryTaxSelect.append(
					'<option value="' +
						this.value +
						'"selected="selected">' +
						label +
						'</option>'
				);
			} else if (false === $(this).prop('checked')) {
				var primaryTaxSelectOption = $(
					'select#primary-' +
						taxonomyName +
						' option[value="' +
						this.value +
						'"]'
				);
				primaryTaxSelectOption.remove();
			}
		};
	}

	/**
	 * change primary category/taxonomy selector
	 */
	$(function() {
		$.each(taxonomies, function(name, taxonomy) {
			if (false === taxonomy.gutenberg) {
				//classic editor specific codes to dynamically change values
				var taxonomyChecklist = $('#' + name + 'checklist');
				taxonomyChecklist.on(
					'click',
					'input[type="checkbox"]',
					termCheckboxHandler(name)
				);
			} else {
				$('#wpbody').on(
					'click',
					'input[class^="editor-post-taxonomies_"][type="checkbox"]',
					termCheckboxHandler(name, taxonomy.gutenberg)
				);
			}
		});
	});
})(jQuery);
