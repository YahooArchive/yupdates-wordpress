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
   $post_status = $_POST['post_status'];
   $original_post_status = $_POST['original_post_status'];
   
   // Secret Hint: if you want to publish a *new* update whenever you update a blog post, 
   // set this var below to FALSE;
   $block_update_publish = TRUE;
   
   if($block_update_publish && $post_status == 'publish' && $original_post_status != 'publish') {
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

      $update = new stdclass();
      $update->title = preg_replace($title_patterns, $title_replacements, $title_template);
      $update->description = substr($post->post_excerpt, 0, 256);
      $update->link = $permalink;

   	$suid = yupdates_insertUpdate($update);

   	return $suid;
   }
}
?>
