<?php
	class WordPressSessionStore {
	    var $optionName = NULL;
	    var $option = NULL;
		var $consumerKey = "";

	    function WordPressSessionStore($user, $consumerKey) {
	        $this->optionName = sprintf("yupdates_tokens_%s", $user);
            $this->consumerKey = $consumerKey;
            $this->option = get_option($this->optionName);
            
            if(is_bool($this->option) && !$this->option) {
               $this->resetOption();
            }
            
            $this->validateConsumerKey();
	    }
	
	    function resetOption()
	    {
            $this->option = array("rt" => NULL, "at" => NULL, "ck" => $this->consumerKey);
            update_option($this->optionName, $this->option);
	    }
	
        function validateConsumerKey()
        {
            if($this->consumerKey != $this->option["ck"]) {
               $this->resetOption();
            }
        }

	    function hasRequestToken() {
	        return array_key_exists("rt", $this->option) && !is_null($this->option["rt"]);
	    }

	    function hasAccessToken() {
	        return array_key_exists("at", $this->option) && !is_null($this->option["at"]);
	    }

	    function storeRequestToken($token) {
	        $this->option["rt"] = base64_encode($token->to_string());
	        return update_option($this->optionName, $this->option);
	    }
	
		function storeAccessToken($token) {
	        $this->option["at"] = base64_encode($token->to_string());
	        return update_option($this->optionName, $this->option);
	    }

	    function fetchRequestToken() {
			$token_data = base64_decode($this->option["rt"]);
	        return YahooOAuthRequestToken::from_string($token_data);
	    }
	
		function fetchAccessToken() {
			$token_data = base64_decode($this->option["at"]);
	        return YahooOAuthAccessToken::from_string($token_data);
	    }

	    function clearRequestToken() {
	        $this->option["rt"] = NULL;
	        return update_option($this->optionName, $this->option);
	    }
		
	    function clearAccessToken() {
	        $this->option["at"] = NULL;
	        return update_option($this->optionName, $this->option);
	    }
	}
?>
