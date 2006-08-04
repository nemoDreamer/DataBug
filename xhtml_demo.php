<?php

// ** Documentation
/*______________________________________________________________________________

	Databugger Demo File

	Last Update: Aug 2006
	Author: Tom Atwell (klenwell@gmail.com)


	SUMMARY:
	
	Provides a few examples of how the php databug class can be used.


	NOTES:
	
	Standard databug usage:
	
		$Databug = new Databug();
		
		// Completely Random Profile
		$Databug->cx_profile($gender='*', $first_name='*', $last_name='*', $state='*');
		echo $Databug->first_name;
		
		$_PROFILE = $Databug->get_profile();
		echo $_PROFILE['first_name'];
		print_r($_PROFILE);
		
		// Less Random Profile
		$Databug->cx_profile('M', 'John', 'Doe', 'CA');
		
		
	See EVENT Module below for additional examples. 

______________________________________________________________________________*/



// *** INITIALIZATION Module (Core Declarations, Includes)
/*____________________________________________________________________________*/

// TIMERS

	# Script Timer
	$_tx1 = microtime();

	
// CORE DECLARATIONS

	# syntax
	define('_DS', DIRECTORY_SEPARATOR);
	
	
// DRIVER

	# Include Stack
	require_once('databug.class.php');

/*____________________________________________________________________________*/



// *** DECLARATION Module (Variable Declarations)
/*____________________________________________________________________________*/

// PARAMETERS

	$_HEAD['title'] = 'PHP Databug Demo';
	$_HEAD['description'] = 'for more info, see databugger.blogspot.com';

// DATA

	$_DATA = array();
	
	$_DATA['sesame'] = 'anaheim';

	# Site Basics
	$_DATA['firefox_tag'] = '<a href="http://www.spreadfirefox.com/?q=affiliates&amp;id=151890&amp;t=85"><img border="0" alt="Get Firefox!" title="Get Firefox!" src="http://sfx-images.mozilla.org/affiliates/Buttons/80x15/firefox_80x15.png"/></a>';
	$_DATA['copyright'] = 'some rights reserved, &#169; ' . date('Y');
	$_DATA['sitename'] = $_SERVER['HTTP_HOST'];


// BOOLEAN FLAGS

	# Flag
	
	# Trigger
	$_TRIGGER['demo'] = 0;
	
	# Show


// OUTPUT

	# HTML
	
	# Blocks
	$_BLOCK['head_tags'] = '';
	$_BLOCK['form'] = '';

	# Panels
	$_PAGE['metabar'] = '';
	$_PAGE['masthead'] = '';
	$_PAGE['core'] = '<span style="color:#eee;">core</span>';
	$_PAGE['footer'] = '';
	
/*____________________________________________________________________________*/



// *** SECURITY Module (Validation, Authentication, Access Authorization)
/*____________________________________________________________________________*/

// INPUT NORMALIZER

	// Strip Magic
	if ( get_magic_quotes_gpc() ) 
	{
    $_REQUEST = array_map('stripslashes', $_REQUEST);
    $_GET = array_map('stripslashes', $_GET);
    $_POST = array_map('stripslashes', $_POST);
    $_COOKIE = array_map('stripslashes', $_COOKIE);
	}

/*____________________________________________________________________________*/


// *** CONTROLLER Module
# set TRIGGER flags
/*____________________________________________________________________________*/

	// Default
	$_TRIGGER['demo'] = 1;

/*____________________________________________________________________________*/



// *** EVENT Module
# set SHOW flags
/*____________________________________________________________________________*/

	if ( $_TRIGGER['demo'] )
	{
		// Create Object
		$Databug = new Databug();		
	
		// Get Zip Info by Range
		if ( 1 )
		{
			// Turn Off Debug Mode
			$Databug->set_debug_mode(0);
			
			// Get Random City, State, Zip in So Cal Region
			$Databug->set_zip_data_randomly_by_range(90000, 93999);
			
			// Output
			$_HTML['demo1'] = <<<HTML
<h1>Getting Random Zip Info Between Zip Codes 90000 and 93999</h1>
<h3>{$Databug->city}, {$Databug->state}  {$Databug->zip}</h3>
HTML;

		}
		
		// Get Random Profile by Range
		if ( 1 )
		{
			// Create Profile in So Cal Area
			$Databug->cx_profile_by_zip_range($zip1=90000, $zip2=93999, $gender='*', $first_name='*', $last_name='*');
			$DATA = $Databug->get_profile();
			$profile = print_r($DATA,1);
		
			// Output
			$_HTML['demo2'] = <<<HTML
<h1>Constructing Random Profile Between Zip Codes 90000 and 93999</h1>
<pre>$profile</pre>
HTML;

		}
		
		// Get Random Profiles
		if ( 1 )
		{
			// Completely Random Profile
			$Databug->cx_profile();
			$Databug->load_extra_fx();			
			$DATA1 = $Databug->get_profile();
			$DATA1['fav_color'] = get_color();
			$DATA1['password'] = set_random_password();
			$DATA1['credit_card'] = set_random_credit_card_number();
			$profile1 = print_r($DATA1,1);

			// Random Male Profile
			$Databug->cx_profile('M');
			$DATA2 = $Databug->get_profile();
			$profile2 = print_r($DATA2,1);
		
			// Output
			$_HTML['demo3'] = <<<HTML
<h1>Databug: Constructing 2 Random Profiles</h1>
<pre>{$profile1}<br />{$profile2}</pre>
HTML;

		}
		

		// Build SQL Insert Statement for Data
		if ( 1 )
		{
			// Create Profile in So Cal Area
			$Databug->cx_profile();
			$DATA = $Databug->get_profile();
			$profile = print_r($DATA,1);
		
			// Output
			$_HTML['demo4'] = <<<HTML
<h1>SQL Insert Statement for Random Profile</h1>
<p>INSERT INTO `table` VALUES (null, '{$DATA['first_name']}', '{$DATA['last_name']}', '{$DATA['gender']}', '{$DATA['zip']}');</p>
HTML;

		}		
		
	}

