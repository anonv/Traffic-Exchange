<?php
session_start();
include("../vars.php");
include("../headfoot.php");
include("../auth.php");
mysql_connect($db_host, $db_user, $db_pwd);
mysql_select_db($db_name);
$res = checkauth();
$usrid = mysql_result($res, 0, "id");
$date = date("Y-m-d");
checkPTCdata($date);
secheader();

echo("<h4>PTC Area</h4>");

echo("<div align=center><table border=\"0\"><tr><td align=center><img src=\"images/home2.png\" border=\"0\"><br><a href=\"".$self_url."members/index.php?".session_name()."=".session_id()."\">Member Homepage</a></td></tr></table></div>");

echo("<script language=\"JavaScript\">\nfunction openptc(tpge,pnme,w,h){\nsettings=\"width=\" + w + \",height=\" + h + \",scrollbars=yes,location=no,directories=no,status=1,menubar=no,toolbar=no,resizable=no\";\nwindow.open(tpge,pnme,settings);\n}\n//-->\n</script>");
echo("<p><br><b>$title's PTC Area</b><br>
<a href=\"./ptc.php?".session_name()."=".session_id()."\" target=_top>Refresh Page</a> to see if new advertisements are available.<br>Want to place an ad? <a href='./upgrade.php?".session_name()."=".session_id()."'>Click Here</a></p>");
$query = "SELECT COUNT(*) FROM ptc_orders WHERE clicks_remain > 0";
$result = mysql_query($query);
$row = mysql_fetch_row($result);
$count = $row[0];
mysql_free_result($result);
print "<p><table width=98% border=0 cellpadding=2 style=\"margin-left:10px;\">";
if (!$_GET['index'] || $_GET['index'] < 1) {
	$index = 0;
} else {
	$index = $_GET['index'];
}
if (!$rows)
$rows = 10;

$query = "SELECT * FROM ptc_orders WHERE clicks_remain > 0  ORDER BY cash_click DESC, credit_click DESC, linkid DESC LIMIT $index, $rows";
$query23 ="select * from ptc_orders WHERE clicks_remain > 0";
if ($result23 = mysql_query($query23))
$cnt=mysql_num_rows($result23);
$result = mysql_query($query);
$tott = 0;
while ($row = mysql_fetch_array($result)) {
	if ($row[day_lock] >= 1) {
		$the_sql_day = strftime("%Y-%m-%d", strtotime("$date + $row[day_lock] days ago"));
		$and_date_sql = " AND (cdate>'$the_sql_day')";
	} else {
		$and_date_sql = "";
	}
	$query1 = "SELECT * FROM ptc_tracking WHERE (userid=$usrid) AND (banlinkid='$row[linkid]')$and_date_sql";
	$result1 = mysql_query($query1) or die (mysql_error());
	$num = mysql_num_rows($result1);
	if ($num == 0) {
		$tott = $tott + 1;
		print "<tr style=\"background-color: #4DA0C6\"><td align=center><b><font color=#FFFFFF>Earn";
		if($row["type2"] == "cash") {
			print "<font color=\"000000\"> \$$row[cash_click] </font>";
		} else {
			print "<font color=\"000000\"> $row[credit_click] Credits </font>";
		}
		print "For Viewing The Below Banner Or Text Advertisement.</b></font></td></tr>";
		if ($row["type"] == "banner") {
	print "<tr><td align=center><a href=\"#\" onClick=\"openptc('paidtoclick.php?adid=$row[linkid]&action=start&".session_name()."=".session_id()."','PaidtoClickPage','640','480');\"><img src='" . $row["banurl"] . "' border='0'></a></td></tr>";
		} else {
			print "<tr><td align=center>" . $row["linktxt"] . "<br><a href=\"#\" onClick=\"openptc('./paidtoclick.php?adid=$row[linkid]&action=start&".session_name()."=".session_id()."','PaidtoClickPage','640','480');\"><b>Click Here To View</b></a></td></tr>";
		}
	print "<tr><td>&nbsp;</td></tr>";
	}
}
mysql_free_result($result);
if(($cnt>$rows) &&(($index+$rows)<=$cnt )) {
	print "<tr><td>&nbsp;</td></tr>";
	print "<tr><td align='right'><b><a href='$PHP_SELF?index=" . ($index + $rows) . "'>Next Page</a></b></font></td></tr>";
}
print "</table></p>";
if ($tott == 0) {
	print"<p><b>Sorry</b>, there are no banner or text ads left for you to click at this time.<br>Please check back later as new ads can become available at any time.</p>";
}

echo("<h4>&nbsp;</h4><div align=center><table border=\"0\"><tr><td align=center><img src=\"images/home2.png\" border=\"0\"><br><a href=\"".$self_url."members/index.php?".session_name()."=".session_id()."\">Member Homepage</a></td></tr></table></div>");
secfooter();
mysql_close;
exit;
?>
