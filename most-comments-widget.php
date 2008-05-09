<?php
/**
 * Plugin Name: Most Comments Widget
 * Plugin URI: http://jamessocol.com/projects/most-comments-widget.php
 * Description: Adds a widget that shows the posts with the most comments.
 * Version: 1.0.1
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
 
function js_most_comments_widget ( $argv )
{
    global $wpdb;
    extract($argv);
    $options = get_option('js_most_comments_widget');
    $title = $options['title'] ? $options['title'] : 'Most Comments';
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

    echo "\t\t\t".'<li><a href="' . get_permalink($id) . '">' . $title . '</a> (' . $count . ')</li>';

    }
?>
        </ul>
    <?php echo $after_widget; ?>
<?php
}

function js_most_comments_widget_control ()
{
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
        <p><label for="most-comments-title"><?php _e('Title:'); ?> <input type="text" style="width: 250px;" id="most-comments-title" name="most-comments-title" value="<?php echo $title; ?>" /></label></p>
        <p><label for="most-comments-count">Number of posts to show: <input type="text" style="width: 30px;" id="most-comments-count" name="most-comments-count" value="<?php echo $count; ?>" /> (at most 15)</label></p>
        <input type="hidden" id="most-comments-submit" name="most-comments-submit" value="1" />
<?php
}

function js_most_comments_widget_init ()
{
    if ( function_exists('register_sidebar_widget') ) {
        register_sidebar_widget("Most Comments", "js_most_comments_widget");
        register_widget_control("Most Comments", 'js_most_comments_widget_control');
    }
}

add_action('widgets_init', 'js_most_comments_widget_init');

?>