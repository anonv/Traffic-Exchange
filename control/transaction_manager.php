<?php
session_start();
//exit(print_r($_POST));
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
if ($_POST['action'] == 'misc' && is_numeric($_POST['mem_id']) && is_numeric($_POST['amt_crd'])) {
	$res = mysql_query("SELECT * FROM user WHERE id=" . $_POST['mem_id']);
	$error = 'no';
	if (mysql_num_rows($res) == 0) {
		$error = 'yes';
		$reson = "That member ID wasn't found!<br>";
	}
	if ($_POST['pay_id'] == "") {
		$_POST['pay_id'] = 'N/A';
	}
	if ($_POST['reason'] == "") {
		$_POST['reason'] = 'None given';
	}
	if ($error == 'no') {
		$get_stats = mysql_query("SELECT * FROM monthly_stats WHERE usrid=" . $_POST['mem_id'] . " && monthis=$monthis && yearis=$yearis");
		if (mysql_num_rows($get_stats) != 0) {
			@mysql_query("UPDATE monthly_stats SET misc_earned=misc_earned+" . $_POST['amt_crd'] . ", tot_owed=tot_owed+" . $_POST['amt_crd'] . " WHERE usrid=" . $_POST['mem_id'] . " && monthis=$monthis && yearis=$yearis") or die(mysql_error());
		} else {
			@mysql_query("INSERT INTO monthly_stats (usrid, misc_earned, tot_owed, monthis, yearis) VALUES (" . $_POST['mem_id'] . ", " . $_POST['amt_crd'] . ", " . $_POST['amt_crd'] . ", $monthis, $yearis)") or die(mysql_error());
		}
		@mysql_query("INSERT INTO other_history (usrid, amount, descr, is_from, adate) VALUES (" . $_POST['mem_id'] . ", " . $_POST['amt_crd'] . ", '" . $_POST['reason'] . "', '" . $_POST['pay_id'] . "', '$date_now')") or die (mysql_error());
		@mysql_query("UPDATE user SET roi_cash=roi_cash+" . $_POST['amt_crd'] . ", lifetime_cash=lifetime_cash+" . $_POST['amt_crd'] . " WHERE id=" . $_POST['mem_id']);
		echo("<p><font face=Tahoma size=2 color=red><b>Success! <br><br>Member #$_POST[mem_id] was credited \$$_POST[amt_crd]<br><br><a href=index.php?y=3&u=$mem_id>Member $mem_id Account Stats</a> - <a href=index.php>Admin area</a> - <a href=transaction_manager.php>Transaction Manager</a></b></font</p>");
		mysql_close;
		exit;
	}
}
if ($_POST['action'] == 'sinv' && is_numeric($_POST['mem_id']) && is_numeric($_POST['amt_shares']) && $sharec > 0 && $sharec > 0) {
	$mem_id = $_POST['mem_id'];
	$res = mysql_query("SELECT * FROM user WHERE id=" . $_POST['mem_id']);
	$error = 'no';
	if (mysql_num_rows($res) == 0) {
		$error = 'yes';
		$reson = "That member ID wasn't found!<br>";
	}
	$investment = mysql_result($res, 0, "invested");
	$acc_is = mysql_result($res, 0, "acctype");
	$my_current_shares = $investment / $sharec;
	$tot_shares = $my_current_shares + $_POST['amt_shares'];
	if ($my_current_shares >= $sharea) {
		$error = 'yes';
		$reson = "That member ID already has the maximum amount of shares possible!<br>";
	} elseif ($tot_shares > $sharea) {
		$error = 'yes';
		$reson = "Too many shares to add - Max allowed is $sharea!<br>";
	}
	if ($_POST['pay_with'] == "") {
		$_POST['pay_with'] = 'N/A';
	}
	if ($_POST['pay_id'] == "") {
		$_POST['pay_id'] = 'N/A';
	}
	if ($_POST['reason'] == "") {
		$_POST['reason'] = 'None given';
	}
	$invest_to_add = $sharec * $_POST['amt_shares'];
	if ($error == 'no') {
		if ($acc_is == 1 && $upgrade_member_if_buy != 0) {
			$updg_days = mysql_result(mysql_query("SELECT upg_time FROM acctype WHERE id=2"), 0) or die (mysql_error());
			$the_day = strftime("%Y-%m-%d", strtotime("$date_now + $updg_days days"));
			$ins_accdet = ", acctype=2, upgrade_ends='$the_day'";
			$ins_accshow = "and was upgraded to Upgraded Status!";
		} elseif ($acc_is == 2 && $upgrade_member_if_buy != 0) {
			$updg_days = mysql_result(mysql_query("SELECT upg_time FROM acctype WHERE id=2"), 0) or die (mysql_error());
			$the_day = strftime("%Y-%m-%d", strtotime("$date_now + $updg_days days"));
			$ins_accdet = ", upgrade_ends='$the_day'";
			$ins_accshow = "and their upgraded status was entended to: $the_day ($updg_days days from $date_now)";
		} else {
			$ins_accdet = "";
			$ins_accshow = "";
		}
		@mysql_query("INSERT INTO investment_history (usrid, amount, descr, is_from, processor, adate) VALUES (" . $_POST['mem_id'] . ", $invest_to_add, '" . $_POST['reason'] . "', '" . $_POST['pay_id'] . "', '" . $_POST['pay_with'] . "', '$date_now')") or die (mysql_error());
		@mysql_query("UPDATE user SET invested=invested+$invest_to_add". "$ins_accdet WHERE id=" . $_POST['mem_id']);
		@mysql_query("UPDATE adminprops SET value=value+$invest_to_add WHERE field='csurpl'");
		echo("<p><font face=Tahoma size=2 color=red><b>SUCCESS! Member #$_POST[mem_id] was credited with " . $_POST['amt_shares'] . " x \$$sharec shares $ins_accshow<br><br><a href=index.php?y=3&u=$mem_id>Member #$mem_id Account Stats</a> - <a href=index.php>Admin area</a> - <a href=transaction_manager.php>Transaction Manager</a></b></font</p>");
		mysql_close;
		exit;
	}
}
if ($_POST['action'] == 'debit' && is_numeric($_POST['mem_id']) && is_numeric($_POST['cash_amt'])) {
	$mem_id = $_POST['mem_id'];
	$res = mysql_query("SELECT * FROM user WHERE id=" . $_POST['mem_id']);
	$error = 'no';
	if (mysql_num_rows($res) == 0) {
		$error = 'yes';
		$reson = "That member ID wasn't found!<br>";
	}
	$mycash = @mysql_result($res, 0, "roi_cash");
	if ($mycash < $_POST['cash_amt']) {
		$error = 'yes';
		$reson = "The amount to credit that member (\$" . $_POST['cash_amt'] . ") is more than what they have in their account (\$$mycash)!<br>";
	}
	if ($_POST['pay_to'] == "") {
		$_POST['paid_to'] = 'N/A';
	}
	if ($_POST['pay_id'] == "") {
		$_POST['pay_id'] = 'N/A';
	}
	if ($_POST['reason'] == "") {
		$_POST['reason'] = 'None given';
	}
	$get_stats = mysql_query("SELECT * FROM monthly_stats WHERE usrid=" . $_POST['mem_id'] . " && monthis=" . $_POST['get_month'] . " && yearis=" . $_POST['get_year']);
	if (mysql_num_rows($get_stats) != 0) {
		$tot_owed = mysql_result($get_stats, 0, "tot_owed");
		$paid_out = mysql_result($get_stats, 0, "paid_out");
		$grander = round($tot_owed - $paid_out, 2);
		if ($_POST['cash_amt'] > $grander) {
			$error = 'yes';
			$reson = "That member (User: " . $_POST['mem_id'] . ") does not have enough earnings for that month - they are only owed \$$grander for that month!<br>";
		}
	} else {
		$error = 'yes';
		$reson = "That member (User: " . $_POST['mem_id'] . ") does not have any earnings for that month!<br>";
	}
	if ($error == 'no') {
		@mysql_query("UPDATE monthly_stats SET paid_out=paid_out+" . $_POST['cash_amt'] . " WHERE usrid=" . $_POST['mem_id'] . " && monthis=" . $_POST['get_month'] . " && yearis=" . $_POST['get_year']);
		@mysql_query("INSERT INTO cashout_history (usrid, amount, descr, pay_merch, paid_to, cdate) VALUES (" . $_POST['mem_id'] . ", " . $_POST['cash_amt'] . ", '" . $_POST['reason'] . "', '" . $_POST['pay_to'] . "', '" . $_POST['pay_id'] . "', '$date_now')") or die (mysql_error());
		@mysql_query("UPDATE user SET roi_cash=roi_cash-" . $_POST['cash_amt'] . ", lifetime_paid=lifetime_paid+" . $_POST['cash_amt'] . " WHERE id=" . $_POST['mem_id']) or die (mysql_error());
		@mysql_query("UPDATE adminprops SET value=value-" . $_POST['cash_amt'] . " WHERE field='csurpl'");
		echo("<p><font face=Tahoma size=2 color=red><b>Success! Member #$_POST[mem_id] was debited \$$_POST[cash_amt]<br><br><a href=index.php?y=3&u=$mem_id>Member #$mem_id Account Stats</a> - <a href=index.php>Admin area</a> - <a href=transaction_manager.php>Transaction Manager</a></b></font</p>");
		mysql_close;
		exit;
	}
}
if ($_POST['action'] == 'comm' && is_numeric($_POST['mem_id']) && is_numeric($_POST['ref_was']) && is_numeric($_POST['cash_amt'])) {
	$mem_id = $_POST['mem_id'];
	$res = mysql_query("SELECT * FROM user WHERE id=" . $_POST['mem_id']);
	$error = 'no';
	if (mysql_num_rows($res) == 0) {
		$error = 'yes';
		$reson = "That member ID wasn't found!<br>";
		$mem_acc = 0;
	} else {
		$mem_acc = mysql_result($res, 0, "acctype");
	}
	$resa = mysql_query("SELECT * FROM user WHERE id=" . $_POST['ref_was']);
	if (mysql_num_rows($resa) == 0) {
		$error = 'yes';
		$reson = "That referrals member ID wasn't found!<br>";
	}
	if ($error == 'no') {
		$commissions_c = mysql_result(mysql_query("SELECT commissions FROM acctype WHERE id=$mem_acc"), 0);
		if ($_POST['reason'] == "") {
			$_POST['reason'] = 'None given';
		}
		$commission_earnt = $_POST['cash_amt'] * $commissions_c;
		$show_comms = $commissions_c * 100;
		$get_stats = mysql_query("SELECT * FROM monthly_stats WHERE usrid=" . $_POST['mem_id'] . " && monthis=$monthis && yearis=$yearis");
		if (mysql_num_rows($get_stats) != 0) {
			@mysql_query("UPDATE monthly_stats SET coms_earned=coms_earned+$commission_earnt, tot_owed=tot_owed+$commission_earnt WHERE usrid=" . $_POST['mem_id'] . " && monthis=$monthis && yearis=$yearis") or die(mysql_error());
		} else {
			@mysql_query("INSERT INTO monthly_stats (usrid, coms_earned, tot_owed, monthis, yearis) VALUES (" . $_POST['mem_id'] . ", $commission_earnt, $commission_earnt, $monthis, $yearis)") or die(mysql_error());
		}
		@mysql_query("INSERT INTO comission_history (paid_to, usrid, wasfor, amount, vdate) VALUES (" . $_POST['mem_id'] . ", " . $_POST['ref_was'] . ", '" . $_POST['reason'] . "', $commission_earnt, '$date_now')") or die ("There was an error entering the commission:<br>" . mysql_error());
		@mysql_query("UPDATE user SET roi_cash=roi_cash+$commission_earnt, lifetime_cash=lifetime_cash+$commission_earnt WHERE id=" . $_POST['mem_id']) or die (mysql_error());
		@mysql_query("UPDATE user SET commstoref=commstoref+$commission_earnt WHERE id=" . $_POST['ref_was']) or die (mysql_error());
		@mysql_query("UPDATE adminprops SET value=value-$commission_earnt WHERE field='csurpl'");
		echo("<p><font face=Tahoma size=2 color=red><b>Success! Member #$_POST[mem_id] was credited with \$$commission_earnt ($show_comms% of \$$_POST[cash_amt])<br><br><a href=index.php?y=3&u=$mem_id>Member #$mem_id Account Stats</a> - <a href=index.php>Admin area</a> - <a href=transaction_manager.php>Transaction Manager</a></b></font</p>");
		mysql_close;
		exit;
	}
}
?>
<html>
<head>
<title>Credit/Debit Members</title>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1">
</head>

