// Views Content Grid

var DDLayout = DDLayout || {};

DDLayout.ViewsGrid = function($)
{
    var self = this;

    self.init = function( initial_layout_json) {
 	 
        $(document).on('views-content-grid-cell.init-dialog-from-content', function(event, content, dialog){
        
        });
        
        $(document).on('views-content-grid-cell.get-content-from-dialog', function(event, content, dialog){
            //Create new new
            if ( $('#ddl-views-grid-new-view').prop('checked') ){
                $grid = jQuery('#js-fluid-views-grid-designer');
                var cell_name = $('#ddl-default-edit-cell-name').val();
                var current_cell = jQuery('#ddl-default-edit').data('cell_view');
                jQuery('#ddl-default-edit').find('.ddl_existing_views_content').show(); 
                
                $thiz = $(this);
                var data = {
                    action : 'ddl_create_new_view',
                    wpnonce : $('#ddl_layout_view_nonce').attr('value'),
                    cell_name : cell_name,
                    cols: $grid.data('cols')
                };
                $thiz.find('select[name="ddl-layout-ddl_layout_view_slug"] options').prop('checked',false);
                $.ajax({
                    url: ajaxurl,
                    type: 'post',
                    data: data,
                    cache: false,
                    success: function(data) {
                        data = jQuery.parseJSON(data);
                        $thiz.find('select[name="ddl-layout-ddl_layout_view_slug"]').append( $("<option/>", {
                            value: data.post_name,
                            text: data.post_title,
                            'data-id': data.id
                        }));
                        content.ddl_layout_view_slug = data.post_name;
                        
                        $thiz.find('[name="view-grid-view-action"]').prop('checked',false);
                        $thiz.find('#ddl-views-grid-exitsting-view').prop('checked',true);
                        DDLayout.ddl_admin_page.render_all();
                        
                        if (self._edit_new_view) {
                            self._open_view_in_new_window(data.id);
                        }
                    }
                });
               
            }
        });
        
        
        $(document).on('views-content-grid-cell.dialog-open', function(event, content, dialog) {
            self._dialog_initialized = false;
            self._edit_new_view = false;
            self._edit_existing_view = false;
            self._initial_view_selected = $('[name="ddl-layout-ddl_layout_view_slug"] option:checked').data('id');
            
            if ( $('.js-views-content-grid_is_views_installed').val() == 0 ){
                $('.ddl-form').hide();
            }else{
                var numberOfColumns = DDLayout.ddl_admin_page._add_cell.getColumnsToAdd();
            
                var $fluidGrid = jQuery('.js-fluid-grid-designer');
                var $fixedGrid = jQuery('.js-fixed-grid-designer');
            
                $fluidGrid.show();
                $fixedGrid.hide();
                jQuery('#js-fluid-views-grid-designer').ddlDrawGrid('destroy');
                jQuery('#js-fluid-views-grid-designer').ddlDrawGrid();
                
                $('.js-ddl-select-existing-view,.js-fluid-grid-designer').hide();		
                
                if ( $('#ddl-views-grid-new-view').prop('checked') ){
                    $('.js-fluid-grid-designer').show();
                    $('.js-dialog-edit-save').prop('disabled', false);
                }
                if ( $('#ddl-views-grid-exitsting-view').prop('checked') ){				
                    $('.js-ddl-select-existing-view').show();						
                    $('.js-ddl-view-select').trigger('change');
                }
                
                if ( $('[name="ddl-layout-ddl_layout_view_slug"]').val() !== ''){
            
                    $('.js-ddl-edit-view-link').attr('href','admin.php?page=views-editor&view_id='+
                    $('[name="ddl-layout-ddl_layout_view_slug"] option:checked').data('id')+
                    '&layout_id='+DDLayout_settings.DDL_JS.layout_id);		
                }
            
            }
            
            self._dialog_initialized = true;
           
        
        });
        
        $(document).on('views-content-grid-cell.dialog-close', function(event, content, dialog) {
            if ( $('.js-views-content-grid_is_views_installed').val() == 0 ){
                $('.ddl-form').show();
            }
            $('.js-dialog-edit-save,.ui-tabs-nav').prop('disabled',false);
            
            jQuery(window).off('beforeunload.views-grid-cell');
            
            
        });

        $(document).on('views-content-grid-cell.dialog-closed', function(event, content, dialog) {
            if (self._edit_existing_view) {
                self._edit_existing_view_in_same_window(self._edit_existing_view);
            }
        });        
        
        $(document).on('click', '.js-ddl-views-grid-create', function(e) {
           $('.js-ddl-select-existing-view,.js-fluid-grid-designer').hide();		
           if ( $('#ddl-views-grid-new-view').prop('checked') ){
               $('.js-fluid-grid-designer').show();
               $('.js-dll-edit-view-link-section').hide();
               $('.js-dialog-edit-save').prop('disabled', false);
               $('#ddl-default-edit-cell-name').focus();
           }
           if ( $('#ddl-views-grid-exitsting-view').prop('checked') ){				
               $('.js-ddl-select-existing-view').show();
               $('.js-dll-edit-view-link-section').show();
               $('.js-ddl-view-select').trigger('change');
           }
        });
        
        $(document).on('change', '.js-ddl-view-select', function (e) {
            if ($(this).val() == '') {
                $('.js-dialog-edit-save').prop('disabled', true);
            } else {
                $('.js-dialog-edit-save').prop('disabled', false);
            }
            if (self._dialog_initialized) {
                
                // The views selected has changed
                // stop the browser from navigating away without a warning.
                
                jQuery(window).on('beforeunload.views-grid-cell', function(){
                    return DDLayout_settings.DDL_JS.strings.page_leave_warning;
                });
                
            }
        });
        
        //Change edit link URL
        $(document).on('change', '[name="ddl-layout-ddl_layout_view_slug"]', function(e) {
           $('.js-dll-edit-view-link-section').hide();
           
           if ( typeof($('[name="ddl-layout-ddl_layout_view_slug"] option:checked').data('id')) !== 'undefined'){
               $('.js-dll-edit-view-link-section').show();
               $('.js-ddl-edit-view-link').attr('href','admin.php?page=views-editor&view_id='+
                   $('[name="ddl-layout-ddl_layout_view_slug"] option:checked').data('id')+
                   '&layout_id='+DDLayout_settings.DDL_JS.layout_id);		
           
           }		
        });
        
        $('button.js-ddl-edit-view-link').on('click', function (e) {
            self._open_view_in_new_window( $('[name="ddl-layout-ddl_layout_view_slug"] option:checked').data('id') );
        });
        
        $(document).on('click', '.js-create-and-edit-view', function (e) {
            self._edit_new_view = true;
            $('.js-dialog-edit-save').trigger('click');
        });

        $('a.js-ddl-edit-view-link').on('click', function (e) {
            self._edit_existing_view = $('[name="ddl-layout-ddl_layout_view_slug"] option:checked').data('id');
            if (self._edit_existing_view != self._initial_view_selected) {
                $('.js-dialog-edit-save').trigger('click');
            } else {
                $('.js-edit-dialog-close').trigger('click');
            }
            return false;
        });
        
    }

    self._open_view_in_new_window = function (view_id) {
        $('<form>' +
            '<input type="hidden" name="page" value="views-editor" />' +
            '<input type="hidden" name="view_id" value="' + view_id + '" />' +
            '<input type="hidden" name="return-to-layout" value="1" />' +
            '</form>')
            .attr('action','admin.php')
            .attr('target', '_blank')
            .attr('method', 'get')
            .appendTo('body')
            .submit();
    };
    
    self._edit_existing_view_in_same_window = function (view_id) {
        if (DDLayout.ddl_admin_page.is_save_required()) {

            var dialog = DDLayout.DialogYesNoCancel(DDLayout_settings.DDL_JS.strings.save_required_open_view,
                                                DDLayout_settings.DDL_JS.strings.save_before_open_view,
                                                {'yes' : DDLayout_settings.DDL_JS.strings.save_layout_yes,
                                                'no' : DDLayout_settings.DDL_JS.strings.save_layout_no},
                                                function(result) {
                                                    if (result == 'yes') {
                                                        DDLayout.ddl_admin_page.save_layout(self._switch_to_view(view_id));
                                                    } else if (result == 'no') {
                                                        self._switch_to_view(view_id)
                                                    }
                                                });
            
        } else {
            self._switch_to_view(view_id);
        }
        
    }
    
    self._switch_to_view = function (view_id) {
        DDLayout.ddl_admin_page.clear_save_required();
        window.location = 'admin.php?page=views-editor&view_id='+ view_id + '&layout_id=' + DDLayout_settings.DDL_JS.layout_id;
    }
    
    self.init();
}

