<?php
session_start();
include("../vars.php");
include("../headfoot.php");
include("../auth.php");
mysql_connect($db_host, $db_user, $db_pwd);
mysql_select_db($db_name);
$res = checkauth();
$usrid = mysql_result($res, 0, "id");
if ($_GET['siteid'] <= 0 || !is_numeric($_GET['siteid']) || $allow_site_validation == 0) {
header("location:index.php?error");
mysql_close;
exit;
}
if ($_GET['siteid'] > 0 && is_numeric($_GET['siteid'])) {
$res = mysql_query("SELECT * FROM site WHERE usrid=$usrid && id=" . $_GET['siteid']);
if (mysql_num_rows($res) == 0) {
header("location:index.php");
mysql_close;
exit;
}
$_SESSION['sess_data']['zeesite'] = $siteid;
$_SESSION['sess_data']['usrauthent'] = rand(1000, 10000000);
$_SESSION['sess_data']['usrauthentica'] = md5($_SESSION['sess_data']['usrauthent']);
$siteurl = mysql_result($res, 0, "url");
$state = mysql_result($res, 0, "state");
if ($state == 'Waiting') {
echo("<html>\n<head>\n<title>$title - Confirming Your Site</title>\n</head><frameset rows=80,* border=0><frame marginheight=0 marginwidth=0 scrolling=no noresize border=0 src=\"./confirmbar.php?siteid=" . $_GET['siteid'] . "&action=go&vc=" . md5($_SESSION['sess_data']['usrauthent']) . "&".session_name() . "=" . session_id() . "\"><frame marginheight=0 marginwidth=0 scrolling=auto noresize border=0 src=\"$siteurl\"></frameset>\n</html>");
mysql_close;
exit;
} else {
secheader();
members_main_menu($members_menu);
echo("<p class=big><a href=./index.php?".session_name() . "=" . session_id() . " title=\"Back to main page\"><font face=$fontface size=2><b>User account #$usrid</b></font></a><font size=2 face=$fontface> : : Validate URL Error</p>");
echo("<br><br><p align=center><font face=$fontface size=2><b>You Can Not Approve This Site!</b></font></p>");
secfooter();
mysql_close;
exit; }
} else {
secheader();
members_main_menu($members_menu);
echo("<p class=big><a href=./index.php?".session_name() . "=" . session_id() . " title=\"Back to main page\"><font color=#000000 onmouseover=\"this.style.color='$links'\" onmouseout=\"this.style.color='#000000'\" face=$fontface size=2 style=\"text-decoration: none\"><b>User account #$usrid</b></font></a><font size=2 face=$fontface> : : Validate URL Error</p>");
echo("<br><br><p align=center><font face=$fontface size=2><b>No site to confirm - <a href=./index.php?".session_name() . "=" . session_id() . ">Click Here</a></b></font></p>");
secfooter();
mysql_close;
exit;
}
?>
