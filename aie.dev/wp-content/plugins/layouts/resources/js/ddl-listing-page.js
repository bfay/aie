var DDLayout = DDLayout || {};

(function($){
	$(function(){
		DDLayout.listing_page = new DDLayout.LayoutsListingPage($);
	});
}(jQuery))

DDLayout.LayoutsListingPage = function($)
{
		var self = this
			, select = $('.js-select-layout-action-in-listing-page')
			, restore_link = $('.js-layout-listing-restore-link')
			, delete_permanently_link = $('.js-layout-listing-delete-permanently-link')
			, ITEMS_PER_PAGE = DDLayout_settings.items_per_page
			, select_all = $('.js-select-all-layouts')
			, checkboxes = $('.js-selected-items')
			, select_bulk = $('.js-select-bulk-action')
			, post_types_change_button = $('.js-ddl-update-post-types-change')
			, apply_bulk = $('.js-do-bulk-action');

		self.current_warn = null;
		self._current_layout = null;
		self._post_types_change_nonce = null;

		self._referrer = null;

		self._checked = [];

		self.init = function()
		{
			self.handle_layout_post_types_change();
			self.manageSelection();
			self.restore_layout_from_link();
			self.delete_permanently_from_link();
			self.managePaginationSettings();
			self.select_bulk_action();
			self.select_single_item_in_list();
			self.handle_bulk_action()
		};

		self.handle_bulk_action = function()
		{
			$(document).on('click', apply_bulk.selector, function(event){
				event.preventDefault();
				if( +select_bulk.val()  ===  -1 )
				{
					return;
				}
				else if( select_bulk.val() === "trash" || select_bulk.val() === "publish" )
				{
					var data = $(this).data('object'),
						to_delete = [];

					$(checkboxes.selector+':checked').each(function(){
						to_delete.push( +$(this).val() );
					});

					data.layout_id = to_delete;
					data.value = select_bulk.val();

					self.changeLayoutStatus( data, select_bulk.val() )
				}
				else if( select_bulk.val() === "delete" )
				{
					var data = $(this).data('object'),
						to_delete = [];

					$(checkboxes.selector+':checked').each(function(){
						to_delete.push( +$(this).val() );
					});

					data.layout_id = to_delete;

					self.deleteForever(data);
				}
			});
		};


		self.managePaginationSettings = function()
		{
			$(document).on('change', '.js-items-per-page', function() {
				var url_params = DDLayout.LayoutsListingPage.decodeURIParams('paged=1&items_per_page=' + $(this).val());
				DDLayout.LayoutsListingPage.navigateWithURIParams(url_params);
			});

			$(document).on('click', '.js-wpv-display-all-items', function(e){
				e.preventDefault();
				var url_params = DDLayout.LayoutsListingPage.decodeURIParams('paged=1&items_per_page=-1');
				DDLayout.LayoutsListingPage.navigateWithURIParams(url_params);
			});

			$(document).on('click', '.js-wpv-display-default-items', function(e){
				e.preventDefault();
				var url_params = DDLayout.LayoutsListingPage.decodeURIParams('paged=1&items_per_page='+ITEMS_PER_PAGE);
				DDLayout.LayoutsListingPage.navigateWithURIParams(url_params);
			});
		};

		self.select_bulk_action = function()
		{
			$(document).on('change', select_all.selector, function( event ){

				if( $(this).is(':checked') === true  )
				{
					select_all.each(function(i){
						$(this).prop( 'checked', true );
					});

					checkboxes.each(function(i){
						$(this).prop( 'checked', true );
					});
				}
				else if( $(this).is(':checked') === false )
				{
					select_all.each(function(i){
						$(this).prop( 'checked', false );
					});
					checkboxes.each(function(i){
						$(this).prop( 'checked', false );
					});
				}
			});
		};


		self.select_single_item_in_list = function()
		{
			$(document).on('change', checkboxes.selector, function(event){
				var len = checkboxes.length;

				if( $(this).is(':checked') === false )
				{
					if( select_all.prop( 'checked' ) === true ) {
						select_all.prop('checked', false);
					}
				}
				else if( $(this).is(':checked') === true && $(checkboxes.selector+':checked').length === len )
				{
					select_all.prop('checked', true);
				}
			});
		};


		self.restore_layout_from_link = function()
		{
			$(document).on('click', restore_link.selector, function(event){
				event.preventDefault();
				var data_object = $(this).data('object');
				self.changeLayoutStatus( data_object, data_object.value );
			});
		};


		self.delete_permanently_from_link = function()
		{
			$(document).on('click', delete_permanently_link.selector, function(event){
				event.preventDefault();
				var data_object = $(this).data('object');
				self.deleteForever( data_object );
			})
		};

	self.manageSelection = function()
	{
		select.on('change', function(event){
			var data_object = $(this).data('object');

				if( $(this).val() === 'change' )
				{
					self.loadChangeUseDialog( data_object, $(this) );
				}
				else if( $(this).val() === 'trash' || $(this).val() === 'publish' )
				{
					self.changeLayoutStatus( data_object, $(this).val() );
				}
				else if( $(this).val() === 'permanent' )
				{
					self.deleteForever( data_object );
				}
                else if( $(this).val() === 'duplicate' )
				{
					self.duplicate( data_object );
				} 
		});
	};

	self.deleteForever = function( data_obj )
	{
		var params = {
			action: 'delete_layout_record',
			'layout-delete-layout-nonce':data_obj.delete_nonce,
			layout_id:data_obj.layout_id
		};

		WPV_Toolset.Utils.do_ajax_post( params, {success:function(response){
			console.log( response )
			window.location.href = window.location.href;
		}});
	};

	self.duplicate = function( data_obj )
	{
		var params = {
			action: 'duplicate_layout',
			'layout-duplicate-layout-nonce':data_obj.duplicate_nonce,
			layout_id:data_obj.layout_id
		};

		WPV_Toolset.Utils.do_ajax_post( params, {success:function(response){

			window.location.href = window.location.href;
		}});
	};
    
	self.changeLayoutStatus = function( data_obj, value )
	{
		var params = {
			action: 'set_layout_status',
			'layout-select-trash-nonce':data_obj.trash_nonce,
			status: value,
			layout_id:data_obj.layout_id
		};

		WPV_Toolset.Utils.do_ajax_post( params, {success:function(response){
			window.location.href = window.location.href;
		}});
	};

	self.handle_layout_post_types_change = function()
	{
		post_types_change_button.on('click', function(event){
			var params = {
				action:'change_layout_usage_for_post_types',
				'layout-set-change-post-types-nonce':self._post_types_change_nonce,
				layout_id:self._current_layout,
				post_types:self.getChecked()
			};

			WPV_Toolset.Utils.do_ajax_post(params, {success:function(response){
				window.location.href = window.location.href;
			}});
		});
	};

	self.loadChangeUseDialog = function( data_obj, select )
	{
		var nonce = data_obj.nonce,
			layout_id = data_obj.layout_id,
			params = {
				action:'change_layout_usage_box',
				'layout-select-set-change-nonce':nonce,
				layout_id:layout_id
			};



		WPV_Toolset.Utils.do_ajax_post( params, {success:function(response){
			self._current_layout = response.message.layout_id;
			self._post_types_change_nonce = response.message.nonce;
			self._referrer = response.message.referrer;

		//	$('#ddl-change-layout-use-for-post-types-box-'+self._current_layouta).addClass('auto-width');
			$('#ddl-change-layout-use-for-post-types-box-'+self._current_layout +' .ddl-dialog-content').html( response.message.html_data );

			jQuery.colorbox({
				href: '#ddl-change-layout-use-for-post-types-box-'+self._current_layout,
				inline: true,
				open: true,
				closeButton:false,
				fixed: true,
				top: false,
				onComplete: function() {
					self._checkboxes = jQuery('.js-ddl-post-type-checkbox-change', $('#ddl-change-layout-use-for-post-types-box-'+self._current_layout) );
					self.setInitialChecked();
					self.manage_check_box_change();
				},
				onCleanup: function() {
					select.val('');
				}
			});
		}});
	};

	self.manage_check_box_change = function()
	{
		self._checkboxes.on('change', function(event){

			if( jQuery(this).is(':checked') === true )
			{
				self._checked.push( jQuery(this).val() );
			}
			else if( jQuery(this).is(':checked') === false )
			{
				self._checked = _.without( self._checked, jQuery(this).val() );
			}
		});
	};

	self.getChecked = function()
	{
		return self._checked;
	};

	self.setInitialChecked = function()
	{
		self._checkboxes.each(function(i){
			if( $(this).is(':checked') )
			{
				self._checked.push( $(this).val() );
			}
		});
	};

		self.init();
};

