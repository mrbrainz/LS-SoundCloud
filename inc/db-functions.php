<?php 

/* User token definition if $oundCloud has already been authorised */

if (is_user_logged_in()) {
	$id = get_current_user_id();
	$dbt = get_user_meta($id,'dropboxtoken',true);
	if ($dbt) {
		define('DB_TOKEN',$dbt);	
	}
	else { define('DB_TOKEN',false); }
}

require_once get_stylesheet_directory()."/inc/Dropbox/autoload.php";
use \Dropbox as dbx;

if (DB_TOKEN) {
	$appInfo = dbx\AppInfo::loadFromJsonFile(get_stylesheet_directory()."/inc/nsscjsonfmb.json");
	$dbxClient = new dbx\Client(DB_TOKEN, "NSSC/1.0");
}

function outputDBUsage($used,$quota) {

	/* percentage of disk used - this will be used to also set the width % of the progress bar */
	$dp = sprintf('%.2f',($used / $quota) * 100);
	
	/* and we formate the size from bytes to MB, GB, etc. */
	$free = formatSize(($quota-$used));
	
	$output = '<div class="progress">
        <div class="prgbar" style="width: '.$dp.'%;"></div>
		<div class="prgtext">'.$dp.'% Used | '.$free.' Free</div>
</div>';
	return $output;
}
function formatSize( $bytes )
{
        $types = array( 'B', 'KB', 'MB', 'GB', 'TB' );
        for( $i = 0; $bytes >= 1024 && $i < ( count( $types ) -1 ); $bytes /= 1024, $i++ );
                return( round( $bytes, 2 ) . " " . $types[$i] );
}
