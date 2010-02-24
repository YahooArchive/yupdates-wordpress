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

define("YUPDATES_USER_OPTION", "yupdates_updates_widget_users");
define("YUPDATES_CONSUMER_KEY_OPTION", "yupdates_consumer_key");
define("YUPDATES_CONSUMER_SECRET_OPTION", "yupdates_consumer_secret");
define("YUPDATES_APPLICATION_ID_OPTION", "yupdates_application_id");
define("YUPDATES_WIDGET_COUNT_OPTION", "yupdates_widget_count");
define("YUPDATES_TITLE_TEMPLATE_OPTION", "yupdates_title_template");

$updateUsers = get_option(YUPDATES_USER_OPTION);

if(is_bool($updateUsers) && !$updateUsers) {
   $updateUsers = array();
} else if(!is_array($updateUsers)) {
   $updateUsers = array();
   delete_option(YUPDATES_USER_OPTION);
}

//////////////////////
// yupdates_db_utils
//////////////////////

function yupdatesdb_hasApplicationInfo() 
{
   return get_option(YUPDATES_CONSUMER_KEY_OPTION) &&
         get_option(YUPDATES_CONSUMER_SECRET_OPTION) &&
         get_option(YUPDATES_APPLICATION_ID_OPTION);
}

function yupdatesdb_getApplicationInfo() 
{
   $info = array();
   $info["ck"] = get_option(YUPDATES_CONSUMER_KEY_OPTION);
   $info["cks"] = get_option(YUPDATES_CONSUMER_SECRET_OPTION);
   $info["appid"] = get_option(YUPDATES_APPLICATION_ID_OPTION);
   
   return $info;
}

function yupdatesdb_addUpdatesUser($user) 
{
   global $updateUsers;
   $updateUsers[$user] = true;
   update_option(YUPDATES_USER_OPTION, $updateUsers);
}

function yupdatesdb_removeUpdatesUser($user) 
{
   global $updateUsers;
   $updateUsers[$user] = false;
   update_option(YUPDATES_USER_OPTION, $updateUsers);
}

function yupdatesdb_isUpdatesUser($user) 
{
   global $updateUsers;
   return array_key_exists($user, $updateUsers) && $updateUsers[$user];
}

function yupdatesdb_listUpdatesUsers() 
{
   global $updateUsers;

   $users = array();
   foreach($updateUsers as $user => $active) {
      if($active) {
         $users[] = $user;
      }
   }
   
   return $users;
}

function yupdatesdb_getWidgetCount() 
{
   $count = get_option(YUPDATES_WIDGET_COUNT_OPTION);
   
   if(is_bool($count) || !is_numeric($count)) {
      $count = 5;
   }
   
   return $count;
}

function yupdatesdb_setWidgetCount($count) 
{
   update_option(YUPDATES_WIDGET_COUNT_OPTION, $count);
}

?>
