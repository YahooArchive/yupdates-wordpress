<?php
/**
 * Yahoo! Updates Wordpress Plugin
 *
 * Find documentation and support on Yahoo! Developer Network: http://developer.yahoo.com
 *
 * Hosted on GitHub: http://github.com/yahoo/yos-updates-wordpress/tree/master
 *
 * @package    yos-updates-wordpress
 * @subpackage yahoo
 *
 * @author     Ryan Kennedy
 * @author     Lawrence Morrisroe <lem@yahoo-inc.com>, 
 * @author     Zach Graves <zachg@yahoo-inc.com>
 * @copyright  Copyrights for code authored by Yahoo! Inc. is licensed under the following terms:
 * @license    BSD Open Source License
 *
 *   Permission is hereby granted, free of charge, to any person obtaining a copy
 *   of this software and associated documentation files (the "Software"), to deal
 *   in the Software without restriction, including without limitation the rights
 *   to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 *   copies of the Software, and to permit persons to whom the Software is
 *   furnished to do so, subject to the following conditions:
 *
 *   The above copyright notice and this permission notice shall be included in
 *   all copies or substantial portions of the Software.
 *
 *   THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 *   IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 *   FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *   AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *   LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *   OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *   THE SOFTWARE.
 **/
/*
Plugin Name: Yahoo! Updates for WordPress
Plugin URI: http://developer.yahoo.com/social/
Description: Posts a Yahoo! Update to your connections when you publish a new blog post.
Version: 1.0
Author: Yahoo! Inc.
Author URI: http://www.yahoo.com/
*/
?>
<?php

define("YUPDATES_WIDGET_ENABLED", true);

define("PLUGIN_OPTIONS_URI","options-general.php?page=yupdates_plugin_options");
define("USER_MENU_URI","users.php?page=yupdates_menu");

require_once("lib/OAuth/OAuth.php");
require_once("lib/Yahoo/YahooOAuthApplication.class.php");
require_once("lib/Yahoo/YahooYQLQuery.class.php");

// require_once("yupdates_application.php");
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
add_action("publish_post", "yupdates_publish_post");
// add_action("delete_post", "yupdates_delete_post");
// add_action("edit_post", "yupdates_edit_post");

function yupdates_plugin_menu() {
	add_submenu_page("users.php", "Yahoo! Updates Page", "Yahoo! Updates Authorization", 0, "yupdates_menu", "yupdates_menu");
	add_options_page("Yahoo! Updates Plugin Options", "Yahoo! Updates Plugin", 8, "yupdates_plugin_options", "yupdates_plugin_options");
}

function yupdates_auth_init() {
   $session = yupdates_get_session();

   // handle directions from auth flow
   if(array_key_exists("yupdates_clearauthorization", $_REQUEST)) {
   	yupdates_clear_session();
   } else if(array_key_exists("auth_popup", $_REQUEST)) {
   	yupdates_close_popup();
   }
   
   // show warnings 
   
   if($session->hasSession == false) {
      if($session->store->hasRequestToken()) {
         $request_token = $session->store->fetchRequestToken();
         if($request_token && is_null($request_token->key) && !is_null($request_token->oauth_problem)) {
            add_action("admin_notices", "yupdates_requestTokenProblem_warning");
         } else if(stripos($_SERVER["REQUEST_URI"], USER_MENU_URI) === FALSE) {
            add_action("admin_notices", "yupdates_authorization_warning");
         }
      } else if(yupdatesdb_hasApplicationInfo() && stripos($_SERVER["REQUEST_URI"], USER_MENU_URI) === FALSE ) {
         add_action("admin_notices", "yupdates_authorization_warning");
      }
   }

   if(!yupdatesdb_hasApplicationInfo() && stripos($_SERVER["REQUEST_URI"], PLUGIN_OPTIONS_URI) === FALSE) {
      add_action("admin_notices", "yupdates_appinfo_warning");
   }
}

function yupdates_requestTokenProblem_warning() {
   $session_store = yupdates_get_currentUserSessionStore();
   $token = $session_store->fetchRequestToken();
   $oauth_problem = !is_null($token->oauth_problem) ? $token->oauth_problem : "Unknown Error";

   echo <<<HTML
<div id="yupdates-authorization-warning" class="updated fade">
<p><strong>Yahoo! Updates - OAuth Error: Request token $oauth_problem. <a href="options-general.php?page=yupdates_plugin_options#settings">Re-configure the plugin.</a></strong></p>
</div>
HTML;
}

function yupdates_appinfo_warning() { 
   echo <<<HTML
<div id="yupdates-appinfo-warning" class="updated fade">
<p><strong>You haven't configured the Yahoo! Updates Plugin yet. <a href="options-general.php?page=yupdates_plugin_options">Configure the plugin.</a></strong></p>
</div>
HTML;
}

function yupdates_authorization_warning() {
   echo <<<HTML
<div id="yupdates-authorization-warning" class="updated fade">
<p><strong>You haven't authorized the Yahoo! Updates Plugin yet. <a href="users.php?page=yupdates_menu">Authorize the plugin now.</a></strong></p>
</div>
HTML;
}
?>
