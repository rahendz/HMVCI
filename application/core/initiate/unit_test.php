<?php if ( ! defined ( 'BASEPATH' ) ) exit ( 'No direct script access allowed' );
// echo 'unit test loaded';
if ( ! function_exists ( 'is_true' ) ) {
	function is_true ( $test )
	{
		return ( is_bool ( $test ) AND $test === true ) ? true : false;
	}
}

if ( ! function_exists ( 'is_false' ) ) {
	function is_false ( $test )
	{
		return ( is_bool ( $test ) AND $test === false ) ? true : false;
	}
}

if ( ! function_exists ( 'echo_r' ) ) {
	function echo_r ( $data, $exit = false, $dump = false )
	{
		if ( $exit === 'json' ) {
			header ( 'Content-Type: application/json' );
			exit ( json_encode ( $data ) );
		}
		echo $dump ? null : '<pre>';
		if ( $dump ) {
			var_dump ( $data ); 
		}
		else { 
			print_r ( $data );
		}
		echo $dump ? null : '</pre>';
		if ( $exit ) {
			exit;
		}
	}
}

if ( ! function_exists ( 'echo_j' ) ) {
	function echo_j ( $data )
	{
		echo_r ( $data, 'json' );
	}
}