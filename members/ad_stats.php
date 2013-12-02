<?php
session_start();
include("../vars.php");
include("../headfoot.php");
include("../auth.php");
mysql_connect($db_host, $db_user, $db_pwd);
mysql_select_db($db_name);
$res = checkauth();
$usrid = mysql_result($res, 0, "id");
$res = mysql_query("SELECT ad_id,ad_name,type,num_exp,num_allow_exp,num_clicks FROM ad_info WHERE adv_user=$usrid ORDER BY ad_id ASC");
secheader();

echo("<h4>Banner & Text Ad Statistics</h4>");

echo("<div align=center><table border=\"0\"><tr><td align=center><img src=\"images/home2.png\" border=\"0\"><br><a href=\"".$self_url."members/index.php?".session_name()."=".session_id()."\">Member Homepage</a></td></tr></table></div>");

echo("<p><br><b>Banner & Text Ad Statistics</b><br>
If you purchase banner or text advertising, the stats (click through ratios, impressions, clicks etc..) can be viewed below.<br>Want to place an ad? <a href='./upgrade.php?".session_name()."=".session_id()."'>Click Here</a></p>");

echo("<table width=98% border=0 cellpadding=2 style=\"margin-left:10px;\">
	<tr style=\"background-color: #4DA0C6\">
                <td align=center><b><font color=#FFFFFF>Ad Name</font></b></td>
	<td align=center><b><font color=#FFFFFF>Ad Type</font></b></td>
	<td align=center><b><font color=#FFFFFF>Exposures</font></b></td>
	<td align=center><b><font color=#FFFFFF>Allowed Exp</font></b></td>
	<td align=center><b><font color=#FFFFFF>Clicks</font></b></td>
	<td align=center><b><font color=#FFFFFF>CTR</font></b></td></tr>\n");
while ($info = mysql_fetch_row($res)) {
print "<tr style=\"background-color: #F0F8FF\"><td>$info[1]</td><td>\n";
if ($info[2] == 1){
print "Banner Ad";
} else {
print "Text Ad";
}
print "</td><td align=center>$info[3]</td>\n";
if ($info[4] == 0){
print "</td><td align=center>Unlimited</td>\n";
} else {
print "</td><td align=center>$info[4]</td>\n";
}
print "<td align=center>$info[5]</td>\n";
if (($info[5] == 0) || ($info[3] == 0)){
$ctr = 0;
} else {
$ctr = $info[5] / $info[3];
$ctr = $ctr * 100;
$ctr = substr($ctr,0,5);
}
print "<td align=center>$ctr %</td></tr>\n";
$found = 1;
}
if (!$found){
print "<tr><td colspan=6>You do not have any banner or text ads running at this time.</td></tr></table>\n";
} else {
print "</table>\n";
}

echo("<h4>&nbsp;</h4><div align=center><table border=\"0\"><tr><td align=center><img src=\"images/home2.png\" border=\"0\"><br><a href=\"".$self_url."members/index.php?".session_name()."=".session_id()."\">Member Homepage</a></td></tr></table></div>");
secfooter();
mysql_close;
exit;
?>
