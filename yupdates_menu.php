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

function yupdates_menu() {
   global $current_user;
   get_currentuserinfo();
   
   if(array_key_exists("yupdates_updateusers", $_REQUEST)) {
      if($_REQUEST["yupdates_include_updates"]) {
         yupdatesdb_addUpdatesUser($current_user->user_login);
      } else {
         yupdatesdb_removeUpdatesUser($current_user->user_login);
      }
   }
	
   $session = yupdates_get_session();
   $sharingUpdates = false;
	
   if($session->hasSession == false) {
      $request_token = $session->store->fetchRequestToken();
      $auth_url = ($request_token && $request_token->key) ? $session->application->getAuthorizationUrl($request_token) : "";
   } else {
      $sharingUpdates = yupdatesdb_isUpdatesUser($current_user->user_login);
   }
?>

<div class="wrap">
    <h2>Yahoo! Updates</h2>

<?php 
   if($session->application && $session->hasSession) { 
      echo <<<HTML
You have already authorized the Yahoo! Updates plugin.
<form method="post">
HTML;
      
      if(YUPDATES_WIDGET_ENABLED) { 
			$checked = $sharingUpdates ? "checked='checked'" : "";
			echo <<<HTML
<p><label for="yupdates-include-updates">Include updates in widget? <input id="yupdates-include-updates" type="checkbox" name="yupdates_include_updates" $checked></label></p>
<input type="submit" name="yupdates_updateusers" value="Update">
HTML;
		} 
		
      echo <<<HTML
<input type="submit" name="yupdates_clearauthorization" value="Unauthorize"></form>	
HTML;
   } else {
      echo <<<HTML
You have not yet authorized the Yahoo! Updates plugin.
<p><input type="hidden" name="yupdates_authorize" value="true"><input type="submit" value="Authorize" onclick="_yupdates_authorize();"></p>
HTML;
	}
?>
</div>

<script type="text/javascript">
   var _gel = function(el) {return document.getElementById(el)};
   var _yupdates_auth_url = "<?php echo $auth_url; ?>";
   
   function _yupdates_authorize() {
      if(_yupdates_auth_url != "") 
         PopupManager.open(_yupdates_auth_url,600,435);
      else alert("Error: No request token / auth url");
   }
</script>

<script type="text/javascript">
// a simplified version of step2 popuplib.js
var PopupManager = {
	popup_window:null,
	interval:null,
	interval_time:80,
	waitForPopupClose: function() {
		if(PopupManager.isPopupClosed()) {
			PopupManager.destroyPopup();
			window.location.reload();
		}
	},
	destroyPopup: function() {
		this.popup_window = null;
		window.clearInterval(this.interval);
		this.interval = null;
	},
	isPopupClosed: function() {
		return (!this.popup_window || this.popup_window.closed);
	},
	open: function(url, width, height) {
		this.popup_window = window.open(url,"",this.getWindowParams(width,height));
		this.interval = window.setInterval(this.waitForPopupClose, this.interval_time);
		
		return this.popup_window;
	},
	getWindowParams: function(width,height) {
		var center = this.getCenterCoords(width,height);
		return "width="+width+",height="+height+",status=1,location=1,resizable=yes,left="+center.x+",top="+center.y;
	},
	getCenterCoords: function(width,height) {
		var parentPos = this.getParentCoords();
		var parentSize = this.getWindowInnerSize();
		
		var xPos = parentPos.width + Math.max(0, Math.floor((parentSize.width - width) / 2));
		var yPos = parentPos.height + Math.max(0, Math.floor((parentSize.height - height) / 2));
		
		return {x:xPos,y:yPos};
	},
	getWindowInnerSize: function() {
		var w = 0;
		var h = 0;
		
		if ('innerWidth' in window) {
			// For non-IE
			w = window.innerWidth;
			h = window.innerHeight;
		} else {
			// For IE
			var elem = null;
			if (('BackCompat' === window.document.compatMode) && ('body' in window.document)) {
				elem = window.document.body;
			} else if ('documentElement' in window.document) {
				elem = window.document.documentElement;
			}
			if (elem !== null) {
				w = elem.offsetWidth;
				h = elem.offsetHeight;
			}
		}
		return {width:w, height:h};
	},
	getParentCoords: function() {
		var w = 0;
		var h = 0;
		
		if ('screenLeft' in window) {
			// IE-compatible variants
			w = window.screenLeft;
			h = window.screenTop;
		} else if ('screenX' in window) {
			// Firefox-compatible
			w = window.screenX;
			h = window.screenY;
	  	}
		return {width:w, height:h};
	}
}
</script>

<?php
}
?>
