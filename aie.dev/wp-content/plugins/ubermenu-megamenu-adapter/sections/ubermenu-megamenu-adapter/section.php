<?php
/*
	Section: UberMenu MegaMenu Adapater
	Author: SevenSpark
	Author URI: http://www.pagelines.com
	Description: An adapter section for the <a target="_blank" href="http://goo.gl/m6kHhe">UberMenu Mega Menu plugin</a>.  Requires separate purchase of UberMenu.  Please note you can only use one per page.
	Class Name: UberNav
	Filter: nav
	Version: 1.0
*/

class UberNav extends PageLinesSection {

	function section_opts(){
		$opts = array(
			array(
				'key'		=> 'ubermenu_notice',
				'type' 		=> 'disabled',
				'title'		=> __( 'Using UberMenu', 'pagelines' ),
				'help'		=> __( 'To use UberMenu with PageLines DMS, first download and install UberMenu, then configure it via the ', 'pagelines' ) . 
							'<a target="_blank" href="'.admin_url('themes.php?page=uber-menu').'">UberMenu Control Panel</a>'.
							'<br/><br/><a target="_blank" class="btn" href="http://sevenspark.com/docs/ubermenu-pagelines-dms">DMS Section Setup Instructions</a><br/>'.
							'<br/><a target="_blank" href="http://sevenspark.com/docs/ubermenu">Knowledgebase</a> | <a target="_blank" href="http://goo.gl/m6kHhe">UberMenu Demo</a> | <a target="_blank" href="http://goo.gl/FvhznI">Purchase UberMenu</a>',


			),
		);

		return $opts;
	}

	function section_template() { 

		if( function_exists('uberMenu_direct') ){
			if( has_nav_menu( 'ubermenu' ) ){
				uberMenu_direct( 'ubermenu' );
			}
			else{
				echo 'Please enable UberMenu <a href="http://sevenspark.com/docs/ubermenu-easy-integration" title="Permalink to Easy Integration" rel="bookmark">Easy Integration</a> and set a menu in the "UberMenu" Theme Location';
			}
		}
		else echo 'Please install <a href="http://wpmegamenu.com">UberMenu</a> in order to use this section';

	}

}

