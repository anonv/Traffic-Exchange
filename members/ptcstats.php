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
if ($_POST['ptcid'] != "" && is_numeric($_POST['ptcid'])) {
	$getid = mysql_query("SELECT linkid FROM ptc_orders WHERE userid=$usrid && ptcid=$_POST[ptcid]");
	if (mysql_num_rows($getid) == 0) {
		header("Location: ptcstats.php");
		mysql_close;
		exit;
	} else {
		$ptc_linkid = mysql_result($getid, 0);
		$getst = mysql_query("SELECT * FROM ptc_tracking WHERE banlinkid='$ptc_linkid'");
		secheader();

echo("<h4>PTC Click Through Statistics</h4>");

echo("<div align=center><table border=\"0\"><tr><td align=center><img src=\"images/home2.png\" border=\"0\"><br><a href=\"".$self_url."members/index.php?".session_name()."=".session_id()."\">Member Homepage</a></td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td align=center><img src=\"images/ptcads.png\" border=\"0\"><br><a href=".$self_url."members/ptcstats.php?".session_name()."=".session_id().">Your PTC Ads</a></td></tr></table></div>

<p><br><b>PTC Statistics</b><br>
Below you will find the click through statistics of your purchased PTC advetisement.</p>");

?>

<table width=50% border=0 cellpadding=2 style="margin-left:10px;">
<tr style="background-color: #4DA0C6">
<td align=center width=45%><b><font color=#FFFFFF>Member ID #</b></font></td>
<td align=center width=55%><b><font color=#FFFFFF>Date Clicked</b></font></td>
</tr>

<?
if (mysql_num_rows($getst) != 0) {
	for ($d = 0; $d < mysql_num_rows($getst); $d++) {
		$clicker = mysql_result($getst, $d, "userid");
		$click_date = mysql_result($getst, $d, "cdate");
		echo("<tr style=\"background-color: #F0F8FF\">
<td align=center>ID #: $clicker</td>
<td align=center>Date: $click_date</td></tr>");
	}
} else {
	echo("<tr><td colspan=\"2\">You do not have any click through stats at this time.</td></tr>");
}
?>
</table>

<?
echo("<h4>&nbsp;</h4><div align=center><table border=\"0\"><tr><td align=center><img src=\"images/home2.png\" border=\"0\"><br><a href=\"".$self_url."members/index.php?".session_name()."=".session_id()."\">Member Homepage</a></td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td align=center><img src=\"images/ptcads.png\" border=\"0\"><br><a href=".$self_url."members/ptcstats.php?".session_name()."=".session_id().">Your PTC Ads</a></td></tr></table></div>");
secfooter();
mysql_close;
exit;
	}
} else {
	$getords = mysql_query("SELECT * FROM ptc_orders WHERE userid=$usrid");
	secheader();

echo("<h4>Your PTC Advertisements</h4>");

echo("<div align=center><table border=\"0\"><tr><td align=center><img src=\"images/home2.png\" border=\"0\"><br><a href=\"".$self_url."members/index.php?".session_name()."=".session_id()."\">Member Homepage</a></td></tr></table></div>

<p><br><b>Your PTC Advertisements</b><br>
If you purchase PTC advertising, the stats can be viewed below.<br>Want to place an ad? <a href='./upgrade.php?".session_name()."=".session_id()."'>Click Here</a></p>");

?>

<table width=98% border=0 cellpadding=2 style="margin-left:10px;">
<tr style="background-color: #4DA0C6">
<td align=center><b><font color=#FFFFFF>PTC ID</b></font></td>
<td align=center><b><font color=#FFFFFF>Clicks Ordered</b></font></td>
<td align=center><b><font color=#FFFFFF>Clicks Remaining</b></font></td>
<td align=center><b><font color=#FFFFFF>Click Stats</b></font></td>
    </tr>
<?
if (mysql_num_rows($getords) != 0) {
	for ($a = 0; $a < mysql_num_rows($getords); $a++) {
		$ptcid = mysql_result($getords, $a, "ptcid");
		$ptc_linkid = mysql_result($getords, $a, "linkid");
		$ordered_clicks = mysql_result($getords, $a, "amt_sent");
		$c_remain = mysql_result($getords, $a, "clicks_remain");
		if ($c_remain <= 0) {
			$date_done = mysql_result($getords, $a, "date_done");
		}
		echo("<tr style=\"background-color: #F0F8FF\">
<td align=center>$ptc_linkid</td>
<td align=center>$ordered_clicks</td>
<td align=center>$c_remain");
		if ($c_remain <= 0) {
			echo(" - Finished: $date_done");
		}
		echo("</td><form method=\"post\" name=\"vstats\" action=\"ptcstats.php\"><td align=center><input name=\"ptcid\" type=\"hidden\" value=\"$ptcid\"><input type=\"submit\" value=\" View Clicks \" style=\"font-size: 11px; padding: 2px;\"></td></form></tr>");
	}
} else {
	echo("<tr><td colspan=\"4\">You do not have any PTC orders at this time.</td></tr>");
}
?>
</table>

<?
echo("<h4>&nbsp;</h4><div align=center><table border=\"0\"><tr><td align=center><img src=\"images/home2.png\" border=\"0\"><br><a href=\"".$self_url."members/index.php?".session_name()."=".session_id()."\">Member Homepage</a></td></tr></table></div>");
secfooter();
mysql_close;
exit;
}
?>
