<?php
/*
Plugin Name: Yahoo! Updates
Plugin URI: http://developer.yahoo.com/social/
Description: A Yahoo! Updates plugin for WordPress.
Version: 0.9
Author: Yahoo! Inc.
Author URI: http://www.yahoo.com/
*/
?>
<?php
	define("YUPDATES_WIDGET_ENABLED", true);

	require_once("lib/OAuth/OAuth.php");
	require_once("lib/Yahoo/YahooOAuthApplication.class.php");
	
	require_once("yupdates_sessionstore.php");
	require_once("yupdates_utils.php");
	require_once("yupdates_menu.php");
	require_once("yupdates_options.php");
	require_once("yupdates_hooks.php");
	require_once("yupdates_db.php");
	
	if(YUPDATES_WIDGET_ENABLED) {
	    require_once("yupdates_widgets.php");
	}

	add_action("admin_menu", "yupdates_plugin_menu");
	add_action("init", "yupdates_auth_init");

	add_action("delete_post", "yupdates_delete_post");
	add_action("edit_post", "yupdates_edit_post");
	add_action("publish_post", "yupdates_publish_post");
	
	$yupdates_session_store = NULL;
?>
<?php
	function yupdates_plugin_menu() {
	    add_submenu_page("users.php", "Yahoo! Updates Page", "Yahoo! Updates Authorization", 0, "yupdates_menu", "yupdates_menu");
	    add_options_page("Yahoo! Updates Plugin Options", "Yahoo! Updates Plugin", 8, "yupdates_plugin_options", "yupdates_plugin_options");
	}

	function yupdates_auth_init() {
		//
		$session_store = yupdates_get_sessionStore();
		
		//
		$app_info = yupdatesdb_getApplicationInfo();
		
		// fetch application keys from user options
	    $ck = get_option("yupdates_consumer_key");
	    $cks = get_option("yupdates_consumer_secret");
	    $appid = get_option("yupdates_application_id");
		
		//
		$application = new YahooOAuthApplication($ck, $cks, $appid);
		$application_has_session = yupdates_has_session($application, $session_store);
		
		// handle directions from auth flow
		if(array_key_exists("yupdates_clearauthorization", $_REQUEST)) 
		{
			yupdates_clear_session();
	    }
	    else if(array_key_exists("auth_popup", $_REQUEST))
		{
			yupdates_close_popup();
		}
		
		//
		if(!yupdatesdb_hasApplicationInfo() && stripos($_SERVER["REQUEST_URI"], "options-general.php?page=yupdates_plugin_options") === FALSE) 
		{
	        function yupdates_appinfo_warning() { 
				echo <<<HTML
<div id="yupdates-appinfo-warning" class="updated fade"><p><strong>You haven't configured the Yahoo! Updates Plugin yet. <a href="options-general.php?page=yupdates_plugin_options">Configure the plugin.</a></strong></p></div>
HTML;

        	}
        	add_action("admin_notices", "yupdates_appinfo_warning");
    	} 
		else if(yupdatesdb_hasApplicationInfo() && stripos($_SERVER["REQUEST_URI"], "users.php?page=yupdates_menu") === FALSE) 
		{
	        if(!$application_has_session) {
	            function yupdates_authorization_warning() {
					echo <<<HTML
<div id="yupdates-authorization-warning" class="updated fade"><p><strong>You haven't authorized the Yahoo! Updates Plugin yet. <a href="users.php?page=yupdates_menu">Authorize the plugin now.</a></strong></p></div>
HTML;
            	}
	            add_action("admin_notices", "yupdates_authorization_warning");
	        }
	    }
	}
?>
