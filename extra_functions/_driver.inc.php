<?php

/***  DOCUMENTATION LAYER

	Databug Extra Function Driver

	Directory: extra_functions
	File: _driver.inc.php
	Last Update: Jul 2006
	Author: Tom Atwell (klenwell@gmail.com)

______________________________________________________________________________*/



// ** File Stack
/*____________________________________________________________________________*/	

	$_STACK = array
	(
		'private_data',
		'art',
	);

/*____________________________________________________________________________*/


// ** Load Stack
/*____________________________________________________________________________*/

	foreach ( $_STACK as $_file ) 
	{
		$_path = $_file . '.inc.php';
		include_once($_path);
	}
	
/*____________________________________________________________________________*/

?>
