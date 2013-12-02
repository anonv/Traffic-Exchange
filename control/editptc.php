<?php
session_start();
include("../vars.php");
include("adminauth.incl.php");
mysql_connect($db_host, $db_user, $db_pwd);
mysql_select_db($db_name);
adminAuth();
if ($_POST['action'] != 'edit' && $_POST['action'] != 'editc') {
header("Location: ptc.php?page=edit");
mysql_close;
exit;
}
if ($_POST['action'] == 'editc' && $_POST['ptcid'] >= 1) {
if ($_POST['user_id'] == "" || !is_numeric($_POST['user_id'])) {
$errorz = $errorz . "Userid is blank or not a number!<br>";
}
if (($_POST['image_url'] == "" || !ereg('http://', $_POST['image_url'])) && $_POST['type'] == 'banner') {
$errorz = $errorz . "You must enter the banner URL!<br>";
}
if ($_POST['text_link'] == "" && $_POST['type'] == 'text') {
$errorz = $errorz . "You must enter the Link Text!<br>";
}
if ($_POST['link_url'] == "" || !ereg('http://', $_POST['link_url'])) {
$errorz = $errorz . "The Link URL is blank or invalid!<br>";
}
if ($_POST['timer'] == "" || !is_numeric($_POST['timer'])) {
$errorz = $errorz . "The timer is blank or not a number (enter 0 for no timer)!<br>";
}
if ($_POST['crdtype'] == 'credit' && ($_POST['click_crd_amount'] == "" || !is_numeric($_POST['click_crd_amount']))) {
$errorz = $errorz . "The credit amount is invalid!<br>";
}
if ($_POST['crdtype'] == 'cash' && ($_POST['click_csh_amount'] == "" || !is_numeric($_POST['click_csh_amount']))) {
$errorz = $errorz . "The credit amount is invalid!<br>";
}
if ($_POST['once_perds'] == "" || !is_numeric($_POST['once_perds'])) {
$errorz = $errorz . "Credit once per X amount of days is invalid!<br>";
}
if ($errorz == "") {
$doupd = mysql_query("UPDATE ptc_orders SET type='$_POST[type]', type2='$_POST[crdtype]', userid='$_POST[user_id]', banurl='$_POST[image_url]', linkurl='$_POST[link_url]', linktxt='$_POST[text_link]', clicks_remain='$_POST[clicks_remaining]', amt_sent='$_POST[amt_sent]', cash_click='$_POST[click_csh_amount]', credit_click='$_POST[click_crd_amount]', adtimer='$_POST[timer]', day_lock='$_POST[once_perds]' WHERE ptcid=$_POST[ptcid]") or die (mysql_error());
header("Location: ptc.php?page=edit");
mysql_close;
exit;
} else {
echo("<html>\n<head>\n<title>Edit PTC Advert Error</title>\n<meta http-equiv=\"Content-Type\" content=\"text/html; charset=iso-8859-1\">\n<script language=\"JavaScript\">\n<!--\ndefaultStatus = 'PTC Admin Area';\n//-->\n</script>\n</head>\n<body>\n");
echo("<p><font color=\"#FF0000\" size=\"2\" face=\"Tahoma\"><strong>ERROR:<br>$errorz</font></p>");
echo("<p><form method=post action=editptc.php name=redo><input type=hidden name=ptcid value=$_POST[ptcid]><input type=hidden name=action value=edit><input type=submit value=\"Go Back\"></form></p>");
echo("\n</body>\n</html>");
mysql_close;
exit;
}
} elseif ($_POST['action'] == 'edit' && $_POST['ptcid'] >= 1) {
$getptc = mysql_query("SELECT * FROM ptc_orders WHERE ptcid=$_POST[ptcid]");
if (mysql_num_rows($getptc) == 0) {
header("Location: ptc.php?page=edit");
mysql_close;
exit;
}
$adtype = mysql_result($getptc, 0, "type");
$crdtype = mysql_result($getptc, 0, "type2");
$owner = mysql_result($getptc, 0, "userid");
$banurl = mysql_result($getptc, 0, "banurl");
$linkurl = mysql_result($getptc, 0, "linkurl");
$linktext = mysql_result($getptc, 0, "linktxt");
$c_remain = mysql_result($getptc, 0, "clicks_remain");
$amt_sent = mysql_result($getptc, 0, "amt_sent");
$cash_click = mysql_result($getptc, 0, "cash_click");
$credit_click = mysql_result($getptc, 0, "credit_click");
$adtimer = mysql_result($getptc, 0, "adtimer");
$day_lock = mysql_result($getptc, 0, "day_lock");
?>
<html>
<head>
<title>Edit PTC Advert</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript">
<!--
defaultStatus = 'PTC Admin Area';
//-->
</script>
</head>
<body>
<form name="add_form" method="post" action="editptc.php">
<table align="center" cellpadding="2" cellspacing="0">
<tr>
<td width="642">
  <p align="center"><strong><font size="4" face="Tahoma">Edit PTC Advert</font></strong></p>
  <table width="640" border="0" align="center" cellpadding="2" cellspacing="0">
<input type='hidden' name='action' value='editc'><input type='hidden' name='ptcid' value='<? echo($_POST['ptcid']); ?>'>
<tr>
<td width="235"><font face='Tahoma' size='2'>Userid Number:</font></td>
<td width="397"><input type='text' name='user_id' size='15' value='<? echo($owner); ?>'></td></tr>
<tr>
  <td><font face='Tahoma' size='2'>Type:</font></td>
  <td><select name='type'>
      <option value='banner'<? if ($adtype == 'banner') { echo(" selected"); }?>>Banner</option>
      <option value='text'<? if ($adtype == 'text') { echo(" selected"); }?>>Text</option>
    </select>

<tr>
  <td><font face='Tahoma' size='2'>Credit Type:</font></td>
<td><select name='crdtype'><option value='credit'<? if ($crdtype == 'credit') { echo(" selected"); }?>>Credits</option><option value='cash'<? if ($crdtype == 'cash') { echo(" selected"); }?>>Cash</option></select>
<tr>
  <td><font face='Tahoma' size='2'>Banner URL:</font></td>
<td><input type='text' name='image_url' size='50' value='<? echo($banurl); ?>'></td></tr>
<tr>
  <td><font face='Tahoma' size='2'>Link URL:<br>
      <font size="1">Where
people go when the link is clicked.</font> </font></td>
<td><input type='text' name='link_url' size='50' value='<? echo($linkurl); ?>'></td></tr>
<tr>
  <td><font face='Tahoma' size='2'>Link Text:</font></td>
<td><textarea name='text_link' wrap='virtual' rows='4' cols='35'><? echo($linktext); ?></textarea></td></tr>
<tr>
  <td><font face='Tahoma' size='2'>Timer (in seconds):</font></td>
<td><input type='text' name='timer' size='5' value='<? echo($adtimer); ?>'></td></tr>
<tr>
  <td><font face='Tahoma' size='2'>Click Amount (credits):</font></td>
  <td><input type='text' name='click_crd_amount' size='15' value='<? echo($credit_click); ?>'>
  </td>
</tr>
<tr>
  <td><font face='Tahoma' size='2'>Click Amount (cash):</font></td>
<td><font size="2" face="Tahoma">$</font>  <input type='text' name='click_csh_amount' size='15' value='<? echo($cash_click); ?>'></td></tr>
<tr>
  <td><font face='Tahoma' size='2'>Total Clicks:</font></td>
  <td><input name='amt_sent' type='text' size="15" value='<? echo($amt_sent); ?>'>
  </td>
</tr>
<tr>
  <td><font face='Tahoma' size='2'>Total Clicks Remaining:</font></td>
<td><input name='clicks_remaining' type='text' size="15" value='<? echo($c_remain); ?>'></td></tr>
<tr>
  <td><font face='Tahoma' size='2'>Day Lock:<br>
      <font size="1">Amount of days between credit.
0 = once per member.</font> </font></td>
  <td><font size="2" face="Tahoma">One Click Every
    <input name="once_perds" type="text" size="2" maxlength="3" value='<? echo($day_lock); ?>'> 
      Days.</font></td></tr>
<tr><td colspan='2'>&nbsp;</td></tr>
<tr><td colspan='2' align='center'><input type='submit' value='Edit PTC Advert'></td></tr>
</table></td>
</tr></table>
</form>
<p align="center"><a href="<? echo("index.php?y=19"); ?>"><font size="2" face="Tahoma">Go Back to the Admin Area</font></a> - <a href="<? echo("ptc.php?page=edit"); ?>"><font size="2" face="Tahoma">Edit PTC Adverts</font></a> - <a href="<? echo("ptc.php?page=addnew"); ?>"><font size="2" face="Tahoma">Add New PTC Advert</font></a></p>
</body>
</html>
<?
mysql_close;
exit;
} else {
header("Location: ptc.php?page=edit");
mysql_close;
exit;
}
?>
