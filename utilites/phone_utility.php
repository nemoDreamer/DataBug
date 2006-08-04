<?php

$_mod_timer1 = $_cycle1 = microtime();

$target_path = 'us_areacodes.inc';
$source_path = 'areacodes.txt';
$skip_token = '%';

# open file (for reading)
$_handle = @fopen($source_path, "r");

# open file (for writing)
$_writeto = @fopen($target_path, 'w');

# control run (set to 1000000 for full run)
$_cycles = 1000000;
$_FLAG['debug'] = 0;

if ($_FLAG['debug']) $_cycles = 20;


# header
$_header = <<<TEXT
% Assigned US Areacodes
% source : http://www.nanpa.com/reports/reports_cocodes_assign.html
% key : STATE, AREA, FIRST3


TEXT;

fwrite($_writeto, $_header);

# fetch file lines    
while ( !feof($_handle) && $_cycles > 0 )
{
	$_FLAG['write'] = 0;
	$_cycles--;
	
	# cycle timer
	$_this_cycle = microtime();
	$_cycle_timer = number_format(((substr($_this_cycle,0,9)) + (substr($_this_cycle,-10)) - (substr($_cycle1,0,9)) - (substr($_cycle1,-10))),2);
	if ($_FLAG['debug']) echo "timer: $_cycle_timer <br>";
	if ( $_cycle_timer > 20 ) 
	{
		set_time_limit(25);
		$_cycle1 = microtime();
		echo '<h1>resetting timer</h1>';
	}
	
	# round counter
	$_round++;
	if ( $_round % 10000 == 0 ) echo "<h1>round: $_round</h1>";
	
	$_buffer = fgets($_handle, 4096);
	$_line = trim($_buffer);
	
	if ($_FLAG['debug']) echo "read: $_line <br>";

	# check for skip token
	if ( substr($_line,0,1) <> $skip_token && !empty($_line) )
	{
		# breakup line
		$_line = preg_replace('%(\s|-)+%i', ',', $_line);
		if ($_FLAG['debug']) echo "$_line <br>";
	
		$DATA = explode(',', $_line);
		$_state = str_replace('"', '', $DATA[0]);
		$_area = str_replace('"', '', $DATA[1]);
		$_first3 = str_replace('"', '', $DATA[2]);
		
		$_FLAG['write'] = ( $DATA[3] <> 'UA' ) ? 1 : 0; 
	}

	if ($_FLAG['debug']) print_r($DATA);
	
	# write filter
	if ( $_FLAG['write'] )
	{
		$_wline = "$_state , $_area , $_first3 \n";

		if ( fwrite($_writeto, $_wline) )
		{
			echo ". ";
		}
		else
		{
			echo "-";
		}
	}
	
}

# close file
fclose($_handle);

// TIMER
$_mod_timer2 = microtime();
$_mod_timer3 = number_format(((substr($_mod_timer2,0,9)) + (substr($_mod_timer2,-10)) - (substr($_mod_timer1,0,9)) - (substr($_mod_timer1,-10))),4);

// STDOUT
echo "<h1>[$source_path] processed in $_mod_timer3 s</h1>";
 
?>
