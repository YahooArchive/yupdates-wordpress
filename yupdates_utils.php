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
 *   FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 *   AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 *   LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 *   OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 *   THE SOFTWARE.
 **/

	function yupdates_has_session($application, $session_store) {
		if($session_store->hasAccessToken()) 
		{
			$access_token = $session_store->fetchAccessToken();
			// $application->token = $access_token;
			$access_token = $application->getAccessToken($access_token);
			$session_store->storeAccessToken($access_token);
			
			return ($application->token && $application->token->key);
		} 
		else if($session_store->hasRequestToken()) 
		{
			$request_token = $session_store->fetchRequestToken();
			
			if(array_key_exists("oauth_token", $_REQUEST) && array_key_exists("oauth_verifier", $_REQUEST)) {
				$oauth_verifier = $_REQUEST["oauth_verifier"];
				$access_token = $application->getAccessToken($request_token, $oauth_verifier);
				
				if($access_token->key && $access_token->secret) {
					$session_store->clearRequestToken();
					$session_store->storeAccessToken($access_token);
					return TRUE;
				}
			}
			
			return FALSE;
		}
		else 
		{
			$callback_params = array("auth_popup"=>"true");
			$callback = yupdates_get_oauthCallback($callback_params);
			$request_token = $application->getRequestToken($callback);
			
			$session_store->storeRequestToken($request_token);
			
			return FALSE;
		}
	}
	
	function yupdates_clear_session() {
		$session_store = yupdates_get_sessionStore();
		
		$session_store->clearRequestToken();
        $session_store->clearAccessToken();
		
		// todo: infinite looping
		header(sprintf("Location: %s", $_SERVER["REQUEST_URI"]));
        exit();
	}
	
	function yupdates_get_oauthCallback($parameters) {
		return sprintf("http://%s%s&%s",$_SERVER["HTTP_HOST"], $_SERVER["REQUEST_URI"], http_build_query($parameters));
	} 
	
	function yupdates_get_sessionStore() {
		if(!$yupdates_session_store) {
			global $current_user;
	    	get_currentuserinfo();
			
			$user = $current_user->user_login;
			$ck = get_option("yupdates_consumer_key");
			
			$yupdates_session_store = new WordPressSessionStore($user, $ck);
		}
		return $yupdates_session_store;
	}

	function yupdates_close_popup() {
?>
<script type="text/javascript">
	window.close();
</script>
<?php
	}
?>