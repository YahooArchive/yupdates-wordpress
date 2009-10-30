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
if ( ! defined( 'WP_CONTENT_URL' ) )
   define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
   define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
   define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
   define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );


function yupdates_plugin_options() {
	$ck = get_option('yupdates_consumer_key');
	$cks = get_option('yupdates_consumer_secret');
	$appid = get_option('yupdates_application_id');
	$bitly_key = get_option("yupdates_bitly_apiKey"); 
	$bitly_login = get_option("yupdates_bitly_login");
	$title_template = get_option('yupdates_title_template');
	
	if($title_template == "") $title_template = "posted '#blog_title' on their WordPress blog '#blog_name'";
?>
<style type="text/css">
	.authTitle {
		text-transform: uppercase;
	}
	.authStep {
		font-weight: bold;
	}
</style>
<script type="text/javascript">
<!--
function switchDisplay(obj) {
	var el = document.getElementById(obj);
	el.style.display = (el.style.display != "none") ? 'none' : '';
}
//-->
</script>
<div class="wrap">
	
	<a name="settings"></a>
	
    <form method="post" action="options.php">
<?php 
	if(function_exists("wp_nonce_field")) {
		wp_nonce_field('update-options'); 
	}
?>
	
	<h3 class="authTitle">Yahoo! API Access Settings</h3>
Enter your API Key, Shared Secret, and App ID from the Yahoo! Developer Network. (These are needed so your WordPress blog can read 
and write data on your behalf without revealing your Yahoo! ID and password). 
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Yahoo! API Key</th>
                <td><input type="text" size="64" name="yupdates_consumer_key" value="<?php echo $ck; ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Yahoo! Shared Secret</th>
                <td><input type="text" size="64" name="yupdates_consumer_secret" value="<?php echo $cks; ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Yahoo! Application ID</th>
                <td><input type="text" size="20" name="yupdates_application_id" value="<?php echo $appid; ?>" /></td>
            </tr>
	</table>

	<p><em>Don't know what these are, or how to get them?</em> <a onclick="switchDisplay('ydnhelp');" title="Switch the Menu">
		Show/hide instructions for how to get a Yahoo! API key.</a> (It's quick and free.)</p>

	<div id="ydnhelp" style="display:none; border: 1px solid #cccccc; margin: 10px; padding: 20px;">

	<h4 class="authTitle">How to get a Yahoo! API Key</h4>
	<ol>
		<li><p>Go to the <a href="https://developer.apps.yahoo.com/dashboard/createKey.html" target="_new"> 
			Yahoo! Developer Network</a> to register for an API key, and complete 
			the form with the following information:</p>
			<ol>
				<li type="a"><strong>Application Name:</strong> Enter "<?php bloginfo('name'); ?>"</li>
				<li type="a"><strong>Kind of Application:</strong> Choose "Web-based"</li>
				<li type="a"><strong>Description:</strong> Describe your site here.</li>
				<li type="a"><strong>Application URL:</strong> Enter "<?php bloginfo('url'); ?>"</li>
				<li type="a"><strong>Favicon URL:</strong> Provide the URL to a GIF, JPG or PNG image (note: ICO is <em>not</em> supported) 
					to serve as the favicon for your blog, if you have one. This will be used if you 
					generate a Yahoo! Update with each blog post.
					<br />
					(e.g. <img src="<?php echo WP_CONTENT_URL; ?>/plugins/yupdates-wordpress/images/favicon.png" width="16" height="16" align="bottom" /> John posted "My First iPhone Experiences" from his blog: Mac User Fans. )
				</li>
				<li type="a"><strong>Application Domain:</strong> Enter "<?php echo sprintf("http://%s", $_SERVER['HTTP_HOST']) ?>"</li>
			</ol>
		 </li>
	
		<li><p class="authStep"><strong>Choose access to private user data.</strong></p>
			<p><img src="<?php echo WP_CONTENT_URL; ?>/plugins/yupdates-wordpress/images/auth_step2_scopes.png"></p>
		</li>
		<li><p class="authStep"><strong>Choose Read/Write access to Yahoo! Updates.</strong></td>
			<p><img src="<?php echo WP_CONTENT_URL; ?>/plugins/yupdates-wordpress/images/auth_step3_read-write.png"></p>
		</li>
		<li><p class="authStep"><strong>Agree to the Yahoo! Terms of Use, and click the "Get API Key" button.</strong></p>
			<p><img src="<?php echo WP_CONTENT_URL; ?>/plugins/yupdates-wordpress/images/auth_step4_apikey.png"></p>
		</li>
		<li><p class="authStep"><strong>If you haven't done so previously, verify 
			ownership of your domain with Yahoo! by creating a file with the specified 
			name and uploading that to the root directory of your domain.</strong></p>
		</li>
		<li><p class="authStep"><strong>Once you've successfully created your API 
			key, copy your API key information from the success screen (sample below) to the Yahoo! API Access Settings below:</strong></p>
			<p><img src="<?php echo WP_CONTENT_URL; ?>/plugins/yupdates-wordpress/images/auth_step6_success.png" width="562" height="233"></p>
		</li>
	</ol>
</div>

		<hr noshade="noshade" />
		<h3 class="authTitle">Yahoo! Updates Settings</h3>
	    <table class="form-table">
           <tr valign="top">
              <th scope="row">Customize your Yahoo! Updates event display:</th>
              <td>
                 <p>&lt;Your Yahoo! name&gt;<input type="text" size="50" name="yupdates_title_template" value="<?php echo $title_template; ?>" />
                 <br />
                 <small>Use the following tags in the display field above:</small>
                 <br />
                 <ul>
                    <li><small>"#blog_title" = the title of your blog post</small></li>
                    <li><small>"#blog_name" = the name of your blog (i.e. "<?php bloginfo('name'); ?>")</small></li>
                 </ul></p>
              </td>
           </tr>
        </table>

		<hr noshade="noshade" />
		<h3 class="authTitle">bit.ly Settings (optional)</h3>
		Configure your <a href="http://bit.ly/account/">bit.ly account</a>. This allows us to shorten the link 
		back to your blog posts in the update, and allows you to track clicks in <a href="http://bit.ly/app/history/">your history</a>:
		<?php if($bitly_key && $bitly_login) { ?>
		<br/>To stop using bit.ly, just remove your credentials below and save.
		<?php } ?>
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

        <input type="hidden" name="action" value="update" />
        <input type="hidden" name="page_options" value="yupdates_consumer_key,yupdates_consumer_secret,yupdates_application_id,yupdates_title_template,yupdates_bitly_apiKey,yupdates_bitly_login" />
        <p class="submit">
            <input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
        </p>
    </form>
</div>
<?
}
?>
