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

define('YUPDATES_DEFAULT_TITLE_TEMPLATE', "posted '#blog_title' on their WordPress blog '#blog_name'");

function yupdates_plugin_options() {
   $session = yupdates_get_session();

   // oauth keys
   $consumer_key = $session->application->consumer_key;
   $consumer_secret = $session->application->consumer_secret;
   $appid = $session->application->application_id;
   
   $has_application = ($consumer_key && $consumer_secret && $appid);

   // extAuth options
   $extAuth_host = $_SERVER["HTTP_HOST"];
   $extAuth_application_url = get_bloginfo('wpurl');
   $extAuth_title = get_bloginfo('name');
   $extAuth_description = get_bloginfo('description');
   $extAuth_third_party = $extAuth_host;
   $extAuth_scopes = 'yurw';
   $extAuth_return_to_url = sprintf("%s/plugins/yupdates_wordpress/yupdates_application.php", WP_CONTENT_URL);
   $extAuth_favicon_url = sprintf("http://%s/favicon.ico", $extAuth_host);

   // blog options
   $title_template_opt = get_option('yupdates_title_template');
   $title_template = ($title_template_opt) ? $title_template_opt : YUPDATES_DEFAULT_TITLE_TEMPLATE;
   $bitly_key = get_option("yupdates_bitly_apiKey");
   $bitly_login = get_option("yupdates_bitly_login");
?>

<style type="text/css">
   .authTitle {text-transform: uppercase;}
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
      <p>Hey, it looks like you've already set up your blog with Yahoo! Updates. Awesome! <a onclick="switchDisplay('yupdates_app_setup');" title="Switch the Menu">Here's the form</a> in case you need it again.</p>
      <div id="yupdates_app_setup" style="display:none;">
<? endif; ?>
      
      <form method="post" action="http://soldsomeheat-vm0.corp.yahoo.com/projects/extAuth" id="yahoo_extAuthForm" name="yahoo_extAuthForm" target="yahoo_extAuthWindow">
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
               <td><input type="text" size="35" name="favicon" value="<?php echo $extAuth_favicon_url; ?>" /></td>
            </tr>
         </table>
         <input type="hidden" name="third_party" value="<?php echo $extAuth_third_party; ?>"/>
         <input type="hidden" name="return_to" value="<?php echo $extAuth_return_to_url; ?>"/>
         <input type="hidden" name="scopes" value="<?php echo $extAuth_scopes; ?>"/>
         <input type="hidden" name="application_url" value="<?php echo $extAuth_application_url ?>">
         <input type="hidden" name="domain" value="<?php echo $extAuth_host ?>">
		
         <p id="createApp" class="submit"><input type="submit" name="Submit" value="<?php _e('Create Application') ?>"/></p>
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
                  <li><small>"#blog_title" = the title of your blog post</small></li>
                  <li><small>"#blog_name" = the name of your blog (i.e. "<?php bloginfo('name'); ?>")</small></li>
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
<script type="text/javascript">
<!--
function yupdates_setCredentials(consumer_key, consumer_secret, application_id) {
	_gel('yupdates_consumer_key').value = consumer_key;
	_gel('yupdates_consumer_secret').value = consumer_secret;
	_gel('yupdates_application_id').value = application_id;
		
	var updated = document.createElement('div');
   updated.className = "updated fade";
   
   if(consumer_key && consumer_secret && application_id) {
     updated.innerHTML = "<p><strong>Thanks! Click '<?php _e('Save Changes'); ?>' below to continue.</strong></p>";
   } else {
     updated.innerHTML = "<p><strong>Uh oh. Missing required keys from extAuth response.";
     updated.innerHTML+= "consumer_key = '"+consumer_key+"', consumer_secret = '"+consumer_secret+"', application_id = '"+application_id+"'</strong></p>";
   }
   
   _gel('createApp').appendChild(updated);
}

function _gel(id) {
   return document.getElementById(id);
}

function switchDisplay(obj) {
	var el = document.getElementById(obj);
	el.style.display = (el.style.display != "none") ? 'none' : '';
}
//-->
</script>
<?
}
?>
