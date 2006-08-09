<?php

/***  DOCUMENTATION LAYER

	Databug Class

 		METHODS
		
			CONSTRUCTOR
			DatabugAbstract()
			
			PUBLIC
			set_debug_mode($on=1)
			load_extra_fx()
			get_sample_text($num_words, $source_fpath='default')
			
			PRIVATE
			_databug_pick_random_array_item($ARRAY)
			_databug_pick_int_from_range($num1, $num2)
			_databug_rip_file($fpath)
			_databug_fetch_lines($source_path)
			_databug_thresh_file($fpath, $ratio_denominator)
			_databug_normalize_gender($gender)
			_databug_int_to_ordinal($int)


	USAGE NOTES:
	
		for the purposes of this class: 1 = female, 2 = male
	
		see demo.php


______________________________________________________________________________*/

/**
 * Package DatabugAbstract
 * generate data for testing purposes
 *
 * @package Databug
 * @author Tom Atwell <klenwell@gmail.com>
 */
 
 
/**
 * Class DatabugAbstract
 *
 * @package databug
 * @author Tom Atwell <klenwell@gmail.com>
 * @version 0.4
 * @abstract
 */
class DatabugAbstract
{
	/**
	 * Databug root director
	 *
	 * @access public
	 * @var string
	 */
	var $root = '';

	/**
	 * directory holding data
	 *
	 * @access public
	 * @var string
	 */
	var $data_root = '';
	
	/**
	 * directory holding source texts
	 *
	 * @access public
	 * @var string
	 */
	var $text_root = '';
	
	/**
	 * debug setting
	 *
	 * @access public
	 * @var string
	 */
	var $debug = 0;

	

  /**
   * Class Constructor
   * Usage: Not meant to be called directly
   * 
   * @access public
   */
	function DatabugAbstract()
	{
		$this->root = dirname(__FILE__) . DIRECTORY_SEPARATOR;
		$this->data_root = $this->root . 'data' . DIRECTORY_SEPARATOR;
		$this->text_root = $this->root . 'texts' . DIRECTORY_SEPARATOR;
		return 1;
	}
	
	
	// PUBLIC METHODS
	/**
	 * loads extra data functions
	 *
	 * @access public
	 * @return void
	 */
	function set_debug_mode($on=1)
	{
		// MANIPULATE
		
			if ( $on )
			{
				echo '<p style="color:red;">Databug: debug is active</p>';
				$this->debug = 1;
			}
			else
			{
				$this->debug = 0;
			}
	}
		
	
	/**
	 * loads extra data functions
	 *
	 * @access public
	 * @return void
	 */
	function load_extra_fx()
	{
		// DATA
		
			$_driver = $this->root . 'extra_functions/_driver.inc.php';
		
		
		// MANIPULATE
		
			require_once($_driver);
		
		
		// RETURN
		
			return;	
	}
	
	
	/**
	 * @access public
	 * @return void
	 */
	function get_sample_text($num_words, $source_fpath='default')
	{
		// DATA
  		
  		# parameters
  		$max_sample_length = 500;	# words
  		$default_source = $this->text_root . 'lipsum.inc';
  		
  		# source
  		$source_fpath = ( $source_fpath == 'default' ) ? $default_source : $source_fpath;
  		
  		# regex
  		$_REGEX['stop_at_next_sent'] = '#([\.\?\!]+["\']*\s+\w)#U';
  		$_REGEX['start_at_next_sent'] = '#([\.\?\!]+["\']*\s+\w)#U';
  		
  		# flags
  		$_FLAG['loop_seam_found'] = 0;
  	
  		# return
  		$sample = '';
		
		
		// MANIPULATE  		
  					
  		# sanity check
  		if ( !is_file($source_fpath) )
  		{
  			trigger_error("file [$source_fpath] not found", E_USER_NOTICE);
  			return FALSE;
  		}
  		
  		# check num words
  		if ( $num_words > $max_sample_length ) 
  		{
  			trigger_error('maximum num of words in sample is 500, sample length will be 500 words', E_USER_NOTICE); 
  			$num_words = $max_sample_length; 
  		}
  	
  		# rip file
  		$_raw_source = $this->_databug_rip_file($source_fpath);
  		$_raw_source = strip_tags(trim($_raw_source));
  		
  		# strlen (PHP)
  		$_strlen = strlen($_raw_source);
  		
  		# random strpos
  		$_start_pos = mt_rand(1, $_strlen);
  		
  		# find start of next sentence
  		
  			# note: try 4 times (in case beginning at end of file)
  			$_num_tries = 0;
  			
  			while ( $_num_tries <= 4 && !$_FLAG['loop_seam_found'] )
  			{
  				$_num_tries++;
  				if ( preg_match($_REGEX['stop_at_next_sent'], $_raw_source, $MATCH, PREG_OFFSET_CAPTURE, $_start_pos) )
  				{
  					$_FLAG['loop_seam_found'] = 1;
  					$_loop_seam = $MATCH[1][1] + strlen($MATCH[1][0] - 1);
  				}
  				
  				# try new start position
  				$_start_pos = mt_rand(1, $_strlen);
  				
  				# reset to beginning of string on try 3
  				if ( $_num_tries == 3 )
  				{
  					$_start_pos = 0;
  				}
  			}
  			
  			# DEBUG
  			#trigger_notice("$_start_pos -> $_loop_seam", 'START POS -> LOOP SEAM');
  			#trigger_notice($MATCH[1][0]);
  				
  			# check
  			if ( !$_FLAG['loop_seam_found'] )
  			{
  				trigger_error('preg match failed', E_USER_WARNING);
  				return FALSE;
  			}
  		
  		# snakeloop text
  		$_snake_source_pt1 = substr($_raw_source, $_loop_seam) . "\n\n";
  		$_snake_source_pt2 = substr($_raw_source, 0, $_loop_seam);
  		$_snaked_source = trim($_snake_source_pt1 . $_snake_source_pt2);
  		
  		# DEBUG
  		#trigger_notice($_snaked_source);
  		
  		# trim by word (see http://www.php.net/manual/en/function.str-word-count.php#59170)
  		$_BLURB = preg_split("/\s+/", $_snaked_source, ($num_words+1));
    	unset($_BLURB[(sizeof($_BLURB)-1)]);
    	$sample =  implode(' ', $_BLURB);
  		
  		# finishing touches
  			
		
		// RETURN
		
			return $sample;
		
	}
	
	
	
/* PRIVATE METHODS */

