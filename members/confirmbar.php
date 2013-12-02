<?php
session_start();
include("../vars.php");
include("../headfoot.php");
include("../auth.php");
mysql_connect($db_host, $db_user, $db_pwd);
mysql_select_db($db_name);
$res = checkauth();
$usrid = mysql_result($res, 0, "id");
if ($allow_site_validation == 0) {
	echo("<html><head><title>$title</title>
link rel=stylesheet type=text/css href=../style.css>\n");
	echo("<script language=JavaScript>\nif (parent.location.href == self.location.href) {window.location.href = 'index.php?".session_name() . "=" . session_id() . "';}\n</script>\n");
	echo("</head><body><font face=$fontface size=2>
	<div align=center><b>This page is not enabled on this site!<br>
	<a href=index.php?".session_name() . "=" . session_id() . " target=_top>Members Area</a></body>
	</html>");
	mysql_close;
	exit;
}
$res = mysql_query("select value from adminprops where field='reftim'");
$wait_timer_amt = mysql_result($res, 0, "value") + 5;
if ($_GET['siteid'] && $_GET['action'] == 'go' && $_GET['vc'] == md5($_SESSION['sess_data']['usrauthent'])) {
	if (!isset($_SESSION['sess_data']['timesa']) || (time() - $_SESSION['sess_data']['timesa']) >= $wait_timer_amt) {
		$_SESSION['sess_data']['timesa'] = time();
	}
	if (!isset($delay)) {
		$delay = $wait_timer_amt;
	}
	$_SESSION['sess_data']['usrauthent'] = md5($_SESSION['sess_data']['usrauthent']);
	$_SESSION['sess_data']['usrauthentica'] = md5($_SESSION['sess_data']['usrauthent']);
	echo("<html>
	<head><title>$title</title>
	<link rel=stylesheet type=text/css href=../style.css>\n<meta http-equiv=\"imagetoolbar\" content=\"no\">\n");
	echo("<script language=JavaScript>\nif (parent.location.href == self.location.href) {
	window.location.href = './confirmsite.php?siteid=" . $_GET['siteid'] . "&".session_name() . "=" . session_id() . "';}\n</script>\n");
	echo("<script language=JavaScript>\ntop.window.moveTo(0,0);\nif (document.all) {\ntop.window.resizeTo(screen.availWidth,screen.availHeight);\n}\nelse if (document.layers||document.getElementById) {\nif\n(top.window.outerHeight<screen.availHeight||top.window.outerWidth<screen.availWidth){\ntop.window.outerHeight = screen.availHeight;\ntop.window.outerWidth = screen.availWidth;\n}\n}\n</script>\n");
	echo("<script language=JavaScript src=../confirm.js></script>\n");
	echo("</head>\n<body onLoad=\"init($delay, $wait_timer_amt);\" class=bar>\n");
	echo("<form name=f action=\"confirmbar.php\" method=\"GET\">
	<input type=hidden name=action value=approved>
	<input type=hidden name=".session_name() . " value=" . session_id() . ">
	<table border=0 width=100%><tr><td><input type=button name=m_text style=\"border: 0px;border-style: none;Color: $timer_txt;background-color: $timer_bg;text-align: center; top: 0; position: relative; float: center;\" onClick=\"return false;\"n><br>
	<font size=2>When the timer above shows <b>GO!</b> click <b>Confirm My Site</b></font><br>
	<input type=hidden name=action value=\"approved\">
	<input type=hidden name=siteid value=\"" . $_GET['siteid'] . "\">
	<input type=hidden name=vc value=\"". md5($_SESSION['sess_data']['usrauthent']) . "\">
	<input type=submit value=\"Confirm My Site\"></td><td></td><td align=right>");
	echo("$surf_ban_rotator</td></tr></table></form>");
} elseif ($_GET['siteid'] && $_GET['action'] == 'approved' && $_GET['vc'] == md5($_SESSION['sess_data']['usrauthent'])) {
	if (!isset($_SESSION['sess_data']['timesa']) || (time() - $_SESSION['sess_data']['timesa']) >= $wait_timer_amt) {
		$_SESSION['sess_data']['timesa'] = "";
	} else {
		$_SESSION['sess_data']['timesa'] = "";
		$_SESSION['sess_data']['usrauthent'] = "";
		$_SESSION['sess_data']['usrauthentica'] = "";
		echo("<head><title>$title</title></head>
		<body><font face=$fontface size=2>
		<div align=center><b>Sorry you did not wait $wait_timer_amt seconds you must start the verification process again<br>
		<a href=./confirmsite.php?siteid=$siteid&".session_name() . "=" . session_id() . " target=_top>Confirm Again</a><br>
		<a href=./index.php?".session_name() . "=" . session_id() . " target=_top>Members Area</a></b></div></body>
		</html>");
		mysql_close;
		exit;
	}
	$wer = mysql_query("SELECT * FROM site WHERE id=" . $_GET['siteid'] . " && usrid=$usrid");
	$state = mysql_result($wer, 0, "state");
	if ($state != 'Waiting') {
		$_SESSION['sess_data']['usrauthent'] = "";
		$_SESSION['sess_data']['usrauthentica'] = "";
		echo("<head><title>$title</title></head>
		<body><font face=$fontface size=2>
		<div align=center><b>Sorry you can not validate this site!<br><a href=./index.php?".session_name() . "=" . session_id() . " target=_top>Members Area</a></b></div></body>
		</html>");
		mysql_close;
		exit; }
		$raz = mysql_query("UPDATE site SET state='Enabled' WHERE id=" . $_GET['siteid']);
		$_SESSION['sess_data']['usrauthent'] = "";
		$_SESSION['sess_data']['usrauthentica'] = "";
		echo("\n<center>Your site has been approved and is now ready for others to view!<br><br><a href=./index.php?".session_name() . "=" . session_id() . " target=_top>Members Home</a></center>");
		mysql_close;
		exit;
} else {
	$_SESSION['sess_data']['usrauthent'] = "";
	$_SESSION['sess_data']['usrauthentica'] = "";
	echo("<html><head>
	<title>$title</title>
	<link rel=stylesheet type=text/css href=../style.css>\n");
	echo("<script language=JavaScript>\nif (parent.location.href == self.location.href) {window.location.href = './index.php?".session_name() . "=" . session_id() . "';}\n</script>\n");
	echo("</head><body><font face=$fontface size=2><div align=center><b>This page was not accessed correctly!<br><a href=./index.php?".session_name() . "=" . session_id() . " target=_top>Members Area</a></body></html>");
	mysql_close;
	exit;
}
?>
