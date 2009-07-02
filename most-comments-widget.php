<?php
/**
 * Plugin Name: Most Comments Widget
 * Plugin URI: http://jamessocol.com/projects/most-comments-widget.php
 * Description: Adds a widget that shows the posts with the most comments.
 * Version: 1.1.0
 * Author: James Socol
 * Author URI: http://jamessocol.com/
 */

/*  Copyright 2008  James Socol  (email : me@jamessocol.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

$js_mcw_domain = 'most-comments-widget';

function js_most_comments_widget ( $argv )
{
    global $wpdb, $js_mcw_domain;
    extract($argv);
    $options = get_option('js_most_comments_widget');
    $title = $options['title'] ? $options['title'] : __('default_title',$js_mcw_domain);
    $count = ctype_digit($options['count']) ? $options['count'] : 5;
    $posts = $wpdb->get_results("SELECT ID, post_title, comment_count FROM $wpdb->posts ORDER BY comment_count DESC LIMIT $count;");

?>
    <?php echo $before_widget; ?>
        <?php echo $before_title.$title.$after_title; ?>
        <ul>
<?php
	foreach($posts as $post) {
		$id = $post->ID;
		$title = $post->post_title;
		$count = $post->comment_count;
		echo '<li><a href="' . get_permalink($id) . '">' . $title . '</a> (' . $count . ')</li>';
	}
?>
        </ul>
    <?php echo $after_widget; ?>
<?php
}

function js_most_comments_widget_control ()
{
	global $js_mcw_domain;
	
    $options = $newoptions = get_option('js_most_comments_widget');
    if ( $_POST['most-comments-submit'] ) {
        $newoptions['title'] = strip_tags(stripslashes($_POST['most-comments-title']));
        $newoptions['count'] = preg_replace('/\D/', '', $_POST['most-comments-count']);
    }

    if ( $newoptions['count'] > 15 ) $newoptions['count'] = 15;

    if ( $options != $newoptions ) {
        $options = $newoptions;
        update_option('js_most_comments_widget', $options);
    }
    $title = $options['title'];
    $count = $options['count'];
?>
		<p><label for="most-comments-title"><?php _e('form_title',$js_mcw_domain); ?> <input type="text" id="most-comments-title" name="most-comments-title" value="<?php echo $title; ?>" /></label></p>
		<p><label for="most-comments-count"><?php _e('form_posts'); ?><input type="text" id="most-comments-count" name="most-comments-count" value="<?php echo $count; ?>" size="3" /> <?php printf(__('form_max'),15); ?></label></p>
		<input type="hidden" id="most-comments-submit" name="most-comments-submit" value="1" />
<?php
}

function js_most_comments_widget_init ()
{
	global $js_mcw_domain;
	$plugin_dir = basename(dirname(__FILE__));
	load_plugin_textdomain($js_mcw_domain, 'wp-content/plugins/'.$plugin_dir.'/languages', $plugin_dir.'/languages');
	
	if ( function_exists('wp_register_sidebar_widget') ) {
		wp_register_sidebar_widget('most-comments-widget', __('widget_name',$js_bsw_domain), 'js_most_comments_widget', array('description'=>__('widget_description',$js_bsw_domain)), 'js_most_comments_widget');
		wp_register_widget_control('most-comments-widget', __('widget_name',$js_bsw_domain), 'js_most_comments_widget_control', array('description'=>__('widget_description',$js_bsw_domain)));
	} else if ( function_exists('register_sidebar_widget') ) {
        register_sidebar_widget(__('widget_name',$js_mcw_domain), "js_most_comments_widget");
        register_widget_control(__('widget_name',$js_mcw_domain), 'js_most_comments_widget_control');
    }
}

add_action('widgets_init', 'js_most_comments_widget_init');
