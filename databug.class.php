<?php

/***  DOCUMENTATION LAYER

	Databug Class

 		METHODS
		
			CONSTRUCTOR
			Databug()
			
			PUBLIC
			cx_identity($gender='*', $first_name='*', $last_name='*')
			cx_profile($gender='*', $first_name='*', $last_name='*', $state='*')
			cx_profile_by_zip_range($zip1, $zip2, $gender='*', $first_name='*', $last_name='*')
			set_gender($gender='*')
			set_first_name($first_name='*')
			set_last_name($last_name='*')
			set_zip_data_randomly($state='*')
			set_zip_data_randomly_by_range($zip1, $zip2)
			set_street($name='*', $suffix='*', $unit_num='*')
			set_user_name($user_name='*')
			set_email($obfuscate=1)
			set_phone($state='*')
			get_profile()
			
			
			PRIVATE
			_get_random_first_name($_gender='*')
			_get_random_last_name()
			_get_random_phone($state, $safe=1)
			_get_random_zip_data($state='rand')


	USAGE NOTES:
	
		see demo.php


______________________________________________________________________________*/

/**
 * Package Databug
 * generate data for testing purposes
 *
 * @package databug
 * @author Tom Atwell <klenwell@gmail.com>
 */
 
// Required Files
require_once('databug_abstract.class.inc.php');
 
 
/**
 * Class Databug
 *
 * NOTES
 *  for the purposes of this class: 1=male, 2=female
 *
 * @package databug
 * @author Tom Atwell <klenwell@gmail.com>
 * @version 0.4
 * @abstract
 */
class Databug extends DatabugAbstract
{
	/**
	 * @access public
	 * @var string
	 */
	var $gender = '';
	
	/**
	 * @access public
	 * @var string
	 */
	var $first_name = '';
	
	/**
	 * @access public
	 * @var string
	 */
	var $last_name = '';

	/**
	 * @access public
	 * @var string
	 */
	var $full_name = '';
	
	/**
	 * @access public
	 * @var string
	 */
	var $user_name = '';
	
	/**
	 * @access public
	 * @var string
	 */
	var $email = '';

	/**
	 * @access public
	 * @var string
	 */
	var $street = '';
	
	/**
	 * @access public
	 * @var string
	 */
	var $city = '';
	
	/**
	 * @access public
	 * @var string
	 */
	var $state = '';
	
	/**
	 * @access public
	 * @var string
	 */
	var $zip = '';

	/**
	 * @access public
	 * @var string
	 */
	var $phone = '';

	

  /**
   * Class Constructor
   * Usage: $Databug = new Databug();
   * 
   * @access public
   */
	function Databug()
	{
		$this->DatabugAbstract();
		return 1;
	}
	
	// PUBLIC METHODS
	
	/**
	 * constructs a basic user identity
	 *
	 * @access public
 	 * @param string $gender
	 * @param string $first_name
	 * @param string $last_name
	 * @param string $state
	 * @return array
	 */
	function cx_identity($gender='*', $first_name='*', $last_name='*')
	{
		# Primary Identity
		$this->set_gender($gender);
		$this->set_first_name($first_name);
		$this->set_last_name($last_name);
		
		# Secondary Identifiers
		$this->full_name = $this->first_name . ' ' . $this->last_name;
		$this->set_user_name();
		$this->set_email();
	}
	
