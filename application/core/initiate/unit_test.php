<?php if ( ! defined ( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );

if ( ! function_exists ( 'is_true' ) ) :
	function is_true ( $test )
	{
		return ( is_bool ( $test ) AND $test === TRUE ) ? TRUE : FALSE;
	}
endif;

if ( ! function_exists ( 'is_false' ) ) :
	function is_false ( $test )
	{
		return ( is_bool ( $test ) AND $test === FALSE ) ? TRUE : FALSE;
	}
endif;

if ( ! function_exists ( 'echo_r' ) ) :
	function echo_r ( $data, $exit = FALSE, $dump = FALSE )
	{
		if ( $exit === 'json' ) {
			header('Content-Type: application/json');
			echo json_encode ( $data ); exit;
		}
		echo $dump ? NULL : '<pre>';
		if ( $dump ) var_dump ( $data ); else print_r ( $data );
		echo $dump ? NULL : '</pre>';
		if ( $exit ) exit;
	}
endif;

if ( ! function_exists ( 'echo_j' ) ) :
	function echo_j ( $data )
	{
		echo_r ( $data, 'json' );
	}
endif;