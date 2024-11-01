<?php
/*
Plugin Name: TwitterRemote Widget
Description: This plugin makes it easy to add the TwitterRemote widget to your blog
Author: Jules Stuifbergen
Plugin URI: http://forwardslash.nl/twitterremote-wordpress-plugin/
Author: Jules Stuifbergen
Version: 1.0
Author URI: http://forwardslash.nl/
License: GPL

*/

/*
Based on the Weather Undergound Widget - http://dropdeaddick.com/2006/weather-widget/
*/

function widget_twitterremote_init() {
 
if ( !function_exists('register_sidebar_widget') )
		return;
		
function widget_twitterremote($args) {
		extract($args);
		$defaults = array('title' => '', 'textcolor' => '#222200', 'usercolor' => '#FF3300', 'username' => 'theremote', 'width' => '200', 'ntoshow' => '6' );
		$options = (array) get_option('widget_twitterremote');
		
		foreach ( $defaults as $key => $value )
			if ( !isset($options[$key]) )
				$options[$key] = $defaults[$key];
				
		echo $before_widget . $before_title;
		echo $options['title'];
		echo $after_title;
		echo "<br />"; 
		
		$username = $options['username'];
		if (!$username) { $username = 'twitter'; };
		$w        = $options['width'];
		if ($w < 180) { $w = '180'; };
		$uc       = $options['usercolor'];
		if (!$uc) { $uc = '#FF3300'; };
		$tc       = $options['textcolor'];
		if (!$tc) { $tc = '#222200'; };
		$n        = $options['ntoshow'];
		if (!$n)  { $n  = '6'; };
		$id       = $options['id'];

		$title = $options['title'];

		echo "<!-- twitterremote plugin by Jules Stuifbergen http://forwardslash.nl/twitterremote-wordpress-plugin/ --><script type=\"text/javascript\" language=\"javascript\" src=\"http://twittercounter.com/remote/?username_owner=$username&users_id=$id&width=$w&nr_show=$n&hr_color=$tc&a_color=$uc\"></script>";

		echo $after_widget;
}
	
// fetch the twittercounter ID using the api (and the name you provided)
function twitterremote_fetch_id($u) {
        if ($u == "none") { return false; };
        $ch = curl_init();
        $timeout = 15; 
        curl_setopt ($ch, CURLOPT_URL, "http://twittercounter.com/api/?username=$u&output=php");
        curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        $content = curl_exec($ch);
        curl_close($ch);
        if ($data = unserialize($content) ) {
                $id = $data['user_id'];
                if ($id) {
                        return "$id";
                }
        }
        return false;
}


function widget_twitterremote_control() {

		$options = get_option('widget_twitterremote');
		if ( !is_array($options) )
			$options = array('title' => '', 'textcolor' => '#222200', 'usercolor' => '#FF3300', 'username' => 'none', 'width' => '200', 'id' => '', 'ntoshow' => '6');
		if ( $_POST['twitterremote-submit'] ) {

			$options['title'] = strip_tags(stripslashes($_POST['twitterremote-title']));
			$options['textcolor'] = strip_tags(stripslashes($_POST['twitterremote-textcolor']));
			$options['username'] = strip_tags(stripslashes($_POST['twitterremote-username']));
			$options['id'] = twitterremote_fetch_id($options['username']);
			if (!$options['id']) { $options['username'] = 'error fetching ID' ;  $options['id'] = 1;  };
			$options['usercolor'] = strip_tags(stripslashes($_POST['twitterremote-usercolor']));
			$options['width'] = strip_tags(stripslashes($_POST['twitterremote-width']));
			$options['ntoshow'] = strip_tags(stripslashes($_POST['twitterremote-ntoshow']));
			update_option('widget_twitterremote', $options);
		}
		
		$textcolor = htmlspecialchars($options['textcolor'], ENT_QUOTES);
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$username = htmlspecialchars($options['username'], ENT_QUOTES);
		$usercolor = htmlspecialchars($options['usercolor'], ENT_QUOTES);
		$width = htmlspecialchars($options['width'], ENT_QUOTES);
		$ntoshow = htmlspecialchars($options['ntoshow'], ENT_QUOTES);

		echo '<p style="text-align:right;"><label for="twitterremote-title">Title: <input style="width: 200px;" id="twitterremote-title" name="twitterremote-title" type="text" value="'.$title.'" /></label></p>
		<p style="text-align:right;"><label for="twitterremote-username">Twitter user name: <input style="width: 200px;" id="twitterremote-username" name="twitterremote-username" type="text" value="'.$username.'" /></label></p>
		<p style="text-align:right;"><label for="twitterremote-textcolor">Text color (default #222200): <input style="width: 200px;" id="twitterremote-textcolor" name="twitterremote-textcolor" type="text" value="'.$textcolor.'" /></label></p>
		<p style="text-align:right;"><label for="twitterremote-usercolor">User color (default: #FF3300): <input style="width: 200px;" id="twitterremote-usercolor" name="twitterremote-usercolor" type="text" value="'.$usercolor.'" /></label></p>
		<p style="text-align:right;"><label for="twitterremote-width">Width (min. 180): <input style="width: 200px;" id="twitterremote-width" name="twitterremote-width" type="text" value="'.$width.'" /></label></p>
		<p style="text-align:right;"><label for="twitterremote-ntoshow">Nr. of visitors to show: <input style="width: 200px;" id="twitterremote-ntoshow" name="twitterremote-ntoshow" type="text" value="'.$ntoshow.'" /></label></p>
		<input type="hidden" id="twitterremote-submit" name="twitterremote-submit" value="1" />';
	
	}
	
	register_sidebar_widget('Twitter Remote', 'widget_twitterremote');
	register_widget_control('Twitter Remote', 'widget_twitterremote_control', 300, 265);
}
	add_action('plugins_loaded', 'widget_twitterremote_init');
?>
