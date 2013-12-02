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
if (isset($_POST['sid']) && is_numeric($_POST['sid'])) {
	$sid = $_POST['sid'];
	$langs = array("English" => "English", "Arabic" => "Arabic", "Chinese" => "Chinese", "Czech" => "Czech", "Danish" => "Danish", "Dutch" => "Dutch", "Estonian" => "Estonian", "Finnish" => "Finnish", "French" => "French", "German" => "German", "Greek" => "Greek", "Hebrew" => "Hebrew", "Hungarian" => "Hungarian", "Icelandic" => "Icelandic", "Italian" => "Italian", "Japanese" => "Japanese", "Korean" => "Korean", "Latvian" => "Latvian", "Lithuanian" => "Lithuanian", "Norwegian" => "Norwegian", "Polish" => "Polish", "Portuguese" => "Portuguese", "Romanian" => "Romanian", "Russian" => "Russian", "Spanish" => "Spanish", "Swedish" => "Swedish", "Turkish" => "Turkish");
	if ($sid == 0) {
		secheader();

echo("<h4>Add A New Website</h4>");

echo("<div align=center><table border=\"0\"><tr><td align=center><img src=\"images/home2.png\" border=\"0\"><br><a href=\"".$self_url."members/index.php?".session_name()."=".session_id()."\">Member Homepage</a></td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td align=center><img src=\"images/websites.png\" border=\"0\"><br><a href=./sitelist.php?".session_name()."=".session_id().">Your Websites</a></td></tr></table></div>");

		echo("<p><br><b>Add A New Website</b><br>Fill out the small form below to add a new website. You are the only one who will see the Website Name. Using a short Website Name will keep your website list more uniform.</p>");
		echo("<p><table width=98% border=0 cellpadding=2 style=\"margin-left:10px;\"><form action=index.php method=post>
		<input type=hidden name=fform value=site>
		<input type=hidden name=sid value=$sid>
		<tr><td valign=top width=150><b>Website Name:</b></td><td align=left>
		<input type=text name=sname></td></tr>
		<tr><td valign=top><b>Website URL:</b></td>
		<td align=left><input type=text name=surl value=\"http://\"></td></tr>
		<tr><td valign=top><b>Website Language:</b></td>
		<td align=left><select name=slangms>");
		while(list($k, $v) = each($langs)) {
			echo("\n<option value=$k>$v</option>");
		}
		echo("</select></td></tr><tr><td>&nbsp;</td><td align=left><input type=submit value=\" Add Website \" style=\"font-size: 11px; padding: 2px;\"></td></tr></form></table></p>");

echo("<h4>&nbsp;</h4><div align=center><table border=\"0\"><tr><td align=center><img src=\"images/home2.png\" border=\"0\"><br><a href=\"".$self_url."members/index.php?".session_name()."=".session_id()."\">Member Homepage</a></td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td align=center><img src=\"images/websites.png\" border=\"0\"><br><a href=./sitelist.php?".session_name()."=".session_id().">Your Websites</a></td></tr></table></div>");
		secfooter();
	} else {
		$res = mysql_query("select name, url, lang, state, cph from site where id=$sid && usrid=$usrid");
		if (mysql_num_rows($res) == 0) {
			header("Location: ".$self_url."index.php?sid=".$sid);
			mysql_close;
			exit;
		}
		secheader();

echo("<h4>Edit Your Website</h4>");

echo("<div align=center><table border=\"0\"><tr><td align=center><img src=\"images/home2.png\" border=\"0\"><br><a href=\"".$self_url."members/index.php?".session_name()."=".session_id()."\">Member Homepage</a></td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td align=center><img src=\"images/websites.png\" border=\"0\"><br><a href=./sitelist.php?".session_name()."=".session_id().">Your Websites</a></td></tr></table></div>");

echo("<p><br><b>Edit Your Website</b><br>Fill out the small form below to edit your website. If you edit your website, it will have to go through the approval process again. You are the only one who will see the Website Name. Using a short Website Name will keep your website list more uniform.</p>");
		$sname = mysql_result($res, 0, "name");
		$sname = stripslashes($sname);
		$surl = mysql_result($res, 0, "url");
		$slang = mysql_result($res, 0, "lang");
		$sstate = mysql_result($res, 0, "state");
		$cph = mysql_result($res, 0, "cph");
		$usstates = array("Waiting" => "Waiting", "On hold" => "On hold");
		echo("<p><table width=98% border=0 cellpadding=2 style=\"margin-left:10px;\">
		<form action=index.php method=post>
		<input type=hidden name=fform value=site>
		<input type=hidden name=sid value=$sid>
		<tr><td valign=top width=150><b>Website Name:</b></td>
		<td align=left><input type=text name=sname value=\"$sname\"></td></tr>
		<tr><td valign=top><b>Website URL:</b></td>
		<td align=left><input type=text name=surl value=\"$surl\"></td></tr>
		<tr><td valign=top><b>Website Language:</b></td>
		<td align=left><select name=slang>
		");
		while (list($k, $v) = each($langs)) {
			echo("\n<option");
			if ($k == $slang) {echo(" selected");}
			echo(" value=$k>$v</option>");
		}
		echo("</select></td></tr>
		<tr><td valign=top><b>Website State:</b></td>
		<td align=left>");
		if ($sstate == 'Waiting' || $sstate == 'Suspended') {
			echo("\n<input type=hidden name=sstate value=$sstate>$sstate");
		} else {
			echo("<select name=sstate>");
			while (list($k, $v) = each($usstates)) {
				echo("\n<option");
				if ($k == $sstate) {echo(" selected");}
				echo(" value=\"$k\">$v</option>");
			}
			echo("</select>");
		}
		echo("</td></tr>");
		if ($acctype != 1) {echo("<tr><td valign=top><b>Max Credits Per Hour:</b></td>
		<td align=left><input type=text name=scph value=$cph> (MCPH)</td></tr>");}
		echo("<tr><td>&nbsp;</td>
<td><br><input type=submit value=\" Save Changes \" style=\"font-size: 11px; padding: 2px;\"></form></td></tr>
<tr><td>&nbsp;</td>
<td><form action=index.php method=post><input type=hidden name=fform value=site>
<input type=hidden name=sid value=$sid>
<input type=hidden name=fac value=trash>
<input type=submit value=\" Delete Website \" style=\"font-size: 11px; padding: 2px;\"></form></td></tr></table></p>");

echo("<h4>&nbsp;</h4><div align=center><table border=\"0\"><tr><td align=center><img src=\"images/home2.png\" border=\"0\"><br><a href=\"".$self_url."members/index.php?".session_name()."=".session_id()."\">Member Homepage</a></td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td align=center><img src=\"images/websites.png\" border=\"0\"><br><a href=./sitelist.php?".session_name()."=".session_id().">Your Websites</a></td></tr></table></div>");
		secfooter();
	}
} else {
	header("Location: ".$self_url."members/index.php?".$sid);
	mysql_close;
	exit;
}
mysql_close;
exit;
?>
