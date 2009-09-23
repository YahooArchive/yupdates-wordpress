<?php
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