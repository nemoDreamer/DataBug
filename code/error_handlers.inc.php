<?php

/***  DOCUMENTATION LAYER

	Common Function Library for Klenwell

	Last Update: Mar 2007
	Author: Tom at klenwell@gmail.com

	FUNCTIONS	
		kw_ehandler_dev($enum, $emsg, $efile, $eline, $econtext)
		kw_ehandler_prod($enum, $emsg, $efile, $eline, $econtext)

  NOTES
		kw_ehandler_dev intended for development environments, kw_ehandler_prod for
		production environments.

______________________________________________________________________________*/


// Global Parameters

	// Dev Display Settings
	$_ERR['show_index'] = 0;		// show index warnings
	$_ERR['show_strict'] = 0;		// show strict warnings
	
	// Killfile (called for fatal errors)
	$_ERR['kill_file'] = 'error.fatal.mod.php';
	
	// Inline Style Settings
	$_ERR['CSS']['bfile'] = 'font-family:Verdana, Geneva, sans-serif; font-size:110%; color:#fff; font-weight:bold;';
	$_ERR['CSS']['bline'] = $_ERR['CSS']['bfile'];
	$_ERR['CSS']['tracebox'] = "padding:2px 4px; color:#ffcccc; background:#660000;";
	$_ERR['CSS']['signal'] = 'color:red; font-size:8px; margin:0 2px; font-weight:bold; font-family:Impact, Arial, Helvetica, sans-serif';
	$_ERR['CSS']['notice_super'] = 'position:absolute; top:-1px; right:2px; font-size:8px;';
	$_ERR['CSS']['notice_footer'] = 'font-weight:bold; font-size:75%;';
	$_ERR['CSS']['notice'] = <<<STYLE
width:98%; margin:2px auto; padding:2px; line-height:1.3em; position:relative;
background:#fff3f3; color:#660000; font-weight:normal; font-size:11px;
border:1px solid #8b0000; text-align:left;
STYLE;

	// Bitmasks
	$_ERR['serious_bitmask'] = 1015;		// 001111110111
	$_ERR['fatal_bitmask'] = 341;				// 000101010101	

	// Types
	$_ERR['TYPE'] = array
	(
		E_ERROR							=> 'Error',
		E_WARNING						=> 'Warning',
		E_PARSE							=> 'Parsing Error',
		E_NOTICE						=> 'Notice',
		E_CORE_ERROR				=> 'Core Error',
		E_CORE_WARNING			=> 'Core Warning',
		E_COMPILE_ERROR			=> 'Compile Error',
		E_COMPILE_WARNING		=> 'Compile Warning',
		E_USER_ERROR				=> 'User Error',
		E_USER_WARNING			=> 'User Warning',
		E_USER_NOTICE				=> 'User Notice',
		E_STRICT						=> 'Runtime Notice',
	);
		

// kw_ehandler_dev
/*____________________________________________________________________________*/
function kw_ehandler_dev($enum, $emsg, $efile, $eline, $econtext) 
{
// *** DATA
	
	// global
	global $_ERR;
	
	// static
	static $e_count = 0;
	static $e_strict_count = 0;
	static $e_index_count = 0;

	// Return (void)
	

// *** MANIPULATE

	// error counter
	$e_count++;
	
	// error reporting on?
	if ( error_reporting() == 0 ) return;

	// undefined index errors
	if ( $enum == E_NOTICE && substr($emsg, 0, 17) == "Undefined index: " ) 
	{
		$e_index_count++;
		if ( !$_ERR['show_index'] ) return;
	}
	
	// strict errors
	if ( defined('E_STRICT') && $enum == E_STRICT ) 
	{
		$e_strict_count++;
		if ( !$_ERR['show_strict'] ) return;
	}
	
	// Collect Error Data
	$etype = $_ERR['TYPE'][$enum];
	
	// Backtrace
	
		$_BUG = debug_backtrace();
		#print_r($_BUG);	// meta-debugging!
		
		// trigger data
		$etrigger_file = isset($_BUG[0]['file']) ? $_BUG[0]['file'] : $_BUG[1]['file'];
		$etrigger_line = isset($_BUG[0]['line']) ? $_BUG[0]['line'] : $_BUG[1]['line'];
		
		// count steps
		$_fx_steps = count($_BUG) + 1;
		
		// build bugtrace output
		foreach ( $_BUG as $_STEP )
  	{
  		$_fx_steps--;
  
  		// filter error_handler function
  		if ( $_STEP['function'] == __FUNCTION__ ) { continue; }
  		
  		$_bfile = str_replace('\\', '/', $_STEP['file']);
  		$_bline = $_STEP['line'];
			$_bpath = substr(dirname($_bfile), strrpos(dirname($_bfile), '/') + 1) . '/' . basename($_bfile);
			$_steps = htmlspecialchars(print_r($_STEP, 1));
  		
  		$_E['bugtrace'] .= "\nFunction Step #{$_fx_steps} :: $_bfile at line $_bline [Fx {$_STEP['function']}()]";
  	
  		$_BUG['trace'] .= <<<HTML
<div class="e_step">

<div class="e_step_h1">Function Step #{$_fx_steps} :: 
<span style="">{$_bpath}</span> at <span style="">line $_bline</span> 
&raquo; {$_STEP['function']}()
</div> 
$_steps
</div>
HTML;
  	}
		
		// wrap bugtrace
		$_bug_trace = "<div style=\"{$_ERR['CSS']['tracebox']}\">" . print_r($_BUG['trace'],1) . '</div>';
		
	// display
	$output = <<<FULL

<!-- ERROR -->
<div style="{$_ERR['CSS']['notice']}">
 <div style="{$_ERR['CSS']['notice_super']}">#$e_count</div>
 <b>$etype Error</b> &raquo; <em>$emsg</em>
 <div style="{$_ERR['CSS']['notice_footer']}">line $etrigger_line in file $etrigger_file</div>

$_bug_trace

</div>

FULL;
	
	// log


	// print
	echo $output;
	

// *** RETURN

	return;
} 
/*____________________________________________________________________________*/


