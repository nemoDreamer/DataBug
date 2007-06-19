<?php

/***  DOCUMENTATION LAYER

Databug Profile Class

Name: DatabugProfile
Version: 1.0
Last Update: Jun 2007
Author: Tom at klenwell@gmail.com

DESCRIPTION
	Extension of Databug class dedicated to creating mock profiles

METHODS
	MAGIC
	DatabugProfile($debug=0, $oid=null)		*php 4 constructor*
	__construct($debug, $oid)							*php 5 constructor*
	__destruct()	
	
	PUBLIC
	build_us_profile($state='*', $gender='*', $first_name='*', $last_name='*')
	build_us_profile_by_zip_range($zip1, $zip2, $gender='*', $first_name='*', $last_name='*')
	set_identity($gender='*', $first_name='*', $last_name='*')
	set_gender($gender='*')
	set_first_name($first_name='*')
	set_last_name($last_name='*')
	set_random_zipcode_by_state($state='*')
	set_random_zipcode_by_range($zip1, $zip2)
	set_street_address($name='*', $suffix='*', $street_num='*', $unit_num='*')
	set_random_us_phone($state='*')
	set_user_name($user_name='*')
	set_email($obfuscate=1)
	to_array()
	
	PRIVATE
	_get_random_last_name()
	_get_random_first_name($gender)
	_get_random_zip_data($state='*')
	_get_random_apt_num()
	_normalize_gender($value)
	_normalize_state($input)
	_get_valid_state_array()

	
USAGE
	$Class = new DatabugProfile();
	$Class->print_r('hello world']);

NOTES

______________________________________________________________________________*/

// Load File of Base Class
$base_fname = 'databug.class.php';
$base_dirpath = dirname(__FILE__) . DIRECTORY_SEPARATOR;
require_once($base_dirpath . $base_fname);


// DatabugProfile
/*____________________________________________________________________________*/
class DatabugProfile extends Databug
{
/* PUBLIC PROPERTIES */
var $debug = 0;
var $class_name = __CLASS__;
var $oid = '';
var $DS = DIRECTORY_SEPARATOR;

// profile properties
var $gender = 0;
var $gender_name = '';
var $first_name = '';
var $last_name = '';
var $full_name = '';
var $user_name = '';
var $email = '';
var $street_address = '';
var $city = '';
var $state = '';
var $zipcode = '';
var $phone = '';

// validity arrays
var $VALID = array();

/* PRIVATE PROPERTIES */
var $_filename = '';
var $_dirpath = '';


/* ** MAGIC METHODS ** */
// php4 constructor
function DatabugProfile($debug=0, $oid=null)
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
	
	// set validity arrays
	$this->VALID['GENDER_VAL'] = array( 1, 2 ); 
	$this->VALID['GENDER_NAME'] = array( 'F', 'M' );
	$this->VALID['GENDER'] = array_merge($this->VALID['GENDER_VAL'], $this->VALID['GENDER_NAME']);
	$this->VALID['STATES'] = $this->_get_valid_state_array();
	
