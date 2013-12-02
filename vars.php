<?php
//===========Your Sites Main Settings===========//

//Database host (usually localhost):
$db_host = 'localhost';

//Database username:
$db_user = 'cs2_demo';

//Database password:
$db_pwd = 'demo';

//Database name:
$db_name = 'cs2_paidauto';

//Payout Merchants (follow suit - "1" => "PayPal", "2" => "Payza" etc...):
$payout_merchants = array("1" => "PayPal", "2" => "Payza", "3" => "Bitcoin");


##################################################################
/*
Don't go below this point, Unless you know what to do.
*/
###################################################################
//Main header root loaction:
$m_header = $_SERVER['DOCUMENT_ROOT'] . '/header.php';

//Main footer root loaction:
$m_footer = $_SERVER['DOCUMENT_ROOT'] . '/footer.php';

//Members header root loaction:
$mem_header = $_SERVER['DOCUMENT_ROOT'] . '/header.php';

//Members footer root loaction:
$mem_footer = $_SERVER['DOCUMENT_ROOT'] . '/footer.php';
if(!function_exists('getdbconfvars')){
	function getdbconfvars(){
		global $db_host, $db_name, $db_user, $db_pwd;
		@mysql_connect($db_host, $db_user, $db_pwd);
		@mysql_select_db($db_name);
		$confvars = array();

		$confrs = mysql_query("SELECT * FROM adminprops");
		if(mysql_num_rows($confrs) > 0){
			while ($conf = mysql_fetch_array($confrs)){
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
	@extract(getdbconfvars());
	$siteurl = str_replace('http://','',$self_url);
	$siteurl = str_replace('https://','',$siteurl);


	//Email headers
	$email_headers = "MIME-Version: 1.0\r\nContent-type: text/plain; charset=iso-8859-1\r\nFrom: \"$title Admin\" <noreply@$siteurl>\r\nReply-To: \"NoReply\" <noreply@$siteurl>\r\nX-Priority: 3\r\nX-Mailer: PHP 4\r\n";

	//Surf bar banner HTML:
	$surf_ban_rotator = "<script language='JavaScript' src='$self_url/banner.php?style=non_ssi'></script>";

	@session_name("TrafExchange");
}
?>
