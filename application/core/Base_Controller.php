<?php if ( ! defined ( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );

if ( ! defined ( 'INITPATH' ) ) {
	show_error ( '<strong>INIT ERROR :</strong> Initialization file not set properly. Call it before system load bootstrap file.' );
}

if ( isset ( $root_files ) ) {
	foreach ( $root_files as $_file ) {
		$_filepath = FCPATH . $_file . EXT;
		if ( file_exists ( $_filepath ) ) {
			@require_once $_filepath;
		} else {
			header ( 'HTTP/1.1 404 Not Found.', TRUE, 404 );
			echo 'The requirement file are missing. ' . $_filepath;
			exit(1); // EXIT_ERROR
		}
	}
}

if ( isset ( $core_files ) ) {
	foreach ( $core_files as $_file ) {
		$_filepath = INITPATH . $_file . EXT;
		if ( file_exists ( $_filepath ) ) {
			@require_once $_filepath;
		} else {
			header ( 'HTTP/1.1 404 Not Found.', TRUE, 404 );
			echo 'The requirement file are missing. ' . $_filepath;
			exit(1); // EXIT_ERROR
		}
	}
}

if ( isset ( $base_files ) ) {
	foreach ( $base_files as $bc ) {
		$_filepath = APPPATH .'includes/controller/'. $bc . EXT;
		if ( file_exists ( $_filepath ) ) {
			@require_once $_filepath;
		} else {
			header ( 'HTTP/1.1 404 Not Found.', TRUE, 404 );
			echo 'The requirement file are missing. ' . $_filepath;
			exit(1); // EXIT_ERROR
		}
	}
}