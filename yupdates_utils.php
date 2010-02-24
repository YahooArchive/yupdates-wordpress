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
 *   FITNESS FOR A PARTICULAR PURPOSE AND NON-INFRINGEMENT. IN NO EVENT SHALL THE
 *   AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *   LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *   OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *   THE SOFTWARE.
 **/	
$yupdates_session_store = NULL;

function yupdates_has_session($session) 
{
   if($session->store->hasAccessToken()) {
      $access_token = $session->store->fetchAccessToken();

      if(!$access_token->key || !$access_token->secret) {
         return false;
      }

      // refresh the token.
      $access_token = yupdates_get_accessToken($session, $access_token);
      
      $token = $session->application->token;
      
      return ($token && $token->key);
   } 
   else if($session->store->hasRequestToken()) 
   {
      // $request_token = yupdates_get_requestToken($session);
      $request_token = $session->store->fetchRequestToken();
      
      if(!$request_token->key || !$request_token->secret) {
         $session->store->clearRequestToken();
         $token = yupdates_get_requestToken($session);

         return false;
      }
      
      if(array_key_exists("oauth_token", $_REQUEST) && array_key_exists("oauth_verifier", $_REQUEST)) {
         $oauth_verifier = $_REQUEST["oauth_verifier"];
         $access_token = $session->application->getAccessToken($request_token, $oauth_verifier);

         if($access_token->key && $access_token->secret) {
            $session->store->clearRequestToken();
            $session->store->storeAccessToken($access_token);
            
            return TRUE;
         }
      }
      
      return false;
   }
   else if($session->application->consumer_key && $session->application->consumer_secret)
   {
      $token = yupdates_get_requestToken($session);

      return false;
   }

   return false;
}

function yupdates_get_requestToken($session) 
{
   $callback_params = array("auth_popup"=>"true");
   $callback = yupdates_get_oauthCallback($callback_params);

   $request_token = $session->application->getRequestToken($callback);
   $session->store->storeRequestToken($request_token);
   
   return $request_token;
}

function yupdates_get_accessToken($session, $access_token=NULL) 
{
   $access_token = $session->application->getAccessToken($access_token);
   $session->store->storeAccessToken($access_token);
   
   return $access_token;
}

function yupdates_clear_session() 
{
   global $current_user;
   get_currentuserinfo();
   
   $user = $current_user->user_login;
   $session_store = yupdates_get_sessionStore($user);
   
   $session_store->clearRequestToken();
   $session_store->clearAccessToken();
   
   /* delete keys 
   go to /wp-admin/options.php to update the array with any yupdates_* keys.
   $options = array();
   foreach($options as $name) {
   delete_option($name);
   }
   */

   header(sprintf("Location: %s", get_bloginfo('url')));
   exit();
}

function yupdates_get_oauthCallback($parameters=array()) 
{
   return sprintf("http://%s%s&%s",$_SERVER["HTTP_HOST"], $_SERVER["REQUEST_URI"], http_build_query($parameters));
} 

function yupdates_get_currentUserSessionStore() 
{
   if(!$yupdates_session_store) {
      global $current_user;
      get_currentuserinfo();

      $user = $current_user->user_login;
      $yupdates_session_store = yupdates_get_sessionStore($user);
   }
   return $yupdates_session_store;
}

function yupdates_get_sessionStore($user) 
{
   $consumer_key = get_option("yupdates_consumer_key");
   return new WordPressSessionStore($user, $consumer_key);
}

function yupdates_get_application() 
{
   // fetch application keys from user options
   $consumer_key = get_option("yupdates_consumer_key");
   $consumer_secret = get_option("yupdates_consumer_secret");
   $appid = get_option("yupdates_application_id");

   return new YahooOAuthApplication($consumer_key, $consumer_secret, $appid);
}

function yupdates_get_session($user=NULL) 
{
   // create session object with application, token store
   $session = new stdclass();
   $session->application = yupdates_get_application();
   $session->store = (is_null($user)) ? yupdates_get_currentUserSessionStore() : yupdates_get_sessionStore($user);
   $session->user = $user;

   // pass the session off to check for tokens in the store. 
   // updates tokens as needed and returns true/false if a session exists
   // (if access token exists)
   $session->hasSession = yupdates_has_session($session);

   return $session;
}

function yupdates_get_bitly_options() 
{
   $options = new stdclass();
   $options->apiKey = get_option("yupdates_bitly_apiKey"); 
   $options->login = get_option("yupdates_bitly_login"); 

   return $options;
}

function yupdates_bitly_shorten($permalink, $apiKey, $login) 
{
   $query = "SELECT statusCode, results FROM bit.ly.shorten where login='%s' and apiKey='%s' and longUrl='%s' and history='1'";
   $query = sprintf($query, $login, $apiKey, $permalink);
   
   // $session = yupdates_get_session();
   $yql = new YahooYQLQuery();
   $rsp = $yql->execute($query);
   
   if(isset($rsp->query) && isset($rsp->query->results)) {
      $results = $rsp->query->results;
      
      $bitly = (isset($results->bitly)) ? $results->bitly : false;
      
      if($bitly && isset($bitly->results) && $bitly->statusCode == 'OK') {
         $results = $bitly->results->nodeKeyVal;
         
         if($results && isset($results->shortUrl)) {
            return $results->shortUrl;
         }
      }
   }
   
   return $permalink;
}

function yupdates_insertUpdate($update) 
{
   $session = yupdates_get_session();
   
   if($update && $session->hasSession) {
      $suid = null;
      $results = $session->application->insertUpdate(null, $update->description, $update->title, $update->link, $suid);
      
      return ($results) ? $suid : false;
   }
   
   return false;
}

function yupdates_close_popup() 
{
?>
 <script type="text/javascript">
  window.close();
 </script>
<?php
}
?>