// kw_ehandler_prod
/*____________________________________________________________________________*/
function kw_ehandler_prod($enum, $emsg, $efile, $eline, $econtext) 
{
// *** DATA
	
	// global
	global $_ERR;
	
	// static
	static $e_count = 0;
	static $e_strict_count = 0;
	static $e_index_count = 0;	

	// Basic Error Data
  $_E = array();
	$_E['file'] = $efile;
	$_E['line'] = $eline;
	$_E['msg'] = $emsg;
	$_E['num'] = $enum;
	#$_E['context'] = $econtext;
	
	// Contextual Data
	$_E['server'] = $_SERVER['SERVER_NAME'];
	$_E['time'] = time();
	$_E['date'] = date("Y-m-d H:i:s (T)");
	
	// Extended Error Data
	$_E['type'] = $_ERR['TYPE'][$enum];
	$_E['count'] = $e_count+1;
	$_E['basename'] = basename($efile);
	$_E['trigger_file'] = '';
	$_E['trigger_line'] = '';
	$_E['is_serious'] = $enum & $_ERR['serious_bitmask'];
	$_E['is_fatal'] = $enum & $_ERR['fatal_bitmask'];
	$_E['bugtrace'] = '';

	// Return (void)
	

// *** MANIPULATE

	// error counter
	$e_count++;
	
	// error reporting on?
	if ( error_reporting() == 0 ) return;

	// undefined index errors
	if ( $enum == E_NOTICE && substr($emsg, 0, 17) == "Undefined index: " ) 
	{
		$e_index_count++;
		if ( !$_ERR['show_index'] ) return;
	}
	
	// strict errors
	if ( defined('E_STRICT') && $enum == E_STRICT ) 
	{
		$e_strict_count++;
		if ( !$_ERR['show_strict'] ) return;
	}
	
	// Backtrace
	
		$_BUG = debug_backtrace();
		#print_r($_BUG);	// meta-debugging!
		
		// trigger data
		$_E['trigger_file'] = isset($_BUG[0]['file']) ? $_BUG[0]['file'] : $_BUG[1]['file'];
		$_E['trigger_line'] = isset($_BUG[0]['line']) ? $_BUG[0]['line'] : $_BUG[1]['line'];
		
		// count steps
		$_fx_steps = count($_BUG) + 1;
		
		// build bugtrace output
		foreach ( $_BUG as $_STEP )
  	{
  		$_fx_steps--;
  
  		// filter error_handler function
  		if ( $_STEP['function'] == __FUNCTION__ ) { continue; }
  		
  		$_bfile = str_replace('\\', '/', $_STEP['file']);
  		$_bline = $_STEP['line'];
			$_bpath = substr(dirname($_bfile), strrpos(dirname($_bfile), '/') + 1) . '/' . basename($_bfile);
			$_steps = htmlspecialchars(print_r($_STEP, 1));
  		
  		$_E['bugtrace'] .= "\nFunction Step #{$_fx_steps} :: $_bfile at line $_bline [Fx {$_STEP['function']}()]";
  	}
		
		// wrap bugtrace
		$_bug_trace = "<div style=\"{$_ERR['CSS']['tracebox']}\">" . print_r($_BUG['trace'],1) . '</div>';

	
	// log
	
	// mail
	
	// fatal
	if ( $_E['is_fatal'] )
	{
		define('KW_ERROR_FILE', 1);
		if ( isset($_ERR['kill_file']) && is_file($_ERR['kill_file']) ) require_once($_ERR['kill_file']);
		die('<!-- fatal error -->');
		
	}
	

// *** RETURN

	return;
} 
/*____________________________________________________________________________*/


// Testbed
/*____________________________________________________________________________*/

// dev handler
if ( 1 )
{
	set_error_handler('kw_ehandler_dev');
	$_ARRAY['no_index']++;
	trigger_error('testing notice');
	trigger_error('testing error', E_USER_ERROR);
}

// production handler
if ( 1 )
{
	set_error_handler('kw_ehandler_prod');
	$_ARRAY['no_index']++;
	trigger_error('testing notice');
	trigger_error('testing error', E_USER_ERROR);
}

// fun with bitmasks
if ( 0 )
{
	echo $bit = ( 512 & base_convert('01111110111',2,10) ) ? 'is serious' : 'not serious';
	echo '<br>serious bitmask: ' . base_convert('001111110111',2,10);	# 1015
	echo '<br>fatal bitmask: ' . base_convert('000101010101',2,10);	#341
}

/*____________________________________________________________________________*/

?>
