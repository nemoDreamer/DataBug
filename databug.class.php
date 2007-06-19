<?php

/***  DOCUMENTATION LAYER

Klenwell Databug Class

Name: Databug
Version: 1.0
Last Update: Jun 2007
Author: Tom at klenwell@gmail.com

DESCRIPTION
	A php class for generating random data useful for testing

METHODS
	MAGIC
	Databug($debug=0, $oid=null)	*php 4 constructor*
	__construct($debug, $oid)		*php 5 constructor*
	__destruct()	
	
	PUBLIC
	get_sample_text($num_words=500, $source_fpath='lipsum.inc')
	burn_file($content, $fpath, $overwrite=1)
	rip_file($fpath, $mode='rb')
	rip_file_to_array($fpath, $include_empty=0, $mode='rb')
	rip_lines($fpath, $SKIP_TOKEN=0)
	thresh_file($fpath, $ratio_denominator=3, $SKIP_TOKENS=0)
	array_dump($ARRAY, $name='ARRAY')
	get_ordinal($number)
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
	$Class = new ClassClass();
	$Class->print_r('hello world']);

NOTES
	
______________________________________________________________________________*/

// Load File of Base Class
$base_fname = '';
$base_dirpath = dirname(__FILE__) . DIRECTORY_SEPARATOR;
#require_once($base_dirpath . $base_fname);


// Databug
/*____________________________________________________________________________*/
class Databug
{
/* PUBLIC PROPERTIES */
var $debug = 0;
var $class_name = __CLASS__;
var $oid = '';
var $DS = DIRECTORY_SEPARATOR;
var $Randomizer = 0;	/* black swan class */
var $SKIP_TOKENS = array();
var $alphaset = 'abcdefghijklmnopqrstuvwxyz';

// gender values
var $female = 1;
var $f_val = 1;
var $f_abr = 'F';
var $male = 2;
var $m_val = 2;
var $m_abr = 'M';

// dir/paths
var $code_root = '';
var $data_bin = '';
var $text_bin = '';

// data sources
var $SOURCE = array();

/* PRIVATE PROPERTIES */
var $_filename = '';
var $_dirpath = '';


/* ** MAGIC METHODS ** */
// php4 constructor
function Databug($debug=0, $oid=null)
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
	
	// libraries
	$this->code_root = $this->_dirpath . 'code'. $this->DS;
	$this->data_bin = $this->_dirpath . 'data_bin'. $this->DS;
	$this->text_bin = $this->_dirpath . 'text_bin'. $this->DS;
	
	// set data sources
	$this->SOURCE['male_first'] = $this->data_bin . 'names_first_m.inc';
	$this->SOURCE['female_first'] = $this->data_bin . 'names_first_f.inc';
	$this->SOURCE['last_name'] = $this->data_bin . 'names_last_us3100.inc';
	$this->SOURCE['us_zipcodes'] = $this->data_bin . 'majoruszips.gov.inc';
	$this->SOURCE['us_streets'] = $this->data_bin . 'anchorage_streets.inc';
	$this->SOURCE['us_phones'] = $this->data_bin . 'phone_data.us.inc';
	
	// skip tokens
	$this->SKIP_TOKENS = array( '%' );
	
