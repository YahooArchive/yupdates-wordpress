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
 * @author     Zach Graves <zachg@yahoo-incnc.com>
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

function yupdates_delete_post($postid) 
{
	// TODO
}

function yupdates_edit_post($postid) 
{
	// TODO
}

function yupdates_publish_post($postid) 
{
   $session = yupdates_get_session();
	
   if($session->hasSession) {
      $post = get_post($postid);
      $permalink = get_permalink($postid);
		
      $bitly_options = yupdates_get_bitly_options();
      if($bitly_options->apiKey && $bitly_options->login) {
         $bitly_permalink = yupdates_bitly_shorten($permalink, $bitly_options->apiKey, $bitly_options->login);
         $permalink = $bitly_permalink;
      }
      
      $title_template = get_option("yupdates_title_template");
      $title_patterns = array('/%blog_title%/', '/%blog_name%/');
      $title_replacements = array($post->post_title, get_bloginfo("name"));
      
      // $rsp = $session->application->insertUpdate(null, $update->description, $update->title, $update->link);
      
      $update = new stdclass();
      $update->title = preg_replace($title_patterns, $title_replacements, $title_template);
      $update->description = substr($post->post_excerpt, 0, 256);
      $update->link = $permalink;
		
		// do this temporarily until we have the PHP5 SDK using YQL exclusively. 
		$update->pubDate = time();
		$update->guid = $session->application->token->yahoo_guid();
		$update->source = 'APP.'.$session->application->application_id;
		
		$suid = sha1(json_encode($update));
		
      $query = "INSERT INTO social.updates (guid, title, description, link, pubDate, source, suid) VALUES (\"%s\", \"%s\", \"%s\", \"%s\", \"%s\", \"%s\", \"%s\");";
      $query = sprintf($query, 
         $update->guid, 
         $update->title, 
         $update->description, 
         $update->link, 
         $update->pubDate, 
         $update->source, 
         $suid
      );
      
      $rsp = $session->application->yql($query, array(), 'PUT');
		
		if(isset($rsp->error)) {
         error_log("Failed to generate Yahoo! Update for blog post ({$postid}) ".json_encode($update));
      }
      
      return $suid;
   }
}
?>
