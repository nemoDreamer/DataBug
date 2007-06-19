<?php

/***  DOCUMENTATION LAYER

	Common Function Library for Klenwell

	Last Update: Feb 2007
	Author: Tom at klenwell@gmail.com

	FUNCTIONS	
		kw_error_handler($enum, $emsg, $efile, $eline, $vardump)
		kw_print_r($Mixed)
		kw_timer()
		kw_lapse_timer($last_read=0)
		kw_cc()
		kw_roll($choices=100) 

  NOTES

______________________________________________________________________________*/


// kw_error_handler
/*____________________________________________________________________________*/
function kw_error_handler($enum, $emsg, $efile, $eline, $vardump) 
{
// *** DATA

	// Return (void)
	

// *** MANIPULATE

	// ignorable errors
	if ( $enum == E_NOTICE && substr($emsg, 0, 17) == "Undefined index: " ) return;
	if ( defined('E_STRICT') && $enum == E_STRICT ) return;
	
	// bugtrace
	$_TRACE = debug_backtrace();
	$_ftrace = basename($_TRACE[1]['file'], '.php');
	$_ltrace = $_TRACE[1]['line'];
	$_fxtrace = $_TRACE[1]['function'];
	
	// output info
	$output = <<<FULL

<!-- ERROR -->
<div style="font-size:11px; padding:4px; background:#000; color:#990000; border:1px solid red;">
 <b>Error #{$enum}</b> &raquo; <em>{$emsg}</em>
 <div style="color:#ff0000;">line {$eline} in file {$efile}</div>
 <div style="color:#ccc;">trace: <i>fx $_fxtrace</i> on line {$_ltrace} of file {$_ftrace}</div>
</div>

FULL;

		// print
		echo $output;
	

// *** RETURN

	return;
} 
set_error_handler('kw_error_handler');
/*____________________________________________________________________________*/


// kw_print_r
/*____________________________________________________________________________*/
function kw_print_r($Mixed) 
{
 $print = print_r($Mixed, 1);
 $output = "<pre style='margin:12px; padding:2px; background:#f3f6f9; border:1px solid #9cf;'>$print</pre>";
 echo $output;
 return;
} 
/*____________________________________________________________________________*/


// kw_timer
/*____________________________________________________________________________*/
function kw_timer() 
{
	$usec = microtime(true);
	return number_format($usec, 4, '.', '');
} 
/*____________________________________________________________________________*/


// kw_lapse_timer
/*____________________________________________________________________________*/
function kw_lapse_timer() 
{
// *** DATA

	static $time0 = 0;
	
	// return
	$time_lapse = 0;


// *** MANIPULATE

	// start timer
	if ( empty($time0) )
	{
		$time0 = microtime(1);
		return 0;
	}
	
	// get current time
	$timen = microtime(1);
	
	// get lapse
	$time_lapse = number_format($timen - $time0, 4, '.', '');
	
	// reset time0
	$time0 = $timen;	


// *** RETURN

	return $time_lapse;

} 
/*____________________________________________________________________________*/


// kw_cc
/*____________________________________________________________________________*/
function kw_cc() 
{
 static $cycles = 0;
 return $cycles++;
} 
/*____________________________________________________________________________*/



// kw_roll
/*____________________________________________________________________________*/

function kw_roll($choices=100) { return mt_rand(1, $choices); }

/*____________________________________________________________________________*/



// Testbed
/*____________________________________________________________________________*/


/*____________________________________________________________________________*/

?>
