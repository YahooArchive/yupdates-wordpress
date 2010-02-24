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

class WordPressSessionStore {
   var $optionName = NULL;
   var $option = NULL;
   var $consumerKey = "";

   function WordPressSessionStore($user, $consumerKey) 
   {
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
         return false;
      }

      return true;
   }

   function hasRequestToken() 
   {
      return array_key_exists("rt", $this->option) && !is_null($this->option["rt"]);
   }

   function hasAccessToken() 
   {
      return array_key_exists("at", $this->option) && !is_null($this->option["at"]);
   }

   function storeRequestToken($token) 
   {
      $this->option["rt"] = base64_encode($token->to_string());
      return update_option($this->optionName, $this->option);
   }

   function storeAccessToken($token) 
   {
      $this->option["at"] = base64_encode($token->to_string());
      return update_option($this->optionName, $this->option);
   }

   function fetchRequestToken() 
   {
      $token_data = base64_decode($this->option["rt"]);
      return YahooOAuthRequestToken::from_string($token_data);
   }

   function fetchAccessToken() 
   {
      $token_data = base64_decode($this->option["at"]);
      return YahooOAuthAccessToken::from_string($token_data);
   }
   
   function clearRequestToken() 
   {
      $this->option["rt"] = NULL;
      return update_option($this->optionName, $this->option);
   }

   function clearAccessToken() 
   {
      $this->option["at"] = NULL;
      return update_option($this->optionName, $this->option);
   }
}
?>
