
var wptCallbacks = {};
wptCallbacks.validationInit = jQuery.Callbacks('unique');
wptCallbacks.addRepetitive = jQuery.Callbacks('unique');
wptCallbacks.removeRepetitive = jQuery.Callbacks('unique');
wptCallbacks.conditionalCheck = jQuery.Callbacks('unique');
wptCallbacks.reset = jQuery.Callbacks('unique');

jQuery(document).ready(function() {
    if (typeof wptValidation !== 'undefined') {
        wptCallbacks.validationInit.add(function() {
            wptValidation.init();
        });
    }
    if (typeof wptCond !== 'undefined') {
        wptCond.init();
    } else {
        wptCallbacks.validationInit.fire();
    }
});


var wptFilters = {};
function add_filter(name, callback, priority, args_num) {
    var args = _.defaults(arguments, ['', '', 10, 2]);
    if (typeof wptFilters[name] === 'undefined')
        wptFilters[name] = {};
    if (typeof wptFilters[name][args[2]] === 'undefined')
        wptFilters[name][args[2]] = [];
    wptFilters[name][args[2]].push([callback, args[3]]);
}
function apply_filters(name, val) {
    if (typeof wptFilters[name] === 'undefined')
        return val;
    var args = _.rest(_.toArray(arguments));
    _.each(wptFilters[name], function(funcs, priority) {
        _.each(funcs, function($callback) {
            var _args = args.slice(0, $callback[1]);
            args[0] = $callback[0].apply(null, _args);
        });
    });
    return args[0];
}
function add_action(name, callback, priority, args_num) {
    add_filter.apply(null, arguments);
}
function do_action(name) {
    if (typeof wptFilters[name] === 'undefined')
        return false;
    var args = _.rest(_.toArray(arguments));
    _.each(wptFilters[name], function(funcs, priority) {
        _.each(funcs, function($callback) {
            var _args = args.slice(0, $callback[1]);
            $callback[0].apply(null, _args);
        });
    });
    return true;
}

/**
 * flat taxonomies functions
 */

function showHideMostPopularTaxonomy(el)
{
    taxonomy = jQuery(el).data('taxonomy');
    jQuery('.shmpt-'+taxonomy, jQuery(el).closest('form')).toggle();
    var curr = jQuery('input[name=sh_'+taxonomy+']').val().trim();
    if (curr=='show popular') {
        jQuery('input[name=sh_'+taxonomy+']').val('hide popular');
    } else {
        jQuery('input[name=sh_'+taxonomy+']').val('show popular');
    }
}

function addTaxonomy(slug, taxonomy, el)
{
    var form = jQuery(el).closest('form');
    var curr = jQuery('input[name=tmp_'+taxonomy+']', form).val().trim();
    if (''==curr) {
        jQuery('input[name=tmp_'+taxonomy+']', form).val(slug);
        setTaxonomy(taxonomy, el);
    } else {
        if (curr.indexOf( slug )==-1) {
            jQuery('input[name=tmp_'+taxonomy+']', form).val(curr+','+slug);
            setTaxonomy(taxonomy, el);
        }
    }
    jQuery('input[name=tmp_'+taxonomy+']', form).val('');
}

function setTaxonomy(taxonomy, el)
{
    var form = jQuery(el).closest('form');
    var tmp_tax = jQuery('input[name=tmp_'+taxonomy+']', form).val();
    if (tmp_tax.trim()=='') return;
    var tax = jQuery('input[name='+taxonomy+']', form).val();
    var arr = tax.split(',');
    if (jQuery.inArray(tmp_tax, arr)!==-1) return;
    var toadd = (tax=='') ? tmp_tax : tax+','+tmp_tax;
    jQuery('input[name='+taxonomy+']', form).val(toadd);
    jQuery('input[name=tmp_'+taxonomy+']', form).val('');
    updateTaxonomies(taxonomy, form);
}

function updateTaxonomies(taxonomy, form)
{
    var taxonomies = jQuery('input[name='+taxonomy+']', form).val();
    if (!taxonomies||(taxonomies&&taxonomies.trim()=='')) return;
    var toshow = taxonomies.split(',');
    var str = '';
    for (var i=0;i<toshow.length;i++) {
        var sh = toshow[i].trim();
        str += '<span><a class=\'ntdelbutton\' onclick=\'del(this, "'+taxonomy+'");\' rel=\''+i+'\' id=\'post_tag-check-num-'+i+'\'>X</a>&nbsp;'+sh+'</span>';
    }
    jQuery('div.tagchecklist-'+taxonomy, form).html(str);
}

function initTaxonomies(values, taxonomy, url, formId)
{
    jQuery('div.tagchecklist-'+taxonomy).html(values);
    jQuery('input[name='+taxonomy+']').val(values);
    updateTaxonomies(taxonomy, jQuery('#'+formId));
    jQuery('input[name=tmp_'+taxonomy+']').autocomplete (
        url+'/external/autocompleter.php',
        {
            delay:10,
            minChars:2,
            matchSubset:1,
            matchContains:1,
            cacheLength:10,
            formatItem:formatItem,
            onItemSelect:onSelectItem,
            autoFill:true
        }
    );
}

function del(x, taxonomy)
{
    var n = jQuery(x).attr('rel');
    var taxonomies = jQuery('input[name='+taxonomy+']').val();
    var arr = taxonomies.split(',');
    var newstr = '';
    var newstr4tax = '';
    var counter = 0;
    for (var i=0;i<arr.length;i++) {
        if (i!=n) {
            var sh = arr[i].trim();
            newstr += '<span><a class=\'ntdelbutton\' onclick=\'del(this);\' rel=\''+i+'\' id=\'post_tag-check-num-'+i+'\'>X</a>&nbsp;'+sh+'</span>';
            newstr4tax += (counter==0) ? sh : ','+sh;
            counter++;
        }
    }
    jQuery('input[name='+taxonomy+']').val(newstr4tax);
    jQuery('div.tagchecklist'+taxonomy).html('');
    jQuery('div.tagchecklist'+taxonomy).html(newstr);
}
