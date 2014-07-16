/*
 * Repetitive JS.
 */
var wptRep = (function($) {
    var count = {};
    function init() {
        // Reorder elements if repetitive
        $('.js-wpt-repetitive').each(function() {
            var $this = $(this), title = $('label', $this).first().clone();
            var description = $('.description', $this).first().clone();
            $('.js-wpt-field-item', $this).each(function() {
                $('label', $this).first().remove();
                $('.description', $this).first().remove();
            });
            $(this).prepend(description).prepend(title);
            $parent = $this.closest('.cred-field');
            if ( $('body').hasClass('wp-admin') ) {
                $parent = $this;
            }
            _toggleCtl($parent);
        });
        $('.cred-field').each(function(){
            $('.js-wpt-repdelete').first().hide();
        });
        // Add field
        $('.js-wpt-repadd').on('click', function() {
            var $this = $(this);
            var $parent = $this.closest('.cred-field');
            var tpl = '';
            if ( $('body').hasClass('wp-admin') ) {
                $parent = $this.parents('.js-wpt-repetitive');
                tpl = $('<div>' + $('#tpl-wpt-field-' + $parent.data('wpt-id')).html() + '</div>');
                $('[id]', tpl).each(function() {
                    var $this = $(this), uniqueId = _.uniqueId('wpt-form-el');
                    tpl.find('label[for="' + $this.attr('id') + '"]').attr('for', uniqueId);
                    $this.attr('id', uniqueId);
                });
                $('label', tpl).first().remove();
                $('.description', tpl).first().remove();
                var _count = tpl.html().match(/\[%%(\d+)%%\]/);
                if (_count != null) {
                    _count = _countIt(_count[1], $parent.data('wpt-id'));
                } else {
                    _count = '';
                }
                $('.js-wpt-field-items', $parent).append(tpl.html().replace(/\[%%(\d+)%%\]/g, '[' + _count + ']'));
            } else {
                template_element = $('.wpt-repctl:first', $parent);
                index = 0;
                $('.js-wpt-repetitive', $parent).each(function(){
                    i = $(this).attr('name').match(/\[(\d+)\]$/);
                    if ( i ) {
                        i = parseInt(i[1]);
                        if ( i > index ) {
                            index = i;
                        }
                    }
                });
                index++;
                /**
                 * template
                 */
                tpl = $('<div class="wpt-repctl">'+template_element.html()+'</div>');
                $('.js-wpt-repdelete', tpl ).show().removeAttr('disabled');
                el = $('.js-wpt-repetitive', tpl );
                wpt_name = el.data('wpt-name');
                el.attr('name', el.attr('name').replace( /\[\d+\]/, '['+index+']') );
                el.attr('id', el.attr('id' ) + '-' + index );
                /**
                 * file
                 */
                if ( 'file' == $('.js-wpt-repetitive', $parent).data('wpt-type') ) {
                    el = $('input[type=hidden]', tpl );
                    el.attr('id', wpt_name+(index)+'_hidden');
                }
                /**
                 * skype
                 */
                if ( el.hasClass('js-wpt-skypename') ) {
                    el = $('input[type=hidden]', tpl );
                    el.attr('name', el.attr('name').replace( /\[\d+\]/, '['+index+']') );
                    el.attr('id', el.attr('id' ) + '-' + index );
                }
                /**
                 * datepicker
                 */
                $('input.hasDatepicker', tpl).each(function(){
                    $('img', tpl).remove();
                    $(this).removeClass('hasDatepicker');
                    wptDate.add($(this), tpl);
                });
                /**
                 * add
                 */
                $(this).before(tpl);
            }
            wptCallbacks.addRepetitive.fire($parent);
            _toggleCtl($parent);

            return false;
        });
        // Delete field
        $('.js-wpt-field,.cred-field').on('click', '.js-wpt-repdelete', function() {
            if ( $('body').hasClass('wp-admin')) {
                var $this = $(this), $parent = $this.parents('.js-wpt-field');
                var value = $this.parent().parent().find('input').val();
                // Allow deleting if more than one field item
                if ($('.js-wpt-field-item', $parent).length > 1) {
                    var formID = $this.parents('form').attr('id');
                    $this.parents('.js-wpt-field-item').remove();
                    wptCallbacks.removeRepetitive.fire(formID);
                }
                /**
                 * if image, try delete images
                 */
                if ( 'image' == $parent.data('wpt-type') ) {
                    $parent.parent().append(
                        '<input type="hidden" name="wpcf[delete-image][]" value="'
                        + value
                        + '"/>'
                        );
                }
            } else {
                $parent = $(this).closest('.cred-field');
                if ($('.js-wpt-repetitive', $parent).length > 1) {
                    $(this).closest('.wpt-repctl').remove();
                    wptCallbacks.removeRepetitive.fire(formID);
                }
            }
            _toggleCtl($parent);
            return false;
        });
    }
    function _toggleCtl($parent) {
        var $sortable = $('.js-wpt-field-items', $parent);
        count = $('.js-wpt-repetitive', $parent ).length;
        if ( $('body').hasClass('wp-admin') ) {
            count = $('.js-wpt-field-item', $parent).length;
        }
        if (count > 1) {
            $('.js-wpt-repdelete', $parent).removeAttr('disabled').show();
            $('.js-wpt-repdrag', $parent).css({opacity: 1, cursor: 'move'});
            if (!$sortable.hasClass('ui-sortable')) {
                $sortable.sortable({
                    revert: true,
                    handle: '.js-wpt-repdrag',
                    axis: 'y',
                    cursor: 'move'
                });
            }
        } else {
            $('.js-wpt-repdelete', $parent).attr('disabled', 'disabled').hide();
            $('.js-wpt-repdrag', $parent).css({opacity: 0.5, cursor: 'default'});
            if ($sortable.hasClass('ui-sortable')) {
                $sortable.sortable('destroy');
            }
        }
    }
    function _countIt(_count, id) {
        if (typeof count[id] == 'undefined') {
            count[id] = _count;
            return _count;
        }
        return ++count[id];
    }
    return {
        init: init
    };
})(jQuery);

jQuery(document).ready(wptRep.init);
