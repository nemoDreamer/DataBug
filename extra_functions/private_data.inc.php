<?php

/***  DOCUMENTATION LAYER

	Databug Extra Function File

	Package: Databug
	File: private_data.inc.php
	Last Update: Jun 2006
	Author: Tom Atwell (klenwell@gmail.com)

	FUNCTIONS:
	
		set_random_credit_card_number()
		set_random_password($length='*')
		set_random_ssn()


  NOTES:
	
		USE WITH CAUTION!

______________________________________________________________________________*/



// set_random_credit_card_number
/*____________________________________________________________________________*/
function set_random_credit_card_number() 
{
	// *** DATA
	
		# return
		$card_number = '';
		
	
	// *** MANIPULATE
	
		// build random card number
		for ( $_i=1; $_i<=19; $_i++ )
		{
			if ( $_i % 5 == 0 )
			{
				$card_number .= ' ';
			}
			else
			{
				$card_number .= (string) mt_rand(0,9);
			}
		}
	
	// *** RETURN
	
		return $card_number;

} # end Fx
/*____________________________________________________________________________*/



// get_credit_card_number
/*____________________________________________________________________________*/
function set_random_password($length='*') 
{
	// *** DATA
	
		# Return
		$password = '';
		
	
	// *** MANIPULATE
	
		// Check Length
		if ( $length == '*' || !is_numeric($length) )
		{
			$length = mt_rand(6,15);
		}
		
		// Get Random Password
		$password = substr(uniqid('drc'),-$length);
		
	
	// *** RETURN
	
		return $password;

} # end Fx
/*____________________________________________________________________________*/



// set_random_ssn
/*____________________________________________________________________________*/
function set_random_ssn() 
{
	// *** DATA
	
		# Return
		$ssn = '';
		
	
	// *** MANIPULATE
	
		// Get Random SSN
		$ssn = (string) mt_rand(0,9) . (string) mt_rand(0,9) . (string) mt_rand(0,9) . '-' . (string) mt_rand(0,9) . (string) mt_rand(0,9) . (string) mt_rand(0,9) . '-' . (string) mt_rand(0,9) . (string) mt_rand(0,9) . (string) mt_rand(0,9) . (string) mt_rand(0,9);
		
	
	// *** RETURN
	
		return $ssn;

} # end Fx
/*____________________________________________________________________________*/



// Testbed
/*____________________________________________________________________________*/


/*____________________________________________________________________________*/

?>
