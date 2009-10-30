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
 * @author     Ryan Kennedy <rckenned@yahoo-inc.com>, Lawrence Morrisroe <lem@yahoo-inc.com>, Zach Graves <zachg@yahoo-inc.com>
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
	
	function yupdates_has_session($session) {
		if($session->store->hasAccessToken()) 
		{
			$access_token = $session->store->fetchAccessToken();
			
			if(!$access_token->key || !$access_token->secret) {
				return FALSE;
			}
			
			$access_token = $session->application->getAccessToken($access_token);
			$session->store->storeAccessToken($access_token);
			
			return ($session->application->token && $session->application->token->key);
		} 
		else if($session->store->hasRequestToken()) 
		{
			$request_token = $session->store->fetchRequestToken();
			
			if(!$request_token->key || !$request_token->secret) {
				$session->store->clearRequestToken();
				yupdates_get_requestToken($session);
				return FALSE;
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
			
			return FALSE;
		}
		else 
		{
			yupdates_get_requestToken($session);
			
			return FALSE;
		}
	}
	
	function yupdates_get_requestToken($session) {
		$callback_params = array("auth_popup"=>"true");
		$callback = yupdates_get_oauthCallback($callback_params);
		$request_token = $session->application->getRequestToken($callback);
		$session->store->storeRequestToken($request_token);
	}
	
	function yupdates_clear_session() {
		global $current_user;
    	get_currentuserinfo();
		
		$user = $current_user->user_login;
		$session_store = yupdates_get_sessionStore($user);
		
		$session_store->clearRequestToken();
        $session_store->clearAccessToken();
		
		// todo: infinite looping
		header(sprintf("Location: %s", $_SERVER["REQUEST_URI"]));
        exit();
	}
	
	function yupdates_get_oauthCallback($parameters=array()) {
		return sprintf("http://%s%s&%s",$_SERVER["HTTP_HOST"], $_SERVER["REQUEST_URI"], http_build_query($parameters));
	} 
	
	function yupdates_get_currentUserSessionStore() {
		if(!$yupdates_session_store) {
			global $current_user;
	    	get_currentuserinfo();
			
			$user = $current_user->user_login;
			$yupdates_session_store = yupdates_get_sessionStore($user);
		}
		return $yupdates_session_store;
	}
	
	function yupdates_get_sessionStore($user) {
		return new WordPressSessionStore($user, get_option("yupdates_consumer_key"));
	}
	
	function yupdates_get_application() {
		// fetch application keys from user options
		$ck = get_option("yupdates_consumer_key");
		$cks = get_option("yupdates_consumer_secret");
		$appid = get_option("yupdates_application_id");
		
		return new YahooOAuthApplication($ck, $cks, $appid);
	}
	
	function yupdates_get_session($user=NULL) {
		// create session object with application, token store
		$session = new stdclass();
		$session->application = yupdates_get_application();
		$session->store = (is_null($user)) ? yupdates_get_currentUserSessionStore() : yupdates_get_sessionStore($user);
		
		$session->hasSession = yupdates_has_session($session);
		
		return $session;
	}
	
	function yupdates_get_bitly_options() {
		$options = new stdclass();
		$options->apiKey = get_option("yupdates_bitly_apiKey"); 
		$options->login = get_option("yupdates_bitly_login"); 
		
		return $options;
	}
	
	function yupdates_bitly_shorten($permalink, $apiKey, $login) {
		$params = array();
		$params["apiKey"] = $apiKey;
		$params["login"] = $login;
		$params["longUrl"] = $permalink;
		$params["version"] = "2.0.1";
		$params["history"] = "1";

		$base_url = "http://api.bit.ly/shorten";

		$http = YahooCurl::fetch($base_url, $params);
		$rsp = $http["response_body"];
		$data = json_decode($rsp);

		if($data && $data->statusCode == "OK" && $data->results) {
			$results = get_object_vars($data->results);
			$site = $results[$permalink];
			$shortUrl = $site->shortUrl;
			return $shortUrl;
		} else {
			return $permalink;
		}
	}

	function yupdates_close_popup() {
?>
<script type="text/javascript">
	window.close();
</script>
<?php
	}
?>