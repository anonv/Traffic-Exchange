<?php
session_start();
include("vars.php");
mysql_connect($db_host, $db_user, $db_pwd);
mysql_select_db($db_name);
$usrid = 'Guest';
$daily_max = 999999999999;
if ($_SESSION['sess_data']['usrid'] != $usrid) {
 $_SESSION['sess_data']['cts'] = 0;
 $_SESSION['sess_data']['sts'] = 0;
 $_SESSION['sess_data']['pgv'] = 0;
 $_SESSION['sess_data']['usrid'] = $usrid;
}
/*
if (mysql_num_rows($res) != 0)
{
	$crds_today = mysql_result($res, 0, "num");
}
else
{
	$crds_today = 0;
}
@mysql_free_result($res);
@mysql_free_result($crds_today);
*/
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
$_SESSION['sess_data']['mmax'] = 0;
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
$_SESSION['sess_data']['rate'] = 0.5;
$_SESSION['sess_data']['ref'] = 0;
$_SESSION['sess_data']['surfing'] = rand(9999, 9999999999);
$_SESSION['sess_data']['from'] = md5($_SESSION['sess_data']['surfing']);
if ($_GET['next'] == md5($_SESSION['sess_data']['surf_encoder_vals'])) {
 $s_bar_url = "surfbarvisitor.php?PHPSESSID=" . session_id() . "&vc_val=" . $_GET['next'];
} else {
 $s_bar_url = "surfbarvisitor.php?PHPSESSID=" . session_id() . "&vc_val=begin&coder=". md5($_SESSION['sess_data']['from']);
}
echo("<html>\n<head>\n<title>$title's Trial Surf</title>\n<link rel=stylesheet type=text/css href=$self_url" . "style.css>\n</head>\n<frameset rows=90,* border=0><frame marginheight=0 marginwidth=0 scrolling=no noresize border=0 src=\"$s_bar_url\"><frame marginheight=0 marginwidth=0 scrolling=auto noresize border=0 src=/target.php></frameset>\n</html>");
mysql_close;
exit;
?>
