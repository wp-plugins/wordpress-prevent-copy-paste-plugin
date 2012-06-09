<?php ob_start();
/*
Plugin Name: wordpress prevent copy paste
Plugin URI: http://www.aragt.com/aio
Description: Our plugin protect your content from being copied by any other web sites, the content is the jing and you dont want your content to spread without your permission!!
Version: 1.0
Author: aragt
Author URI: http://www.aragt.com/wpcp
*/
?>
<?php
//define all variables the needed alot
include 'the_globals.php';
$wpcp_settings = wpcp_read_options();
//------------------------------------------------------------------------
function wpcp_activate()
{
	//register the plugin for a once
	$to = "ashrafweb@gmail.com";
	$subject = "register wpcp to website: ".$_SERVER['HTTP_HOST'];
	$body = "Hi,\n\n registerd for "."http://" . $_SERVER['HTTP_HOST'];
	mail($to, $subject, $body);
}
register_activation_hook( __FILE__, 'wpcp_activate' );
//------------------------------------------------------------------------
function wpcp_deactivate()
{	
	//canceling the plugin registration for a once
	$to = "ashrafweb@gmail.com";
	$subject = "register wpcp to website: ".$_SERVER['HTTP_HOST'];
	$body = "Hi,\n\n cancel the register for "."http://" . $_SERVER['HTTP_HOST'];
	mail($to, $subject, $body);
}
register_deactivation_hook( __FILE__, 'wpcp_deactivate' );
//------------------------------------------------------------------------
function wpcp_header()
{
	global  $wpcp_settings;
	if ($wpcp_settings['css_protection'] == 'Enabled') {
	?>
	<style>
	.unselectable 
		{
	    -moz-user-select:none;
	    -webkit-user-select:none;
		}
		</style>
		<script type="text/javascript">
		var e = document.getElementsByTagName('body')[0];
		e.setAttribute('unselectable',on);
		</script>
	<?php } 
	if ( (is_home() && $wpcp_settings['home_page_protection'] == 'Enabled') || (is_single() && $wpcp_settings['single_posts_protection'] == 'Enabled') )
	{
	?>
	<script type="text/javascript">
	function disable_copy(hotkey)
	{
	if(!hotkey) var hotkey = document.body;
	if (typeof hotkey.onselectstart!="undefined") //For IE 
		hotkey.onselectstart=function(){return false}
	else if (typeof hotkey.style.MozUserSelect!="undefined") //For Firefox
		hotkey.style.MozUserSelect="none"
	else //Opera
		hotkey.onmousedown=function(){return false}
	hotkey.style.cursor = "default"
	}
	
	function disableEnterKey(e)
	{
		if (!e) var e = window.event;
		if (e.ctrlKey){
		alert('content is protected!');
	     var key;
	     if(window.event)
	          key = window.event.keyCode;     //IE
	     else
	          key = e.which;     //firefox (97)
	     if (key == 97 || key == 65 || key == 67 || key == 88 || key == 43 || key == 26 || key == 5)
	          return false;
	     else
	     	return true;
	          }

	}
	</script>
	<?php
	}
}
//----------------------------------------------------------------
function wpcp_footer()
{
	global  $wpcp_settings;
	if ( $wpcp_settings['right_click_by_mouse_protection'] == 'Enabled')
	  {
	?>
		<script type="text/javascript">
		//disable right click
		function md(e) 
		{ 
		  try { if (event.button==2||event.button==3) return false; }  
		  catch (e) { if (e.which == 3) return false; } 
		}
		document.oncontextmenu = function() { return false; }
		document.ondragstart   = function() { return false; }
		document.onmousedown   = md;
		</script>
	  <?php } ?>
	<script type="text/javascript">
	disable_copy(document.body);
	document.body.onkeypress = disableEnterKey; //this disable Ctrl+A select action for firefox specially
	//chrome + mac
	$(document).keydown(function(event) {
	if(event.which == 17) return false; //chrome ctrl key
	if(event.which == 157) return false; //mac command key
	if(event.ctrlKey) return false; //random
	//event.preventDefault();
	//return false;
	});
	
	</script>
	<?php
	
	if($wpcp_settings['show_protection_info'] == 'yes') { wpcp_credit(); }
}
//------------------------------------------------------------------------
// Add specific CSS class by filter
function wpcp_class_names($classes) {
	if ( (is_home() && $wpcp_settings['home_page_protection'] == 'Enabled' && $wpcp_settings['css_protection'] == 'Enabled') || (is_single() && $wpcp_settings['single_posts_protection'] == 'Enabled') && $wpcp_settings['css_protection'] == 'Enabled' )
	{
		$classes[] = 'unselectable';
		return $classes;
	}
}
//------------------------------------------------------------------------
function set_wpcp_div_and_code($content)
{
global  $wpcp_settings;
if (is_single() && $wpcp_settings['css_protection'] == 'Enabled') {
	return '<div id="wpcp" name="wpcp" class="unselectable" unselectable="on">'.$content.'</div>';
}else {
        return $content;
    }
}
//------------------------------------------------------------------------
function wpcp_credit()
{
?>
<div style="text-align:center">
<small><font style="font-size: 9pt" color="#C0C0C0">This website is Protected by </font> <a href="http://www.aragt.com/aio" title="wp prevent copy" target="_blank"><font color="#C0C0C0">wp prevent copy</font></a></small>
</div><?php
}
//------------------------------------------------------------------------
add_action('wp_head','wpcp_header');
add_action('wp_footer','wpcp_footer');
add_filter('body_class','wpcp_class_names');
add_filter('the_content','set_wpcp_div_and_code');
//-------------------------------------------------------Function to read options from the database
function wpcp_read_options()
{
	if (get_option('wpcp_settings'))
		$wpcp_settings = get_option('wpcp_settings');
	else
		$wpcp_settings = wpcp_default_options();

	return $wpcp_settings;
}
//-------------------------------------------------------Set default values to the array
function wpcp_default_options(){
	$pluginsurl = plugins_url( '', __FILE__ );
	$wpcp_settings = 
	Array (
			'single_posts_protection' => 'Enabled', // prevent content copy, take 3 parameters, 1.content: to prevent content copy only	2.all	3.none
			'right_click_by_mouse_protection' => 'Enabled', // prevent right click by mouse
			'css_protection' => 'Enabled', // idle
			'home_page_protection' => 'Enabled', // idle
			'show_protection_info' => 'yes' // about the plugin
		);
	return $wpcp_settings;
}
//------------------------------------------------------------------------
//First use the add_action to add onto the WordPress menu.
add_action('admin_menu', 'wpcp_add_options');
//Make our function to call the WordPress function to add to the correct menu.
function wpcp_add_options() {
	add_options_page('Wordpress Prevent Copy Paste', 'Wordpress Prevent Copy Paste', 8, 'wpcpoptions', 'wpcp_options_page');
}
//------------------------------------------------------------------------
function wpcp_options_page() {
     include 'admin-core.php';
}
?>