	/**
	 * constructs a user profile
	 *
	 * @access public
 	 * @param string $gender
	 * @param string $first_name
	 * @param string $last_name
	 * @param string $state
	 * @return array
	 */
	function cx_profile($gender='*', $first_name='*', $last_name='*', $state='*')
	{
		// Get Identity
		$this->cx_identity($gender='*', $first_name='*', $last_name='*');
		
		// Get Contact Info
		$this->set_street();
		$this->set_zip_data_randomly($state);
		$this->set_phone($this->state);
	}
	
	
	/**
	 * constructs a user profile within a certain zipcode range
	 *
	 * @access public
 	 * @param string $gender
	 * @param string $first_name
	 * @param string $last_name
	 * @param string $state
	 * @return array
	 */
	function cx_profile_by_zip_range($zip1, $zip2, $gender='*', $first_name='*', $last_name='*')
	{
		# Get Profile
		$this->cx_profile($gender='*', $first_name='*', $last_name='*', $state='AK');
		
		# Reassign Zip Data by Zip Range and Phone
		$this->set_zip_data_randomly_by_range($zip1, $zip2);
		$this->set_phone($this->state);
	}
	
	
	/**
	 * @access public
	 * @param string $gender
	 * @return void
	 */
	function set_gender($gender='*')
	{
		// * DATA
		
			# Valid Array
			$_VALID = array( 'm', 'f', '*' );
		
			# Return
			$_this_gender = '';
		
		
		// * MANIPULATE
		
			# normalize strings
			if ( is_string($gender) )
			{
				if ( strlen($gender) > 1 )
				{
					$gender = substr($gender,0,1);
				}
			
				$gender = strtolower($gender);
			}
		
			# sanity check
			if ( !in_array($gender, $_VALID) )
			{
				trigger_error('invalid value -- gender will be picked at random', E_USER_NOTICE);
				$gender = '*';
			}
			
			# random gender
			if ( $gender == '*' )
			{
				$_this_gender = ( mt_rand(1,2) == 1 ) ? 'f' : 'm';
			}
			else
			{
				$_this_gender = $gender;
			}
			
		
		// * RETURN
		
			$this->gender = $_this_gender;
			return;
	}
	
	
	/**
	 * @access public
	 * @param string $first_name
	 * @return void
	 */
	function set_first_name($first_name='*')
	{
		// DATA
		
			# Return
			$_this_name = '';
		
		// MANIPULATE
		
			# Get Random Name
			if ( $first_name == '*' )
			{
				if ( !isset($this->gender) ) $this->set_gender();
				$_this_name = $this->_get_random_first_name($this->gender);
			}
			else
			{
				$_this_name = $first_name;
			}
			
			# Capitalize
			$_this_name = ucwords($_this_name);
			
			# Adjust Hyphenates
			if ( $_pos = strpos($_this_name, '-') )
			{
				$_this_name = substr($_this_name, 0, $_pos+1) . '-' . ucwords( substr($_this_name, $_pos+1) );
			}
		
		// RETURN
		
			$this->first_name = ucwords($_this_name);
			return;
	}
	
	
	/**
	 * @access public
	 * @param string $last_name
	 * @return void
	 */
	function set_last_name($last_name='*')
	{
		// DATA
		
			# Return
			$_this_name = '';
		
		// MANIPULATE
		
			# Get Random Name
			if ( $last_name == '*' )
			{
				$_this_name = $this->_get_random_last_name();
			}
			else
			{
				$_this_name = $last_name;
			}
			
			# Capitalize
			$_this_name = ucwords($_this_name);
			
			# Adjust Mc's
			if ( substr($_this_name, 0, 2) == 'Mc' )
			{
				$_this_name = 'Mc' . ucwords( substr($_this_name, 2) );
			}
		
		// RETURN
		
			$this->last_name = $_this_name;
			return;
	}
	
	
	/**
	 * @access public
	 * @return void
	 */
	function set_zip_data_randomly($state='*')
	{
		// DATA
		
			# Return
			# void

		
		// MANIPULATE
		
			# Get Random Name
			if ( $state == '*' )
			{
				$_ZIP = $this->_get_random_zip_data();
			}
			else
			{
				$_ZIP = $this->_get_random_zip_data($state);
			}
						
		
		// RETURN
		
			$this->zip = trim($_ZIP[0]);
			$this->state = trim($_ZIP[1]);
			$this->city = trim(ucwords(strtolower($_ZIP[2])));
			return;
	}
	
	
	/**
	 * @access public
	 * @param string $name
	 * @param string $suffix
	 * @return void
	 */
	function set_street($name='*', $suffix='*', $street_num='*', $unit_num='*')
	{
		// DATA
		
			# Date File
			$_fpath = $this->data_root . 'anchorage_streets.inc';
		
			# Return
			$street_name = '';
			
		
		// MANIPULATE
		
			# Get Unit Num
			if ( $unit_num='*' )
			{
				if ( mt_rand(1,3) % 3 == 0 )
				{
					$unit_num = mt_rand(1,1000);
					if ( mt_rand(1,2) % 2 == 0 )
					{
						$unit_num = '#' . ceil($unit_num % 10);
					}
					else
					{
						$unit_num = '#' . $unit_num;
					}
				}
				else
				{
					$unit_num = '';
				}
			}
			elseif ( !empty($unit_num) )
			{
				$unit_num = '#' . $unit_num;
			}
			else
			{
				$unit_num = '';
			}
		
			# Get Default Suffix
			if ( $suffix == '*' )
			{
				$_LINE = $this->_databug_thresh_file($_fpath, 1000);
				$_rand_line = $this->_databug_pick_random_array_item($_LINE);
				$_SUFFIX = explode(',', $_rand_line);
				$suffix = $_SUFFIX[1];
			}
			
			# Get Default Street Name
			if ( $name == '*' )
			{
				$_LINE = $this->_databug_thresh_file($_fpath, 1000);
				$_rand_line = $this->_databug_pick_random_array_item($_LINE);
				$_NAME = explode(',', $_rand_line);
				$name = $_NAME[0];
			}
			
			# Get Street Address Number
			if ( $street_num == '*' )
			{
				$street_num = mt_rand(1,12500);
			}

		
		// RETURN
		
			$this->street = trim(ucwords(strtolower("$street_num $name $suffix $unit_num")));
			return;
	}
	
	
	/**
	 * @access public
	 * @param string $user_name
	 * @return void
	 */
	function set_user_name($user_name='*')
	{
		// DATA
		
			# Return
			$_name = '';
		
		// MANIPULATE
		
			# Get Default User Name
			if ( $user_name == '*' )
			{
				if ( !isset($this->first_name) || !isset($this->last_name) )
				{
					trigger_error('first name and last name must be set to use * symbol', E_USER_ERROR);
					return 0;
				}
				
				$_name = substr($this->first_name,0,1) . $this->last_name;
			}
			
			# Set User Name
			else
			{
				$_name = $user_name;
			}
		
		// RETURN
		
			$this->user_name = strtolower($_name);
			return;
	}


