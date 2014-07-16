var DDLayout = DDLayout || {};

DDLayout.listing = {};
DDLayout.listing.views = {};
DDLayout.listing.models = {};
DDLayout.listing.views.abstract = {};

DDLayout_settings.DDL_JS.ns = head;
DDLayout_settings.DDL_JS.listing_open = {1:true, 2:true, 3:true};

DDLayout_settings.DDL_JS.ns.js(
	DDLayout_settings.DDL_JS.lib_path + "jstorage.min.js"
	, DDLayout_settings.DDL_JS.lib_path + "prototypes.js"
	, DDLayout_settings.DDL_JS.listing_lib_path + "models/ListingItem.js"
	, DDLayout_settings.DDL_JS.listing_lib_path + "models/ListingItems.js"
	, DDLayout_settings.DDL_JS.listing_lib_path + "models/ListingGroup.js"
	, DDLayout_settings.DDL_JS.listing_lib_path + "models/ListingGroups.js"
	, DDLayout_settings.DDL_JS.listing_lib_path + "models/ListingTable.js"
	, DDLayout_settings.DDL_JS.listing_lib_path + "views/abstract/CollectionView.js"
	, DDLayout_settings.DDL_JS.listing_lib_path + "views/ListingGroupView.js"
	, DDLayout_settings.DDL_JS.listing_lib_path + "views/ListingGroupsView.js"
	, DDLayout_settings.DDL_JS.listing_lib_path + "views/ListingItemView.js"
	, DDLayout_settings.DDL_JS.listing_lib_path + "views/ListingTableView.js"
);

(function($){
	DDLayout_settings.DDL_JS.ns.ready(function(){
		DDLayout.listing_manager = new DDLayout.ListingMain($);
	});
}(jQuery));


DDLayout.ListingMain = function($)
{
	var self = this
		, post_types_change_button = $('.js-ddl-update-post-types-change')

	self._current_layout = null;
	self._post_types_change_nonce = null;


	self.init = function()
	{
			// create a namespace for our js templates to prevent conflict with reserved names in the global namespace
			_.templateSettings.variable = "ddl";

			var json = jQuery.parseJSON( jQuery('.js-hidden-json-textarea').text() ),
				listing_table = DDLayout.listing.models.ListingTable.get_instance( json );
			    self.listing_table_view = new DDLayout.listing.views.ListingTableView({model:listing_table});

				self.handle_layout_post_types_change();
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

			$('#ddl-change-layout-use-for-post-types-box-'+self._current_layout+'-'+data_obj.group +' .ddl-dialog-content').html( response.message.html_data );

			jQuery.colorbox({
				href: '#ddl-change-layout-use-for-post-types-box-'+self._current_layout+'-'+data_obj.group,
				inline: true,
				open: true,
				closeButton:false,
				fixed: true,
				top: false,
				onComplete: function() {
					self._checked = [];
					self._checkboxes = jQuery('.js-ddl-post-type-checkbox-change', $('#ddl-change-layout-use-for-post-types-box-'+self._current_layout+'-'+data_obj.group) );
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
		return  self._checked;
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

	self.handle_layout_post_types_change = function()
	{
		jQuery(document).on('click', post_types_change_button.selector, function(event){
			var params = {
				action:'change_layout_usage_for_post_types_js',
				'layout-set-change-post-types-nonce':self._post_types_change_nonce,
				layout_id:self._current_layout,
				post_types:self.getChecked()
			};

			jQuery(this).prop( 'disabled', true);

			var spinnerContainer = jQuery('<div class="spinner ajax-loader">');
			jQuery(this).parent().insertAtIndex( 0, spinnerContainer.css({float:'none', display:'inline-block'}) );

			self.listing_table_view.model.trigger('make_ajax_call',  params, function( model, response, object, args ){
					self.listing_table_view.current = +params.layout_id;
					spinnerContainer.hide();
			});
		});

	};
	self.init();
};



