<?php
session_start();
include("../vars.php");
include("../headfoot.php");
include("../auth.php");
mysql_connect($db_host, $db_user, $db_pwd);
mysql_select_db($db_name);
$res = checkauth();
$usrid = mysql_result($res, 0, "id");
$acctype = mysql_result($res, 0, "acctype");
if ($acctype == 1) {
 header("Location: upgrade.php");
 mysql_close;
 exit;
}
secheader();
members_main_menu($members_menu);
// EDIT THE HTML BELOW THE NEXT LINE - DO NOT EDIT ANY OF THE ABOVE UNLESS YOU KNOW WHAT YOU ARE DOING! REUPLOAD THIS ORIGINAL FILE IF YOU EDIT THIS FILE AND IT DOES NOT WORK.
?>
<p>Upgraded members area</p>
<p>Place your upgraded member content here.</p>
<p><a href="/members/">Back to members area</a></p>

<?
secfooter();
mysql_close;
exit;
?>
