<?php
/*
Plugin Name: Mark Unread Comments
Version: 0.2
Plugin URI: http://www.26horas.com/open-source/wordpress/plugins/mark-unread-comments/
Description: Controls the visits of the user on an cookie based system and marks the new comments appeared after the last visit to the post with the 'unread' css class. 
Author: Pablo Fernandez
Author URI: http://blog.26horas.com/
*/

// execute cookies stuff before output!
add_action('template_redirect', 'muc_set_cookie', 1);
// Add the action to every comment
add_filter('comment_class', 'muc_comment_class', 10);

// Updates the cookie when an user reads a post
function muc_set_cookie() {
	global $wp_query, $muc_posts;
	$db =& $GLOBALS['wpdb'];
	if (is_single()) {
		$post_id = $wp_query->post->ID;
		$time = time();
		$timeout = 6*30*24*60*60; // Timeout of the cookie. Set to 6 months. Set it to whatever you want
		$cookie = (!$_COOKIE['muc_posts']) ? array() : $_COOKIE['muc_posts'];
		$muc_posts = unserialize($cookie);
		$muc_cookie_posts = $muc_posts;
		$muc_cookie_posts[$post_id] = $time;
		$cookiepath = parse_url(get_bloginfo('wpurl'));
		if ($cookiepath['path'] == "") {
			$cookiepath['path'] = "/";
		}
		setcookie("muc_posts", serialize($muc_cookie_posts), $time+$timeout, $cookiepath['path']);
	}
}

// Adds the unread class to every matched comment
function muc_comment_class($classes = array()) {
	global $comment, $muc_posts;
	
	$comment_id = $comment->comment_ID;
	$post_id = $comment->comment_post_ID;
	$comment_time = strtotime($comment->comment_date);
	
	if (isset($muc_posts[$post_id])) {
		if ($comment_time > $muc_posts[$post_id]) {
			$classes [] = 'unread';
		}
	}
	
	return $classes;
}

?>
