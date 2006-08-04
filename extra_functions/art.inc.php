<?php

/***  DOCUMENTATION LAYER

	Databug Extra Function File

	Package: Databug
	File: art.inc.php
	Last Update: Jun 2006
	Author: Tom Atwell (klenwell@gmail.com)

	FUNCTIONS:
	
		get_color($hex_code='*')	
		set_random_color()


  NOTES:

______________________________________________________________________________*/


// get_color
/*____________________________________________________________________________*/
function get_color($hex_code='*') 
{
	// *** DATA
	
		# Return
		$span = '';
		
	
	// *** MANIPULATE
	
		# Sanity Check
		
			# Random
			if ( $hex_code == '*' )
			{
				$hex_code = set_random_color();
			}
			
			# Valid Hex Code?
			if ( strlen($hex_code) != 3 && strlen($hex_code) != 6 )
			{
				trigger_error("invalid hex code [$hex_code]", E_USER_WARNING);
				return 0;
			}

  	
  	# Style
  	$color = "<span style=\"background:#{$hex_code}; padding:0 .5em; margin-right:.5em;\">&nbsp;</span><span style=\"color:#{$hex_code}\">#{$hex_code}</span>";
		
	
	// *** RETURN
	
		return $color;

} # end Fx
/*____________________________________________________________________________*/



// set_random_color
/*____________________________________________________________________________*/
function set_random_color() 
{
	// *** DATA
	
		# Return
		$hex_code = '';
		
	
	// *** MANIPULATE
	
  	# Get Color Components
  	$_rhex = str_pad(dechex(mt_rand(0,255)), 2, '0', STR_PAD_LEFT);
  	$_ghex = str_pad(dechex(mt_rand(0,255)), 2, '0', STR_PAD_LEFT);
  	$_bhex = str_pad(dechex(mt_rand(0,255)), 2, '0', STR_PAD_LEFT);
  	
  	# Conflate
  	$hex_code = "{$_rhex}{$_ghex}{$_bhex}";
		
	
	// *** RETURN
	
		return $hex_code;

} # end Fx
/*____________________________________________________________________________*/




// Testbed
/*____________________________________________________________________________*/

if ( 0 )
{
	echo get_color();
}

/*____________________________________________________________________________*/

?>
