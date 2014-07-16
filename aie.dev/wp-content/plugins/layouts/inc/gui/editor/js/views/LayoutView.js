DDLayout.views.LayoutView = Backbone.View.extend({
	//this is our document view it points to the root element of our page - .wrap
	el:"#js-dd-layout-editor",
	events: {
	//	'ajaxError': 'handleAjaxError'
	}
	,initialize: function(options)
	{
		var self = this;

		self.options = options;

		self.options.invisibility = false;

		self.scroll_position = 0;

		self.errors_div = jQuery(".js-ddl-message-container");

		self.model.set( "slug", jQuery(".js-edit-layout-slug", self.$el).val() );

		_.bindAll( self, 'beforeRender', 'render', 'afterRender');

		self.render = _.wrap(self.render, function(render, args) {
			self.beforeRender();
			render(args);
			self.afterRender();
			return self;
		});

		self.$el.data('view', self);

		self.rows_view = null;

		self.listenTo(self.eventDispatcher, "save_layout_to_server", self.saveLayout, self );

		self.listenToOnce(self.eventDispatcher, 're_render_all', function(event, model){
			if ( jQuery.jStorage.get( 'editorBasics' ) !== 'collapsed' && jQuery.jStorage.get( 'editorBasics' ) !== 'disabled' ) {
				_.delay( DDLayout.ddl_admin_page._user_help.collapseToolbar, 5000, true );
			}
		});

		self.listenTo( self.eventDispatcher, 're_render_all', self.render );

		self.listenTo( self.eventDispatcher, 'ddl-delete-child-layout-cell', self.setChildrenToDelete );

		self.listenTo(self.model, 'request', self.ajaxSentRequest, self);

		self.listenTo(self.model, 'sync', self.ajaxSynced, self);

		self.hide_show_containers_button = self.hideContainerEdit();

		self.handleSave();

		self._modelSlugChange();

		self.render();

		return self;
	},
	beforeRender:function()
	{
		var self = this;

		jQuery(window).scroll(function(){
			self.scroll_position =  window.pageYOffset;
		});

		self.show_div_after_self = false;
	}
	,render:function( option )
	{
		var self = this,
			options = null;

		DDLayout.preview_manager.reset();

		if( self.model.has("Rows") && self.model.numRows() )
		{

			self._hide_show_button_container_edit();

			options = _.extend({el:'div.js-layout-container', model:self.model.get("Rows"), compound:"", invisibility:self.options.invisibility}, option )

		//	self.$el.find('span.js-layout-title').text( self.model.get('name') );

			//make sure we garbage collected previuos instances
			if( self.rows_view !== null )
			{
				self.rows_view = null;
			}

			self.rows_view = new DDLayout.views.RowsView( options );

			if( DDLayout.ddl_admin_page === undefined )
			{
				self.show_div_after_self = true;
				jQuery( "> div", self.rows_view.$el ).hide();

			}

			if( self.options.invisibility === true )
			{
				jQuery('.js-layout-container').addClass('containers-toolbars-disabled');
			}
			else
			{
				jQuery('.js-layout-container').removeClass('containers-toolbars-disabled');
			}

		}

		return self;
	},
	afterRender:function()
	{
		var self = this;

		if( self.scroll_position > 0 ) window.scrollTo( 0, self.scroll_position );

		if( self.show_div_after_self )
		{
			jQuery( "> div", self.rows_view.$el ).fadeIn(300);
		}
		// set the main layout size.
		if ( self.model.get('width') != DDLayout.MAXIMUM_SPAN ) {
			var width = self.model.get('width') * (DDLayout.CELL_MIN_WIDTH + DDLayout.MARGIN_BETWEEN_CELLS) - DDLayout.MARGIN_BETWEEN_CELLS;
			width += 6; // allow room for shadows.
			jQuery('.js-layout-container > .row-container').css({ width : width });
		}

	},
	setChildrenToDelete:function( event_type, children )
	{
		this.set_delete_children = event_type;
		this.model.setChildrenToDelete( children, event_type );
	},
	handleSave:function()
	{
		var self = this, button_save = jQuery('input[name="save_layout"]');
		button_save.on("click", function(event){
			self.eventDispatcher.trigger('save_layout_to_server', jQuery(event.target) );
			return false;
		});

	},
	saveLayout:function( caller, callback )
	{
		var self = this,
			obj = caller ? caller.parent() : jQuery(document.body),
			save_params = {};
		self.save_layout_callback = callback;
		WPV_Toolset.Utils.loader.loadShow( obj );

		if( DDLayout.ddl_admin_page.cssEditor.css_did_change )
		{
			save_params.layout_css = self.model.getCssString();
		}
		if( self.model.postTypesOptionsDidChange() )
		{
			save_params.ddl_post_types_options = JSON.stringify( self.model.getPostTypesOptions() );
		}

		save_params = _.extend(save_params, {
			action:'save_layout_data',
			layout_id:self.model.get('id'),
			save_layout_nonce:DDLayout_settings.DDL_JS.save_layout_nonce,
			// layout_model: encodeURIComponent( JSON.stringify( self.model.toJSON() ) )
			layout_model:JSON.stringify( self.model.toJSON() )
		});

		self.model.save({},{
			contentType:'application/x-www-form-urlencoded; charset=UTF-8',
			type:'post',
			dataType:'json',
			data:jQuery.param(save_params)
		});
	},
	ajaxSentRequest:function( model, response, xhr )
	{
		//console.log("Request", arguments);
	},
	ajaxSynced:function( model, response, xhr )
	{

		var self = this;

		WPV_Toolset.Utils.loader.loadHide();

		if( response.Data.error )
		{
			self.errors_div.wpvToolsetMessage({
				text: response.Data.error,
				type: 'error',
				stay: true,
				close: false,
				onOpen: function() {
					jQuery('html').addClass('toolset-alert-active');
				},
				onClose: function() {
					jQuery('html').removeClass('toolset-alert-active');
				}
			});
		}
		else if( response.Data.message )
		{
			self.errors_div.wpvToolsetMessage({
				text:response.Data.message,
				type: 'message',
				stay: false,
				close: false,
				onOpen: function() {
					jQuery('html').addClass('toolset-alert-active');
				},
				onClose: function() {
					jQuery('html').removeClass('toolset-alert-active');
				}
			});
		}
		else if( response.Data.css_saved )
		{
			if( 'db_ok' in response.Data.css_saved && response.Data.css_saved.db_ok )
			{
				self.errors_div.wpvToolsetMessage({
					text:DDLayout_settings.DDL_JS.strings.save_and_also_save_css,
					type:'success',
					stay:false,
					close:false,
					onOpen: function() {
						jQuery('html').addClass('toolset-alert-active');
					},
					onClose: function () {
						DDLayout.ddl_admin_page.clear_save_required();
						if (self.save_layout_callback) {
							self.save_layout_callback();
						}
						jQuery('html').removeClass('toolset-alert-active');
					}
				});
			}
			else
			{
				self.errors_div.wpvToolsetMessage({
					text:DDLayout_settings.DDL_JS.strings.save_and_save_css_problem + response.Data.css_saved.file_name,
					type:'warning',
					stay:false,
					close:false,
					onOpen: function() {
						jQuery('html').addClass('toolset-alert-active');
					},
					onClose: function () {
						DDLayout.ddl_admin_page.clear_save_required();
						if (self.save_layout_callback) {
							self.save_layout_callback();
						}
						jQuery('html').removeClass('toolset-alert-active');
					}
				});
			}

			DDLayout.ddl_admin_page.cssEditor.css_did_change = false;
		}
		else
		{
			self.errors_div.wpvToolsetMessage({
				text:DDLayout_settings.DDL_JS.strings.save_complete,
				type: 'success',
				stay: false,
				close: false,
				onOpen: function() {
					jQuery('html').addClass('toolset-alert-active');
				},
				onClose: function () {
					DDLayout.ddl_admin_page.clear_save_required();
					if (self.save_layout_callback) {
						self.save_layout_callback();
					}
					jQuery('html').removeClass('toolset-alert-active');
				}
			});
		}

		if( response && response.Data && response.Data.post_types_options_html )
		{
			jQuery('.js-selected-post-types-in-layout-div').empty().html( response.Data.post_types_options_html );
			DDLayout.ddl_admin_page.post_types_options_manager.setChangeEvents();
		}

		if( response && response.Data && response.Data.layout_children_deleted )
		{
			// reset traking properties in Layout model
			this.setChildrenToDelete( undefined, null );
		}
	},
	getLayoutModelToJs:function()
	{
		return this.model.toJSON();
	},
	getLayoutType:function()
	{
		return this.model.getType();
	},
	hideContainerEdit:function()
	{
		var self = this, button = jQuery("input#hide-containers");

			if( self.options.invisibility )
			{
				button.val(DDLayout_settings.DDL_JS.strings.show_grid_edit);
			}
			else
			{
				button.val(DDLayout_settings.DDL_JS.strings.hide_grid_edit);
			}

			button.on("click", function(event){
				event.preventDefault();

				if( self.options.invisibility )
				{
					jQuery(this).val(DDLayout_settings.DDL_JS.strings.hide_grid_edit);
					self.options.invisibility = false;
				}
				else
				{
					jQuery(this).val(DDLayout_settings.DDL_JS.strings.show_grid_edit);
					self.options.invisibility = true;
				}

				self.render( );
			});

		   return button;
	},
	_hide_show_button_container_edit:function()
	{
		if( this._hasContainers() ){
			this.hide_show_containers_button.show();
		}
		else{
			this.hide_show_containers_button.hide();
		}
	},
	_hasContainers:function()
	{
		return this.model.getLayoutContainers( ).length > 0;
	},
	_modelSlugChange:function()
	{

		var self = this, el = jQuery(".js-edit-layout-slug", self.$el), prev = el.val();

		el.on("change", function(event){
			var val = jQuery(event.target).val();

			if( val.replace(/[^A-Z]/g, "").length || val == '' )
			{
				self.errors_div.wpvToolsetMessage({
					text: DDLayout_settings.DDL_JS.strings.invalid_slug,
					type: 'error',
					stay: false,
					close: false,
					onOpen: function() {
						jQuery('html').addClass('toolset-alert-active');
					},
					onClose: function() {
						jQuery('html').removeClass('toolset-alert-active');
					}
				});
				jQuery( event.target ).val(prev);
			}
			else
			{
				self.model.set( "slug", jQuery(event.target).val() );
			}
		});

	},
	handleAjaxError:function(  )
	{
		var self = this;

		if( event && event.target.status === 200 && event.target.response && event.target.response != 500 && event.target.response != 0  && event.target.response.indexOf('wp-auth-check') === -1 && event.target.response.indexOf('server_time') === -1 )
		{
			//console.log( 'ajaxError 1', event.target.status, event.target.response, parseInt(event.target.response) )
			self.errors_div.wpvToolsetMessage({
				text: event.target.response,
				type: 'error',
				stay: true,
				close: false,
				onOpen: function() {
					jQuery('html').addClass('toolset-alert-active');
				},
				onClose: function() {
					jQuery('html').removeClass('toolset-alert-active');
				}
			});
		}
		else if( event && event.target.status !== 200 && event.target.response.indexOf('wp-auth-check') === -1 && event.target.response.indexOf('server_time') === -1 && event.target.response != 500 && event.target.response != 0  )
		{
			//console.log( 'ajaxError 2', event.target.status, event.target.response, parseInt(event.target.response)  )
				self.errors_div.wpvToolsetMessage({
					text: event.target.status,
					type: 'error',
					stay: true,
					close: false,
					onOpen: function() {
						jQuery('html').addClass('toolset-alert-active');
					},
					onClose: function() {
						jQuery('html').removeClass('toolset-alert-active');
					}
				});

				console.log('ajaxError', event.target.status, event.target.response);

		}
	}
});