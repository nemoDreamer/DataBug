<?php 

$target_path = 'uszips.gov.inc';
$source_path = 'us_census_zips.inc';
$skip_token = '%';

# open file (for reading)
$_handle = @fopen($source_path, "r");

# open file (for writing)
$_writeto = @fopen($target_path, 'w');

# header
$_header = <<<TEXT
% US zipcodes with populations greater than 20,000
% source : http://www.census.gov/tiger/tms/gazetteer/zips.txt
% key : ZIP, STATE, CITY, 1990 POP, LONGITUDE (West), LATITUDE (North)
% see also : http://www.census.gov/tiger/tms/gazetteer/zip90r.txt
% see also : http://en.wikipedia.org/wiki/List_of_ZIP_Codes_in_the_United_States


TEXT;

fwrite($_writeto, $_header)

# fetch file lines    
while ( !feof($_handle) )
{
	$_buffer = fgets($_handle, 4096);
	$_line = trim($_buffer);
	echo "read: $_line <br>";

	# check for skip token
	if ( substr($_line,0,1) <> $skip_token && !empty($_line) )
	{
		$DATA = explode(',', $_line);
		$_zip = str_replace('"', '', $DATA[1]);
		$_state = str_replace('"', '', $DATA[2]);
		$_city = str_replace('"', '', $DATA[3]);
		$_lon = str_replace('"', '', $DATA[4]);
		$_lat = $DATA[5];
		$_pop = $DATA[6]; 
	}
	print_r($DATA);
	echo "pop: $_pop <br>";
	
	# write filter
	if ( $_pop >= 20000 )
	{
		$_wline = "$_zip , $_state , $_city , $_pop , $_lon , $_lat \n";

		if ( fwrite($_writeto, $_wline) )
		{
			echo "write: $_wline <br>";
		}
		else
		{
			echo "0 <br>";
		}
	}
	
}

# close file
fclose($_handle);
 
?>
