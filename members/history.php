<?php
session_start();
include("../vars.php");
include("../headfoot.php");
include("../auth.php");
mysql_connect($db_host, $db_user, $db_pwd);
mysql_select_db($db_name);
$res = checkauth();
$usrid = mysql_result($res, 0, "id");
$cshfrmallrefs = mysql_result($res, 0, "cshfrmallrefs");
$crdsfrmallrefs = mysql_result($res, 0, "crdsfrmallrefs");
$rbon_credits = mysql_result($res, 0, "rbon_credits");
$rpage_credits = mysql_result($res, 0, "rpage_credits");
$lifetime_cash = mysql_result($res, 0, "lifetime_cash");
$lifetime_paid = mysql_result($res, 0, "lifetime_paid");
$lifetot_roi = mysql_result($res, 0, "lifetot_roi");
$lifetime_credits = mysql_result($res, 0, "lifetime_credits");
$lifetime_pages = mysql_result($res, 0, "lifetime_pages");
$sb_credits = mysql_result($res, 0, "sb_credits");
$sb_cash = mysql_result($res, 0, "sb_cash");
$ptc_clicks = mysql_result($res, 0, "ptc_clicks");
$ptc_credits = mysql_result($res, 0, "ptc_crds");
$ptc_cash = mysql_result($res, 0, "ptc_cash");
secheader();

echo("<h4>Your Complete Account History</h4>");

echo("<div align=center><table border=\"0\"><tr><td align=center><img src=\"images/home2.png\" border=\"0\"><br><a href=\"".$self_url."members/index.php?".session_name()."=".session_id()."\">Member Homepage</a></td></tr></table></div>");

