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
$invested = round(mysql_result($res, 0, "invested"), 2);
$roi_cash = round(mysql_result($res, 0, "roi_cash"), 3);
$sharec = mysql_result(mysql_query("select value from adminprops where field='sharec'"), 0);
$sharea = mysql_result(mysql_query("select value from adminprops where field='sharea'"), 0);
$my_shares = round($invested / $sharec);
if ($_POST['submit'] == '  Confirm Purchase  ' && ($_POST['purch'] >= 1 || round($_POST['purch_2']) >= 1)) {
$error = "no";
if ($_POST['purch'] == "" && round($_POST['purch_2']) >= 1) {
if ($_POST['extra_message'] != "" && (ereg('<', $_POST['extra_message']) || ereg('>', $_POST['extra_message']))) {
$error = "yes";
$why = $why . "The additional details cannot contain html characters (< or >)!<br>";
}
$my_share_tot = $my_shares + round($_POST['purch_2']);
if ($my_share_tot > $sharea) {
$error = "yes";
$why = $why . "You could not have purchased that many $upgrade_title"."s!<br>";
} elseif ($error == 'no') {
$cash_ammmt = round($_POST['purch_2']) * $sharec;
mail($private_sys_email, "User $usrid Requesting $upgrade_title"."s Confirmation", "Hi $title system here..\n\nMember $usrid has just confirmed their purchase of:\n\n" . $_POST['purch_2'] . " $upgrade_title(s) @ \$$sharec per $upgrade_title - Totaling: \$$cash_ammmt\n\nPaid with: " . $_POST['paid_with'] . "\n\nAdditional Comments:\n".trim($_POST['extra_message'])."\n\nRegards\n\n$title System", $email_headers);
secheader();
members_main_menu($members_menu);
echo("<p class=big><a href=/members/ title=\"Back to main page\">User account #$usrid</a> : Thank you!</p>");
echo("Your request has been received and $title Admin will update your account when your payment is verified.");
secfooter();
mysql_close;
exit;
}
} elseif ($_POST['purch_2'] == "" && $_POST['purch'] >= 1 && $error == 'no') {
if ($_POST['extra_message'] != "" && (ereg('<', $_POST['extra_message']) || ereg('>', $_POST['extra_message']))) {
$error = "yes";
$why = $why . "The additional details cannot contain html characters (< or >)!<br>";
}
$res = mysql_query("select * from sellcredit where id=" . $_POST['purch']);
if (mysql_num_rows($res) == 0) {
$error = "yes";
$why = $why . "That purchase wasn't found!<br>";
} elseif ($error == 'no') {
$sell_id = mysql_result($res, 0, "id");
$sell_name = mysql_result($res, 0, "name");
$sell_cost = mysql_result($res, 0, "cost");
mail($private_sys_email, "User $usrid Requesting Payment Update", "Hi $title system here..\n\nMember $usrid has confirmed their purchase of:\n\n$sell_name - \$$sell_cost\n\nPaid with: " . $_POST['paid_with'] . "\n\nAdditional Comments:\n".trim($_POST['extra_message'])."\n\nRegards\n\n$title System", $email_headers);
secheader();
members_main_menu($members_menu);
echo("<p class=big><a href=/members/ title=\"Back to main page\">User account #$usrid</a> : Thank you!</p>");
echo("Your request has been received and $title Admin will update your account when your payment is verified.");
secfooter();
mysql_close;
exit;
}
} else {
$error = "yes";
$why = $why . "You must enter only one purchase type!<br>";
}
}
secheader();
members_main_menu($members_menu);
echo("<p class=big><a href=/members/ title=\"Back to main page\">User account #$usrid</a> : Thank you!</p>");
if ($error == 'yes') {
echo("<p align=center><font face=\"$fontface\" size=2 color=red><b>$why</b></font></p>");
}
?>
<form name="thanks" method="post">
<table width="100%" border="0" cellspacing="0" cellpadding="2">
  <tr>
    <td colspan="2"><div align="center"><font size="2" face="<? echo($fontface); ?>"><strong>Confirm Purchase</strong></font></div></td>
  </tr>
  <tr>
    <td width="25%"><font size="2" face="<? echo($fontface); ?>">Purchased :</font></td>
    <td width="75%"><font size="2" face="<? echo($fontface); ?>">
      <select name="purch" class="webforms">
<?
$res = mysql_query("select * from sellcredit order by id asc");
if (mysql_num_rows($res) != 0) {
$pay_selectersz = "<option value=\"\">Select</option>";
for ($q = 0; $q < mysql_num_rows($res); $q++) {
$sell_id = mysql_result($res, $q, "id");
$sell_name = mysql_result($res, $q, "name");
$sell_cost = mysql_result($res, $q, "cost");
$pay_selectersz = $pay_selectersz . "<option value=\"$sell_id\">$sell_name - \$$sell_cost</option>\n";
}
} else {
$pay_selectersz = "<option value=\"\">Site admin must add advertising sales!</option>";
}
echo($pay_selectersz);
?>
      </select> or <input name="purch_2" type="text" size="5" maxlength="11" class="webforms">
      <font size="1" face="<? echo($fontface); ?>">      <? echo($upgrade_title); ?>(s) @ <? echo("$sharec per $upgrade_title"); ?></font></font></td>
  </tr>
  <tr>
    <td><font size="2" face="<? echo($fontface); ?>">Paid with :</font></td>
    <td><select name="paid_with" class="webforms">
<?
$m_codes = mysql_query("select * from merchant_codes");
for ($z = 0; $z < mysql_num_rows($m_codes); $z++) {
$pay_name = mysql_result($m_codes, $z, "name");
$pay_selecter = $pay_selecter . "<option value=\"$pay_name\">$pay_name</option>\n";
}
echo($pay_selecter);
?>
    </select></td>
  </tr>
  <tr>
    <td><font size="2" face="<? echo($fontface); ?>">Additional Details (No HTML!)
        :<br>
        <font size="1">PTC advert details etc..
        </font> </font></td>
    <td><textarea name="extra_message" cols="45" rows="6" wrap="VIRTUAL" class="webforms"></textarea></td>
  </tr>
  <tr>
    <td><font size="2" face="<? echo($fontface); ?>">Confirm :</font></td>
    <td><input type="submit" name="submit" value="  Confirm Purchase  " class="formbutton"></td>
  </tr>
</table>
</form>
<?
secfooter();
mysql_close;
exit;
?>
