<?php

/***  DOCUMENTATION LAYER

Black Swan Class

Name: BlackSwan
Version: 0.4
Last Update: Jun 2007
Author: Tom at klenwell@gmail.com

DESCRIPTION
	A class for doing random things (though not in wholly unpredictable ways)

METHODS
	MAGIC
	BlackSwan($debug=0, $oid=null)		*php 4 constructor*
	__construct($debug, $oid)					*php 5 constructor*
	__destruct()	
	
	PUBLIC
	pick_random_array_item($ARRAY)
	pick_int_from_range($num1, $num2=1)
	array_lottery($ARRAY, $num_picks=1)
	thresh_file($fpath, $ratio_denominator=3, $SKIP_TOKENS=0)
	print_d($message, $color='c33')
	print_r()
	dump()
	
	PRIVATE
	_set_session_data()
	_get_session_data()
	_has_session_data()
	_set_filename()
	_set_dirpath()
	
USAGE
	$Swan = new BlackSwan($debug=1);
	$chances_bush_admin_will_get_it_right = $Swan->pick_int_from_range(50000, $num2=1);

NOTES
	see Nassim Nicholas Taleb

______________________________________________________________________________*/

// BlackSwan
/*____________________________________________________________________________*/
class BlackSwan
{
/* PUBLIC PROPERTIES */
var $debug = 0;
var $class_name = __CLASS__;
var $oid = '';
var $DS = DIRECTORY_SEPARATOR;

/* PRIVATE PROPERTIES */
var $_filename = '';
var $_dirpath = '';


/* ** MAGIC METHODS ** */
// php4 constructor
function BlackSwan($debug=0, $oid=null)
{
	$this->__construct($debug, $oid);
	register_shutdown_function( array($this, '__destruct') );
}
// END constructor

// php5 constructor
function __construct($debug=0, $oid=null)
{
	// default
	$this->debug = $debug;
	$this->oid = ( empty($oid) ) ? $this->class_name : $oid;
	$this->_set_filename();
	$this->_set_dirpath();
	
	// additional code 
	
	// debug
	if ( $this->debug ) $this->print_d('debugging is active for oid ' . $this->oid);
	if ( $this->debug ) $this->print_d('constructor complete for class ' . __CLASS__);
}
// END constructor

// destructor
function __destruct()
{
	if ( $this->debug ) $this->print_d("destroying class {$this->class_name} (oid: {$this->oid})");
}
// END destructor



/* ** PUBLIC METHODS ** */
// method: pick random array item
function pick_random_array_item($ARRAY)
{
	$item = '';		// return

	if ( is_scalar($ARRAY) )
	{
		if ( $this->debug ) 
		{
			trigger_error('scalar value passed to fx pick_random_array_item -- var will be returned (error in debug only)');
		}
		return $ARRAY;
	}
	
	$item = $ARRAY[array_rand($ARRAY)];
	return $item;
}
// END method

// method: pick int from range
function pick_int_from_range($num1, $num2=1)
{
	$int = FALSE;		// return
	
	// reorder
	if ( $num1 > $num2 )
	{
		$_numt = $num2;
		$num2 = $num1;
		$num1 = $_numt;
	}
	
	// sanity check
	if ( !is_int($num1) || !is_int($num2) )
	{
		trigger_error('numbers must be integers', E_USER_WARNING);
		return FALSE;
	}

	// rand
	$int = mt_rand($num1, $num2);

	return $int;
}
// END method

// method: array lottery
/* note: ARRAY should be an associative array where the key is an id and the value
	is the (raw) number of chances that element has of being picked. */
function array_lottery($ARRAY, $num_picks=1) 
{
	// Return
	$PICKS = array();

	// internal
	$num_candidates = count($ARRAY);
	$BALL = array();
	
	// assign lottery balls
	foreach ( $ARRAY as $key => $lots )
	{
		for ( $i=0;$i<$lots;$i++ ) $BALL[] = $key;
	}
	
	// pick winners
	while ( $num_picks > 0 )
	{
		$ball_count = count($BALL);
		$this_pick = $BALL[array_rand($BALL)];

		for ( $i=0; $i<$ball_count; $i++ )
		{
			if ( $BALL[$i] == $this_pick ) unset($BALL[$i]);
		}
		
		$PICKS[] = $this_pick;
		$num_picks--;
		sort($BALL);
	}

	return $PICKS;
} 
// END method

// method: thresh file
function thresh_file($fpath, $ratio_denominator=3, $SKIP_TOKENS=0)
{
	$LINES = array();		// return

	// internal
	$_d = $ratio_denominator;
	$_i = 0;

	// skip token array
	if ( empty($SKIP_TOKEN) ) $SKIP_TOKEN = array();
	if ( !empty($SKIP_TOKEN) && is_scalar($SKIP_TOKEN) ) $SKIP_TOKEN = array( $SKIP_TOKEN );

	// sanity check
	if ( !is_file($fpath) )
	{
		trigger_error("file [$fpath] not found", E_USER_WARNING);
		return 0;
	}
	
	// open file (for reading)
	$_handle = @fopen($fpath, "r");

	// Set Mod Offset
	$_mod_offset = mt_rand(0, $_d-1);
	
	// fetch file lines (feof not reliable -> see comments for feof at php.net)
	while ( !feof($_handle) ) 
	{ 
		$_i++;	
	
		$_buffer = fgets($_handle);
		if ( $_i % $_d <> $_mod_offset ) continue;
		$_line = trim($_buffer);
		
		// check for skip token
		$_token = $_line{0};
		if ( !in_array($_token, $SKIP_TOKEN) && !empty($_line) ) $LINES[] = $_line;
	}
	
	// close file
	fclose($_handle);

	if ( $this->debug ) $this->print_d($this->print_r($LINES)); 

	// safety check
	if ( !count($LINES) )
	{
		trigger_error('no lines found', E_USER_WARNING);
	}
	elseif ( count($LINES) == 1 )
	{
		trigger_error('only 1 line fetched -> you may wish to check EOL delimiter', E_USER_NOTICE);
	}
	
	return $LINES;
}
// END method

// method: print_d
function print_d($message, $color='c33')
{
	$out = "<div style='line-height:1.5em; font-family:monospace; color:$color;'>$message</div>";
	echo $out;
	return;
}
// END method

// method: print_r
function print_r($Mixed)
{
	$return = htmlspecialchars(print_r($Mixed, 1));
	$return = "<pre>$return</pre>";
	return $return;
}
// END method

// method: dump
function dump()
{
	echo $this->print_r($this);
	return;
}
// END method



/* ** PRIVATE METHODS ** */
// method: _set_session_data
function _set_session_data()
{
	// initialize session
	if ( !session_id() ) session_start(); 
	$_SESSION[$this->oid] = array();
	
	// add session data here
	
	return;
}
// END method

// method: get session data
function _get_session_data()
{
	// initialize session
	if ( !$this->_has_session_data() ) return; 
		
	// retrieve session variables
	// $this->var = $_SESSION[$this->oid]['var'];
	
	return;
}
// END method

// method: has session data
function _has_session_data()
{
	// initialize session
	if ( !session_id() ) session_start(); 
		
	// retrieve session variables
	if ( empty($_SESSION[$this->oid]) ) return 0;
	else return 1;
}
// END method

function _set_filename() { $this->_filename = basename(__FILE__); }
function _set_dirpath() { $this->_dirpath = dirname(__FILE__) . $this->DS; }

} // end class
/*____________________________________________________________________________*/

?>
