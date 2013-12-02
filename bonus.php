<?php
//session_register("sess_data");
session_start();
include("vars.php");
include("headfoot.php");
include("auth.php");
mysql_connect($db_host, $db_user, $db_pwd);
mysql_select_db($db_name);
$res = checkauth();
$usrid = mysql_result($res, 0, "id");
if ($_SESSION['sess_data']['won'] != 'really' || $_GET['next'] != md5($_SESSION['sess_data']['surf_encoder_vals'])) {
header("Location: $self_url" . "surf.php?next=" . $_GET['next']);
mysql_close;
exit;
}
unset($_SESSION['sess_data']['won']);
$res = mysql_query("select value from adminprops where field='contex'");
if (mysql_result($res, 0, "value") != 0) {
$contex = mysql_result($res, 0, "value");
$res = mysql_query("select value from adminprops where field='contey'");
$contey = mysql_result($res, 0, "value");
} else {
header("Location: $self_url" . "surf.php?next=" . $_GET['next']);
mysql_close;
exit;
}
if ($contey != $_SESSION['sess_data']['contey']) {
$_SESSION['sess_data']['contey'] = $contey;
}
if ($contex != $_SESSION['sess_data']['contex']) {
header("Location: $self_url" . "surf.php?next=" . $_GET['next']);
mysql_close;
exit;
}
$res = mysql_query("update user set credits=credits+" . $_SESSION['sess_data']['contey'] . ", sb_credits=sb_credits+" . $_SESSION['sess_data']['contey'] . " where id=$usrid") or die (mysql_error());
$surpres = mysql_query("update adminprops set value=value-" . $_SESSION['sess_data']['contey'] . " where field='surplu'");
secheader();

echo("<h4>Bonus Credits Won!</h4>
<p>Congratulations! <b>" . $_SESSION['sess_data']['contey'] . " Free Credits</b> were added to your account!</p>\n<p><a href=$self_url" . "surf.php?next=" . $_GET['next'] . ">Continue Back To Surf</a><br>
<a href=$self_url" . "members/>Go To Member Area</a></p>\n");
secfooter();
mysql_close;
exit;
?>
