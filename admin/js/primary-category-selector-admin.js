(function($) {
	'use strict';

	var taxonomies = primaryCategorySelector.taxonomies;

	/**
	 * Function to dynamically change the value of primary taxonomy/cat selector
	 * @param {*} taxonomyName
	 */
	function termCheckboxHandler ( taxonomyName, isGutenberg ) {
		return function() {
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
