<?php

/***  DOCUMENTATION LAYER

	Klenwell Basic File Operations Library

	Last Update: Mar 2007
	Author: Tom at klenwell@gmail.com

	FUNCTIONS
		kw_write_to_file($content, $fpath, $overwrite=1)
		kw_read_file($fpath, $mode='rb')
		kw_read_file_to_array($fpath, $include_empty=0, $mode='rb') 

  NOTES
		don't forget to set file permissions accordingly

______________________________________________________________________________*/


// kw_write_to_file
/*____________________________________________________________________________*/
function kw_write_to_file($content, $fpath, $overwrite=1) 
{
// *** DATA

	// write modes
	$_MODE = array
	(
		'rw_start' => 'w+',		// read/write, pointer at start of file
		'w_start' => 'w',			// write, start
		'w_end' => 'a',				// write, end
	);
	
	// internal
	$_mode = $_MODE['rw_start'];

	// Return
	$is_written = 0;


// *** MANIPULATE

	// set mode
	$_mode = ( $overwrite ) ? $_MODE['w_start'] : $_MODE['w_end'] ;

	// open file (with lock?)
	if ( !$_handle = fopen($fpath, $_mode) )
	{
		trigger_error("unable to open file for writing [$fpath]", E_USER_WARNING);
		return 0;
	}
	
	// write
	if ( fwrite($_handle, $content) === FALSE )
	{
		trigger_error("unable to update public query log [$log_path]", E_USER_WARNING);
		$is_written = 0;
	}
	else
	{
		$is_written = 1;
	}    
		
	// close file
	fclose($_handle);

// *** RETURN

	return $is_written;

} // end Fx
/*____________________________________________________________________________*/


// kw_read_file
/*____________________________________________________________________________*/
function kw_read_file($fpath, $mode='rb') 
{
// *** DATA

	// Return
	$content = '';

// *** MANIPULATE

	// file_get_contents (php >= 4.3, faster)
	if ( function_exists('file_get_contents') ) return file_get_contents($fpath);
	
	// open file (with lock)
	if ( !$_handle = fopen($fpath, $mode) )
	{
		trigger_error("unable to open file for reading [$fpath]", E_USER_WARNING);
		return 0;
	}
	
	// read contents 
	while ( !feof($_handle) ) $content .=  fscanf($_handle, "%s\n");
	
	// close file
	fclose($_handle);

	
// *** RETURN

	return $content;

} // end Fx
/*____________________________________________________________________________*/


// kw_read_file_to_array
/*____________________________________________________________________________*/
function kw_read_file_to_array($fpath, $include_empty=0, $mode='rb') 
{
// *** DATA

	// internal
	$_buffer = '';

	// Return
	$LINES = array();
	

// *** MANIPULATE

	// open file
	if ( !$_handle = fopen($fpath, $mode) )
	{
		trigger_error("unable to open file for reading [$fpath]", E_USER_WARNING);
		return 0;
	}
	
	// read contents 
	while ( !feof($_handle) ) 
	{ 
		// get line (see http://www.php.net/manual/en/function.fgets.php#68144)
		$_buffer = fgets($_handle);
		if ( $include_empty )
		{
			$LINES[] = $_buffer; 
		}
		else
		{
			$_buffer = trim($_buffer);
			if ( !empty($_buffer) ) $LINES[] = $_buffer; 			
		}
	}
	
	// close file
	fclose($_handle);

	
// *** RETURN

	return $LINES;

} // end Fx
/*____________________________________________________________________________*/





// Testbed
/*____________________________________________________________________________*/

if ( 0 )
{
	// test function
}

/*____________________________________________________________________________*/

?>
