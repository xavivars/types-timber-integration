<?php
/*
Plugin Name: Types Timber Integration
Plugin URI: http://timber.upstatement.com
Description: Integrates the Timber (Twig) template engine with the Types plugin
Version: 0.2
Author: Xavi Ivars xavi.ivars@gmail.com
Author URI: http://xavi.ivars.me
License: GPLv3
GitHub Plugin URI: xavivars/types-timber-integration
*/

class WpTypesTimber {

	function __construct() {
		add_filter( 'timber_post_get_meta', array( $this, 'post_get_meta' ), 10, 2 );
		add_filter( 'timber_post_get_meta_field', array( $this, 'post_get_meta_field' ), 10, 3 );
	}

	function post_get_meta( $customs ) {
		foreach($customs as $key=>$value){
			$no_wpcf_key = str_replace('wpcf-', '', $key);
			$customs[$no_wpcf_key] = $value;
		}

		return $customs;
	}
	
	function post_get_meta_field( $value, $post_id, $field_name ) {
		if( ! empty($value) ) {
			return $value;
		}

		$children = types_child_posts( $field_name, $post_id );

		if ( is_array( $children )) {
			foreach ( $children as &$child ) {
				$child = new TimberPost( $child->ID );
			}

			$children = array_values($children);

			return $children;
		}

		return false;
	}
}
 
function types_timber_integration_init() {
	if ( defined( 'WPCF_META_PREFIX' ) && class_exists( 'Timber' )) {
			new WpTypesTimber();
	}
}

add_action( 'plugins_loaded', 'types_timber_integration_init' );