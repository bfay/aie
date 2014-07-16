// post-content-cell.js


jQuery(document).ready(function($){

	DDLayout.PostContentCell = function($)
	{
		var self = this;

		self.init = function() {
			
			self._ct_editor = null;
			self._ct_code_mirror = null;
			self._preview_cache = {};
			
			self._cell_content = null;

			jQuery('.js-ct-name').on('click', self._switch_to_edit_ct_name);
			
			jQuery('.js-create-new-ct').on('click', self._create_new_ct);
			
			jQuery('.js-ct-edit-name').on('blur', self._end_ct_name_edit);
			
			jQuery('.js-load-different-ct').on('click', self._switch_to_select_different_ct)
			
			jQuery('.js-ddl-post-content-post-type').on('change', self._handle_post_type_change);

			jQuery('input[name="ddl-layout-page"]').on('change', function(e) {

				self.adjust_specific_page_state();
			});

			jQuery('#post-content-view-template').on('change', self._handle_ct_change);
			
			// Handle the dialog open.
			
			jQuery(document).on('cell-post-content.dialog-open', function(e, content, dialog) {
				
				DDLayout.types_views_popup_manager.start();
				
				self._original_ct_name = '';
				self._original_ct_value = ''
				
				jQuery('#ddl-layout-selected_post').select2({
					'width' : 'resolve'
				});
				

				var select_post_type = jQuery('.js-ddl-post-content-post-type').val();
				if (select_post_type != jQuery('#ddl-layout-selected_post').data('post-type')) {
					self._cell_content = content;
					jQuery('.js-ddl-post-content-post-type').trigger('change');
				}

				self.adjust_specific_page_state();
				
				var selected_ct = jQuery('#post-content-view-template').val();
				if (selected_ct == 'None') {
					jQuery('.js-post-content-ct').hide();
					self._set_use_tc_mode_radio('no');
				} else {
					jQuery('.js-post-content-ct').show();
					jQuery('#post-content-view-template').trigger('change');
					self._set_use_tc_mode_radio('yes');
				}
			});
			jQuery(document).on('cell-post-content.dialog-close', function(e) {
				jQuery('#ddl-layout-selected_post').select2('destroy');
				
				self._close_codemirror();
				
				DDLayout.types_views_popup_manager.end();
				
			});

			jQuery(document).on('cell-post-content.get-content-from-dialog', function(e, content, dialog) {
				self._save_ct(content);
			});
			
			jQuery('input[name="use-ct"]').on('change', function(e) {
				self.adjust_ct_mode();
			});
			

		};

		self._handle_ct_change = function() {
			if (jQuery(this).val() == 'None') {
				jQuery('.js-dialog-edit-save').prop('disabled', true);
			} else {
				jQuery('.js-dialog-edit-save').prop('disabled', false);
				
				if (jQuery('.js-create-new-ct').length > 0) {
					
					// Only show CT editor if Views plugin is available.
					
					var ct_id = jQuery(this).find('option:selected').data('ct-id');
					var ct_name = jQuery(this).find('option:selected').text();
					self._open_ct_editor(ct_id, ct_name);
					jQuery('.js-ct-selector').hide();
					jQuery('.js-ct-edit').hide();
				}
			}
		};
		
		self._handle_post_type_change = function() {
			var data = {
					post_type : jQuery(this).val(),
					action : 'get_posts_for_post_content',
					nonce : jQuery(this).data('nonce')
			};

			var spinnerContainer = jQuery('<div class="spinner ajax-loader">').insertAfter(jQuery(this)).show();
			jQuery('#ddl-layout-selected_post').hide();
			jQuery('#ddl-layout-selected_post').select2('destroy');

			jQuery.ajax({
					type:'post',
					url:ajaxurl,
					data:data,
					success: function(response){ // TODO: success is deprecated http://api.jquery.com/jQuery.ajax/
						jQuery('#ddl-layout-selected_post').replaceWith(response);
						if (self._cell_content) {
							jQuery('#ddl-layout-selected_post').val(self._cell_content.selected_post);
						}
						spinnerContainer.remove();
						jQuery('#ddl-layout-selected_post').select2({
							'width' : 'resolve'
						});
						jQuery('#ddl-layout-selected_post').fadeIn(200);
					},
				});
		};
		
		self._save_ct = function (content) {
			if (self._ct_editor) {
				
				var ct_title = jQuery('.js-ct-edit-name').val();
				var ct_value = self._ct_code_mirror.getValue();

				self._preview_cache[content.view_template] = ct_value;
				
				if (self._original_ct_name != ct_title || self._original_ct_value != ct_value) {

					var data = {
						action : 'wpv_ct_update_inline',
						ct_value : ct_value,
						ct_id : self._ct_editor,
						ct_title : ct_title,
						wpnonce : $('#wpv-ct-inline-edit').attr('value')
					};
					$.post(ajaxurl, data, function(response) {
						
						if (self._original_ct_name != ct_title) {
							
							// we need to refresh the ct drop down.
							
							self._refresh_ct_dropdown(0);
						}
						
						
					});
				}				
			}
			
		}
		
		self._refresh_ct_dropdown = function (select_id) {
			var data = {
				action : 'dll_refresh_ct_list',
				wpnonce : $('#wpv-ct-inline-edit').attr('value')
			};
			$.post(ajaxurl, data, function(response) {
				
				jQuery('.js-ct-select-box').html(response);
				
				if (select_id) {
					jQuery('#post-content-view-template option').each( function () {
						if (jQuery(this).data('ct-id') == select_id) {
							jQuery('#post-content-view-template').val(jQuery(this).val());
						}
					})
				}
				
				jQuery('#post-content-view-template').on('change', self._handle_ct_change);
			});
		}
		
		self._set_use_tc_mode_radio = function (value) {
			jQuery('input[name="use-ct"]').each (function () {
				jQuery(this).prop('checked', jQuery(this).val() == value);
			})
		}
		self.adjust_ct_mode = function () {
			if (jQuery('input[name="use-ct"]:checked').val() == 'yes') {
				var no_ct_selected = jQuery('#post-content-view-template').val() == 'None';

				if (no_ct_selected) {
					jQuery('.js-ct-edit').hide();
					jQuery('.js-ct-selector').show();
				}
					
				
				jQuery('.js-dialog-edit-save').prop('disabled', no_ct_selected);
				
				if (jQuery('#post-content-view-template option').length == 1) {
					// Only the "None" option
					// Create a new CT automatically
					
					self._create_new_ct();
					
				}
				
			} else {
				jQuery('.js-post-content-ct').hide();
				jQuery('#post-content-view-template').val('None');
				
				self._close_codemirror();
				$('.js-wpv-ct-inline-edit').html('');
				
				jQuery('.js-dialog-edit-save').prop('disabled', false);
			}
		}
		
		self._close_codemirror = function () {
			self._ct_value = '';
			if (self._ct_editor) {
				self._ct_value = self._ct_code_mirror.getValue();
				icl_editor.codemirror('wpv-ct-inline-editor-' + self._ct_editor, false);
				self._ct_editor = null;
			}
		}
			
		self.adjust_specific_page_state = function () {
			var page = jQuery('input[name="ddl-layout-page"]:checked').val();
			if (page == 'current_page') {
				jQuery('#js-post-content-specific-page').hide();
			} else {
				jQuery('#js-post-content-specific-page').show();
			}
		};

		self.display_post_content_info = function(content, loading_text) {
			var preview = '';
			switch (content.page) {
				case 'current_page':
					preview += DDLayout_post_content_strings.current_post;
					break;

				case 'this_page':
					preview += DDLayout_post_content_strings.this_post;
					break;

			}
			
			if (content.view_template != 'None') {
				preview += '<br />';
				
				var div_place_holder = 'js-content-template-preview-' + content.view_template;
				
				if (typeof (self._preview_cache[content.view_template]) !== 'undefined' && self._preview_cache[content.view_template] != null) {
					// get it from the cache.
					preview += '<div class="' + div_place_holder + '">' + self._preview_cache[content.view_template] + '</div>';
				} else {
					// create a place holder and fetch it.
					preview += '<div class="' + div_place_holder + '">' + loading_text + '</div>';
					
					if ( typeof (self._preview_cache[content.view_template]) == 'undefined' ) {
						self._preview_cache[content.view_template] = null;
						
						var data = {
								action : 'ddl_content_template_preview',
								view_template: content.view_template,
								wpnonce : $('#wpv-ct-inline-edit').attr('value'),
						};
						jQuery.ajax({
								url: ajaxurl,
								type: 'post',
								data: data,
								cache: false,
								success: function(data) {
									//cache view id data
									self._preview_cache[content.view_template] = data;
									jQuery(div_place_holder).html(data);
									
									// If we have received all the previews we need to refresh
									// the layout display to re-calculate the heights.
									
									var all_previews_ready = true;
									for (var key in self._preview_cache) {
										 if (self._preview_cache.hasOwnProperty(key)) {
											  if (self._preview_cache[key] == null) {
												   all_previews_ready = false;
											  }
										 }
									}
									
									if (all_previews_ready) {
										 DDLayout.ddl_admin_page.render_all();     
									}
								}
						});
						
					}
				}
			}
			
			return preview;
		};

		self.get_content_template_title = function (name) {
			var title = name;

			jQuery('#post-content-view-template option').each( function () {
				if (jQuery(this).val() == name) {
					title = jQuery(this).text();
				}
			});

			return title;
		};

		self._open_ct_editor = function (id, name) {		
			$('<div class="spinner ajax-loader-bar js-ct-loading">').insertBefore($('.js-ct-selector:first')).show();
			jQuery('input[name="use-ct"]').prop('disabled', true);
			jQuery('.js-dialog-edit-save').prop('disabled', true);

			if (id == 0) {
				// we need to create a new one
				data = {
					action : 'dll_add_view_template',
					ct_name : name,
					wpnonce : $('#wpv-ct-inline-edit').attr('value')
				};
				$.post(ajaxurl, data, function(response) {
					response = jQuery.parseJSON(response);
					id = response['id'];
					self._fetch_ct_and_show_editor(id, name, true);
					
					self._refresh_ct_dropdown(id);
				});
				
			} else {
				self._fetch_ct_and_show_editor(id, name, false);
			}
		}
		
		self._fetch_ct_and_show_editor = function (id, name, focus_on_name) {

			data = {
				action : 'wpv_ct_loader_inline',
				id : id,
				wpnonce : $('#wpv-ct-inline-edit').attr('value')
			};
			
			$.post(ajaxurl, data, function(response) {
				
				jQuery('input[name="use-ct"]').prop('disabled', false);
				jQuery('.js-dialog-edit-save').prop('disabled', false);
			
				$('.js-wpv-ct-inline-edit').html(response).show().attr('id', "wpv_ct_inline_editor_" + id);
				$('.js-wpv-ct-inline-edit .js-wpv-ct-update-inline').remove();
				
				if( typeof cred_cred != 'undefined'){
					cred_cred.posts();
				}
				
				self._ct_editor = id;
				self._ct_code_mirror = icl_editor.codemirror('wpv-ct-inline-editor-'+id, true);

				// Hide the "Media" button (it doesn't work at the moment)
				jQuery('.js-wpv-media-manager').hide();
				
				// Hide "CRED forms" button (it doesn't work at the moment)
				jQuery('.cred-form-shortcode-button2').hide();
				
				jQuery('.js-ct-edit-name').hide();
				jQuery('.js-ct-name').html(name);
				jQuery('.js-ct-edit-name').val(name);
				jQuery('.js-ct-edit').show();
				
				jQuery('.js-ct-loading').remove();
				
				self._original_ct_name = name;
				self._original_ct_value = self._ct_code_mirror.getValue();
				
				self._ct_code_mirror.refresh();

				DDLayout.types_views_popup_manager.set_position_and_target(				
						jQuery('#ddl-default-edit .js-code-editor-toolbar-button-v-icon'),
						'wpv-ct-inline-editor-'+id);
				
				if (focus_on_name) {
					self._switch_to_edit_ct_name();
				}
						
			});


		}
		
		self._switch_to_ct_select_mode = function () {
			jQuery('.js-ct-selector').show();
			jQuery('.js-ct-editor').hide();
		}
		
		self._create_new_ct = function () {
			jQuery('.js-ct-selector').hide();
			var name = self._get_unique_name(ddl_new_ct_default_name);
			self._open_ct_editor(0, name);
		}
		
		self._get_unique_name = function (name) {
			var count = 0;
			name = name.replace('%s', DDLayout.ddl_admin_page.get_layout().get_name());
			var test_name = name;
			
			do {
				in_use = false;
				
				jQuery('#post-content-view-template option').each(function () {
					if (jQuery(this).html() == test_name) {
						in_use=true;
					}
				});
				
				if (in_use) {
					count++;
					test_name = name + ' - ' + count;
				}
			} while (in_use);
			
			return test_name;
		}
		
		self._switch_to_edit_ct_name = function () {
			jQuery('.js-ct-editing').hide();
			jQuery('.js-ct-edit-name').val(jQuery('.js-ct-name').html());
			jQuery('.js-ct-edit-name').show().focus();
		}
		
		self._end_ct_name_edit = function () {
			jQuery('.js-ct-edit-name').hide();
			jQuery('.js-ct-name').html(jQuery('.js-ct-edit-name').val());
			jQuery('.js-ct-editing').show();
		}
		
		self._switch_to_select_different_ct = function () {
			jQuery('#post-content-view-template').val('None');
			jQuery('.js-ct-edit').hide();
			self._close_codemirror();
			self._switch_to_ct_select_mode();
		}
		
		self.init();
	};


    DDLayout.post_content_cell = new DDLayout.PostContentCell($);

});

