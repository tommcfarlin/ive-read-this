<?php
/*
Plugin Name: I've Read This
Plugin URI: http://github.com/tommcfarlin/ive-read-this/
Description: A simple plugin for allowing site members to mark when they've read a post.
Version: 1.0
Author: Tom McFarlin
Author URI: http://tommcfarlin.com/
Author Email: tom@tommcfarlin.com
License:

  Copyright 2012 Tom McFarlin (tom@tommcfarlin.com)

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

class IveReadThis {
	 
	/*--------------------------------------------*
	 * Constructor
	 *--------------------------------------------*/
	
	/**
	 * Initializes the plugin by setting localization, filters, and administration functions.
	 */
	function __construct() {
	
		load_plugin_textdomain( 'ive-read-this', false, dirname( plugin_basename( __FILE__ ) ) . '/lang' );
	
		// Register site styles and scripts
		add_action( 'wp_enqueue_scripts', array( &$this, 'register_plugin_styles' ) );
		add_action( 'wp_enqueue_scripts', array( &$this, 'register_plugin_scripts' ) );
		
		// Include the Ajax library on the front end
		add_action( 'wp_head', array( &$this, 'add_ajax_library' ) );
		
		// Setup the event handler for marking this post as read for the current user
		add_action( 'wp_ajax_mark_as_read', array( &$this, 'mark_as_read' ) );
		
		// Setup the filter for rendering the checkbox
		add_filter( 'the_content', array( &$this, 'add_checkbox' ) );

	} // end constructor

	/*--------------------------------------------*
	 * Action Functions
	 *--------------------------------------------*/

	/**
	 * Adds the WordPress Ajax Library to the frontend.
	 */
	public function add_ajax_library() {
		
		$html = '<script type="text/javascript">';
			$html .= 'var ajaxurl = "' . admin_url( 'admin-ajax.php' ) . '"';
		$html .= '</script>';
		
		echo $html;	
		
	} // end add_ajax_library

	/**
	 * Registers and enqueues plugin-specific styles.
	 */
	public function register_plugin_styles() {
	
		wp_register_style( 'ive-read-this', plugins_url( 'ive-read-this/css/plugin.css' ) );
		wp_enqueue_style( 'ive-read-this' );
	
	} // end register_plugin_styles
	
	/**
	 * Registers and enqueues plugin-specific scripts.
	 */
	public function register_plugin_scripts() {
	
		wp_register_script( 'ive-read-this', plugins_url( 'ive-read-this/js/plugin.js' ), array( 'jquery' ) );
		wp_enqueue_script( 'ive-read-this' );
	
	} // end register_plugin_scripts
	
	/**
	 * Uses the current user ID and the incoming post ID to mark this post as read
	 * for the current user.
	 *
	 * We store this post's ID in the associated user's meta so that we can hide it
	 * from displaying in the list later.
	 */
	public function mark_as_read() {
		
		// First, we need to make sure the post ID parameter has been set and that's it's a numeric value
		if( isset( $_POST['post_id'] ) && is_numeric( $_POST['post_id'] ) ) {

			// If we fail to update the user meta, respond with -1; otherwise, respond with 1.
			echo false == update_user_meta( wp_get_current_user()->ID, $_POST['post_id'], 'ive_read_this' ) ? "-1" : "1";
			
		} // end if
		
		die();
		
	} // end mark_as_read
	
	/*--------------------------------------------*
	 * Filter Functions
	 *--------------------------------------------*/
	 
	 /**
	  * Adds a checkbox to the end of a post in single view that allows users who are logged in
	  * to mark their post as read.
	  * 
	  * @param	$content	The post content
	  * @return				The post content with or without the added checkbox
	  */
	 public function add_checkbox( $content ) {
		 
		 // We only want to modify the content if the user is logged in
		 if( is_single() ) {

			 // If the user is logged in...
			 if( is_user_logged_in() ) {
				 
				 // And if they've previously read this post...
				 if( 'ive_read_this' == get_user_meta( wp_get_current_user()->ID, get_the_ID(), true ) ) {
				  
					 // Build the element to indicate this post has been read
					 $html = '<div id="ive-read-this-container">';
					 	$html .= '<strong>';
					 		$html .= __( "I've read this post.", 'ive-read-this' );
					 	$html .= '</strong>';
					 $html .= '</div><!-- /#ive-read-this-container -->';
				 
				 // Otherwise, give them the option to mark this post as read
				 } else {

				 	// Build the element that will be used to mark this post as read
					 $html = '<div id="ive-read-this-container">';
					 	$html .= '<label for="ive-read-this">';
					 		$html .= '<input type="checkbox" name="ive-read-this" id="ive-read-this" value="0" />';
					 		$html .= __( "I've read this post.", 'ive-read-this' );
					 	$html .= '</label>';
					 $html .= '</div><!-- /#ive-read-this-container -->';				 
				 
				 } // end if
				 
				 // Append it to the content
				 $content .= $html;
				 
			 } // end if
			 
		 } // end if
		 
		 return $content;
		 
	 } // end add_checkbox
  
} // end class

new IveReadThis();
?>