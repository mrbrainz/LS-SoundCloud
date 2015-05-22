<?php 
session_start();
# Include the Dropbox SDK libraries
require_once "Dropbox/autoload.php";
use \Dropbox as dbx;

# Useless bullshit asked of by DB

class AEStore implements Dropbox\ValueStore
{
    private $array;
    private $key;

    function __construct(&$array, $key)
    {
        $this->array = &$array;
        $this->key = $key;
    }

    function get()
    {
        if (isset($this->array[$this->key])) {
            return $this->array[$this->key];
        } else {
            return null;
        }
    }

    function set($value)
    {
        $this->array[$this->key] = $value;
    }

    function clear()
    {
        unset($this->array[$this->key]);
    }
}


include LOCATION_OF_WP_INSTALL_ON_SERVER.'/wp-blog-header.php';
header("HTTP/1.1 200 OK");
// if (!is_user_logged_in()) { wp_redirect(get_bloginfo('url')); exit; }

$appInfo = dbx\AppInfo::loadFromJsonFile("nsscjsonfmb.json");
if (isset($_GET['nsscid'])) { $_SESSION['nsscid'] = sc_decryptf($_GET['nsscid']); }
$aestore = new AEStore($_SESSION['aestore'],'nssc-dbauth');
$webAuth = new dbx\WebAuth($appInfo, "NSSC/1.0", SSL_DOMAIN."/nssc/dropboxauth/?authenticate",$aestore);

if (strpos( $_SERVER['QUERY_STRING'],'authorize') === 0 ) {
	$dbxd = 'authorize';
} 
elseif ((strpos( $_SERVER['QUERY_STRING'],'authenticate') !== false)) {
	$dbxd = 'authenticate';
}
else  {
	$dbxd = false;
}

switch ($dbxd) :

	case 'authorize' :
		header("Location: " . $webAuth->start());	
		exit;
	break;

	case 'authenticate' :
		if ($aestore->get() == $_GET['state']) {
			if (isset($_GET['code'])) {
				parse_str($_SERVER['QUERY_STRING'], $qs);
				list($accessToken, $dropboxUserId) = $webAuth->finish($qs);						
				update_user_meta($_SESSION['nsscid'],'dropboxtoken',$accessToken);
				update_user_meta($_SESSION['nsscid'],'dropboxuserid',$dropboxUserId);				
				$aestore->clear();
				wp_redirect(get_bloginfo('url').'/?dropboxsuccess');
				exit;
			} else {
				wp_redirect(get_bloginfo('url').'/?dropboxfail=nocode');
				exit;	
			}
		}
		else {
			wp_redirect(get_bloginfo('url').'/?dropboxfail=statemismatch');
			exit;
		}
	break;
	case false :
		wp_redirect(get_bloginfo('url'));
		exit;
	break;
endswitch;


/*
echo "1. Go to: " . $authorizeUrl . "\n";
echo "2. Click \"Allow\" (you might have to log in first).\n";
echo "3. Copy the authorization code.\n";
$authCode = \trim(\readline("Enter the authorization code here: "));

list($accessToken, $dropboxUserId) = $webAuth->finish($authCode);
print "Access Token: " . $accessToken . "\n"; */