<body>
<p><center><a href=index.php>Go Back To Admin Area</a></center></p>

<?
if ($error) {
	echo("<p><font face=Tahoma size=2 color=red><b>" . $reson . "</b></font></p>");
}
?>
<font face=Tahoma size=2>
<form name="forma" method="post">
  <p><strong><font size="4">Credit Member (Shares)</font></strong></p>
  <table width="769" border="0" cellspacing="0" cellpadding="2">
  <tr>
    <td width="318"><font size="2" face="Tahoma">Member ID to Credit :</font></td>
    <td width="443"><input name="mem_id" type="text" size="11"></td>
  </tr>
  <tr>
    <td><font size="2" face="Tahoma">Amount of Shares (1 Share = $<? echo($sharec); ?> Max <? echo($sharea); ?> Shares)
        :</font></td>
    <td><input name="amt_shares" type="text" size="8"></td>
  </tr>
  <tr>
    <td><font size="2" face="Tahoma">Paid With (StormPay, E-Gold etc..)
          :</font></td>
    <td><input type="text" name="pay_with"></td>
  </tr>
  <tr>
    <td><font size="2" face="Tahoma">Payment ID (E-Gold number etc..) :</font></td>
    <td><input type="text" name="pay_id"></td>
  </tr>
  <tr>
    <td><font size="2" face="Tahoma">Reason (i.e. 500 Shares Purchased) :</font></td>
    <td><input name="reason" type="text" maxlength="100"></td>
  </tr>
  <tr>
    <td><font size="2" face="Tahoma">Confirm :</font></td>
    <td><input type="submit" name="submit" value="  Add Shares  "><input type="hidden" name="action" value="sinv"></td>
  </tr>
