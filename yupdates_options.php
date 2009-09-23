<?php
function yupdates_plugin_options() {
    $ck = get_option('yupdates_consumer_key');
    $cks = get_option('yupdates_consumer_secret');
    $appid = get_option('yupdates_application_id');
	$title_template = get_option('yupdates_title_template');

	if($title_template == "") $title_template = "posted '#blog_title' on their WordPress blog '#blog_name'";
?>
<div class="wrap">
    <h2>Yahoo! Updates Plugin Options</h2>

    <p>In order to use the Yahoo! Updates plugin, you will need to register as an application developer on <a href="http://developer.apps.yahoo.com/dashboard/createKey.html" target="_blank">Yahoo!</a>. When signing up, make sure to select "This app requires access to private user data" and select "Read/Write" for Yahoo! Updates.</p>

    <p>After completing the process, you will be given a consumer key, consumer secret and an application ID. Include those values below and save your changes. When done, visit the <a href="users.php?page=yupdates_menu">Yahoo! Updates Authorization page</a> to authorize the Yahoo! Updates plugin to access your Yahoo! Updates.</p>

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
                <th scope="row">Your Update Title</th>
                <td><input type="text" size=50 name="yupdates_title_template" value="<?php echo $title_template; ?>" /></td>
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
