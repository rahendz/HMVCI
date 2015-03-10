<?php if ( ! defined ( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );

if ( isset ( $core_files ) ) {
	foreach ( $core_files as $_file ) {
		$_filepath = dirname ( __FILE__ ) . '/initiate/' . $_file . EXT;
		if ( file_exists ( $_filepath ) ) {
			@require_once $_filepath;
		}
	}
}

if ( isset ( $base_files ) ) {
	foreach ( $base_files as $bc ) {
		$filepath_controller = APPPATH .'core/controller/'. $bc . EXT;
		if ( file_exists ( $filepath_controller ) ) {
			@require_once $filepath_controller;
		}
	}
}