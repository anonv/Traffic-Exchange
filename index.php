<?php
session_start();
include("vars.php");
include("headfoot.php");
mysql_connect($db_host, $db_user, $db_pwd);
mysql_select_db($db_name);
if ($_SERVER['REMOTE_ADDR']=='127.0.0.1') {$_SERVER['REMOTE_ADDR']=$_SERVER['HTTP_X_FORWARDED_FOR'];}
if (isset($_GET['ref']) && is_numeric($_GET['ref']) && !isset($_SESSION['refdata'])) {
	$rck = mysql_query("SELECT id, acctype FROM user WHERE id=$_GET[ref] && status='Active'");
	if (mysql_num_rows($rck) != 0) {
		$acctype = mysql_result($rck, 0, "acctype");
		$creditme = mysql_result(mysql_query("SELECT rpgebonus FROM acctype WHERE id=$acctype"), 0);
		$_SESSION['ref'] = $_GET['ref'];
		$ipis = $_SERVER['REMOTE_ADDR'];
		$validip = 'no';
		@mysql_free_result($rck);
		if (is_string($ipis) && ereg('^([0-9]{1,3})\.([0-9]{1,3})\.' . '([0-9]{1,3})\.([0-9]{1,3})$', $ipis, $sect)) {
			if ($sect[1] <= 255 && $sect[2] <= 255 && $sect[3] <= 255 && $sect[4] <= 255) {
				$validip = 'yes';
				$recip = "$sect[1].$sect[2].$sect[3]";
			} else {
				$validip = 'no';
			}
		} else {
			$validip = 'no';
		}
		if ($validip == 'yes' && $recip != "" && $creditme > 0) {
			$_SESSION['ref'] = $_GET['ref'];
			$ressu = mysql_query("SELECT id FROM referstats WHERE usrid=$_GET[ref] && refip='$recip'") or die (mysql_error());
			if (mysql_num_rows($ressu) == 0) {
				$todayis = date("Y-m-d");
				$timeis = date("H:i:s");
				$htt_ref = $_SERVER['HTTP_REFERRER'];
				if ($htt_ref == "") {
					//
					$htt_ref = "Direct Request/Referring Info Blocked";
				}
				@mysql_query("INSERT INTO referstats (usrid, orgip, refip, cdate, ctime, httpref, browser) VALUES ($_GET[ref], '$ipis', '$recip', '$todayis', '$timeis', '$htt_ref', '".$_SERVER['HTTP_USER_AGENT']."')") or die (mysql_error());
				if ($creditme > 0) { @mysql_query("UPDATE user SET credits=credits+$creditme, rpage_credits=rpage_credits+$creditme, lifetime_credits=lifetime_credits+$creditme WHERE id=$_GET[ref]") or die (mysql_error()); $iearned_n = "Your sponsor just earned <b>$creditme</b> credits for showing you this page. Become a member and you can too!"; }
			}
			@mysql_free_result($ressu);
			header("location:index.php");
			exit();
		}
	} else {
		$_GET['ref'] = 0;
		@mysql_free_result($rck);
	}
}
uheader();
include("main_page.php");
ufooter();
mysql_close;
exit;
?>