/*____________________________________________________________________________*/



// *** OUTPUT Module
/*____________________________________________________________________________*/

// *** Page Panels

	# masthead
	$_PAGE['masthead'] = <<<PAGE
<div id="masthead_panel">
<h1>{$_HEAD['title']}</h1>
<p>{$_HEAD['description']}</p>
</div>
PAGE;

	# footer
	$_PAGE['footer'] = <<<PAGE
<div id="footer_panel">
<div id="footer_left">{$_DATA['sitename']}</div>
<div id="footer_right">{$_DATA['firefox_tag']}</div>
{$_DATA['copyright']}
</div>
PAGE;

/*____________________________________________________________________________*/



// TEMPLATE (HTML)
/*____________________________________________________________________________*/

// STOP PHP / START HTML...
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
 "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
<head>

<!-- VERSION 0.9 -->
<title><?php echo $_HEAD['title']; ?></title>


<!-- Meta Tags -->
<meta http-equiv="content-type" content="application/xhtml+xml; charset=utf-8" />
<meta name="robots" content="index, follow" />
<meta name="description" content="<?php echo $_HEAD['description']; ?>" />
<meta name="keywords" content="php, phpclasses, google code, open source, klenwell" />
<meta name="author" content="Tom Atwell, wiredjawtech.com" />


<!-- INTERNAL STYLE SHEET -->
<style type="text/css">

/* BASIC LAYOUT */
body
{
 margin:0; padding:0;
 font-family:Arial, Helvetica, sans-serif;
 background:#ccc;
}
#page
{
 width:auto;
 margin:0 2%; padding:1em;
 background:white;
}
p
{
 margin:0; padding:0;
 line-height:1.4em;
}
h1 { margin:1em 0 0; padding:0; font-size:1.5em; }
h3 { margin:0; padding:0; font-weight:normal; }
pre { color:#666; }
a { color:#666; }
a:hover { color:lime; text-decoration:none; }
#time { margin:1em 0; font-size:11px; color:#990000; }

/* MASTHEAD PANEL */
#masthead_panel
{
 width:auto;
 margin-top:10px;
 padding:10px;
 clear:both;
}
#masthead_panel h1 
{
 margin:0 0 10px;
 font-size:2em;
}

/* FOOTER PANEL */
#footer_panel
{
 width:auto;
 padding:4px;
 clear:both;
 text-align:center;
 font:11px/1.4em Verdana, Geneva, sans-serif;
 border-top:1px solid #fafaf8;
}
#footer_left
{
 float:left;
}
#footer_right
{
 float:right;
}

/* end BASIC LAYOUT */

</style>
<!-- end INTERNAL STYLE SHEET -->

</head>
<!-- *** END DOCUMENT HEAD *** -->


<body>


<!-- PAGE -->
<div id="page">



<!-- ECHO CODE OUTPUT -->
<?php 
echo $_PAGE['masthead']; 

echo $_HTML['demo1'];
echo $_HTML['demo2'];
echo $_HTML['demo3'];
echo $_HTML['demo4'];

// PAGE TIMER
$_tx2 = microtime();
$_tx3 = number_format(((substr($_tx2,0,9)) + (substr($_tx2,-10)) - (substr($_tx1,0,9)) - (substr($_tx1,-10))),4); 
echo "<div id=\"time\">page loaded in $_tx3 s</div>";

echo $_PAGE['footer']; 
?>

</div>	
<!-- end PAGE -->

<!-- Close HTML -->
</body>
</html>


<?php 
/*__END TEMPLATE______________________________________________________________*/


// POSTSCRIPTS
/*____________________________________________________________________________*/

// FLUSH
while (ob_get_level() > 0) 
{
	ob_end_flush();
}
flush();
echo "<!-- buffer flushed at " . date('r') . " -->";


echo "\n<!-- generated in $_tx3 s -->";
	

// PSEUDO-DAEMONS

/*____________________________________________________________________________*/

?>
