<?php 

	require_once('databug.class.php');

	// Get Zip Info by Range
	if ( 1 )
	{
		$_tx1 = microtime();
		echo "<h1>Getting Random Zip Info Between Zip Codes 90000 and 93999</h1>";
		$Databug = new Databug();
		$Databug->set_debug_mode(1);
		$Databug->set_zip_data_randomly_by_range(90000, 93999);
		echo "<h3>$Databug->city, $Databug->state  $Databug->zip</h3>";

		$_tx2 = microtime();
		$_tx3 = number_format(((substr($_tx2,0,9)) + (substr($_tx2,-10)) - (substr($_tx1,0,9)) - (substr($_tx1,-10))),4); 
		echo "<p>completed in {$_tx3} s</p>";
	}

	
	// Get Random Profile by Range
	if ( 1 )
	{
		$_tx1 = microtime();
		
		echo '<h1>Constructing Random Profile Between Zip Codes 90000 and 93999</h1>';
		$Databug = new Databug();
		$Databug->set_debug_mode(1);
		$Databug->cx_profile_by_zip_range(90000, 93999, $gender='*', $first_name='*', $last_name='*');
		$DATA = $Databug->get_profile();
		$profile = print_r($DATA,1);
		echo "<pre>$profile</pre>";
		
		$_tx2 = microtime();
		$_tx3 = number_format(((substr($_tx2,0,9)) + (substr($_tx2,-10)) - (substr($_tx1,0,9)) - (substr($_tx1,-10))),4); 
		echo "<p>completed in {$_tx3} s</p>";
	}

	
	// Get Random Profiles
	if ( 1 )
	{
		$_tx1 = microtime();
	
		echo '<h1>Databug: Constructing 2 Random Profiles</h1>';
		$Databug = new Databug();
		$Databug->cx_profile();
		$DATA1 = $Databug->get_profile();
		$Databug->load_extra_fx();
		$DATA1['fav_color'] = set_random_color();
		$DATA1['password'] = set_random_password();
		$DATA1['credit_card'] = set_random_credit_card_number();
		$profile1 = print_r($DATA1,1);

		$Databug->cx_profile('M');
		$DATA2 = $Databug->get_profile();
		$profile2 = print_r($DATA2,1);
		
		echo "<pre>{$profile1}<br />{$profile2}</pre>";
		$_tx2 = microtime();
		$_tx3 = number_format(((substr($_tx2,0,9)) + (substr($_tx2,-10)) - (substr($_tx1,0,9)) - (substr($_tx1,-10))),4); 
		echo "<p>completed in {$_tx3} s</p>";
	}

	
	// Build SQL Insert Statement for Data
  if ( 1 )
  {
		$_tx1 = microtime();
	
		echo '<h1>SQL Insert Statement for Random Profile</h1>';
  	$Databug->cx_profile();
  	$DATA = $Databug->get_profile();
  	$profile = print_r($DATA,1);
		
  	echo "INSERT INTO `table` VALUES (null, '{$DATA['first_name']}', '{$DATA['last_name']}', '{$DATA['gender']}', '{$DATA['zip']}');";
		$_tx2 = microtime();
		$_tx3 = number_format(((substr($_tx2,0,9)) + (substr($_tx2,-10)) - (substr($_tx1,0,9)) - (substr($_tx1,-10))),4); 
		echo "<p>completed in {$_tx3} s</p>";
  }

	
	// Get Filler Text
	if ( 1 )
	{
		$_tx1 = microtime();
	
		echo '<h1>Get Filler Text (250 words)</h1>';
  	$text = $Databug->get_sample_text($num_words=250, $source_fpath='default');
		
  	echo $text;
		$_tx2 = microtime();
		$_tx3 = number_format(((substr($_tx2,0,9)) + (substr($_tx2,-10)) - (substr($_tx1,0,9)) - (substr($_tx1,-10))),4); 
		echo "<p>completed in {$_tx3} s</p>";
	}
?>
