<?php
session_start();
include("../vars.php");
include("../headfoot.php");
include("../auth.php");
mysql_connect($db_host, $db_user, $db_pwd);
mysql_select_db($db_name);
$res = checkauth();
$usrid = mysql_result($res, 0, "id");

if ($_GET['adid'] != "" && $_GET['action'] == 'start') {
	$gptc = mysql_query("SELECT * FROM ptc_orders WHERE linkid='$_GET[adid]'");
	if (mysql_num_rows($gptc) != 0) {
		$ptclinkurl = urlencode(mysql_result($gptc, 0, "linkurl"));
		$clks_remain = mysql_result($gptc, 0, "clicks_remain");
		$_SESSION['sess_data']['ptcurlis'] = $ptclinkurl;
	} else {
		echo("<html>\n<head>\n<title>$title Paid to Click</title>\n</head><body onLoad=\"alert('Ad not found!'); window.close();\"></body></html>");
		mysql_close;
		exit;
	}
	if ($clks_remain <= 0) {
		echo("<html>\n<head>\n<title>$title Paid to Click</title>\n</head><body onLoad=\"alert('This ad has expired!'); window.close();\"></body></html>");
		mysql_close;
		exit;
	}
	$grand = md5(rand(10000, 1000000));
	$_SESSION['sess_data']['verif_data'] = $grand;
	echo("<html>\n<head>\n<title>$title Paid to Click</title>\n");
	echo("<script language=\"JavaScript\">
<!--
defaultStatus = '$title Paid to Click';
if ((self.frames.name != 'PaidtoClickPage' && self.name !='PaidtoClickPage') || self.frames.name == '') {
top.location = \"$member_url/ptc.php?".session_name()."=".session_id()."\";
}
if (window != top) {
top.location.href=location.href;
}
top.window.moveTo(0,0);
if (document.all) {
top.window.resizeTo(screen.availWidth,screen.availHeight);
}
else if (document.layers||document.getElementById) {
if (top.window.outerHeight<screen.availHeight||top.window.outerWidth<screen.availWidth){
top.window.outerHeight = screen.availHeight;
top.window.outerWidth = screen.availWidth;
}
}
//-->
</script>\n");
	echo("</head>\n<frameset rows=95,* border=0><frame marginheight=0 marginwidth=0 scrolling=auto noresize border=0 src=\"./ptcbar.php?banid=$_GET[adid]&vc=$grand&site=$ptclinkurl&action=open&".session_name()."=".session_id()."\"><frame marginheight=0 marginwidth=0 scrolling=auto noresize border=0 src=\"".urldecode($ptclinkurl)."\"></frameset>\n</html>");
	mysql_close;
	exit();
	
} else {
	echo("<html>\n<head>\n<title>$title Paid to Click</title>\n</head><body onLoad=\"alert('Invalid link in ptc!'); window.close();\"></body></html>");
	mysql_close;
	exit;
}
?>
