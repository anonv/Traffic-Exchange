<?php
session_start();
include("vars.php");
include("headfoot.php");
mysql_connect($db_host, $db_user, $db_pwd);
mysql_select_db($db_name);
if (!isset($_SESSION['sess_data']['usrid']) && ($_GET['s'] == "" || $_POST['s'] == "")) {
	header("Location: $site_url"."surf.php?site-error");
	mysql_close;
	exit;
}
if (is_numeric($_GET['s'])) {
	$s = $_GET['s'];
} elseif (is_numeric($_POST['s'])) {
	$s = $_POST['s'];
} else {
	header("Location: $site_url"."surf.php?error");
	mysql_close;
	exit;
}
if ($_POST['form'] == 'sent') {
	if (is_numeric($s) && $s >= 1) {
		if ($_POST['text'] == "") {
			$text = "No commments given.";
		} else {
			$text = addslashes($_POST['text']);
			$text = addcslashes($_POST['text'], "%");
		}
		$res = mysql_query("insert into abuse (siteid, usrid, text) values ($s, " . $_SESSION['sess_data']['usrid'] . ", '$text')");
	}
	header("Location: $self_url" . "surf.php?done");
	mysql_close;
	exit;
} else {
	secheader();
	echo("<h4>Report Surf Abuse</h4>
<p><form action=$self_url" . "report.php method=post>
<input type=hidden name=form value=sent>
<input type=hidden name=s value=$s>
If you think this site in any way violates <b>$title</b> rules, please, type your complaint in the field below and press 'Send Report'. Reports are checked and verified by administration. If your report is found to be true, you may be rewarded.</p>
<p><textarea name=text cols=40 rows=10></textarea><br>
<input type=submit value=\" Send Report \" style=\"font-size: 11px; padding: 2px;\"></form></p>");
	secfooter();
	mysql_close;
	exit;
}
?>
