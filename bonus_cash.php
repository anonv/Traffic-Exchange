<?php
session_start();
//session_register("sess_data");
include("vars.php");
include("headfoot.php");
include("auth.php");
mysql_connect($db_host, $db_user, $db_pwd);
mysql_select_db($db_name);
$res = checkauth();
$usrid = mysql_result($res, 0, "id");
if ($_SESSION['sess_data']['ccwon'] != 'ccreallycc' || $_GET['next'] != md5($_SESSION['sess_data']['surf_encoder_vals'])) {
header("Location: $self_url" . "surf.php?next=" . $_GET['next']);
mysql_close;
exit;
}
unset($_SESSION['sess_data']['ccwon']);
$res = mysql_query("select value from adminprops where field='contcx'");
if (mysql_result($res, 0, "value") != 0) {
$contcx = mysql_result($res, 0, "value");
$res = mysql_query("select value from adminprops where field='contcy'");
$contcy = mysql_result($res, 0, "value");
} else {
header("Location: $self_url" . "surf.php?next=" . $_GET['next']);
mysql_close;
exit;
}
if ($contcy != $_SESSION['sess_data']['contcy']) {
$_SESSION['sess_data']['contcy'] = $contcy;
}
if ($contcx != $_SESSION['sess_data']['contcx']) {
header("Location: $self_url" . "surf.php?next=" . $_GET['next']);
mysql_close;
exit;
}
$get_stats = mysql_query("SELECT * FROM monthly_stats WHERE usrid=$usrid && yearis=" . date("Y") . " && monthis=" . date("m"));
if (mysql_num_rows($get_stats) == 0) {
$ins_upd = mysql_query("INSERT INTO monthly_stats (usrid, sbcash_earned, tot_owed, monthis, yearis) VALUES ($usrid, " . $_SESSION['sess_data']['contcy'] . ", " . $_SESSION['sess_data']['contcy'] . ", " . date("m") . ", " . date("Y") . ")") or die (mysql_error());
} else {
$ins_upd = mysql_query("UPDATE monthly_stats SET sbcash_earned=sbcash_earned+" . $_SESSION['sess_data']['contcy'] . ", tot_owed=tot_owed+" . $_SESSION['sess_data']['contcy'] . " WHERE usrid=$usrid && yearis=" . date("Y") . " && monthis=" . date("m")) or die (mysql_error());
}
$res = mysql_query("update user set roi_cash=roi_cash+" . $_SESSION['sess_data']['contcy'] . ", lifetime_cash=lifetime_cash+" . $_SESSION['sess_data']['contcy'] . ", sb_cash=sb_cash+" . $_SESSION['sess_data']['contcy'] . " where id=$usrid") or die (mysql_error());
$surpres = mysql_query("update adminprops set value=value-" . $_SESSION['sess_data']['contcy'] . " where field='csurpl'");
secheader();

echo("<h4>Bonus Cash Won!</h4>
<p>Congratulations! <b>\$" . $_SESSION['sess_data']['contcy'] . " Cash</b> was just added to your account!</p>\n<p><a href=$self_url" . "surf.php?next=" . $_GET['next'] . ">Continue back To Surf</a><br>
<a href=$self_url" . "members/>Go To Member Area</a></p>\n");
secfooter();
mysql_close;
exit;
?>