	// parent constructor
	parent::__construct($debug, $oid);
	
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
// method: build us profile
function build_us_profile($state='*', $gender='*', $first_name='*', $last_name='*')
{
	$this->set_identity($gender, $first_name, $last_name);
	$this->set_street_address();
	$this->set_random_zipcode_by_state($state);
	$this->set_random_us_phone($this->state);
}
// END method

// method: build us profile
function build_us_profile_by_zip_range($zip1, $zip2, $gender='*', $first_name='*', $last_name='*')
{
	$this->set_identity($gender, $first_name, $last_name);
	$this->set_street_address();
	$this->set_random_zipcode_by_range($zip1, $zip2);
	$this->set_random_us_phone($this->state);
}
// END method

// method: set identity
function set_identity($gender='*', $first_name='*', $last_name='*')
{
	// primary identifiers
	$this->set_gender($gender);
	$this->set_first_name($first_name);
	$this->set_last_name($last_name);
	
	// secondary
	$this->full_name = $this->first_name . ' ' . $this->last_name;
	$this->set_user_name();
	$this->set_email();
}
// END method

// method: set gender
function set_gender($gender='*')
{
	$this->gender = 0;		// return

	// normalize gender
	if ( is_string($gender) )
	{
		$gender = substr($gender,0,1);
		$gender = strtoupper($gender);
	}

	// validity check
	if ( $gender != '*' && !in_array($gender, $this->VALID['GENDER']) )
	{
		trigger_error('invalid value -- gender will be picked at random', E_USER_NOTICE);
		$gender = '*';
	}
	
	// random gender
	if ( $gender == '*' )
	{
		$this->gender = mt_rand(1,2);
		$this->_normalize_gender($this->gender);
	}
	elseif ( is_numeric($gender) )
	{
		$this->gender = $gender;
		$this->_normalize_gender($this->gender);
	}
	else
	{
		$this->gender_name = $gender;
		$this->_normalize_gender($this->gender_name);
	}

	return $this->gender;
}
// END method

// method: set first name
function set_first_name($first_name='*')
{
	$this->first_name = '';		// return
	
	// Get Random Name
	if ( $first_name == '*' )
	{
		if ( !isset($this->gender) ) $this->set_gender();
		$this->first_name = $this->_get_random_first_name($this->gender);
	}
	else
	{
		$this->first_name = $first_name;
	}
	
	// capitalize
	$this->first_name = ucwords($this->first_name);
	
	// adjust hyphenates
	if ( $_pos = strpos($this->first_name, '-') )
	{
		$this->first_name = substr($this->first_name, 0, $_pos+1) . '-' . ucwords( substr($this->first_name, $_pos+1) );
	}
	
	return $this->first_name;
}
// END method

// method: set last name
function set_last_name($last_name='*')
{
	$this->last_name = '';		// return
	
	// get random name
	if ( $last_name == '*' )
	{
		$this->last_name = $this->_get_random_last_name();
	}
	else
	{
		$this->last_name = $last_name;
	}
	
	// capitalize
	$this->last_name = ucwords($this->last_name);
	
	// tweaks
	if ( substr($this->last_name, 0, 2) == 'Mc' ) $this->last_name = 'Mc' . ucwords( substr($this->last_name, 2) );
	
	return $this->last_name;
}
// END method

// method: set random zipcode by state
function set_random_zipcode_by_state($state='*')
{
	// normalize state
	if ( $state <> '*' )
	{
		$state = $this->_normalize_state($state);
		if ( !$state ) return 0;
	}

	// random state
	if ( $state == '*' )
	{
		$_ZIP = $this->_get_random_zip_data();
	}
	else
	{
		$_ZIP = $this->_get_random_zip_data($state);
	}

	$this->zipcode = trim($_ZIP[0]);
	$this->state = trim($_ZIP[1]);
	$this->city = trim(ucwords(strtolower($_ZIP[2])));
	return;
}
// END method

// method: set random zipcode by range
function set_random_zipcode_by_range($zip1, $zip2)
{
	$skip_token = '%';

	if ( $this->debug ) $this->print_d(__FUNCTION__ . " : setting a zipcode between $zip1 & $zip2");
	
	// order zips
	if ( $zip1 > $zip2 )
	{
		$_numt = $zip2;
		$zip2 = $zip1;
		$zip1 = $_numt;
	}
	
	// rip file
	$_LINES = $this->rip_file_to_array($this->SOURCE['us_zipcodes'], $include_empty=0, $mode='rb');
	
	// select zip codes in range
	foreach ( $_LINES as $i => $_line )
	{
		$_zip = substr($_line, 0, 5);
		if ( ( substr($_line,0,1) <> $skip_token ) && $_zip >= $zip1 && $_zip <= $zip2 )
		{
			$_ZIP_POOL[] = $_line;
			if ($this->debug) $this->print_d("adding record to zip pool: $_line");			
		}
	}
	
	// validity check
	if ( empty($_ZIP_POOL) )
	{
		trigger_error("unable to find any zips in range: $zip1 - $zip2", E_USER_WARNING);
		return 0;
	}
	
	// pick at random
	$data_line = $this->Randomizer->pick_random_array_item($_ZIP_POOL);
	$ZIP_DATA = explode(',', $data_line);
	if ( $this->debug ) $this->print_d(__FUNCTION__ . " : picked data line > $data_line");
	
	// parse result
	$this->zipcode = trim($ZIP_DATA[0]);
	$this->state = trim($ZIP_DATA[1]);
	$this->city = trim(ucwords(strtolower($ZIP_DATA[2])));
	return;
}
// END method

// method: set street address
function set_street_address($name='*', $suffix='*', $street_num='*', $apt_num='*')
{
	if ( $this->debug ) $this->print_d(__FUNCTION__ . ' : set street address');
	
	// random apartment num
	if ( $apt_num == '*' ) $apt_num = $this->_get_random_apt_num();
	
	// random suffix (St, Ave, Blvd, etc.)
	if ( $suffix == '*' )
	{
		$_LINE = $this->thresh_file($this->SOURCE['us_streets'], $ratio_denominator=1000, $this->SKIP_TOKENS);
		$_SUFFIX = explode(',', $this->Randomizer->pick_random_array_item($_LINE));
		$suffix = $_SUFFIX[1];
		if ( $this->debug ) $this->print_d("random suffix: $suffix");
	}
	
	// random street name
	if ( $name == '*' )
	{
		$_LINE = $this->thresh_file($this->SOURCE['us_streets'], $ratio_denominator=1000, $this->SKIP_TOKENS);
		$_NAME = explode(',', $this->Randomizer->pick_random_array_item($_LINE));
		$name = $_NAME[0];
		if ( $this->debug ) $this->print_d("random name: $name");
	}
	
	// street number (quick and dirty)
	if ( $street_num == '*' )
	{
		$street_num = mt_rand(1,12500);
		if ( $this->debug ) $this->print_d("random street number: $street_num");
	}
	
	// set street prop
	$this->street_address = trim(ucwords(strtolower("$street_num $name $suffix $apt_num")));
	if ( $this->debug ) $this->print_d("random street_address : {$this->street_address}");
	return;
}
// END method

// method: set random us phone
function set_random_us_phone($state='*')
{
	// get random state
	if ( $state == '*' )
	{
		$_ZIP_DATA = $this->_get_random_zip_data();
		$state = $_ZIP_DATA[1];
	}
	
	// get random phone
	$phone = $this->_get_random_us_phone($state, 1);
	
	// set prop
	$this->phone = $phone;
	return;
}
// END method

// method: set user name
function set_user_name($user_name='*')
{
	// Get Default User Name
	if ( $user_name == '*' )
	{
		if ( empty($this->first_name) || empty($this->last_name) )
		{
			trigger_error('first name and last name must be set to auto-set user name', E_USER_WARNING);
			return 0;
		}
		
		$user_name = substr($this->first_name,0,1) . $this->last_name;
	}
	// Set User Name
	else
	{
		$user_name = $user_name;
	}
	
	// set prop
	$this->user_name = strtolower($user_name);
	return;
}
// END method

// method: set email
function set_email($email='*', $obfuscate=1)
{
	// autoset
	if ( $email == '*' )
	{
		// check user name
		if ( empty($this->user_name) ) $this->set_user_name('*');
		
		// obfuscate
		$ob = '';
		if ( $obfuscate )
		{
			if ( mt_rand(1,2) == 1 )
			{
				$_ob = '_' . substr(uniqid('69'),-4);
			}
			else
			{
				$_ob = mt_rand(10000,99999);
			}
		}
		
		// get domain
		$domain = $this->_get_random_email_domain();
		
		// compile
		$email = strtolower($this->user_name . $_ob . '@' . $domain);
	}
	
	// cheap validity check
	if ( !strpos($email, '@') || !strpos($email, '.') )
	{
		trigger_error("invalid email addres [$email]", E_USER_WARNING);
		return 0;
	}
	
	$this->email = $email;
	return;
}
// END method

// method: to array
function to_array()
{
	$ARRAY = array();		// return
	
	$ARRAY['first_name'] = ( !empty($this->first_name) ) ? $this->first_name : null;
	$ARRAY['last_name'] = ( !empty($this->last_name) ) ? $this->last_name : null;
	$ARRAY['full_name'] = ( !empty($this->full_name) ) ? $this->full_name : null;
	$ARRAY['user_name'] = ( !empty($this->user_name) ) ? $this->user_name : null;
	$ARRAY['email'] = ( !empty($this->email) ) ? $this->email : null;
	$ARRAY['gender'] = ( !empty($this->gender) ) ? $this->gender : null;
	$ARRAY['gender_name'] = ( $this->gender == $this->female  ) ? 'female' : ( $this->gender == $this->male  ? 'male' : null );
	$ARRAY['street_address'] = ( !empty($this->street_address) ) ? $this->street_address : null;
	$ARRAY['city'] = ( !empty($this->city) ) ? $this->city : null;
	$ARRAY['state'] = ( !empty($this->state) ) ? $this->state : null;
	$ARRAY['zipcode'] = ( !empty($this->zipcode) ) ? $this->zipcode : null;
	$ARRAY['address1'] = $ARRAY['street_address'];
	$ARRAY['address2'] = "{$ARRAY['city']}, {$ARRAY['state']}  {$ARRAY['zipcode']}";
	$ARRAY['phone'] = ( !empty($this->phone) ) ? $this->phone : null;
	
	return $ARRAY;
}
// END method



/* ** PRIVATE METHODS ** */
// method: get random last name
function _get_random_last_name()
{
	$name = '';		// return
	
	if ( $this->debug ) $this->print_d('calling method ' . __FUNCTION__);

	// get names from file
	$_NAMES = $this->rip_lines($this->SOURCE['last_name'], $this->SKIP_TOKENS);
	$_num_names = count($_NAMES);
	
	// get name
	$i = 0;
	while ( empty($name) && $i < 25 )
	{
		$i++;
		$_rand = mt_rand(0,$_num_names-1);
		$name = $_NAMES[$_rand];
		if ( $this->debug ) $this->print_d("randomly selecting name: $name");
	}
	
	return $name;
}
// END method

// method: get random first name
function _get_random_first_name($gender)
{
	$name = '';		// return

	// sanity check
	$this->_normalize_gender($gender);
	
	// set source
	$source = ( $this->gender == $this->f_val ) ? $this->SOURCE['female_first'] : $this->SOURCE['male_first'];

	// get lines
	$_NAMES = $this->rip_lines($source, $this->SKIP_TOKENS);
	$_num_names = count($_NAMES);

	// get name
	$i = 0;
	while ( empty($name) && $i < 25 )
	{
		$i++;
		$_rand = mt_rand(0,$_num_names-1);
		$name = $_NAMES[$_rand];
	}

	return $name;
}
// END method

// method: get random zip data
function _get_random_zip_data($state='*')
{
	$ZIP_DATA = array();		// return
	
	// debug
	if ( $this->debug ) $this->print_d("getting zip data for state [$state]");
	
	// rip file
	$_ZIPS = $this->rip_lines($this->SOURCE['us_zipcodes'], $this->SKIP_TOKENS);
	
	// filter by state
	if ( $state <> '*' )
	{
		foreach ( $_ZIPS as $_line )
		{
			$_ARRAY = explode(',', $_line);
			if ( strtoupper($_ARRAY[1]) == $state )
			{
				$_ZIPS2[] = $_line;
			}
		}
		
		$_ZIPS = ( !empty($_ZIPS2) ) ? $_ZIPS2 : $_ZIPS;
	}
	
	// get random zip
	$_num_zips = count($_ZIPS);
	$loop_limit = 10;
	while ( empty($ZIP[0]) && $loop_limit )
	{
		$loop_limit--;
		$_rand = mt_rand(0,$_num_zips-1);
		$_line = $_ZIPS[$_rand];
		$ZIP_DATA = explode(',', $_line);
	}
	
	return $ZIP_DATA;
}
// END method

// method: get random us phone number
function _get_random_us_phone($state='*', $safe=1)
{
	$phone = '';		// return
	$safe_code = '555';
	
	if ( $this->debug ) $this->print_d(__FUNCTION__ . ' : selecting random us phone num');
	
	// rip file
	$_LINES = $this->rip_lines($this->SOURCE['us_phones'], $this->SKIP_TOKENS);

	// filter by state
	if ( $state <> '*' )
	{
		foreach ( $_LINES as $_line )
		{
			$_NUM = explode(',', $_line);
			if ( trim($_NUM[0]) == trim($state) )
			{
				$_CODES[] = $_line;
			}
		}
		
		$_LINES = ( !empty($_CODES) ) ? $_CODES : $_LINES;
	}

	// get random entry
	$data_line = $this->Randomizer->pick_random_array_item($_LINES);
	if ( $this->debug ) $this->print_d(__FUNCTION__ . " : picked data line > $data_line");
	
	// parse line
	$_NUM = explode(',', $data_line);
	$_area = trim($_NUM[1]);
	$_pre = trim($_NUM[2]);
	
	// safe area code?
	$_area = ( $safe ) ? $safe_code : $_area;
	if ( $this->debug && $safe ) $this->print_d("safe flag active -- setting area code to $safe_code");
	
	// last 4 digits
	for ( $_i=1; $_i<=4; $_i++ ) $_post .= (string) mt_rand(0,9);
	
	// set prop	
	$phone = "({$_area}) {$_pre}-{$_post}";
	if ( $this->debug ) $this->print_d("returning: $phone");
	return $phone;
}
// END method

// method: get random apartment number
function _get_random_apt_num()
{
	$apt_num = '';		// return
	$symbol = '#';
	
	if ( $this->debug ) $this->print_d(__FUNCTION__ . ' : selecting random apt num');

	// option: pct
	$_PCT['is_empty'] = 50;
	$_PCT['is_low_num'] = $_PCT['is_empty'] + 25;
	$_PCT['is_letter'] = $_PCT['is_low_num'] + 20;
	$_PCT['is_hi_num'] = $_PCT['is_letter'] + 5;
	if ( $this->debug ) $this->print_d('percentages: ' . $this->print_r($_PCT));
	
	// roll dice
	$roll = mt_rand(1,100);
	if ( $this->debug ) $this->print_d("rolled: $roll");
	
	// case tree
	if ( $roll <= $_PCT['is_empty'] )
	{
		if ( $this->debug ) $this->print_d("returning no apartment number");
		return '';
	}
	elseif ( $roll <= $_PCT['is_low_num'] )
	{
		$apt_num = ( mt_rand(1,2) == 1 ) ? mt_rand(1,8) : mt_rand(1,16);
	}
	elseif ( $roll <= $_PCT['is_letter'] )
	{
		$idx = ( mt_rand(1,2) == 1 ) ? mt_rand(0,8) : mt_rand(0,25);
		$symbol = ( mt_rand(1,3) >= 2 ) ? 'Apt. ' : 'Ste. ';
		$apt_num = strtoupper($this->alphaset{$idx});
	}
	else
	{
		$apt_num = mt_rand(100,999);
	}
	
	$apt_num = $symbol . $apt_num;
	if ( $this->debug ) $this->print_d("returning: $apt_num");
		
	return $apt_num;
}
// END method

// method: get random email domain
function _get_random_email_domain()
{
	$service = '';		// return

	$SERVICE = array
	(
		'gmail.com' => 5,
		'yahoo.com' => 4,
		'hotmail.com' => 3,
		'msn.com' => 2,
		'aol.com' => 2,
		'earthlink.net' => 1,
		'juno.net' => 1,
	);
	
	$PICK = $this->Randomizer->array_lottery($SERVICE, $num_picks=1);
	$service = $PICK[0];
	return $service;	
}
// END method

// method: normalize gender
function _normalize_gender($value)
{
	// normalize gender
	if ( is_string($value) )
	{
		$gender = substr($value,0,1);
		$gender = strtoupper($value);
	}
	
	// validity check
	if ( !in_array($value, $this->VALID['GENDER']) )
	{
		$list = print_r($this->VALID['GENDER'],1);
		trigger_error("invalid gender value -- must set to one of following: $list", E_USER_WARNING);
		return 0;
	}
	
	// set gender number and abbreviation
	if ( is_numeric($value) )
	{
		$this->gender = $value;
		if ( $value == $this->m_val ) $this->gender_name = $this->m_abr;
		else $this->gender_name = $this->f_abr;
	}
	else
	{
		$this->gender_name = $value;
		if ( $value == $this->m_abr ) $this->gender = $this->m_val;
		else $this->gender = $this->f_val;
	}
	
	return;
}
// END method

// method: normalize state
function _normalize_state($input)
{
	$state = '';		// return
	$this->print_d("normalizing state value ($input)");

	$upper = strtoupper($input);
	if ( in_array($upper, $this->VALID['STATES']) )
	{
		$state = array_search($upper, $this->VALID['STATES']);
		$this->debug("input [$input] normalized to [$state]");
	}
	elseif ( $state = array_search($upper, $this->VALID['STATES']) )
	{
		$state = $upper;
		$this->debug("input [$input] normalized to [$state]");
	}
	else
	{
		trigger_error("invalid state value [$input]");
		return 0;
	}
	
	return $state;
}
// END method

// method: set valid state array
function _get_valid_state_array()
{
	$ARRAY = array();		// return

	$STATES = array
	(
   'AL' => 'Alabama',
   'AK' => 'Alaska',
   'AR' => 'Arizona',
   'AZ' => 'Arkansas',
   'CA' => 'California',
   'CO' => 'Colorado',
   'CT' => 'Connecticut',
   'DE' => 'Delaware',
   'FL' => 'Florida',
   'GA' => 'Georgia',
   'HI' => 'Hawaii',
   'ID' => 'Idaho',
   'IL' => 'Illinois',
   'IN' => 'Indiana',
   'IA' => 'Iowa',
   'KS' => 'Kansas',
   'KY' => 'Kentucky',
   'LA' => 'Lousiana',
   'ME' => 'Maine',
   'MD' => 'Maryland',
   'MA' => 'Massachusetts',
   'MI' => 'Michigan',
   'MN' => 'Minnesota',
   'MS' => 'Mississippi',
   'MO' => 'Missouri',
   'MT' => 'Montana',
   'NE' => 'Nebraska',
   'NV' => 'Nevada',
   'NH' => 'New Hampshire',
   'NJ' => 'New Jersey',
   'NM' => 'New Mexico',
   'NY' => 'New York',
   'NC' => 'North Carolina',
   'ND' => 'North Dakota',
   'OH' => 'Ohio',
   'OK' => 'Oklahoma',
   'OR' => 'Oregon',
   'PA' => 'Pennsylvania',
   'RI' => 'Rhode Island',
   'SC' => 'South Carolina',
   'SD' => 'South Dakota',
   'TN' => 'Tennessee',
   'TX' => 'Texas',
   'UT' => 'Utah',
   'VT' => 'Vermont',
   'VA' => 'Virgina',
   'WA' => 'Washington',
   'WV' => 'West Virginia',
   'WI' => 'Wisconsin',
   'WY' => 'Wyoming',
   'DC' => 'District of Columbia'
	);
	
	foreach ( $STATES as $key => $state )
	{
		$abr = strtoupper($key);
		$ARRAY[$abr] = strtoupper($state);
	}
	
	return $ARRAY;
}
// END method

function _set_filename() { $this->_filename = basename(__FILE__); }
function _set_dirpath() { $this->_dirpath = dirname(__FILE__) . $this->DS; }

} // end class
/*____________________________________________________________________________*/

?>
