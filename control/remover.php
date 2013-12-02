<?php
session_start();
include("../vars.php");
include("adminauth.incl.php");
mysql_connect($db_host, $db_user, $db_pwd);
mysql_select_db($db_name);
adminAuth();
$date_now = date("Y-m-d");
$datetime_now = date("Y-m-d H:i:s");
$yearis = date("Y");
$monthis = date("m");
$sharec = mysql_result(mysql_query("select value from adminprops where field='sharec'"), 0);
$sharea = mysql_result(mysql_query("select value from adminprops where field='sharea'"), 0);
if ($_POST['submit'] == 'Delete' && is_numeric($_POST['tid'])) {
$get_trans = mysql_query("SELECT * FROM investment_history WHERE id=" . $_POST['tid']);
if (mysql_num_rows($get_trans) == 0) {
header("Location: /control/?error");
mysql_close;
exit;
} else {
$amount = mysql_result($get_trans, 0, "amount");
$owner = mysql_result($get_trans, 0, "usrid");
$del_trans = mysql_query("DELETE FROM investment_history WHERE id=" . $_POST['tid']);
$upd_user = mysql_query("UPDATE user SET invested=invested-$amount WHERE id=$owner");
header("Location:index.php?y=3&u=$owner&result=done");
mysql_close;
exit;
}
} elseif ($_POST['submit'] == 'Delete' && is_numeric($_POST['cid'])) {
$get_trans = mysql_query("SELECT * FROM cashout_history WHERE id=" . $_POST['cid']);
if (mysql_num_rows($get_trans) == 0) {
header("Location:index.php?error");
mysql_close;
exit;
} else {
$amount = mysql_result($get_trans, 0, "amount");
$owner = mysql_result($get_trans, 0, "usrid");
$updsts = mysql_query("UPDATE monthly_stats SET paid_out=paid_out-$amount WHERE usrid=$owner && monthis=$monthis && yearis=$yearis") or die (mysql_error());
$upd_user = mysql_query("UPDATE user SET roi_cash=roi_cash+$amount, lifetime_paid=lifetime_paid-$amount WHERE id=$owner") or die(mysql_error());
$del_trans = mysql_query("DELETE FROM cashout_history WHERE id=" . $_POST['cid']) or die(mysql_error());
$csures = mysql_query("UPDATE adminprops SET value=value+$amount WHERE field='csurpl'");
header("Location:index.php?y=3&u=$owner&result=done");
mysql_close;
exit;
}
} elseif ($_POST['submit'] == 'Delete' && is_numeric($_POST['commid'])) {
$get_trans = mysql_query("SELECT * FROM comission_history WHERE id=" . $_POST['commid']);
if (mysql_num_rows($get_trans) == 0) {
header("Location:index.php?error");
mysql_close;
exit;
} else {
$amount = mysql_result($get_trans, 0, "amount");
$owner = mysql_result($get_trans, 0, "paid_to");
$ref_was = mysql_result($get_trans, 0, "usrid");
$upd_user = mysql_query("UPDATE user SET roi_cash=roi_cash-$amount, lifetime_cash=lifetime_cash-$amount WHERE id=$owner") or die (mysql_error());
$updsts = mysql_query("UPDATE monthly_stats SET coms_earned=coms_earned-$amount, tot_owed=tot_owed-$amount WHERE usrid=$owner && monthis=$monthis && yearis=$yearis") or die (mysql_error());
$upd = mysql_query("UPDATE user SET commstoref=commstoref-$amount WHERE id=$ref_was") or die (mysql_error());
$del_trans = mysql_query("DELETE FROM comission_history WHERE id=" . $_POST['commid']) or die (mysql_error());
$csures = mysql_query("UPDATE adminprops SET value=value+$amount WHERE field='csurpl'");
header("Location:index.php?y=3&u=$owner&result=done");
mysql_close;
exit;
}
} elseif ($_POST['submit'] == 'Delete' && is_numeric($_POST['oid'])) {
$get_trans = mysql_query("SELECT * FROM other_history WHERE id=" . $_POST['oid']);
if (mysql_num_rows($get_trans) == 0) {
header("Location:index.php?error");
mysql_close;
exit;
} else {
$amount = mysql_result($get_trans, 0, "amount");
$owner = mysql_result($get_trans, 0, "usrid");
$upd_user = mysql_query("UPDATE user SET roi_cash=roi_cash-$amount, lifetime_cash=lifetime_cash-$amount WHERE id=$owner") or die (mysql_error());
$updsts = mysql_query("UPDATE monthly_stats SET misc_earned=misc_earned-$amount, tot_owed=tot_owed-$amount WHERE usrid=$owner && monthis=$monthis && yearis=$yearis") or die (mysql_error());
$del_trans = mysql_query("DELETE FROM other_history WHERE id=" . $_POST['oid']);
$csures = mysql_query("UPDATE adminprops SET value=value+$amount WHERE field='csurpl'");
header("Location:index.php?y=3&u=$owner&result=done");
mysql_close;
exit;
}
} else {
header("Location:index.php?error");
mysql_close;
exit;
}
?>
