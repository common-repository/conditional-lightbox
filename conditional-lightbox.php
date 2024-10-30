<?php
/* 
Plugin Name: Conditional Lightbox
Plugin URI: --
Description: Use a lightbox only if the screen is big enough.
Version: 1.0 
Author: Stefan Matei
Author URI: http://vileworks.com 
*/  

// ------------------------------------------------------------------------
// PLUGIN PREFIX:                                                          
// ------------------------------------------------------------------------
// A PREFIX IS USED TO AVOID CONFLICTS WITH EXISTING PLUGIN FUNCTION NAMES.
// WHEN CREATING A NEW PLUGIN, CHANGE THE PREFIX AND USE YOUR TEXT EDITORS 
// SEARCH/REPLACE FUNCTION TO RENAME THEM ALL QUICKLY.
// ------------------------------------------------------------------------

// 'coli_' prefix is derived from [co]nditional [li]ghtbox

// ------------------------------------------------------------------------
// REGISTER HOOKS & CALLBACK FUNCTIONS:
// ------------------------------------------------------------------------
// HOOKS TO SETUP DEFAULT PLUGIN OPTIONS, HANDLE CLEAN-UP OF OPTIONS WHEN
// PLUGIN IS DEACTIVATED AND DELETED, INITIALISE PLUGIN, ADD OPTIONS PAGE.
// ------------------------------------------------------------------------

// Set-up Action and Filter Hooks
register_activation_hook(__FILE__, 'coli_add_defaults');
register_uninstall_hook(__FILE__, 'coli_delete_plugin_options');
add_action('admin_init', 'coli_init' );
add_action('admin_menu', 'coli_add_options_page');
add_filter( 'plugin_action_links', 'coli_plugin_action_links', 10, 2 );

// --------------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: register_uninstall_hook(__FILE__, 'coli_delete_plugin_options')
// --------------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE USER DEACTIVATES AND DELETES THE PLUGIN. IT SIMPLY DELETES
// THE PLUGIN OPTIONS DB ENTRY (WHICH IS AN ARRAY STORING ALL THE PLUGIN OPTIONS).
// --------------------------------------------------------------------------------------

