<?php
/*
Plugin Name: Retailers Plugin
Plugin URI: #
Description: Manages a map of retailers.
Version: 0.1
Author: David Beveridge, Studio DBC
Author URI: http://www.studiodbc.com
License: X11


Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:
The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE X CONSORTIUM BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/

global $wpdb;

/*
 * Configuration:
 */
if(!defined('DBC_RT_PATH'))	{
	define('DBC_RT_PATH',ABSPATH.basename(dirname(__FILE__)));
}

if(!defined('DBC_RT_URL'))	{
	define('DBC_RT_URL',get_bloginfo('wpurl').'/wp-content/plugins/'.basename(dirname(__FILE__)));
}

if(!defined('DBC_RT_TABLENAME'))	{
	define('DBC_RT_TABLENAME',$wpdb->prefix.'retailers_dbc');
}

class DBC_Retailers_Plugin{

	function __construct()	{
		if(!is_admin())	{
			add_action('get_header',array(&$this,'register_scripts'));
			add_action('wp_head',array(&$this,'define_ajax_path'),0);
			add_filter('the_content',array(&$this,'apply_map_filter'));
		}
	}

	function register_scripts()	{
		wp_register_script('jquery.mousewheel', DBC_RT_URL.'/js/jquery.mousewheel.js',array('jquery'),'3.0');
		wp_register_script('jquery.em', DBC_RT_URL.'/js/jquery.em.js',array('jquery'),'1.0');
		wp_register_script('jScrollPane', DBC_RT_URL.'/js/jScrollPane.js',array('jquery','jquery.em','jquery.mousewheel'),'1.0');
		wp_register_script('jmap', DBC_RT_URL.'/js/jquery.jmap.min-r72.js',array('jquery'),'0.72');
		wp_register_script('retailers-plugin', DBC_RT_URL.'/js/retailers-plugin.js',array('jmap'),'0.1');
		wp_register_script('googlemaps',DBC_RT_URL.'/js/googlemaps.js');

		wp_register_style('jScrollPane',DBC_RT_URL.'/css/jScrollPane.css');
		wp_register_style('retailers-plugin',DBC_RT_URL.'/css/retailers-plugin.css');

		wp_enqueue_script('jquery');
		wp_enqueue_script('jmap');
		//wp_enqueue_script('retailers-plugin');
		wp_enqueue_script('googlemaps');

		wp_enqueue_style('jScrollPane');
		wp_enqueue_style('retailers-plugin');
	}

	function define_ajax_path()	{
	?>
	<script type="text/javascript">
	window.DBC_RT_URL = '<?php echo DBC_RT_URL ?>/retailer-ajax.php';
	</script>
	<?
	}

	function apply_map_filter($content)	{
		$content = str_ireplace('[retailersmap]','<div id="retailer-map"></div>',$content);
		return $content;
	}

}

new DBC_Retailers_Plugin;