	/**
	 * @access private
	 * @return string
	 */
	function set_email($obfuscate=1)
	{
		// DATA
			
  		# domains
  		$_SERVICE = array
  		(
  			'gmail.com',
				'gmail.com',
  			'yahoo.com',
				'yahoo.com',
  			'aol.com',
  			'aol.com',
  			'msn.com',
  			'hotmail.com',
				'att.com',
  			'sbcglobal.net',
  		);
      
      # return
      $_email = '';

		
		// MANIPULATE
		
  		# obfuscate
			if ( $obfuscate )
			{
	  		if ( mt_rand(1,2) % 2 == 0 )
  			{
  				$_ob = '_' . substr(uniqid('69'),-4);
  			}
	  		else
  			{
  				$_ob = mt_rand(10000,99999);
  			}
			}
  		
  		# domain
  		$_r = mt_rand(0, count($_SERVICE)-1);
  		$domain = $_SERVICE[$_r];
  		
  		# build
  		$_email = $this->user_name . $_ob . '@' . $domain;
				
		
		// RETURN
		
			$this->email = strtolower($_email);
			return;
	}
	
	
		/**
	 * @access public
	 * @return void
	 */
	function set_phone($state='*')
	{
		// DATA
		
			# Return
			# void

		
		// MANIPULATE
		
			# Get Random State
			if ( $state == '*' )
			{
				$_STATE = $this->_get_random_zip_data();
				$state = $_STATE[1];
			}

			# Get Random Phone
			$_phone = $this->_get_random_phone($state, 1);						
		
		// RETURN
		
			$this->phone = $_phone;
			return;
	}

			
	/**
	 * returns all basic attributes of object as array
	 *
	 * @access public
	 * @return array
	 */
	function get_profile()
	{
		// DATA
		
			$ARRAY = array();
			
		// MANIPULATE
		
			# gather data
			$_first_name = ( isset($this->first_name) ) ? $this->first_name : '';
			$_last_name = ( isset($this->last_name) ) ? $this->last_name : '';
			$_user_name = ( isset($this->user_name) ) ? $this->user_name : '';
			$_full_name = ( isset($this->full_name) ) ? $this->full_name : '';
			$_gender = ( isset($this->gender) ) ? $this->gender : '';
			$_email = ( isset($this->email) ) ? $this->email : '';
			$_street = ( isset($this->street) ) ? $this->street : '';
			$_zip = ( isset($this->zip) ) ? $this->zip : '';
			$_city = ( isset($this->city) ) ? $this->city : '';
			$_state = ( isset($this->state) ) ? $this->state : '';
			$_phone = ( isset($this->phone) ) ? $this->phone : '';
			
			# Add to Array
			$ARRAY = array
			(
				'first_name' => $_first_name,
				'last_name' => $_last_name,
				'user_name' => $_user_name,
				'full_name' => $_full_name,
				'gender' => $_gender,
				'email' => $_email,
				'street' => $_street,
				'zip' => $_zip,
				'city' => $_city,
				'state' => $_state,
				'phone' => $_phone,
			);
			
		// RETURN
		
			return $ARRAY;
	}
	
	
	
/* PRIVATE METHODS */
	
