DDLayout.PostTypes_Options = function(adm)
{
	var self = this, admin = adm;

		self.options = {};



	self.init = function( )
	{
		self._checkboxes = jQuery('.js-ddl-post-type-checkbox');
		self.setInitialStateLayoutPostTypes( );
		self.setChangeEvents();

	};

	self.get_options = function()
	{
		return self.options;
	};

	self.set_options = function( options )
	{
		self.options = _.extend({}, self.options, options )
	};

	self.setPostTypesLayout = function( option, add )
	{
		var layout_view = admin.instance_layout_view,
			layout_model = layout_view.model;

		layout_model.setPostTypesOptions( option, add );
	};

	self.initPostTypesLayout = function()
	{
		self.setPostTypesLayout( DDLayout_options.ddl_post_types_options.post_types, true );
	};

	self.setChangeEvents = function()
	{
		self._checkboxes = jQuery('.js-ddl-post-type-checkbox');
		self._checkboxes.on('change', function(event){
			self.setPostTypesLayout( jQuery(this).val(), jQuery(this).is(':checked') );
			self.manageApplyToAll( jQuery(this), jQuery(this).is(':checked') );
		});
	};

	self.setInitialStateLayoutPostTypes = function()
	{
		self._checkboxes.each(function(i){
			if( jQuery(this).is(':checked') )
			{
				self.setPostTypesLayout( jQuery(this).val(), jQuery(this).is(':checked') );
			}
		});
	};

	self.manageApplyToAll = function(checkbox, checked)
	{
		var span = checkbox.parent().next();
		if( checked === false ) span.hide();
		if( checked === true ) span.show();
	};

	self.init();
};