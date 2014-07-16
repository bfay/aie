jQuery(document).ready(function($) {

	if (typeof DDLayout == 'undefined') {
		DDLayout = {};
	}

	DDLayout.templateSelector = function($)
	{
		var self = this;

		self.init = function() {

			if (jQuery('#page_template').length > 0) {
				// Combine the layout selection and template selection on
				// the "page" post edit.
				jQuery('#wpddl_template').hide();
	
				// Add drag and drop layout selector under the template selector.
				var html = '<div class="js-dd-layout-selector">';
				html += jQuery('.js-dd-layout-selector').html();
				html += '</div>';
	
				jQuery('.js-dd-layout-selector').remove();
	
				jQuery('#page_template').after(html);
	
				self._initialize_combined_combo();
	
				self._hide_separate_combos();
			} else {
				jQuery('#js-layout-template-name').on('change', self._handle_layout_change);
				self._handle_layout_change(null); // set intial state
			}
			
			self._show_template_warning();

		};

		self._handle_layout_change = function (event) {
			var $editLayoutTempalte = jQuery('.js-edit-layout-template');

			var layout_id = jQuery('#js-layout-template-name option:selected').data('id');
			if (layout_id != '0') {
				$editLayoutTempalte.show();
				$editLayoutTempalte.attr('href', $editLayoutTempalte.data('href') + layout_id);
			} else {
				jQuery('.js-edit-layout-template').hide();
			}

			self._show_template_warning();
			
		}
		
		self._show_template_warning = function () {
			var warning = jQuery('#js-layout-template-name option:selected').data('ddl-warning');
			
			if (jQuery('#page_template').length > 0) {
				var template = jQuery('#page_template').val();
				if (jQuery.inArray(template, DDLayout_settings_editor.layout_templates) == -1) {
					// Don't show the warning because a template without layouts is selected.
					warning = '';
				}
			}
			
			jQuery('.js-layout-support-warning').html(warning);
			
			if (warning) {
				jQuery('.js-layout-support-warning').show();
			} else {
				jQuery('.js-layout-support-warning').hide();
			}
		}
		
		self._initialize_combined_combo = function () {
			var selected_template = jQuery('#page_template').val();
			var force_layout = jQuery('#js-layout-template-name').find(":selected").data('force-layout');
			var selected_layout = jQuery('#js-layout-template-name').find(":selected").val();
			var $combinedLayoutTemplate = jQuery('#js-combined-layout-template-name');
			var $editLayoutTempalte = jQuery('.js-edit-layout-template');

			jQuery('#page_template option').each( function () {
				var template = jQuery(this).val();
				var text = jQuery(this).text();

				if (jQuery.inArray(template, DDLayout_settings_editor.layout_templates) == -1) {
					// A template without a layout
					if ( !force_layout && selected_template == template ) {
						$combinedLayoutTemplate.append('<option selected="selected" value="' + template + '">' + text + '</option>');
						$editLayoutTempalte.hide();
					} else {
						$combinedLayoutTemplate.append('<option value="' + template + '">' + text + '</option>');
					}
				} else {
					// A template with a layout

					if( adminpage === 'post-new-php' )
					{
						if (force_layout) {
							// The layout is one that's been assigned to this post type.
							// Select the first template that supports layouts
							selected_template = template;
							force_layout = false
						}
						var selected = selected_template;

						jQuery('#page_template').val(selected); // Initialize the WP template selector.
					}
					else if( adminpage === 'post-php' )
					{
						var selected = selected_template;
					}
					
					jQuery('#js-layout-template-name option').each( function () {

						var selected_combined = selected_layout + ' in ' + selected;
						var combined = jQuery(this).val() + ' in ' + template;
						var id = jQuery(this).data('id');
						var warning = jQuery(this).data('ddl-warning');

						if ( combined == selected_combined ) {
							$combinedLayoutTemplate.append('<option selected="selected" value="' + combined + '" data-id="' + id + '" data-ddl-warning="' + warning + '">' + jQuery(this).text() + ' in ' + text + '</option>');
							$editLayoutTempalte.show();
							$editLayoutTempalte.attr('href', $editLayoutTempalte.data('href') + id);
						} else {
							$combinedLayoutTemplate.append('<option value="' + combined + '" data-id="' + id + '" data-ddl-warning="' + warning + '">' + jQuery(this).text() + ' in ' + text + '</option>');
						}
					});
				}
			});

			$combinedLayoutTemplate.on('change', self._handle_combined_combo_change);
			$combinedLayoutTemplate.select2({
				'width': '100%'
			});
			
			if (jQuery('.js-layout-support-missing').length) {
				jQuery('.js-layout-support-missing').insertAfter($editLayoutTempalte);
			}
		};

		self._handle_combined_combo_change = function (event) {
			var selected = jQuery('#js-combined-layout-template-name option:selected').val();
			var data = selected.split(' in ');
			var $editLayoutTempalte = jQuery('.js-edit-layout-template');

			if (data.length == 2) {
				// Layout template
				jQuery('#page_template').val(data[1]);
				jQuery('#js-layout-template-name').val(data[0]);
				jQuery('#views_template.postbox').hide();

				if (data[0] != '0') {
					$editLayoutTempalte.show();
					$editLayoutTempalte.attr('href', $editLayoutTempalte.data('href') + jQuery('#js-combined-layout-template-name option:selected').data('id'));
				} else {
					jQuery('.js-edit-layout-template').hide();
				}
			} else {
				// WP template
				jQuery('#page_template').val(selected);
				jQuery('#js-layout-template-name').val('0');
				jQuery('#views_template.postbox').show();
				jQuery('.js-edit-layout-template').hide();
			}

			console.log( 'value ', jQuery(event.target).val(), data, data.length, jQuery('#js-layout-template-name').val() )
			
			self._show_template_warning();
		};

		self._hide_separate_combos = function () {
			jQuery('#page_template').hide();
			jQuery('#js-layout-template-name').hide();
			jQuery('#page_template').prevUntil('#parent_id').each(function () {
				var html = jQuery(this).html();
				if (html.indexOf(ddl_old_template_text) != -1) {
					jQuery(this).hide();
				}
			});
		};

		self.init();
	};

    DDLayout.template_selector = new DDLayout.templateSelector($);

});