	/**
	 * pulls a random element out of an array
	 *
	 * @access public
	 * @return void
	 */
	function _databug_pick_random_array_item($ARRAY)
	{
		// DATA
		
			# Return
			$item = '';
		
		
		// MANIPULATE
		
			// Sanity Check
			if ( !is_array($ARRAY) )
			{
				trigger_error('requires array', E_USER_WARNING);
				return 0;
			}
		
			// array_rand
			$item = $ARRAY[array_rand($ARRAY)];
		
		
		// RETURN
		
			return $item;
	}
	
	
	/**
	 * pulls a random integer between two integers
	 *
	 * @access public
	 * @return void
	 */
	function _databug_pick_int_from_range($num1, $num2=0)
	{
		// DATA
		
			# Return
			$int = 0;
		
		
		// MANIPULATE
		
			// Order Numbers
			if ( $num1 > $num2 )
			{
				$_numt = $num2;
				$num2 = $num1;
				$num1 = $_numt;
			}
		
			// Sanity Check
			if ( !is_int($num1) || !is_int($num2) )
			{
				trigger_error('numbers must be integers', E_USER_WARNING);
				return FALSE;
			}
		
			// array_rand
			$int = mt_rand($num1, $num2);
		
		
		// RETURN
		
			return $int;
	}

	
	/**
	 * @access public
	 * @return void
	 */
	function _databug_rip_file($fpath)
	{
		// DATA
		
  		# flags
  		$_FLAG['fx_exists'] = 1;
  	
  		# return
  		$file_content = '';
		
		
		// MANIPULATE
		
  		# sanity checks
  			
  			# file
  			if ( !is_file($fpath) )
  			{
  				trigger_error("file [$fpath] not found", E_USER_NOTICE);
  				return FALSE;
  			}
  			
  			# function
  			if ( !function_exists('file_get_contents') )
  			{
  				$_FLAG['fx_exists'] = FALSE;
  			}
  			
  		# rip file
  		if ( $_FLAG['fx_exists'] )
  		{
  			$file_content = file_get_contents($fpath);
  		}
  		else
  		{
  			$file_content = file($fpath);
  			$file_content = implode('', $file_content);
  		}
  		
  		# remove last EOL
  		#$file_content = substr($file_content,0,-1);
		
		
		// RETURN
		
			return $file_content;
		
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
      	return 0;
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
	
	
	/**
	 * @access private
	 * @return array
	 */
	function _databug_thresh_file($fpath, $ratio_denominator=3)
	{
		// *** DATA
	
			# internal
			$skip_token = '%';
			$_d = $ratio_denominator;
			$_i = 0;
  
	    # return
	    $LINES = array();
			
	
		// *** MANIPULATE
  
	    # sanity check
	    if ( !is_file($fpath) )
  	  {
    	  trigger_error("file [$fpath] not found", E_USER_WARNING);
      	return 0;
	    }
    
	    # open file (for reading)
  	  $_handle = @fopen($fpath, "r");
			
			# Set Mod Offset
			$_mod_offset = mt_rand(0, $_d-1);
    
	    # fetch file lines (feof not reliable -> see comments for feof at php.net)
  	  while ( $_buffer = fgets($_handle, 4096) )
    	{
				$_i++;
				
				if ( $_i % $_d <> $_mod_offset )
				{
					continue;
				}
	      
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
  	    trigger_error('only 1 line fetched -> check EOL delimiter', E_USER_NOTICE);
    	}
	
		// *** RETURN
		
  	  return $LINES;
	}
	
	
	/**
	 * normalizes gender to int value (female = 1, male = 2)
	 *
	 * @access public
	 * @return void
	 */
	function _databug_normalize_gender($gender)
	{
		// DATA
		
			# Control Array
			$_GENDER = array
			(
				'f' => 1,
				'm' => 2
			);
		
			# Return
			$gender_int = 0;
		
		
		// MANIPULATE
		
			# Strings
			if ( is_string($gender) )
			{
				$_cap = strtolower(substr($gender,0,1));
				if ( isset($_GENDER[$_cap]) )
				{
					$gender_int = $_GENDER[$_cap];
				}
				else
				{
					trigger_error('invalid gender -> may be 0, 1, m, or f', E_USER_WARNING);
					return 0;
				}
			}
			
			# Int
			elseif ( is_int($gender) )
			{
				if ( in_array($gender, $_GENDER) )
				{
					$gender_int = $gender;
				}
				else
				{
					trigger_error('gender must be set to 1 (female) or 2 (male)', E_USER_WARNING);
					return 0;
				}
			}
			
			# Catch
			else
			{
				trigger_error('invalid gender -> may be 0, 1, m, or f', E_USER_WARNING);
				return 0;
			}
	
		
		// RETURN
		
			return $gender_int;
		
	}	
	
	
	/**
	 * converts integer (eg, 2) to ordinal (eg, 2nd)
	 *
	 * @access public
	 * @return void
	 */
	function _databug_int_to_ordinal($number)
	{
		// DATA
		
			# Internal
			$suffix = '';
			
			# Return
			$ordinal = '';
			
		
		// MANIPULATE
		
			if ($number % 100 > 10 && $number %100 < 14)
			{
				$suffix = "th";
			}
			else
			{
				switch($number % 10) 
				{
					case 0:
						$suffix = "th";
						break;
						
					case 1:
						$suffix = "st";
						break;
						
					case 2:
						$suffix = "nd";
						break;
						
					case 3:
						$suffix = "rd";
						break;
						
					default:
						$suffix = "th";
						break;
				}
			}

    	$ordinal = "${number}<sup>$suffix</sup>";
		
		// RETURN
		
			return $ordinal;
		
	}	
	

}


// Testbed
/*____________________________________________________________________________*/

	if ( 0 )
	{
	}
	
/*____________________________________________________________________________*/



?>
