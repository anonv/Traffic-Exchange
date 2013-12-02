<?php
include("../vars.php");
@mysql_connect($db_host, $db_user, $db_pwd);
@mysql_select_db($db_name);
@mysql_query("REPAIR TABLE `7stat` , `7statsite` , `abuse` , `acctype` , `ad_info` , `admin` , `adminprops` , `banned_emails` , `banned_ipadds` , `banned_sites` , `banner` , `cashout_history` , `comission_history` , `faq` , `gp_info` , `gp_name` , `html` , `investment_history` , `member_refs` , `merchant_codes` , `monthly_stats` , `other_history` , `ptc_orders` , `ptc_tracking` , `referstats` , `sellcredit` , `site` , `tads` , `user` ");
@mysql_query("OPTIMIZE TABLE `7stat` , `7statsite` , `abuse` , `acctype` , `ad_info` , `admin` , `adminprops` , `banned_emails` , `banned_ipadds` , `banned_sites` , `banner` , `cashout_history` , `comission_history` , `faq` , `gp_info` , `gp_name` , `html` , `investment_history` , `member_refs` , `merchant_codes` , `monthly_stats` , `other_history` , `ptc_orders` , `ptc_tracking` , `referstats` , `sellcredit` , `site` , `tads` , `user` ");
echo "<?xml version=\"1.0\" encoding=\"iso-8859-1\"?".">\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
 <head>
  <title>Energency Database Repair File</title>
  <meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
 </head>
 <body>
  <p>Your database was successfully repaired and optimized.</p>
  <p><a href="index.php">Click here to login to your admin area</a></p>
 </body>
</html>
<?
@mysql_close;
exit;
?>
