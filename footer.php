</div>
</div>
</div>
</div>

<? include ("vars.php"); ?>

<div id="left">
<div id="top_left"></div>
<?php if (!isset($_SESSION['sess_name']) || !isset($_SESSION['sess_passwd'])) { ?>
<div class="titl_left">Member Login</div>
<div class="ug">
<form action="<? echo($self_url); ?>members/mem_auth.php" method="post" name="login">
<b>E-mail:</b><br>
<input type="text" name="email" maxlength="100" size="25">
<br>
<b>Password:</b><br>
<input type="password" name="passwd" maxlength="20" size="25">
<br>
<a href="<? echo($self_url); ?>lost.php<? if ($ref) {echo("?ref=$ref"); }?>">Lost Password?</a> - <a href="<? echo($self_url); ?>signup.php<? if ($ref) {echo("?ref=$ref"); }?>">Not Registered?</a>
<br>
<? echo "<input type=hidden name=\"".session_name()."\" value=" . session_id() . ">"; ?>
<input type="hidden" name="form" value="sent">
<input name="submit" type="submit" style="font-size: 11px; padding: 2px;" value=" Login " class="formbutton">
</form><br>
</div>
<?php }else{ ?>
<!-- after login block -->
<div class="titl_left">Member Menu</div>
<div class="ug">
<ul>
<li><a href="<? echo($self_url)."members/index.php?".session_name()."=".session_id();?>" title="Member Homepage">Member Homepage</a></li>
<li><a href="<? echo($self_url)."surf.php?".session_name()."=".session_id();?>" title="Start Surfing Now">Start Surfing Now</a></li>
<li><a href="<? echo($self_url)."members/ptc.php?".session_name()."=".session_id();?>" title="Paid To Click">Paid To Click</a></li>
<li><a href="<? echo($self_url)."members/sitelist.php?".session_name()."=".session_id();?>" title="Your Websites & Stats">Your Websites & Stats</a></li>
<li><a href="<? echo($self_url)."members/accountdetails.php?".session_name()."=".session_id();?>" title="Account Details & Earnings">Account Details & Earnings</a></li>
<li><a href="<? echo($self_url)."members/history.php?".session_name()."=".session_id();?>" title="Detailed Account History">Detailed Account History</a></li>
<li><a href="<? echo($self_url)."members/upgrade.php?".session_name()."=".session_id();?>" title="Upgrades & Purchases">Upgrades & Purchases</a></li>
<li><a href="<? echo($self_url)."members/ad_stats.php?".session_name()."=".session_id();?>" title="Banner & Text Ad Stats">Banner & Text Ad Stats</a></li>
<li><a href="<? echo($self_url)."members/ptcstats.php?".session_name()."=".session_id();?>" title="Paid To Click Ad Stats">Paid To Click Ad Stats</a></li>
<li><a href="<? echo($self_url)."members/referral.php?".session_name()."=".session_id();?>" title="Referral Links & Stats">Referral Links & Stats</a></li>
<li><a href="<? echo($self_url)."members/banners.php?".session_name()."=".session_id();?>" title="Referral Banners">Referral Banners</a></li>
<li><a href="<? echo($self_url); ?>logout.php" title="Logout Of Your Account">Logout Of Your Account</a></li>
</ul>
</div>
<?php } ?>
<div class="titl_left" style="display:block">Website Statistics</div>
<div class="ug">
<?
// Users Count
$c1 = totalmembers();

// Upgraded Users Count
$c2 = totalupgrademembers();

// Sites in rotation
$c5 = totalsiteinrotation();

// Sites Shown Today
$c6 = totalsiteshowntoday();

//Members Surfing Now
$c7 =totalmembersufringnow();

//Total Paid Out
$c8 = totalpayout();

echo "\n<table border=0 width=100% style=\"padding-right: 10px;\">";
echo "\n<tr align=left><td>Total Members:</td><td align=right><b>$c1</b></td></tr>";
echo "\n<tr align=left><td>Upgraded Members:</td><td align=right><b>$c2</b></td></tr>";
echo "\n<tr align=left><td>Members Surfing Now:</td><td align=right><b>$c7</b></td></tr>";
echo "\n<tr align=left><td>Websites In Surf:</td><td align=right><b>$c5</b></td></tr>";
echo "\n<tr align=left><td>Websites Shown Today:</td><td align=right><b>$c6</b></td></tr>";
echo "\n<tr align=left><td>Total Paid Out:</td><td align=right><b>$c8</b></td></tr>";
echo "\n</table>";
?><br />
<ul>
<li><a href="<? print ("$self_url"); ?>stats.php<? if ($ref) {echo("?ref=$ref"); }?>" title="Surf Statistics">View 7 Day Surf Stats</a></li>
<li><a href="<? echo($self_url); ?>surfvisitor.php" title="Test The Surf Bar" target="_blank">Test Drive The Surf Bar</a></li>
</ul>
</div>

<div class="titl_left" style="display:block">Featured Links</div>
<div class="ug">
<ul>
<?php text(5,'<li>','</li>'); ?>
</ul>
</div>

</div>
<div style="clear: both"></div>
</div>

<div id="footer">
<p>
<br>

<table><tr>
<td width=50% align=right><script language="JavaScript" src="<? print ("$self_url"); ?>banner.php?style=non_ssi"></script></td>
<td width=50% align=left><script language="JavaScript" src="<? print ("$self_url"); ?>banner.php?style=non_ssi"></script></td>
</tr></table>
</p>
<p>Copyright <?php echo date("Y");?> <? print ("$title"); ?><br>
Powered By: <a href="" target="_blank" title="">Traffic Exchange Open Source</a></p>
</div>

</div>
</div>

</body>
</html>