</table>
</form>
<form name="formb" method="post">
  <p><strong><font size="4">Credit Member (Commissions)</font></strong></p>
  <table width="769" border="0" cellspacing="0" cellpadding="2">
    <tr>
      <td width="318"><font size="2" face="Tahoma">Member ID to Credit Commission:</font></td>
      <td width="443"><input name="mem_id" type="text" size="11">
      </td>
    </tr>
    <tr>
      <td width="318"><font size="2" face="Tahoma">Referral ID (Person who purchased) :</font></td>
      <td width="443"><input name="ref_was" type="text" size="11">
      </td>
    </tr>
    <tr>
      <td><font size="2" face="Tahoma">*Total Amount of Cash <br>
        (1 = $0.10 commission
          at 10%) :</font></td>
      <td><input name="cash_amt" type="text" size="8">
      </td>
    </tr>
    <tr>
      <td><font size="2" face="Tahoma">Reason (i.e. $1 earned for referral upgrade) :</font></td>
      <td><input name="reason" type="text" maxlength="100">
      </td>
    </tr>
    <tr>
      <td><font size="2" face="Tahoma">Confirm :</font></td>
      <td><input type="submit" name="submit" value="  Add Commission  "><input type="hidden" name="action" value="comm">
      </td>
    </tr>
  </table>
