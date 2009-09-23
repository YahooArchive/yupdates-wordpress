<?php

function yupdates_delete_post($postid) {

}

function yupdates_edit_post($postid) {

}

function yupdates_publish_post($postid) {
   	$session_store = yupdates_get_sessionStore();
	
	$app_info = yupdatesdb_getApplicationInfo();
	
	// fetch application keys from user options
    $ck = get_option("yupdates_consumer_key");
    $cks = get_option("yupdates_consumer_secret");
    $appid = get_option("yupdates_application_id");
    $title_template = get_option("yupdates_title_template");

	$application = new YahooOAuthApplication($ck, $cks, $appid);
	$application_has_session = yupdates_has_session($application, $session_store);
	
    if($application_has_session) {
		$post = get_post($postid);
		
		$title_patterns = array('/#blog_title/', '/#blog_name/');
		$title_replacements = array($post->post_title,get_bloginfo("name"));
		
		$update = new stdclass();
		$update->title = preg_replace($title_patterns, $title_replacements, $title_template);
		$update->description = substr($post->post_content, 0, 256);
		$update->link = get_bloginfo("url");	
		
		$response = $application->insertUpdate(null, $update->description, $update->title, $update->link);
		
		// todo: better error handling
        if(is_null($response)) {
            error_log("Failed to generate Yahoo! Update for blog post.");
        }
    } else {
		error_log('no session available');
	}
}

?>
