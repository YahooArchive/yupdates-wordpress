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

function yupdates_plugin_options() {
	$ck = get_option('yupdates_consumer_key');
	$cks = get_option('yupdates_consumer_secret');
	$appid = get_option('yupdates_application_id');
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
<div class="wrap">
	<h3 class="authTitle">Get a Yahoo! API Key</h3>
	<p>1. Go to the <a href="https://developer.apps.yahoo.com/dashboard/createKey.html" target="_new">Yahoo! Developer Network </a>to register for an API key and complete the form with the following information:</p>
	<p><img src="<?php echo get_option('siteurl'); ?>/wp-content/plugins/yupdates-wordpress/images/auth_step1_ydn.png" width="632" height="220"></p>
	
	<p class="authStep"><strong>Select access to private user data.</strong></p>
	<p><img src="<?php echo get_option('siteurl'); ?>/wp-content/plugins/yupdates-wordpress/images/auth_step2_scopes.png"></p>

	<p class="authStep"><strong>3. Select Read/Write access to Yahoo! Updates</strong></td>
	<p><img src="<?php echo get_option('siteurl'); ?>/wp-content/plugins/yupdates-wordpress/images/auth_step3_read-write.png"></p>

	<p class="authStep"><strong>4. Agree to the Yahoo! Terms of Use and click Get API Key.</strong></p>
	<p><img src="<?php echo get_option('siteurl'); ?>/wp-content/plugins/yupdates-wordpress/images/auth_step4_apikey.png"></p>

	<p class="authStep"><strong>5. If you haven't already done so, verify 
		ownership of your domain to Yahoo! by creating a file of a specific 
		name and uploading that to the root directory of your domain.</strong></p>
	<p><img src="<?php echo get_option('siteurl'); ?>/wp-content/plugins/yupdates-wordpress/images/auth_step5_domain.png"></p>
	
	<p class="authStep"><strong>6. Once you've successfully created your API key, copy your authentication information to the Plugin Settings below:</strong></p>
	<p><img src="<?php echo get_option('siteurl'); ?>/wp-content/plugins/yupdates-wordpress/images/auth_step6_success.png" width="743" height="413"></p>

	<h3 class="authTitle">Yahoo! Plugin Settings</h3>
    <form method="post" action="options.php">
	<?php 
        if(function_exists("wp_nonce_field")) {
            wp_nonce_field('update-options'); 
        }
	?>
        <table class="form-table">
            <tr valign="top">
                <th scope="row">Yahoo! Consumer Key</th>
                <td><input type="text" size=64 name="yupdates_consumer_key" value="<?php echo $ck; ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Yahoo! Consumer Secret</th>
                <td><input type="text" size=64 name="yupdates_consumer_secret" value="<?php echo $cks; ?>" /></td>
            </tr>
            <tr valign="top">
                <th scope="row">Yahoo! Application ID</th>
                <td><input type="text" size=20 name="yupdates_application_id" value="<?php echo $appid; ?>" /></td>
            </tr>
            <tr valign="top">
				<th scope="row">Customize your Yahoo! Updates stream:</th>
				<td><p>&lt;Your Yahoo! name&gt;<input type="text" size=50 name="yupdates_title_template" value="<?php echo $title_template; ?>" /></p></td>
			</tr>
        </table>
        <input type="hidden" name="action" value="update" />
        <input type="hidden" name="page_options" value="yupdates_consumer_key,yupdates_consumer_secret,yupdates_application_id,yupdates_title_template" />
        <p class="submit">
            <input type="submit" name="Submit" value="<?php _e('Save Changes') ?>" />
        </p>
    </form>
</div>
<?
}
?>
