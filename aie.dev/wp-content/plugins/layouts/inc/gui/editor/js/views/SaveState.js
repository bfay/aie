// SaveState.js

DDLayout.SaveState = function($)
{
    var self = this;
    self.requires_save = false;
    self.last_save_json = '';

    self.init = function( initial_layout_json)
    {
        self.requires_save = false;
        self.last_save_json = initial_layout_json;
    };

    self.set_save_required = function () {
        self.requires_save = true;
        /*jQuery('.js-ddl-message-container').wpvToolsetMessage({
            text: DDLayout_settings.DDL_JS.strings.save_required,
            stay: true,
            close: false,
            inline: true,
            type: 'info'
        });*/

        jQuery(window).bind('beforeunload', function(){
            return DDLayout_settings.DDL_JS.strings.page_leave_warning;
        });
    }

    self.clear_save_required = function () {
        self.requires_save = false;
        jQuery('.js-ddl-message-container .toolset-alert').fadeOut(500, function() {jQuery(this).remove()});

        self.last_save_json = DDLayout.ddl_admin_page.get_layout_as_JSON();

        jQuery(window).unbind('beforeunload');
    }

    self.is_save_required = function () {
        return self.requires_save;
    }


    self.init();
};