	// randomizer
	require_once('blackswan.class.php');
	$this->Randomizer = new BlackSwan($debug);
	
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
// method: get sample text
function get_sample_text($num_words=500, $source_fpath='lipsum.inc')
{
	$sample = '';		// return
	
	// debug
	if ( $this->debug ) $this->print_d('calling method ' . __FUNCTION__);
	
	// source
	$source_fpath = ( $source_fpath == 'lipsum.inc' ) ? $this->text_bin . 'lipsum.inc' : $source_fpath;

	// regex
	$_REGEX['stop_at_next_sent'] = '#([\.\?\!]+["\']*\s+\w)#U';
	$_REGEX['start_at_next_sent'] = '#([\.\?\!]+["\']*\s+\w)#U';
	
	// flags
	$_FLAG['loop_seam_found'] = 0;

	// sanity check
	if ( !is_file($source_fpath) )
	{
		trigger_error("file [$source_fpath] not found", E_USER_NOTICE);
		return FALSE;
	}

	// rip file
	$_raw_source = $this->rip_file($source_fpath);
	$_raw_source = strip_tags(trim($_raw_source));
	
	// strlen (PHP)
	$_strlen = strlen($_raw_source);
	
	// random strpos
	$_start_pos = mt_rand(1, $_strlen);

	// find start of next sentence
		// note: try 4 times (in case beginning at end of file)
		$_num_tries = 0;
		
		while ( $_num_tries <= 4 && !$_FLAG['loop_seam_found'] )
		{
			$_num_tries++;
			if ( preg_match($_REGEX['stop_at_next_sent'], $_raw_source, $MATCH, PREG_OFFSET_CAPTURE, $_start_pos) )
			{
				$_FLAG['loop_seam_found'] = 1;
				$_loop_seam = $MATCH[1][1] + strlen($MATCH[1][0] - 1);
			}
			
			// try new start position
			$_start_pos = mt_rand(1, $_strlen);
			
			// reset to beginning of string on try 3
			if ( $_num_tries == 3 ) $_start_pos = 0;
		}
		
	// debug
	if ( $this->debug ) $this->print_d("START POS -> LOOP SEAM : $_start_pos -> $_loop_seam");
	if ( $this->debug ) $this->print_d('loop seam: ' . $MATCH[1][0]);
	
	// check
	if ( !$_FLAG['loop_seam_found'] )
	{
		trigger_error('preg match failed', E_USER_WARNING);
		return FALSE;
	}
	
	// snakeloop text
	$_snake_source_pt1 = substr($_raw_source, $_loop_seam) . "\n\n";
	$_snake_source_pt2 = substr($_raw_source, 0, $_loop_seam);
	$_snaked_source = trim($_snake_source_pt1 . $_snake_source_pt2);
	
	// debug?
	if ( $this->debug ) $this->print_d('snaked source: <br />' . $_snaked_source, '#c99');
	
	// trim by word (see http://www.php.net/manual/en/function.str-word-count.php#59170)
	$_BLURB = preg_split("/\s+/", $_snaked_source, ($num_words+1));
	unset($_BLURB[(sizeof($_BLURB)-1)]);
	$sample =  implode(' ', $_BLURB);

	// finishing touches?

	return $sample;

}
// END method

// method: burn file
function burn_file($content, $fpath, $overwrite=1)
{
	require_once($this->code_root . 'fileops.inc.php');
	return kw_write_to_file($content, $fpath, $overwrite=1);
}
// END method

// method: rip file
function rip_file($fpath, $mode='rb')
{
	$content = '';		// return
	require_once($this->code_root . 'fileops.inc.php');
	$content = kw_read_file($fpath, $mode);
	return $content;
}
// END method

// method: rip file to array
function rip_file_to_array($fpath, $include_empty=0, $mode='rb')
{
	$LINES = array();		// return
	$_buffer = '';

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

	return $LINES;
}
// END method

// method: rip lines
function rip_lines($fpath, $SKIP_TOKEN=0)
{
	$LINES = array();		// return
	$_buffer = '';
	
	// skip token array
	if ( empty($SKIP_TOKEN) ) $SKIP_TOKEN = array();
	if ( !empty($SKIP_TOKEN) && is_scalar($SKIP_TOKEN) ) $SKIP_TOKEN = array( $SKIP_TOKEN );

	// open file
	if ( !$_handle = fopen($fpath, 'r') )
	{
		trigger_error("unable to open file for reading [$fpath]", E_USER_WARNING);
		return 0;
	}
	
	// read contents 
	while ( !feof($_handle) ) 
	{ 
		// get line (see http://www.php.net/manual/en/function.fgets.php#68144)
		$_buffer = fgets($_handle);
		$_line = trim($_buffer);
		
		// check for skip token
		$_token = $_line{0};
		if ( !in_array($_token, $SKIP_TOKEN) && !empty($_line) ) $LINES[] = $_line;
	}
	
	// close file
	fclose($_handle);

	return $LINES;
}
// END method

// method: thresh file
function thresh_file($fpath, $ratio_denominator=3, $SKIP_TOKENS=0)
{
	return $this->Randomizer->thresh_file($fpath, $ratio_denominator, $SKIP_TOKENS);
}
// END method

// method: array dump
/* output an array such that you can burn it to a file in a way that PHP can use */
function array_dump($ARRAY, $name='ARRAY')
{
	return array_smart_dump($ARRAY, $name);
}
// END method

// method: get ordinal value
function get_ordinal($number)
{
	$ordinal = '';		// return

	// internal
	$_suffix = '';

	// Case Tree
	if ( $number % 100 > 10 && $number % 100 < 14 ) $_suffix = 'th';
	elseif ( $number % 10 == 0 ) $_suffix = 'th';
	elseif ( $number % 10 == 1 ) $_suffix = 'st';
	elseif ( $number % 10 == 2 ) $_suffix = 'nd';
	elseif ( $number % 10 == 3 ) $_suffix = 'rd';
	else $_suffix = 'th';
		
	// format 
	$ordinal = "{$number}<span class=\"ordinal\">{$_suffix}</span>";		

	return $ordinal;	
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