	/**
	 * @access private
	 * @return string
	 */
	function _get_random_first_name($_gender='*')
	{
		// DATA
			
			# data source
			$_PATH['source'] = '';
			$_PATH['fsource'] = $this->root . 'data/names_first_f.inc';
			$_PATH['msource'] = $this->root . 'data/names_first_m.inc';
			
			# internal
			$_NAMES = array();
		
			# Return
			$name = '';
		
		// MANIPULATE
		
			# sanity check
				
				# gender
				if ( !$_gender )
				{
					trigger_error('no gender provided -- gender will be picked at random', E_USER_NOTICE);
					$_gender = $this->set_gender();
				}
			
			# set source
			$_PATH['source'] = ( $_gender == 'f' ) ? $_PATH['fsource'] : $_PATH['msource'];
			
			# get lines
    	$_NAMES = $this->_databug_fetch_lines($_PATH['source']);
    
    	# DEBUG
    	#if ( $this->debug ) print_r($_NAMES);
    
    	# get random name
    	$_num_names = count($_NAMES);
    	while ( empty($name) )
  	  {
	      $_rand = mt_rand(0,$_num_names-1);
	      $name = $_NAMES[$_rand];
	    }
				
		
		// RETURN
		
			return $name;
	}
	
	
	/**
	 * @access private
	 * @return string
	 */
	function _get_random_last_name()
	{
		// DATA
			
	    # data source
      $_PATH['source'] = $this->root . 'data/names_last_us3100.inc';
			
			# internal
			$_NAMES = array();
      
      # return
      $name = '';

		
		// MANIPULATE
		
			# get lines
    	$_NAMES = $this->_databug_fetch_lines($_PATH['source']);
    
    	# DEBUG
    	#if ( $this->debug ) print_r($_NAMES);
    
    	# get random name
    	$_num_names = count($_NAMES);

    	while ( empty($name) )
  	  {
	      $_rand = mt_rand(0,$_num_names-1);
	      $name = $_NAMES[$_rand];
	    }
				
		
		// RETURN
		
			return $name;
	}
	
	
	/**
	 * @access private
	 * @return string
	 */
	function _get_random_phone($state, $safe=1)
	{
		// DATA
    
      # data source
      $_PATH['source'] = $this->root . 'data/us_areacodes_sampler.inc';
			
			# internal
			$_area = '';
			$_pre = '';
			$_post = '';
      
      # return
      $phone = '';

		
		// MANIPULATE
		
			# get lines
    	$_AREAS = $this->_databug_fetch_lines($_PATH['source']);
    
    	# DEBUG
    	#if ( $this->debug ) print_r($_NAMES);
			
			# filter by state
			if ( $state <> 'rand' )
			{
				foreach ( $_AREAS as $_line )
				{
					$_NUM = explode(',', $_line);
					if ( trim($_NUM[0]) == trim($state) )
					{
						$_CODES[] = $_line;
					}
				}
				
				$_AREAS = ( !empty($_CODES) ) ? $_CODES : $_AREAS;
			}

    	# get random values
    	$_num_codes = count($_AREAS);

    	while ( empty($_area) )
  	  {
	      $_rand = mt_rand(0,$_num_codes-1);
	      $_line = $_AREAS[$_rand];
				$_NUM = explode(',', $_line);
				$_area = trim($_NUM[1]);
				$_pre = trim($_NUM[2]);
	    }

			$_pre = ( $safe ) ? '555' : $_pre;
			
			for ( $_i=1; $_i<=4; $_i++ )
      {
      	$_post .= (string) mt_rand(0,9);
      }
			
			$phone = "({$_area}) {$_pre}-{$_post}";
				
		
		// RETURN
		
			return $phone;
	}
	

	
	/**
	 * @access private
	 * @return string
	 */
	function _get_random_zip_data($state='rand')
	{
		// DATA
    
      # data source
      $_PATH['source'] = $this->root . 'data/majoruszips.gov.inc';
			
			# internal
			$_ZIPS = array();
      
      # return
      $ZIP = array();

		
		// MANIPULATE
		
			# get lines
    	$_ZIPS = $this->_databug_fetch_lines($_PATH['source']);
    
    	# DEBUG
    	#if ( $this->debug ) print_r($_NAMES);
			
			# filter by state
			if ( $state <> 'rand' )
			{
				foreach ( $_ZIPS as $_line )
				{					
					$_ARRAY = explode(',', $_line);
					if ( $_ARRAY[1] == $state )
					{
						$_ZIPS2[] = $_line;
					}
				}
				
				$_ZIPS = ( !empty($_ZIPS2) ) ? $_ZIPS2 : $_ZIPS;
			}
    
    	# get random name
    	$_num_zips = count($_ZIPS);

    	while ( empty($ZIP[0]) )
  	  {
	      $_rand = mt_rand(0,$_num_zips-1);
	      $_line = $_ZIPS[$_rand];
				$ZIP = explode(',', $_line);
	    }
			
				
		
		// RETURN
		
			return $ZIP;
	}

	
	/**
	 * @access public
	 * @return string
	 */
	function set_zip_data_randomly_by_range($zip1, $zip2)
	{
		// DATA
    
      # data source
      $_PATH['source'] = $this->root . 'data/majoruszips.gov.inc';
			
			# internal
			$skip_token = '%';
			$_ZIPS = array();
			$_ratio = 0;
			$_i = 0;
			$_j = 0;
      
      # return
      $ZIP = array();

		
		// MANIPULATE
		
			// Order Arguments
			if ( $zip1 > $zip2 )
			{
				$_numt = $zip2;
				$zip2 = $zip1;
				$zip1 = $_numt;
			}
			
			// Set Ratio Denominator
			$_ratio = round( ( $zip2 - $zip1 ) / 20 );
			if ( !$_ratio ) $_ratio = 1;
			if ($this->debug) echo "thresh ratio for get_random_zip_data_by_range is $_ratio<br>";

	    # Open File (for reading)
  	  $_handle = @fopen($_PATH['source'], "r");
			
			# Set Mod Offset
			$_mod_offset = mt_rand(0, $_ratio-1);
    
	    # fetch file lines    
  	  while ( !feof($_handle) )
    	{
				$_i++;
				
	      $_buffer = fgets($_handle, 4096);
  	    $_line = ',' . trim($_buffer);
				
				if ( ($_i % $_ratio == $_mod_offset || strpos($_line,$zip1) == 1 || strpos($_line,$zip2) == 1) && substr($_line,0,1) <> $skip_token && !empty($_line) )
				{
					$_zip = substr($_line, 1, 5);
					if ( $_zip >= $zip1 && $_zip <= $zip2 )
					{
						$LINES[] = substr($_line,1);
						if ($this->debug) echo "fetching zip line #{$_i}<br>";
					}
				}
	    }
    
  	  # close file
    	fclose($_handle);
		
    	# DEBUG
    	if ( $this->debug ) print_r($LINES);
			
    	// Get Random Zip
			
				# Prep Loop
	    	$_num_zips = count($LINES);
				$_c = 5;
				
				# Loop
      	while ( empty($ZIP[0]) )
    	  {
  	      $_rand = mt_rand(0,$_num_zips-1);
  	      $_line = $LINES[$_rand];
  				$ZIP = explode(',', $_line);
  								
  				if ( !$_c-- )
  				{
  					trigger_error('not able to find zip', E_USER_WARNING);
  					return 0;
  				}
  	    }
				

		// RETURN
		
			$this->zip = trim($ZIP[0]);
			$this->state = trim($ZIP[1]);
			$this->city = trim(ucwords(strtolower($ZIP[2])));
			return;
	}

	
	/**
	 * @access private
	 * @return array
	 */
	function _databug_fetch_lines($source_path)
	{
		// *** DATA
		
			# internal
			$skip_token = '%';
  
	    # return
	    $LINES = array();
	
		// *** MANIPULATE
  
	    # sanity check
	    if ( !is_file($source_path) )
  	  {
    	  trigger_error("file [$source_path] not found", E_USER_WARNING);
      	return FALSE;
	    }
    
	    # open file (for reading)
  	  $_handle = @fopen($source_path, "r");
    
	    # fetch file lines    
  	  while ( !feof($_handle) )
    	{
	      $_buffer = fgets($_handle, 4096);
      
  	    $_line = trim($_buffer);
      
    	  # check for skip token
	      if ( substr($_line,0,1) <> $skip_token && !empty($_line) )
  	    {
    	    $LINES[] = $_line;
      	}
	    }
    
  	  # close file
    	fclose($_handle);
    
	    # DEBUG
  	  #print_r($LINES);
    
	    # catch
  	  if ( !count($LINES) )
    	{
	      trigger_error('no lines found', E_USER_WARNING);
  	  }
    	elseif ( count($LINES) == 1 )
	    {
  	    trigger_error('only 1 line fetch -> check EOL delimiter', E_USER_NOTICE);
    	}
	
		// *** RETURN
  
  	  return $LINES;
	}

}


// Testbed
/*____________________________________________________________________________*/

	// Get Zip Info by Range
	if ( 0 )
	{
		$Databug = new Databug();
		$Databug->set_debug_mode(1);
		$Databug->set_zip_data_randomly_by_range(90000, 93999);
		$Databug->set_street();
		echo "<h1>$Databug->set_street</h1><h1>$Databug->city, $Databug->state  $Databug->zip</h1>";
	}
	
/*____________________________________________________________________________*/



?>