// Delete options table entries ONLY when plugin deactivated AND deleted
function coli_delete_plugin_options() {
	delete_option('coli_options');
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: register_activation_hook(__FILE__, 'coli_add_defaults')
// ------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE PLUGIN IS ACTIVATED. IF THERE ARE NO THEME OPTIONS
// CURRENTLY SET, OR THE USER HAS SELECTED THE CHECKBOX TO RESET OPTIONS TO THEIR
// DEFAULTS THEN THE OPTIONS ARE SET/RESET.
//
// OTHERWISE, THE PLUGIN OPTIONS REMAIN UNCHANGED.
// ------------------------------------------------------------------------------

// Define default option settings
function coli_add_defaults() {
	$tmp = get_option('coli_options');
    if(($tmp['chk_default_options_db']=='1')||(!is_array($tmp))) {
		delete_option('coli_options'); // so we don't have to reset all the 'off' checkboxes too! (don't think this is needed but leave for now)
		$arr = array(	'txt_width' => '600'		);
		update_option('coli_options', $arr);
	}
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: add_action('admin_init', 'coli_init' )
// ------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE 'admin_init' HOOK FIRES, AND REGISTERS YOUR PLUGIN
// SETTING WITH THE WORDPRESS SETTINGS API. YOU WON'T BE ABLE TO USE THE SETTINGS
// API UNTIL YOU DO.
// ------------------------------------------------------------------------------

// Init plugin options to white list our options
function coli_init(){
	register_setting( 'coli_plugin_options', 'coli_options', 'coli_validate_options' );
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION FOR: add_action('admin_menu', 'coli_add_options_page');
// ------------------------------------------------------------------------------
// THIS FUNCTION RUNS WHEN THE 'admin_menu' HOOK FIRES, AND ADDS A NEW OPTIONS
// PAGE FOR YOUR PLUGIN TO THE SETTINGS MENU.
// ------------------------------------------------------------------------------

// Add menu page
function coli_add_options_page() {
	add_options_page('Conditional Lightbox Options', 'Conditional Lightbox', 'manage_options', __FILE__, 'coli_render_form');
}

// ------------------------------------------------------------------------------
// CALLBACK FUNCTION SPECIFIED IN: add_options_page()
// ------------------------------------------------------------------------------
// THIS FUNCTION IS SPECIFIED IN add_options_page() AS THE CALLBACK FUNCTION THAT
// ACTUALLY RENDER THE PLUGIN OPTIONS FORM AS A SUB-MENU UNDER THE EXISTING
// SETTINGS ADMIN MENU.
// ------------------------------------------------------------------------------

// Render the Plugin options form
function coli_render_form() {
	?>
	<div class="wrap">
		
		<!-- Display Plugin Icon, Header, and Description -->
		<div class="icon32" id="icon-options-general"><br></div>
		<h2>Conditional Lightbox</h2>
		

		<!-- Beginning of the Plugin Options Form -->
		<form method="post" action="options.php">
			<?php settings_fields('coli_plugin_options'); ?>
			<?php $options = get_option('coli_options'); ?>

			<!-- Table Structure Containing Form Controls -->
			<!-- Each Plugin Option Defined on a New Table Row -->
			<p>
				Don't use a lightbox if the page width is under <input type="text" size="4" name="coli_options[txt_width]" value="<?php echo $options['txt_width']; ?>" style="width:3em"/> pixels.
			</p>
			<p class="submit">
			<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>

		</form>
		
		--

		<p style="margin-top:15px;">
			<p style="font-weight: bold;color: #26779a;">This plugin was developed by <a href="http://stefanmatei.com" title="Stefan Matei">Stefan Matei</a> for <a href="http://www.vileworks.com">VileWorks.com</a>.</p>
			<span><a href="http://fb.me/VileWorks" title="Our Facebook page" target="_blank"><img style="border:1px #ccc solid;" src="<?php echo plugins_url(); ?>/conditional-lightbox/images/facebook-icon.png" /></a></span>
			&nbsp;&nbsp;<span><a href="http://twitter.com/VileWorks" title="Follow on Twitter" target="_blank"><img style="border:1px #ccc solid;" src="<?php echo plugins_url(); ?>/conditional-lightbox/images/twitter-icon.png" /></a></span>
		</p>

	</div>
	<?php	
}

// Sanitize and validate input. Accepts an array, return a sanitized array.
function coli_validate_options($input) {
	 // strip html from textboxes
	$input['txt_width'] =  wp_filter_nohtml_kses($input['txt_width']); // Sanitize textbox input (strip html tags, and escape characters)
	return $input;
}

// Display a Settings link on the main Plugins page
function coli_plugin_action_links( $links, $file ) {

	if ( $file == plugin_basename( __FILE__ ) ) {
		$coli_links = '<a href="'.get_admin_url().'options-general.php?page=conditional-lightbox/conditional-lightbox.php">'.__('Settings').'</a>';
		// make the 'Settings' link appear first
		array_unshift( $links, $coli_links );
	}

	return $links;
}

// ------------------------------------------------------------------------------
// USAGE FUNCTIONS:
// ------------------------------------------------------------------------------
// THE FOLLOWING FUNCTIONS USE THE PLUGINS OPTIONS DEFINED ABOVE.
// ------------------------------------------------------------------------------


function coli_plugin_init() {
	if (!is_admin()) {
		wp_enqueue_script('jquery');

		// load Slimbox2 script and style
		wp_enqueue_script('my_script', plugins_url() . '/conditional-lightbox/slimbox-2.04/js/slimbox2.js', array('jquery'), '1.0', true);
		wp_enqueue_style('my_script', plugins_url() . '/conditional-lightbox/slimbox-2.04/css/slimbox2.css');

		add_action('wp_footer', 'coli_print_my_script');
	}
}
add_action('init', 'coli_plugin_init');

function coli_print_my_script() { $options = get_option('coli_options'); ?>

	<script type="text/javascript">
	jQuery(function ($) {
		minwidth = <?php echo $options['txt_width']; ?>;
		if ( $(window).width() > minwidth ) {

			$('a[href$=".jpg"], a[href$=".png"]').slimbox({/* Put custom options here */}, null, function(el) {
				return (this == el) || ((this.rel.length > 8) && (this.rel == el.rel));
			});
		}
	});
	</script>

<?php } ?>
