/*
 * @see WPToolset_Forms_Conditional (classes/conditional.php)
 *
 */
var wptCondTriggers = {};
var wptCondFields = {};
var wptCondCustomTriggers = {};
var wptCondCustomFields = {};
var wptCondDebug = false;

var wptCond = (function($) {

    function init()
    {
        _.each(wptCondTriggers, function(triggers, formID) {
            _.each(triggers, function(fields, trigger) {
                var $trigger = _getTrigger(trigger, formID);
                _bindChange(formID, $trigger, function(e) {
                    _check(formID, fields);
                });
                _check(formID, fields);
            });
        });
        _.each(wptCondCustomTriggers, function(triggers, formID) {
            _.each(triggers, function(fields, trigger) {
                var $trigger = _getTrigger(trigger, formID);
                _bindChange(formID, $trigger, function(e) {
                    _custom(formID, fields);
                });
            });
        });
        // Fire validation after init conditional
        wptCallbacks.validationInit.fire();
    }

    function _getTrigger(trigger, formID)
    {
        var $container = $('[data-wpt-name="'+ trigger + '"]', formID).closest('.cred-field');
        /**
         * handle date field
         */
        if ( $container.length < 1 ) {
            $container = $('[data-wpt-name="'+ trigger + '[datepicker]"]', formID).closest('.cred-field');
        }
        /**
         * wp-admin area
         */
        if ( $('body').hasClass('wp-admin') ) {
            $container = $('[data-wpt-id="' + trigger + '"]', formID).closest('.form-item');
        }
        if ( $container.length < 1 ) {
            $container = $('[name="'+trigger+'"]', formID ).closest('.cred-field');;
        }
        var $trigger = $('.js-wpt-cond-trigger', $container);
        if ($trigger.length < 1) {
            $trigger = $(':input', $container).first();
        }
        return $trigger;
    }

    function _getTriggerValue($trigger, formID)
    {
        if ( wptCondDebug ) {
            console.info('_getTriggerValue');
            console.log( '$trigger', 1, $trigger );
            console.log( 'formID', 1, formID );
        }
        // Do not add specific filtering for fields here
        // Use add_filter() to apply filters from /js/$type.js
        var val = null;
        switch( $trigger.data('wpt-type') ) {
            case 'radio':
            case 'radios':
                radio = $('[name="' + $trigger.attr('name') + '"]:checked', formID);
                if ( 'undefined' == typeof( radio.data('types-value' ) ) ) {
                    val = radio.val();
                } else {
                    val = radio.data('types-value');
                }
                break;
            case 'select':
                option = $('[name="' + $trigger.attr('name') + '"] option:selected', formID);
                if ( 'undefined' == typeof( option.data('types-value' ) ) ) {
                    val = option.val();
                } else {
                    val = option.data('types-value');
                }
                break;
            case 'checkbox':
                if ( $trigger.is(':checked') ) {
                    val = $trigger.val();
                }
                break;
            default:
                val = $trigger.val();
        }
        return val;
    }

    function _getAffected(affected, formID)
    {
        if ( wptCondDebug ) {
            console.info('_getAffected');
        }
        var $el = $('[data-wpt-id="' + affected + '"]', formID);
        if ( $('body').hasClass('wp-admin') ) {
            $el = $el.closest('.form-item');
            if ($el.length < 1) {
                $el = $('#' + affected, formID).closest('.form-item');
            }
        } else if ( $el.length < 1 ) {
            $el = $('[data-wpt-id="' + affected + '_file"]', formID).closest('.cred-field');
            /**
             * handle by wpt field name
             */
            if ( $el.length < 1 ) {
                re = new RegExp(formID+'_');
                name = '#'+affected;
                name = name.replace( re, '' );
                $el = $('[data-wpt-name="'+ name + '"]', formID).closest('.cred-field');
            }
            /**
             * handle date field
             */
            if ( $el.length < 1 ) {
                re = new RegExp(formID+'_');
                name = '#'+affected;
                name = name.replace( re, '' );
                $el = $('[data-wpt-name="'+ name + '[datepicker]"]', formID).closest('.cred-field');
            }
            /**
             * handle skype field
             */
            if ( $el.length < 1 ) {
                re = new RegExp(formID+'_');
                name = '#'+affected;
                name = name.replace( re, '' );
                $el = $('[data-wpt-name="'+ name + '[skypename]"]', formID).closest('.cred-field');
            }
            /**
             * catch by id
             */
            if ($el.length < 1) {
                $el = $('#' + affected, formID).closest('.cred-field');
            }
        }
        if ($el.length < 1) {
            $el = $('#' + affected, formID);
        }
        if ( wptCondDebug ) {
            console.log(affected);
            console.log($el);
        }
        return $el;
    }

    function _checkOneField(formID, field, next)
    {
        var __ignore = false;
        var c = wptCondFields[formID][field];
        var passedOne = false, passedAll = true, passed = false;
        var $trigger;
        _.each(c.conditions, function(data) {
            if (__ignore) {
                return;
            }
            $trigger = _getTrigger(data.id, formID);
            var val = _getTriggerValue($trigger, formID);
            if ( wptCondDebug ) {
                console.log( 'formID', formID );
                console.log( '$trigger', $trigger );
                console.log('val', 1, val);
            }

            val = apply_filters('conditional_value_' + $trigger.data('wpt-type'), val, $trigger);
            if ( wptCondDebug ) {
                console.log('val', 2, val);
            }
            do_action('conditional_check_' + data.type, formID, c, field);
            var operator = data.operator, _val = data.args[0];
            /**
             * handle types
             */
            switch(data.type) {
                case 'date':
                    if ( _val ) {
                        _val = Date.parse(_val);
                    }
                    val = Date.parse(val);
                    break;
            }
            if ('__ignore' == val ) {
                __ignore = true;
                return;
            }
            /**
             * debug
             */
            if ( wptCondDebug ) {
                console.log('val', 3, val);
                console.log('_val', _val);
            }
            /**
             * for __ignore_negative set some dummy operator
             */
            if ( 0 && '__ignore_negative' == val ) {
                operator = '__ignore';
            }
            switch (operator) {
                case '===':
                case '==':
                case '=':
                    passed = val == _val;
                    break;
                case '!==':
                case '!=':
                    passed = val != _val;
                    break;
                case '>':
                    passed = parseInt(val) > parseInt(_val);
                    break;
                case '<':
                    passed = parseInt(val) < parseInt(_val);
                    break;
                case '>=':
                    passed = parseInt(val) >= parseInt(_val);
                    break;
                case '<=':
                    passed = parseInt(val) <= parseInt(_val);
                    break;
                case 'between':
                    passed = parseInt(val) > parseInt(_val) && parseInt(val) < parseInt(data.args[1]);
                    break;
                default:
                    passed = false;
                    break;
            }
            if (!passed) {
                passedAll = false;
            } else {
                passedOne = true;
            }
        });

        if (c.relation === 'AND' && passedAll) {
            passed = true;
        }
        if (c.relation === 'OR' && passedOne) {
            passed = true;
        }
        /**
         * debug
         */
        if ( wptCondDebug ) {
            console.log('passedAll', passedAll, 'passedOne', passedOne, 'passed', passed, '__ignore', __ignore);
            console.log('field', field);
        }
        if (!__ignore) {
            _showHide(passed, _getAffected(field, formID));
        }
        if ( $trigger.length && next && $trigger.hasClass('js-wpt-date' ) ) {
            setTimeout(function() {
                _checkOneField( formID, field, false );
            }, 200);
        }
    }

    function _check(formID, fields)
    {
        if ( wptCondDebug ) {
            console.info('_check');
        }
        _.each(fields, function(field) {
            _checkOneField(formID, field, true);
        });
        wptCallbacks.conditionalCheck.fire(formID);
    }

    function _bindChange(formID, $trigger, func)
    {
        // Do not add specific binding for fields here
        // Use add_action() to bind change trigger from /js/$type.js
        // if not provided - default binding will be performed
        var binded = do_action('conditional_trigger_bind_' + $trigger.data('wpt-type'), $trigger, func, formID);
        if (binded) {
            return;
        }
        /**
         * debug
         */
        if ( wptCondDebug ) {
            console.info('_bindChange');
            console.log($trigger);
            console.log($trigger.data('wpt-type'));
        }
        switch( $trigger.data('wpt-type') ) {
            case 'checkbox':
                $trigger.on('click', func);
                break;
            case 'radio':
            case 'radios':
                $('[name="' + $trigger.attr('name') + '"]').on('click', func);
                break;
            case 'select':
                $trigger.on('change', func);
                break;
            default:
                $($trigger).on('blur', func);
        }
    }

    function _custom(formID, fields)
    {
        var data = {action: 'wptoolset_custom_conditional', 'conditions': {}, 'values': {}, 'field_types': {}};
        _.each(fields, function(field) {
            var c = wptCondCustomFields[formID][field];
            data.conditions[field] = c.custom;
            _.each(c.triggers, function(t) {
                var $trigger = _getTrigger(t);
                data.values[t] = _getTriggerValue($trigger);
                data.field_types[t] = $trigger.data('wpt-type');
            });
        });
        $.post(ajaxurl, data, function(res) {
            _.each(res.passed, function(affected) {
                _showHide(true, _getAffected(affected, formID));
            });
            _.each(res.failed, function(affected) {
                _showHide(false, _getAffected(affected, formID));
            });
            wptCallbacks.conditionalCheck.fire(formID);
        }, 'json').fail(function(data) {
            alert(data.responseText);
        });
    }

    function _showHide(show, $el)
    {
        if ( wptCondDebug ) {
            console.info('_showHide');
            console.log(show, $el);
        }
        if (show) {
            $el.slideDown().removeClass('js-wpt-remove-on-submit js-wpt-validation-ignore');
        } else {
            $el.slideUp().addClass('js-wpt-remove-on-submit js-wpt-validation-ignore');
        }
    }

    function ajaxCheck(formID, field, conditions)
    {
        var values = {};
        _.each(conditions.conditions, function(c) {
            var $trigger = _getTrigger(c.id, formID);
            values[c.id] = _getTriggerValue($trigger);
        });
        var data = {
            'action': 'wptoolset_conditional',
            'conditions': conditions,
            'values': values
        };
        $.post(ajaxurl, data, function(passed) {
            _showHide(passed, _getAffected(field, formID));
            wptCallbacks.conditionalCheck.fire(formID);
        }).fail(function(data) {
            alert(data);
        });
    }

    function addConditionals(data)
    {
        _.each(data, function(c, formID) {
            if (typeof c.triggers != 'undefined'
                    && typeof wptCondTriggers[formID] != 'undefined') {
                _.each(c.triggers, function(fields, trigger) {
                    wptCondTriggers[formID][trigger] = fields;
                    var $trigger = _getTrigger(trigger, formID);
                    _bindChange(formID, $trigger, function() {
                        _check(formID, fields);
                    });
                });
            }
            if (typeof c.fields != 'undefined'
                    && typeof wptCondFields[formID] != 'undefined') {
                _.each(c.fields, function(conditionals, field) {
                    wptCondFields[formID][field] = conditionals;
                });
            }
            if (typeof c.custom_triggers != 'undefined'
                    && typeof wptCondCustomTriggers[formID] != 'undefined') {
                _.each(c.custom_triggers, function(fields, trigger) {
                    wptCondCustomTriggers[formID][trigger] = fields;
                    var $trigger = _getTrigger(trigger, formID);
                    _bindChange(formID, $trigger, function() {
                        _custom(formID, fields);
                    });
                });
            }
            if (typeof c.custom_fields != 'undefined'
                    && typeof wptCondCustomFields[formID] != 'undefined') {
                _.each(c.custom_fields, function(conditionals, field) {
                    wptCondCustomFields[formID][field] = conditionals;
                });
            }
        });
    }

    return {
        init: init,
        ajaxCheck: ajaxCheck,
        addConditionals: addConditionals
    };

})(jQuery);