jQuery(document).ready(function($) {
    DDLayout.views_grid = new DDLayout.ViewsGrid($);
});

DDLayout['views-cache'] = {};

function ddl_views_content_grid_preview( view_id, error_text, loading_text ){
	
    if ( view_id == '' ){
		return '<div>'+ error_text +'</div>';
	}else{
		var divclass = 'js-views-content-grid-'+view_id;
		var divplaceholder = '.'+divclass;
		
		//Return if view data cached
		if ( typeof(DDLayout['views-cache'][view_id]) !== 'undefined' && DDLayout['views-cache'][view_id] != null){
			var out = '<div class="'+ divclass +'">'+ DDLayout['views-cache'][view_id] +'</div>';
			return out;
		} 
		
		//If view not cached, get data using Ajax
        var out = '<div class="'+ divclass +'">'+ loading_text +'</div>';
    
        if (typeof(DDLayout['views-cache'][view_id]) == 'undefined') {
    
            DDLayout['views-cache'][view_id] = null;
    
            var data = {
                    action : 'ddl_views_content_grid_preview',
                    view_id: view_id,
                    wpnonce : jQuery('#ddl_layout_view_nonce').attr('value')
            };
            jQuery.ajax({
                    url: ajaxurl,
                    type: 'post',
                    data: data,
                    cache: false,
                    success: function(data) {
                        //cache view id data
                        DDLayout['views-cache'][view_id] = data;
                        jQuery(divplaceholder).html(data);
                        
                        // If we have received all the previews we need to refresh
                        // the layout display to re-calculate the heights.
                        
                        var all_previews_ready = true;
                        for (var key in DDLayout['views-cache']) {
                             if (DDLayout['views-cache'].hasOwnProperty(key)) {
                                  if (DDLayout['views-cache'][key] == null) {
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

		return out; 
	}
}