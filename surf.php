<?php
session_start();
include("vars.php");
include("auth.php");
mysql_connect($db_host, $db_user, $db_pwd);
mysql_select_db($db_name);
$res = checkauth();
$usrid = mysql_result($res, 0, "id");
$acctype = mysql_result($res, 0, "acctype");
@mysql_free_result($res);
if ($acctype == 1) {$daily_max = $surf_max_free;} else {$daily_max = $surf_max_pro;}
if ($_SESSION['sess_data']['usrid'] != $usrid) {
	$_SESSION['sess_data']['cts'] = 0;
	$_SESSION['sess_data']['sts'] = 0;
	$_SESSION['sess_data']['pgv'] = 0;
	$_SESSION['sess_data']['usrid'] = $usrid;
}
$res = mysql_query("SELECT num FROM 7stat WHERE usrid=$usrid && date='".date('Y-m-d')."'");
if (mysql_num_rows($res) != 0)
{
	$crds_today = mysql_result($res, 0, "num");
}
else
{
	$crds_today = 0;
}
@mysql_free_result($res);
if ($crds_today >= $daily_max)
{
	echo("Sorry you have earned your daily maximum of $daily_max credits!<br />Please return tomorrow!<br /><br /><a href=\"$self_url\">Login to your account here</a>");
	session_destroy();
	mysql_close;
	exit;
}
@mysql_free_result($crds_today);
$res = mysql_query("select value from adminprops where field='negact'");
$_SESSION['sess_data']['negact'] = mysql_result($res, 0, "value");
@mysql_free_result($res);
$res = mysql_query("select value from adminprops where field='reftim'");
$_SESSION['sess_data']['reftim'] = mysql_result($res, 0, "value");
@mysql_free_result($res);
$res = mysql_query("select value from adminprops where field='contex'");
if (mysql_result($res, 0, "value") != 0) {
	$_SESSION['sess_data']['contex'] = mysql_result($res, 0, "value");
	@mysql_free_result($res);
	$res = mysql_query("select value from adminprops where field='contey'");
	$_SESSION['sess_data']['contey'] = mysql_result($res, 0, "value");
}
@mysql_free_result($res);
$res = mysql_query("select value from adminprops where field='contcx'");
if (mysql_result($res, 0, "value") != 0) {
	$_SESSION['sess_data']['contcx'] = mysql_result($res, 0, "value");
	@mysql_free_result($res);
	$res = mysql_query("select value from adminprops where field='contcy'");
	$_SESSION['sess_data']['contcy'] = mysql_result($res, 0, "value");
}
@mysql_free_result($res);
$res = mysql_query("select minmax from user where id=$usrid");
$rate = mysql_result($res, 0, "minmax");
$_SESSION['sess_data']['mmax'] = $rate;
switch($rate) {
	case 1:
		$rate = 'ratemin';
		break;
	case 0:
		$rate = 'ratemax';
		break;
	default:
		$rate = 'ratemax';
		break;
}
@mysql_free_result($res);
$res = mysql_query("select $rate, ref from acctype, user where acctype.id=user.acctype && user.id=$usrid");
$_SESSION['sess_data']['rate'] = mysql_result($res, 0, $rate);
$_SESSION['sess_data']['ref'] = mysql_result($res, 0, "ref");
@mysql_free_result($res);
$_SESSION['sess_data']['surfing'] = rand(9999, 9999999999);
$_SESSION['sess_data']['from'] = md5($_SESSION['sess_data']['surfing']);
if ($_GET['next'] == md5($_SESSION['sess_data']['surf_encoder_vals'])) {
	$s_bar_url = "surfbar.php?PHPSESSID=" . session_id() . "&vc_val=" . $_GET['next'];
} else {
	$s_bar_url = "surfbar.php?PHPSESSID=" . session_id() . "&vc_val=begin&coder=". md5($_SESSION['sess_data']['from']);
}
echo("<html>\n<head>\n<title>$title: Surf</title>\n<link rel=stylesheet type=text/css href=$self_url" . "style.css>\n</head>\n<frameset rows=90,* border=0><frame marginheight=0 marginwidth=0 scrolling=no noresize border=0 src=\"$s_bar_url\"><frame marginheight=0 marginwidth=0 scrolling=auto noresize border=0 src=./target.php></frameset>\n</html>");
mysql_close;
exit;
?>
