<?php
session_start();
session_destroy();
session_start();
include("../vars.php");
include("../headfoot.php");
mysql_connect($db_host, $db_user, $db_pwd);
mysql_select_db($db_name);
if ($_POST['form'] == 'sent') {
	if (trim($_POST['email']) == "" || trim($_POST['passwd']) == "") {
uheader();
		echo("<h4>There Has Been An Error</h4><p><b>Login details cannot be blank!</b> Please try again.</p>");
ufooter();
		$_SESSION = array();
		session_destroy();
		mysql_close;
		exit;
	} elseif (ereg('@', trim($_POST['email']))) {
		$_SESSION['sess_name'] = trim($_POST['email']);
		$_SESSION['sess_passwd'] = md5(trim($_POST['passwd']));
		header("Location: $self_url" . "members/?".session_name() . "=" . session_id());
		mysql_close;
		exit;
	} elseif (is_numeric(trim($_POST['email']))) {
		$res = mysql_query("select * from user where id='" . trim($_POST['email']) . "' && passwd='".trim($_POST['passwd'])."'");
		if (mysql_num_rows($res) != 0) {
			$_SESSION['sess_name'] = mysql_result($res, 0, "email");
			$_SESSION['sess_passwd'] = md5(trim($_POST['passwd']));
			header("Location: $self_url" . "members/?".session_name() . "=" . session_id());
			mysql_close;
			exit;
		} else {
uheader();
		echo("<h4>There Has Been An Error</h4><p><b>Invalid login details!</b> Please try again.</p>");
ufooter();
			$_SESSION = array();
			session_destroy();
			mysql_close;
			exit;
		}
	} else {
uheader();
		echo("<h4>There Has Been An Error</h4><p><b>Invalid login details!</b> Please try again.</p>");
ufooter();
		$_SESSION = array();
		session_destroy();
		mysql_close;
		exit;
	}
} else {
uheader();
		echo("<h4>There Has Been An Error</h4><p><b>You are not authorized to be in this part of the website!</b> Please try again.</p>");
ufooter();
	$_SESSION = array();
	session_destroy();
	mysql_close;
	exit;
}
?>
