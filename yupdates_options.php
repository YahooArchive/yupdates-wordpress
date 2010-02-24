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
 * @author     Ryan Kennedy, 
 * @author     Lawrence Morrisroe <lem@yahoo-inc.com>
 * @author     Zach Graves <zachg@yahoo-inc.com>
 * @author     Micah Laaker <micahl@yahoo-inc.com>
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

// Pre-2.6 compatibility
if (!defined( 'WP_CONTENT_URL')) define('WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if (!defined( 'WP_CONTENT_DIR')) define('WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if (!defined( 'WP_PLUGIN_URL'))  define('WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if (!defined( 'WP_PLUGIN_DIR'))  define('WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );

define('YUPDATES_DEFAULT_TITLE_TEMPLATE', "posted '%blog_title%' on their WordPress blog '%blog_name%'");
define('YUPDATES_EXTAUTH_HOST', "http://developer.apps.yahoo.com/projects/createconsumerkey");
define('YUPDATES_EXTAUTH_DEFAULT_SCOPES', "yurw");

function yupdates_plugin_options() {
   $session = yupdates_get_session();

   // oauth keys
   $consumer_key = $session->application->consumer_key;
   $consumer_secret = $session->application->consumer_secret;
   $appid = $session->application->application_id;
   
   $has_application = ($consumer_key && $consumer_secret && $appid);

   // extAuth application options
   $extAuth_host = $_SERVER["HTTP_HOST"];
   $extAuth_application_url = get_bloginfo('wpurl');
   $extAuth_title = get_bloginfo('name');
   $extAuth_description = get_bloginfo('description');
   $extAuth_third_party = $extAuth_host;
   $extAuth_scopes = YUPDATES_EXTAUTH_DEFAULT_SCOPES;
   $extAuth_favicon_url = sprintf("http://%s/favicon.ico", $extAuth_host);
   
   // Important note:
   // If the plugin source files are located in a different directory than listed above, 
   // you'll need to set the correct path to yupdates_application.php
   $extAuth_return_to_url = sprintf("%s/plugins/yahoo-updates-for-wordpress/yupdates_application.php", WP_CONTENT_URL);

   // blog options
   $title_template_opt = get_option('yupdates_title_template');
   $title_template = ($title_template_opt) ? $title_template_opt : YUPDATES_DEFAULT_TITLE_TEMPLATE;
   $bitly_key = get_option("yupdates_bitly_apiKey");
   $bitly_login = get_option("yupdates_bitly_login");
?>

<style type="text/css">
   .authTitle {text-transform: uppercase;}
   .hidden-node {display:none;}
</style>

<div class="wrap">
   <a name="application_settings"></a>
   <h3 class="authTitle">Connect your blog with Yahoo! Updates</h3>
   
<?php if($has_application == FALSE): ?>
      <p>In order to get started, you'll need to create a new Yahoo! Application and later authorize it to access your Yahoo! account. 
         (This is needed so your WordPress blog can read and write updates on your behalf without you revealing your Yahoo! ID and password).</p>
      <p>We've filled in the required fields below, click 'Create Application' below to submit.</p>
      <div id="yupdates_app_setup">
<? else: ?>
      <p>Hey, it looks like you've already set up your blog with Yahoo! Updates, awesome! <a id="switchForm" title="Switch the Menu">Here's the form</a> if you'd like to update the application.</p>
      <div id="yupdates_app_setup" style="display:none;">
<? endif; ?>
      
      <form method="POST" action="<?php echo YUPDATES_EXTAUTH_HOST; ?>" id="yahoo_extAuthForm" name="yahoo_extAuthForm" target="yahoo_extAuthWindow">
         <table class="form-table">
            <tr valign="top">
               <th scope="row">Blog Name</th>
               <td><input type="text" size="64" name="name" value="<?php echo $extAuth_title; ?>" /></td>
            </tr>
            <tr valign="top">
               <th scope="row">Blog Description</th>
               <td><input type="text" size="64" name="description" value="<?php echo $extAuth_description; ?>" /></td>
            </tr>
            <tr valign="top">
               <th scope="row">Favicon URL</th>
               <td><input type="text" size="35" name="favicon" value="<?php echo $extAuth_favicon_url; ?>" />
                <br/><small>A valid favicon URL is required.</small></td>
            </tr>
         </table>
         
         <input type="hidden" name="third_party" value="<?php echo $extAuth_third_party; ?>"/>
         <input type="hidden" name="return_to" value="<?php echo $extAuth_return_to_url; ?>"/>
         <input type="hidden" name="scopes" value="<?php echo $extAuth_scopes; ?>"/>
         <input type="hidden" name="application_url" value="<?php echo $extAuth_application_url ?>">
         <input type="hidden" name="domain" value="<?php echo $extAuth_host ?>">
         <input type="hidden" name="appid" value="<?php echo $appid; ?>"/>
         
         <input type="hidden" name="debug" value="true"/>
		
         <p id="createApp" class="submit"><input id="authSubmit" type="submit" name="Submit" value="<?php ($appid) ? _e('Update Application') : _e('Create Application') ?>"/></p>
      </form>
   </div>
   
   <hr noshade="noshade" />
   <a name="settings"></a>
   
   <h3 class="authTitle">Yahoo! Updates Settings</h3>
	<form method="post" action="options.php">
      <table class="form-table">
         <tr valign="top">
            <th scope="row">Customize your Yahoo! Updates event display:</th>
            <td>&lt;Your Yahoo! name&gt;<input type="text" size="50" name="yupdates_title_template" value="<?php echo $title_template; ?>" />
               <br /><small>Use the following tags in the display field above:</small><br />
               <ul>
                  <li><small>"%blog_title%" = the title of your blog post</small></li>
                  <li><small>"%blog_name%" = the name of your blog (i.e. "<?php echo $extAuth_title; ?>")</small></li>
               </ul>
            </td>
         </tr>
      </table>
      
      <hr noshade="noshade" />
		
      <h3 class="authTitle">bit.ly Settings (optional)</h3>
      <p>Configure your <a href="http://bit.ly/account/">bit.ly account</a>. This allows us to shorten the link 
         back to your blog posts in the update, and allows you to track clicks in <a href="http://bit.ly/app/history/">your history</a>:</p>
      
<?php if($bitly_key && $bitly_login): ?>
         <br/>To stop using bit.ly, just remove your credentials below and save.
<?php endif; ?>
      
      <table class="form-table">
         <tr valign="top">
            <th scope="row">bit.ly API Key</th>
            <td><input type="text" size="64" name="yupdates_bitly_apiKey" value="<?php echo $bitly_key; ?>" /></td>
         </tr>
         <tr valign="top">
            <th scope="row">bit.ly Username</th>
            <td><input type="text" size="20" name="yupdates_bitly_login" value="<?php echo $bitly_login; ?>" /></td>
         </tr>
      </table>
		
      <input type="hidden" id="yupdates_consumer_key" name="yupdates_consumer_key" value="<?php echo $consumer_key; ?>"/>
      <input type="hidden" id="yupdates_consumer_secret" name="yupdates_consumer_secret" value="<?php echo $consumer_secret; ?>"/>
      <input type="hidden" id="yupdates_application_id" name="yupdates_application_id" value="<?php echo $appid; ?>"/>
      <input type="hidden" name="action" value="update" />
      <input type="hidden" name="page_options" value="yupdates_consumer_key,yupdates_consumer_secret,yupdates_application_id,yupdates_title_template,yupdates_bitly_apiKey,yupdates_bitly_login" />
      <?php if(function_exists("wp_nonce_field")) wp_nonce_field('update-options'); ?>
		
      <p class="submit"><input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" class="button-primary"/></p>
   </form>
</div>
<script src="http://yui.yahooapis.com/3.0.0/build/yui/yui-min.js"></script>
<script type="text/javascript">
var _Y = null;

<!--
function yupdates_setCredentials(consumer_key, consumer_secret, application_id) {
   if(_Y) _Y.fire('yupdates:handleSetCredentials', consumer_key, consumer_secret, application_id);
   
   return true;
}

function _gel(id) {
   return document.getElementById(id);
}

YUI().use('node', function(Y) {
   _Y = Y;
   Y.on('click', function(event){
      var form = Y.one('#yahoo_extAuthForm');
      var formTarget = form.getAttribute('target');
      window.open('', formTarget, 'status=0,toolbar=0,location=0,menubar=0,width=545,height=650');
      document.yahoo_extAuthForm.submit();
   }, '#authSubmit');
   
   Y.on('click', function(event){
      var node = Y.one('#yupdates_app_setup');
      var display = (node.getStyle('display') == 'none') ? '' : 'none';
      node.setStyle('display', display);
   }, '#switchForm');
   
   Y.on('yupdates:handleSetCredentials', function(key, secret, appid) {
      Y.one('#yupdates_consumer_key').set('value', key);
      Y.one('#yupdates_consumer_secret').set('value', secret);
      Y.one('#yupdates_application_id').set('value', appid);
      
      var updated = Y.Node.create('<div>').addClass('updated fade');
      if(key && secret && appid) {
         updated.setContent("<p><strong>Thanks! Click '<?php _e('Save Changes'); ?>' below to continue.</strong></p>");
      } else {
         var content = "<p><strong>Missing required keys from response.";
         content += "consumer_key = '"+key+"', consumer_secret = '"+secret+"', application_id = '"+appid+"'</strong></p>";
         updated.setContent(content);
      }
      
      Y.one('#createApp').append(updated);
   });
});
//-->
</script>
<?
}
?>