echo("<p><br><b>Your Account History</b><br>
Lifetime Earned Cash: \$$lifetime_cash<br>
Lifetime Cash Paid: \$$lifetime_paid<br>
Lifetime Upgrade Credit Earnings: \$$lifetot_roi<br><br>
Lifetime Credits Earned: $lifetime_credits<br>
Lifetime Pages Surfed: $lifetime_pages<br><br>
Referring Page Credits: $rpage_credits<br>
New Referral Bonus Credits: $rbon_credits<br>
Credits From All Referrals: $crdsfrmallrefs<br>
Cash From All Referrals: \$$cshfrmallrefs<br><br>
Lifetime Surf Bonus Credits Won: $sb_credits<br>
Lifetime Surf Bonus Cash Won: \$$sb_cash<br><br>
PTC Clicks: $ptc_clicks<br>
PTC Cash Earned: \$$ptc_cash<br>
PTC Credits Earned: $ptc_credits<br>");

echo("<h4>&nbsp;</h4></p>");

$get_history = mysql_query("SELECT * FROM investment_history WHERE usrid=$usrid ORDER BY adate");
$get_cashouts = mysql_query("SELECT * FROM cashout_history WHERE usrid=$usrid ORDER BY cdate");
$get_refcomms = mysql_query("SELECT * FROM comission_history WHERE paid_to=$usrid ORDER BY vdate");
$get_other = mysql_query("SELECT * FROM other_history WHERE usrid=$usrid ORDER BY adate");

echo("<p><b>Your Upgrade History</b><br>");
if (mysql_num_rows($get_history) != 0) {
echo("<table width=98% border=0 cellpadding=2 style=\"margin-left:10px;\">
<tr style=\"background-color: #4DA0C6\">
    <td align=center><b><font color=#FFFFFF>ID</font></b></td>
    <td align=center><b><font color=#FFFFFF>Date</font></b></td>
    <td align=center><b><font color=#FFFFFF>Amount</font></b></td>
    <td align=center><b><font color=#FFFFFF>Description</font></b></td>
    <td align=center><b><font color=#FFFFFF>Processor</font></b></td>
    <td align=center><b><font color=#FFFFFF>From</font></b></td></tr>");
for ($i = 0; $i < mysql_num_rows($get_history); $i++) {
$history_id = mysql_result($get_history, $i, "id");
$amount = mysql_result($get_history, $i, "amount");
$descr = mysql_result($get_history, $i, "descr");
$is_from = mysql_result($get_history, $i, "is_from");
$processor = mysql_result($get_history, $i, "processor");
$adate = mysql_result($get_history, $i, "adate");
echo("<tr style=\"background-color: #F0F8FF\">
    <td align=center>$history_id</td>
    <td align=center>$adate</td>
    <td align=center>\$$amount</td>
    <td align=center>$descr</td>
    <td align=center>$processor</td>
    <td align=center>$is_from</td></tr>");
}
echo("</table></p>");
} else {
echo("You do not have any upgrade history at this time.</p>");
}
echo("<p><br><b>Your Cashout History</b><br>");
if (mysql_num_rows($get_cashouts) != 0) {
echo("<table width=98% border=0 cellpadding=2 style=\"margin-left:10px;\">
<tr style=\"background-color: #4DA0C6\">
    <td align=center><b><font color=#FFFFFF>ID</font></b></td>
    <td align=center><b><font color=#FFFFFF>Date</font></b></td>
    <td align=center><b><font color=#FFFFFF>Amount</font></b></td>
    <td align=center><b><font color=#FFFFFF>Description</font></b></td>
    <td align=center><b><font color=#FFFFFF>Processor/Paid To</font></b></td></tr>");
for ($ii = 0; $ii < mysql_num_rows($get_cashouts); $ii++) {
$cashout_id = mysql_result($get_cashouts, $ii, "id");
$camount = mysql_result($get_cashouts, $ii, "amount");
$cdescr = mysql_result($get_cashouts, $ii, "descr");
$pay_merch = mysql_result($get_cashouts, $ii, "pay_merch");
$psid_to = mysql_result($get_cashouts, $ii, "paid_to");
$cdate = mysql_result($get_cashouts, $ii, "cdate");
echo("<tr style=\"background-color: #F0F8FF\">
    <td align=center>$cashout_id</td>
    <td align=center>$cdate</td>
    <td align=center>\$$camount</td>
    <td align=center>$cdescr</td>
    <td align=center>$pay_merch<br>$psid_to</td></tr>");
}
echo("</table></p>");
} else {
echo("You do not have any cashout history at this time.</p>");
}
echo("<p><br><b>Your Referral Upgrade Commissions</b><br>");
if (mysql_num_rows($get_refcomms) != 0) {
echo("<table width=98% border=0 cellpadding=2 style=\"margin-left:10px;\">
<tr style=\"background-color: #4DA0C6\">
    <td align=center><b><font color=#FFFFFF>ID</font></b></td>
    <td align=center><b><font color=#FFFFFF>Date</font></b></td>
    <td align=center><b><font color=#FFFFFF>Amount</font></b></td>
    <td align=center><b><font color=#FFFFFF>Description</font></b></td>
    <td align=center><b><font color=#FFFFFF>Referral</font></b></td></tr>");
for ($z = 0; $z < mysql_num_rows($get_refcomms); $z++) {
$comm_id = mysql_result($get_refcomms, $z, "id");
$comamount = mysql_result($get_refcomms, $z, "amount");
$wasfor = mysql_result($get_refcomms, $z, "wasfor");
$vdate = mysql_result($get_refcomms, $z, "vdate");
$cupline = mysql_result($get_refcomms, $z, "usrid");
echo("<tr style=\"background-color: #F0F8FF\">
    <td align=center>$comm_id</td>
    <td align=center>$vdate</td>
    <td align=center>\$$comamount</td>
    <td align=center>$wasfor</td>
    <td align=center>User $cupline</td></tr>");
}
echo("</table></p>");
} else {
echo("You do not have any referral commissions at this time.</p>");
}
echo("<p><br><b>Other Cash Credits</b><br>");
if (mysql_num_rows($get_other) != 0) {
echo("<table width=98% border=0 cellpadding=2 style=\"margin-left:10px;\">
<tr style=\"background-color: #4DA0C6\">
    <td align=center><b><font color=#FFFFFF>ID</font></b></td>
    <td align=center><b><font color=#FFFFFF>Date</font></b></td>
    <td align=center><b><font color=#FFFFFF>Amount</font></b></td>
    <td align=center><b><font color=#FFFFFF>Description</font></b></td>
    <td align=center><b><font color=#FFFFFF>From</font></b></td></tr>");
for ($v = 0; $v < mysql_num_rows($get_other); $v++) {
$oth_id = mysql_result($get_other, $v, "id");
$othamount = mysql_result($get_other, $v, "amount");
$other_descr = mysql_result($get_other, $v, "descr");
$oadate = mysql_result($get_other, $v, "adate");
$isfrm = mysql_result($get_other, $v, "is_from");
echo("<tr style=\"background-color: #F0F8FF\">
    <td align=center>$oth_id</td>
    <td align=center>$oadate</td>
    <td align=center>\$$othamount</td>
    <td align=center>$other_descr</td>
    <td align=center>$isfrm</td></tr>");
}
echo("</table></p>");
} else {
echo("You do not have any other cash credits at this time.</p>");
}

echo("<h4>&nbsp;</h4><div align=center><table border=\"0\"><tr><td align=center><img src=\"images/home2.png\" border=\"0\"><br><a href=\"".$self_url."members/index.php?".session_name()."=".session_id()."\">Member Homepage</a></td></tr></table></div>");
secfooter();
mysql_close;
exit;
?>
