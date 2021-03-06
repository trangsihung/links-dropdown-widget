<?php
/*
Plugin Name: Links Dropdown Widget
Plugin URI: http://sihung.net/plugins/links-dropdown-widget
Description: Display links as dropdown
Version: 1.1
Author: Hung Trang Si
Author URI: http://sihung.net
Author Email: trangsihung@gmail.com
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html
Text Domain: linkdrop

Copyright 2016 Hung Trang Si

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License, version 2, as
published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

class Links_Dropdown_Widget extends WP_Widget {

	/*--------------------------------------------------*/
	/* Constructor
	/*--------------------------------------------------*/

	/**
	 * Specifies the classname and description, instantiates the widget,
	 * loads localization files, and includes necessary stylesheets and JavaScript.
	 */
	public function __construct() {

		// Return Links Manager for Wordpress 3.5 or newer
		add_filter( 'pre_option_link_manager_enabled', '__return_true', 100 );

		parent::__construct(
			'links-dropdown-widget',
			__( 'Links Dropdown', 'linkdrop' ),
			array(
				'classname'		=>	'links-dropdown-widget',
				'description'	=>	__( 'Display links as dropdown', 'linkdrop' )
			)
		);

		add_action( 'wp_enqueue_scripts', array( $this, 'register_widget_styles' ) );

	} // end constructor

	/*--------------------------------------------------*/
	/* Widget API Functions
	/*--------------------------------------------------*/

	/**
	 * Outputs the content of the widget.
	 *
	 * @param	array	args		The array of form elements
	 * @param	array	instance	The current instance of the widget
	 */
	public function widget( $args, $instance ) {

		extract( $args, EXTR_SKIP );

		$title = apply_filters('widget_title', empty( $instance['title'] ) ? __( 'Links' ) : $instance['title'], $instance, $this->id_base);
		$default_option = $instance['default_option'];
		$link_cat = $instance['link_cat'];
		$open_same_window = $instance['open_same_window'];

		echo $before_widget;

		echo $before_title . $title . $after_title;

		include( plugin_dir_path( __FILE__ ) . 'views/widget.php' );

		echo $after_widget;

	} // end widget

	/**
	 * Processes the widget's options to be saved.
	 *
	 * @param	array	new_instance	The new instance of values to be generated via the update.
	 * @param	array	old_instance	The previous instance of values before the update.
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		$instance['title'] = strip_tags($new_instance['title']);
		$instance['default_option'] = strip_tags($new_instance['default_option']);
		$instance['link_cat'] = (int)($new_instance['link_cat']);
		$instance['open_same_window'] = $new_instance['open_same_window'];

		return $instance;

	} // end widget

	/**
	 * Generates the administration form for the widget.
	 *
	 * @param	array	instance	The array of keys and values for the widget.
	 */
	public function form( $instance ) {

		$instance = wp_parse_args(
			(array) $instance,
			array(
				'title' => '',
				'default_option' => 'Select Option',
				'link_cat' => 0
			)
		);

		$title = esc_attr( $instance['title'] );
		$default_option = esc_attr( $instance['default_option'] );
		$link_cat = (int) $instance['link_cat'];
		$open_same_window = $instance['open_same_window'];

		// Display the admin form
		include( plugin_dir_path(__FILE__) . 'views/admin.php' );

	} // end form

	/**
	 * Registers and enqueues widget-specific styles.
	 */
	public function register_widget_styles() {

		wp_enqueue_style( 'links-widget-widget-styles', plugins_url( 'css/widget.css',__FILE__ ) );

	} // end register_widget_styles

} // end class

add_action( 'widgets_init', create_function( '', 'register_widget("Links_Dropdown_Widget");' ) );


function linkdrop_categories_to_array($taxonomy){
    $terms = get_terms($taxonomy,array(
        'hide_empty'=>false
    ));
    $terms_production = array();
    foreach ($terms as $key=>$term){
        $terms_production[$term->term_id] = $term->name;
    }
    $terms_production[0] = __('All', 'linkdrop');
    return $terms_production;
}