// Redirection functions for search, delete and duplicate
DDLayout.LayoutsListingPage.decodeURIParams = function(query) {
	if (query == null)
		query = window.location.search;
	if (query[0] == '?')
		query = query.substring(1);

	var params = query.split('&');
	var result = {};
	for (var i = 0; i < params.length; i++) {
		var param = params[i];
		var pos = param.indexOf('=');
		if (pos >= 0) {
			var key = decodeURIComponent(param.substring(0, pos));
			var val = decodeURIComponent(param.substring(pos + 1));
			result[key] = val;
		} else {
			var key = decodeURIComponent(param);
			result[key] = true;
		}
	}
	result['untrashed'] = null;
	result['trashed'] = null;
	result['deleted'] = null;
	return result;
};

DDLayout.LayoutsListingPage.encodeURIParams = function(params, addQuestionMark) {
	var pairs = [];
	for (var key in params) if (params.hasOwnProperty(key)) {
		var value = params[key];
		if (value != null) /* matches null and undefined */ {
			pairs.push(encodeURIComponent(key) + '=' + encodeURIComponent(value))
		}
	}
	if (pairs.length == 0)
		return '';
	return (addQuestionMark ? '?' : '') + pairs.join('&');
};

DDLayout.LayoutsListingPage.navigateWithURIParams = function(newParams) {
	window.location.search = DDLayout.LayoutsListingPage.encodeURIParams(jQuery.extend(DDLayout.LayoutsListingPage.decodeURIParams(), newParams), true);
};