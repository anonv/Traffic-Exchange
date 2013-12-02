<?php
function adminAuth() {
if (!isset($_SESSION['asess_name']) || !isset($_SESSION['asess_passwd'])) {
session_destroy();
header("location:index.php");
mysql_close;
exit;
} else {
$res = mysql_query("select value from admin where field='login'");
$sdblogin = mysql_result($res, 0);
$res = mysql_query("select value from admin where field='passwd'");
$sdbpasswd = md5(mysql_result($res, 0));
if ($_SESSION['asess_name'] == $sdblogin && $_SESSION['asess_passwd'] == $sdbpasswd) {
return;
} else {
session_destroy();
header("location:index.php");
mysql_close;
exit;
}
}
}

function ranid($len){
$pass = NULL;
for ($i=0; $i<$len; $i++) {
$char = chr(rand(48,122));
while (!ereg("[a-zA-Z0-9]", $char)) {
if ($char == $lchar) continue;
$char = chr(rand(48,90));
}
$pass .= $char;
$lchar = $char;
}
return $pass;
}
?>