</form>
<form name="forma" method="post">
  <p><strong><font size="4">Credit Member (Miscellaneous)</font></strong></p>
  <table width="769" border="0" cellspacing="0" cellpadding="2">
  <tr>
    <td width="318"><font size="2" face="Tahoma">Member ID to Credit :</font></td>
    <td width="443"><input name="mem_id" type="text" size="11"></td>
  </tr>
  <tr>
    <td><font size="2" face="Tahoma">Amount to Credit (1 = $1) :</font></td>
    <td><input name="amt_crd" type="text" size="8"></td>
  </tr>
  <tr>
    <td><font size="2" face="Tahoma">From (Upline etc..) :</font></td>
    <td><input type="text" name="pay_id"></td>
  </tr>
  <tr>
    <td><font size="2" face="Tahoma">Reason (i.e. Free Upgrade from
          upline etc..) :</font></td>
    <td><input name="reason" type="text" maxlength="100"></td>
  </tr>
  <tr>
    <td><font size="2" face="Tahoma">Confirm :</font></td>
    <td><input type="submit" name="submit" value="  Add Misc. Credit  "><input type="hidden" name="action" value="misc"></td>
  </tr>
</table>
</form>
<form name="formc" method="post">
  <p><strong><font size="4">Debit Member</font></strong></p>
  <table width="769" border="0" cellspacing="0" cellpadding="2">
    <tr>
      <td width="318"><font size="2" face="Tahoma">Member ID to Debit :</font></td>
      <td width="443"><input name="mem_id" type="text" size="11">
      </td>
    </tr>
    <tr>
      <td><font size="2" face="Tahoma">Monthly earnings to deduct from :</font></td>
      <td><select name="get_month">
	          <option value="01">January</option>
	          <option value="02">February</option>
	          <option value="03">March</option>
	          <option value="04">April</option>
	          <option value="05">May</option>
	          <option value="06">June</option>
	          <option value="07">July</option>
	          <option value="08">August</option>
	          <option value="09">September</option>
	          <option value="10">October</option>
	          <option value="11">November</option>
	          <option value="12">December</option>
            </select><select name="get_year">
	          <option value="2009">2009</option>
	          <option value="2010">2010</option>
	          <option value="2011">2011</option>
	          <option value="2012">2012</option>
	          <option value="2013">2013</option>
	          <option value="2014">2014</option>
	          <option value="2015">2015</option>
	          <option value="2016">2016</option>
	          <option value="2017">2017</option>
	          <option value="2018">2018</option>
	          <option value="2019">2019</option>
	          <option value="2020">2020</option>
            </select></td>
    </tr>
    <tr>
      <td><font size="2" face="Tahoma">Amount of Cash (1 = $1) :</font></td>
      <td><input name="cash_amt" type="text" size="8">
      </td>
    </tr>
    <tr>
      <td><font size="2" face="Tahoma">Paid to (StormPay, E-Gold etc..)
            :</font></td>
      <td><input type="text" name="pay_to">
      </td>
    </tr>
    <tr>
      <td><font size="2" face="Tahoma">Payment ID (E-Gold number etc..) :</font></td>
      <td><input type="text" name="pay_id">
      </td>
    </tr>
    <tr>
      <td><font size="2" face="Tahoma">Reason (i.e. $10 in earnings cashed out) :</font></td>
      <td><input name="reason" type="text" maxlength="100">
      </td>
    </tr>
    <tr>
      <td><font size="2" face="Tahoma">Confirm :</font></td>
      <td><input type="submit" name="submit" value="  Add Debit  "><input type="hidden" name="action" value="debit">
      </td>
    </tr>
  </table>
</form>
 <p><font size="2">* = Enter the amount to add as commissions stats (i.e. if
     you enter 10 here and have commissions set at 10%, the member will get $1
     credited as commission
   (you do not need to work out the maths!).</font></p>
</font>
</body>
</html>
<?
mysql_close;
exit;
?>
