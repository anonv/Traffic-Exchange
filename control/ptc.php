<?php
session_start();
include("../vars.php");
include("adminauth.incl.php");
mysql_connect($db_host, $db_user, $db_pwd);
mysql_select_db($db_name);
adminAuth();
$todaysdate = date("Y-m-d");
if ($_POST['action'] == 'add') {
	$errorz = "";
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
	if ($_POST['clicks_remaining'] <= 0 || !is_numeric($_POST['clicks_remaining'])) {
		$errorz = $errorz . "The total clicks is invalid!<br>";
	}
	if ($_POST['once_per'] == 'mye' && ($_POST['once_perds'] == "" || !is_numeric($_POST['once_perds']) || $_POST['once_perds'] <= 7)) {
		$errorz = $errorz . "Credit once per X amount of days is invalid!<br>";
	}
	if ($_POST['once_per'] == 'mye') {
		$oncer = $_POST['once_perds'];
	} else {
		$oncer = $_POST['once_per'];
	}
	if ($errorz == "") {
		$linkids = ranid(25);
		if ($_POST['type'] == 'text') {
			if ($_POST['crdtype'] == 'credit') {
				$upd = mysql_query("INSERT INTO ptc_orders (type, type2, userid, linkid, linkurl, linktxt, clicks_remain, amt_sent, date_sent, credit_click, adtimer, day_lock) VALUES ('text', 'credit', $_POST[user_id], '$linkids', '$_POST[link_url]', '$_POST[text_link]', $_POST[clicks_remaining], $_POST[clicks_remaining], '$todaysdate', $_POST[click_crd_amount], $_POST[timer], $oncer)") or die (mysql_error());
				$ddone = 'yes';
			} else {
				$upd = mysql_query("INSERT INTO ptc_orders (type, type2, userid, linkid, linkurl, linktxt, clicks_remain, amt_sent, date_sent, cash_click, adtimer, day_lock) VALUES ('text', 'cash', $_POST[user_id], '$linkids', '$_POST[link_url]', '$_POST[text_link]', $_POST[clicks_remaining], $_POST[clicks_remaining], '$todaysdate', $_POST[click_csh_amount], $_POST[timer], $oncer)") or die (mysql_error());

				$ddone = 'yes';}
		} else {
			if ($_POST['crdtype'] == 'credit') {
				$upd = mysql_query("INSERT INTO ptc_orders (type, type2, userid, linkid, linkurl, banurl, clicks_remain, amt_sent, date_sent, credit_click, adtimer, day_lock) VALUES ('banner', 'credit', $_POST[user_id], '$linkids', '$_POST[link_url]', '$_POST[image_url]', $_POST[clicks_remaining], $_POST[clicks_remaining], '$todaysdate', $_POST[click_crd_amount], $_POST[timer], $oncer)") or die (mysql_error());
				$ddone = 'yes';
			} else {
				$upd = mysql_query("INSERT INTO ptc_orders (type, type2, userid, linkid, linkurl, banurl, clicks_remain, amt_sent, date_sent, cash_click, adtimer, day_lock) VALUES ('banner', 'cash', $_POST[user_id], '$linkids', '$_POST[link_url]', '$_POST[image_url]', $_POST[clicks_remaining], $_POST[clicks_remaining], '$todaysdate', $_POST[click_csh_amount], $_POST[timer], $oncer)") or die (mysql_error());
				$ddone = 'yes';
			}
		}
	}
} elseif ($_POST['action'] == 'delete' && $_POST['ptcid'] >= 1) {
	$del = mysql_query("SELECT linkid FROM ptc_orders WHERE ptcid=$_POST[ptcid]");
	if (mysql_num_rows($del) != 0) {
		$delinkid = mysql_result($del, 0, "linkid");
		$upd = mysql_query("DELETE FROM ptc_orders WHERE ptcid=$_POST[ptcid]");
		$upd = mysql_query("DELETE FROM ptc_tracking WHERE banlinkid='$delinkid'");
	}
	header("Location: ptc.php?page=edit");
	mysql_close;
	exit;
}
?>
<html>
<head>
<title><? echo($title); ?> PTC Admin Area</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
<script language="JavaScript">
<!--
defaultStatus = 'PTC Admin Area';
//-->
</script>
</head>
<body>
<?
if ($errorz != "") {
?>
<p><font color="#FF0000" size="2" face="Tahoma"><strong>ERROR:<br>
<?
echo($errorz);
?>
<br>
</strong></font><font size="2" face="Tahoma"><a href="javascript:history.go(-1)">Go Back</a></font></p>
<?
} elseif ($ddone == 'yes') {
?>
<p><font color="#336600" size="2" face="Tahoma"><strong>SUCCESS: PTC Advert Successfully
      added</strong></font></p>
<?
}
if ($_GET['page'] == 'addnew') {
?>
<form name="add_form" method="post" action="ptc.php">
<table align="center" cellpadding="2" cellspacing="0">
<tr>
<td width="642">
  <p align="center"><strong><font size="4" face="Tahoma">Add New PTC Advert</font></strong></p>
  <p align="center"><font face='Tahoma' size='2'>If you are adding a text link,
      leave the <strong>Banner URL</strong> field blank. If you are adding a banner link, leave
      the <strong>Link Text</strong> field blank. If the <strong>Credit Type</strong> is cash the <strong>Click
      Amount (credits)</strong> does not apply, vica versa if the <strong>Credit Type</strong> is credits, the
      <strong>Click Amount (cash)</strong> does not apply.</font></p>
  <table width="640" border="0" align="center" cellpadding="2" cellspacing="0">
<input type='hidden' name='action' value='add'>
<tr>
<td width="235"><font face='Tahoma' size='2'>Userid Number:</font></td>
<td width="397"><input type='text' name='user_id' size='15'></td></tr>
<tr>
  <td><font face='Tahoma' size='2'>Type:</font></td>
  <td><select name='type'>
      <option value='banner'>Banner</option>
      <option value='text'>Text</option>
    </select>

<tr>
  <td><font face='Tahoma' size='2'>Credit Type:</font></td>
<td><select name='crdtype'><option value='credit'>Credits</option><option value='cash'>Cash</option></select>
<tr>
  <td><font face='Tahoma' size='2'>Banner URL:</font></td>
<td><input type='text' name='image_url' size='50'></td></tr>
<tr>
  <td><font face='Tahoma' size='2'>Link URL:<br>
      <font size="1">Where
people go when the link is clicked.</font> </font></td>
<td><input type='text' name='link_url' size='50'></td></tr>
<tr>
  <td><font face='Tahoma' size='2'>Link Text:</font></td>
<td><textarea name='text_link' wrap='virtual' rows='4' cols='35'></textarea></td></tr>
<tr>
  <td><font face='Tahoma' size='2'>Timer (in seconds):</font></td>
<td><input type='text' name='timer' size='5'></td></tr>
<tr>
  <td><font face='Tahoma' size='2'>Click Amount (credits):</font></td>
  <td><input type='text' name='click_crd_amount' size='15'>
  </td>
</tr>
<tr>
  <td><font face='Tahoma' size='2'>Click Amount (cash):</font></td>
<td><font size="2" face="Tahoma">$</font>  <input type='text' name='click_csh_amount' size='15'></td></tr>
<tr>
  <td><font face='Tahoma' size='2'>Total Clicks:</font></td>
<td><input name='clicks_remaining' type='text' size="15"></td></tr>
<tr>
  <td><font face='Tahoma' size='2'>Day Lock:<br>
      <font size="1">Amount of days between credit.
0 = once per member.</font> </font></td>
<td><select name='once_per'><option value='0'>One click per member</option><option value='1'>Once per day</option><option value='2'>Once every two days</option><option value='3'>Once every three days</option><option value='4'>Once every four days</option><option value='5'>Once every five days</option><option value='6'>Once every six days</option><option value='7'>Once per week</option><option value='mye'>I will enter this</option></select>
  <font size="2" face="Tahoma">  or One Click Every
  <input name="once_perds" type="text" size="2" maxlength="3"> 
    Days.</font></td>
</tr>
<tr><td colspan='2'>&nbsp;</td></tr>
<tr><td colspan='2' align='center'><input type='submit' value='Add New PTC Advert'></td></tr>
</table></td>
</tr></table>
</form>
<?
} elseif ($_GET['page'] == 'edit') {
	$getptcads = mysql_query("SELECT * FROM ptc_orders");
?>
<table width="666" align="center" cellpadding="2" cellspacing="0">
<tr>
<td width="613"><div align="center">
  <p><font size="4" face="Tahoma"><strong>Edit PTC Adverts</strong></font></p>
  <table width="625" cellpadding="2" cellspacing="0">
    <tr style="background-color: #4DA0C6">
      <td width="300"><strong><font size="2" face="Tahoma">Linkid</font></strong></td>
      <td width="39"><strong><font size="2" face="Tahoma">Type</font></strong></td>
      <td width="87"><strong><font size="2" face="Tahoma">Total Clicks</font></strong></td>
      <td width="94"><strong><font size="2" face="Tahoma">Clicks Remain</font></strong></td>
      <td width="25"><div align="center"><strong><font size="2" face="Tahoma">Edit</font></strong></div></td>
      <td width="42"><div align="center"><strong><font size="2" face="Tahoma">Delete</font></strong></div></td>
    </tr>
<?
for ($i = 0; $i < mysql_num_rows($getptcads); $i++) {
	$ptcid = mysql_result($getptcads, $i, "ptcid");
	$linkid = mysql_result($getptcads, $i, "linkid");
	$adtype = mysql_result($getptcads, $i, "type");
	$tclicks = mysql_result($getptcads, $i, "amt_sent");
	$cremain = mysql_result($getptcads, $i, "clicks_remain");
	$ban_url = mysql_result($getptcads, $i, "banurl");
	$linkurl = mysql_result($getptcads, $i, "linkurl");
	$linktxt = mysql_result($getptcads, $i, "linktxt");
	echo("<tr><td><font size=\"2\" face=\"Tahoma\">$linkid</font></td><td><font size=\"2\" face=\"Tahoma\"><b>$adtype</b></font></td><td><font size=\"2\" face=\"Tahoma\">$tclicks</font></td><td><font size=\"2\" face=\"Tahoma\">$cremain</font></td><form name=\"edit\" method=\"post\" action=\"editptc.php\"><input type=\"hidden\" name=\"ptcid\" value=\"$ptcid\"><input type=\"hidden\" name=\"action\" value=\"edit\"><td><div><input type=\"Submit\" value=\"Edit\"></div></td></form><form name=\"remove\" method=\"post\" action=\"ptc.php\"><input type=\"hidden\" name=\"ptcid\" value=\"$ptcid\"><input type=\"hidden\" name=\"action\" value=\"delete\"><td><div><input type=\"Submit\" value=\"Delete\"></div></td></form></tr>");
	if ($adtype == 'banner') {
		echo("<tr><td><font size=\"2\" face=\"Tahoma\"><b>Advertisement:</b></td><td colspan=5></font><a href=\"$linkurl\" target=_blank><img src=\"$ban_url\" border=0></a></td></tr>
<tr><td colspan=6><hr></td></tr>");
	} else {
		echo("<tr><td><font size=\"2\" face=\"Tahoma\"><b>Advertisement:</b></td><td colspan=5><a href=\"$linkurl\" target=_blank>$linktxt</a></font></td></tr>
<tr><td colspan=6><hr></td></tr>");
	}
}
?>
  </table>
</div></td>
</tr></table>
<?
}
mysql_close;
?>
<p align="center"><a href="<? echo("index.php?y=19"); ?>"><font size="2" face="Tahoma">Go Back to the Admin Area</font></a> - <a href="<? echo("ptc.php?page=edit"); ?>"><font size="2" face="Tahoma">Edit PTC Adverts</font></a> - <a href="<? echo("ptc.php?page=addnew"); ?>"><font size="2" face="Tahoma">Add New PTC Advert</font></a></p>
</body>
</html>
<?
exit;
?>
