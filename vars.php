<?php
//===========Your Sites Main Settings===========//

//Database host (usually localhost):
$db_host = 'localhost';

//Database username:
$db_user = 'root';

//Database password:
$db_pwd = '';

//Database name:
$db_name = 'traffic';

//Payout Merchants (follow suit - "1" => "PayPal", "2" => "Payza" etc...):
$payout_merchants = array("1" => "PayPal", "2" => "Payza", "3" => "Bitcoin");

//website root
if( !defined('SITE_URL') ){
	if($_SERVER['HTTP_HOST'] == "localhost"){// For local
		define('SITE_URL', 'http://' . $_SERVER['HTTP_HOST'] . '/Traffic-exchange');
		define('SITEPATH', $_SERVER['DOCUMENT_ROOT'] . '/Traffic-exchange');
	}
	else{ // For Web
		define('SITE_URL', "http://" . $_SERVER['HTTP_HOST']);
		define('SITEPATH', $_SERVER['DOCUMENT_ROOT']);
	}
}


##################################################################
/*
Don't go below this point, Unless you know what to do.
*/
###################################################################
//Main header root loaction:
$m_header = SITEPATH . '/header.php';

//Main footer root loaction:
$m_footer = SITEPATH . '/footer.php';

//Members header root loaction:
$mem_header = SITEPATH . '/header.php';

//Members footer root loaction:
$mem_footer = SITEPATH . '/footer.php';

if(!function_exists('getdbconfvars')){
	function getdbconfvars(){
		global $db_host, $db_user, $db_pwd, $db_name;

		$mysqli = new mysqli($db_host, $db_user, $db_pwd, $db_name);

		// Check connection
		if ($mysqli -> connect_errno) {
		echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
		exit();
		}
		$confvars = array();

		$confrs = $mysqli ->query("SELECT * FROM adminprops");
		if($confrs ->num_rows > 0){
			while ($conf = $confrs -> fetch_array(MYSQLI_BOTH)){
				$confvars[$conf['field']] = stripslashes($conf['value']);
			}
		}else{
			exit('There is some error in installation.<br>Please Re-install the script');
		}
		//mysql_close();
		return $confvars;

	}
}
if(!defined('NEWINSTALLATION')){
	extract(getdbconfvars());
	$self_url = SITE_URL ."/";
	$siteurl = str_replace('http://','',SITE_URL);
	$siteurl = str_replace('https://','',$siteurl);


	//Email headers
	$email_headers = "MIME-Version: 1.0\r\nContent-type: text/plain; charset=iso-8859-1\r\nFrom: ".$title." Admin\" <noreply@".$siteurl.">\r\nReply-To: \"NoReply\" <noreply@".$siteurl.">\r\nX-Priority: 3\r\nX-Mailer: PHP 4\r\n";

	//Surf bar banner HTML:
	$surf_ban_rotator = "<script language='JavaScript' src='".SITE_URL."/banner.php?style=non_ssi'></script>";

	if(!isset($_SESSION)){
        session_start();
		session_name("TrafExchange");
    }
}
?>
