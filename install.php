<?php

define('NEWINSTALLATION', 'TRUE');

$installpathurl = 'http://'.$_SERVER['HTTP_HOST'].$_SERVER["PHP_SELF"];

$installpathurl = str_replace(basename($_SERVER["PHP_SELF"]),'',$installpathurl);

$sqlarray = array(

"CREATE TABLE 7stat (

  usrid int(11) default NULL,

  date date default NULL,

  time time default '00:00:00',

  pg_views int(11) default '0',

  num float default NULL,

  received_pay char(3) default 'no'

) ENGINE=MyISAM",

"CREATE TABLE 7statsite (

  siteid int(11) default NULL,

  date date default NULL,

  last_hit_time time default '00:00:00',

  num int(11) default NULL

) ENGINE=MyISAM",

"CREATE TABLE abuse (

  id int(11) NOT NULL auto_increment,

  siteid int(11) default NULL,

  usrid int(11) default NULL,

  text text,

  date timestamp(6) NOT NULL,

  PRIMARY KEY  (id)

) ENGINE=MyISAM",

"CREATE TABLE acctype (

  id int(11) NOT NULL auto_increment,

  name varchar(100) default NULL,

  descr text,

  ratemin float default '0',

  ratemax float default '0',

  cost float default '0',

  cashout float default '0',

  commissions float default '0',

  min_sites int(11) default '0',

  monthly_bonus float default '0',

  upg_time int(11) default '0',

  rpgebonus float default '0',

  rbonuses varchar(100) default NULL,

  levels varchar(100) default NULL,

  ptc_levels varchar(100) default NULL,

  PRIMARY KEY  (id)

) ENGINE=MyISAM",

"CREATE TABLE ad_info (

  ad_id int(11) NOT NULL auto_increment,

  ad_name varchar(100) NOT NULL default '',

  type int(11) NOT NULL default '0',

  link_url text NOT NULL,

  banner_url text NOT NULL,

  h int(11) NOT NULL default '0',

  w int(11) NOT NULL default '0',

  alt varchar(100) NOT NULL default '',

  bottom_text varchar(100) NOT NULL default '',

  mouse_text varchar(100) NOT NULL default '',

  html text NOT NULL,

  num_allow_exp int(11) NOT NULL default '0',

  num_exp int(11) NOT NULL default '0',

  num_clicks int(11) NOT NULL default '0',

  prob int(11) NOT NULL default '0',

  win int(11) NOT NULL default '0',

  adv_user int(11) default '0',

  date varchar(10) NOT NULL default '',

  PRIMARY KEY  (ad_id)

) ENGINE=MyISAM",

"CREATE TABLE admin (

  field varchar(20) default NULL,

  value varchar(255) default NULL,

  UNIQUE KEY `field` (`field`)

) ENGINE=MyISAM",

"CREATE TABLE `adminprops` (

  `field` varchar(50) default NULL,

  `value` varchar(255) default '0',

  UNIQUE KEY `field` (`field`)

) ENGINE=MyISAM DEFAULT CHARSET=latin1",

"CREATE TABLE banned_emails (

  id int(11) NOT NULL auto_increment,

  value varchar(255) default NULL,

  PRIMARY KEY  (id)

) ENGINE=MyISAM",

"CREATE TABLE banned_ipadds (

  id int(11) NOT NULL auto_increment,

  value varchar(15) default NULL,

  PRIMARY KEY  (id)

) ENGINE=MyISAM",

"CREATE TABLE banned_sites (

  id int(11) NOT NULL auto_increment,

  domain varchar(255) default NULL,

  type char(1) default NULL,

  PRIMARY KEY  (id)

) ENGINE=MyISAM",

"CREATE TABLE banner (

  id int(11) NOT NULL auto_increment,

  imgurl varchar(255) default NULL,

  widtheight varchar(24) default NULL,

  PRIMARY KEY  (id)

) ENGINE=MyISAM",

"CREATE TABLE cashout_history (

  id int(11) NOT NULL auto_increment,

  usrid int(11) default '0',

  amount float(10,2) default '0.00',

  descr varchar(100) default NULL,

  pay_merch varchar(50) default NULL,

  paid_to varchar(150) default NULL,

  cdate date default '0000-01-01',

  PRIMARY KEY  (id)

) ENGINE=MyISAM",

"CREATE TABLE comission_history (

  id int(11) NOT NULL auto_increment,

  paid_to int(11) default '0',

  usrid int(11) default '0',

  wasfor varchar(100) default NULL,

  amount float(11,2) default '0.00',

  vdate date default '0000-01-01',

  PRIMARY KEY  (id)

) ENGINE=MyISAM",

"CREATE TABLE faq (

  id int(11) NOT NULL auto_increment,

  quest text,

  answ text,

  PRIMARY KEY  (id)

) ENGINE=MyISAM",

"CREATE TABLE gp_info (

  gp_id int(11) NOT NULL auto_increment,

  gid int(11) NOT NULL default '0',

  ad_id int(11) NOT NULL default '0',

  ad_prob int(11) NOT NULL default '0',

  PRIMARY KEY  (gp_id)

) ENGINE=MyISAM",

"CREATE TABLE gp_name (

  id int(11) NOT NULL auto_increment,

  gp_name varchar(100) NOT NULL default '',

  PRIMARY KEY  (id)

) ENGINE=MyISAM",

"CREATE TABLE html (

  type varchar(15) default NULL,

  content text,

  UNIQUE KEY `type` (`type`)

) ENGINE=MyISAM",

"CREATE TABLE investment_history (

  id int(11) NOT NULL auto_increment,

  usrid int(11) default '0',

  amount float(10,2) default '0.00',

  descr varchar(100) default NULL,

  is_from varchar(50) default '0',

  processor varchar(100) default NULL,

  adate date default '0000-01-01',

  expired char(3) default 'no',

  PRIMARY KEY  (id)

) ENGINE=MyISAM",

"CREATE TABLE member_refs (

  mem_id int(11) NOT NULL default '0',

  ref_id int(11) NOT NULL default '0',

  UNIQUE KEY uniqueness (mem_id,ref_id),

  KEY usersid (mem_id),

  KEY upline (ref_id)

) ENGINE=MyISAM",

"CREATE TABLE merchant_codes (

  id int(11) NOT NULL auto_increment,

  name varchar(100) default NULL,

  code text,

  PRIMARY KEY  (id)

) ENGINE=MyISAM",

"CREATE TABLE monthly_stats (

  id int(11) NOT NULL auto_increment,

  usrid int(11) default '0',

  days_paid_roi int(11) default '0',

  past_earnings float(10,5) default '0.00000',

  roi_earned float(10,4) default '0.0000',

  coms_earned float(10,4) default '0.0000',

  sbcash_earned float(10,5) default '0.00000',

  ptc_cash_e float(10,4) default '0.0000',

  refptc_cash float(10,4) default '0.0000',

  misc_earned float(10,4) default '0.0000',

  tot_owed float(10,5) default '0.00000',

  paid_out float(10,3) default '0.000',

  monthis int(2) default '0',

  yearis int(4) default '0',

  this_month date default '0000-01-01',

  month_transfer char(3) default 'no',

  earn_pay char(3) default 'no',

  paidout char(3) default 'no',

  finalized char(3) default 'no',

  PRIMARY KEY  (id)

) ENGINE=MyISAM",

"CREATE TABLE other_history (

  id int(11) NOT NULL auto_increment,

  usrid int(11) default '0',

  amount float(10,2) default '0.00',

  descr varchar(100) default NULL,

  is_from varchar(50) default '0',

  adate date default '0000-01-01',

  PRIMARY KEY  (id)

) ENGINE=MyISAM",

"CREATE TABLE ptc_orders (

  ptcid int(11) NOT NULL auto_increment,

  type enum('banner','text') NOT NULL default 'banner',

  type2 enum('cash','credit') NOT NULL default 'cash',

  userid int(11) default NULL,

  linkid varchar(25) default NULL,

  banurl varchar(200) default NULL,

  linkurl text NOT NULL,

  linktxt text,

  clicks_remain int(11) NOT NULL default '0',

  amt_sent int(11) NOT NULL default '0',

  date_sent date default '0000-01-01',

  date_done date default '0000-01-01',

  cash_click float default '0',

  credit_click float default '0',

  adtimer int(5) NOT NULL default '0',

  day_lock int(5) NOT NULL default '0',

  PRIMARY KEY  (ptcid)

) ENGINE=MyISAM",

"CREATE TABLE ptc_tracking (

  userid int(11) default NULL,

  banlinkid varchar(25) default NULL,

  cdate date default NULL

) ENGINE=MyISAM",

"CREATE TABLE referstats (

  id int(11) NOT NULL auto_increment,

  usrid int(11) default '0',

  orgip varchar(15) default NULL,

  refip varchar(15) default NULL,

  httpref varchar(255) default NULL,

  browser varchar(255) default NULL,

  cdate date default NULL,

  ctime time default NULL,

  PRIMARY KEY  (id)

) ENGINE=MyISAM",

"CREATE TABLE sellcredit (

  id int(11) NOT NULL auto_increment,

  name varchar(255) default NULL,

  descr text,

  cost float default '0',

  PRIMARY KEY  (id)

) ENGINE=MyISAM",

"CREATE TABLE site (

  id int(11) NOT NULL auto_increment,

  usrid int(11) default NULL,

  name varchar(255) default NULL,

  url varchar(255) default NULL,

  lang varchar(30) default NULL,

  state varchar(30) default NULL,

  credits float default '0',

  totalhits int(11) default '0',

  hitslastmail int(11) default '0',

  hour varchar(13) default '0',

  cph int(11) default '0',

  cth int(11) default '0',

  PRIMARY KEY  (id)

) ENGINE=MyISAM",

"CREATE TABLE tads (

  id int(11) NOT NULL auto_increment,

  text text,

  PRIMARY KEY  (id)

) ENGINE=MyISAM",

"CREATE TABLE user (

  id int(11) NOT NULL auto_increment,

  name varchar(100) default NULL,

  email varchar(100) default NULL,

  passwd varchar(20) default NULL,

  pay_to int(11) default '0',

  payout_address varchar(150) default NULL,

  ref int(11) default '0',

  invested float(10,2) default '0.00',

  acctype int(11) default NULL,

  credits float default '0',

  roi_cash float(10,4) default '0.0000',

  cshfrmallrefs float(10,5) default '0.00000',

  lifetime_cash float(10,3) default '0.000',

  lifetime_paid float(10,2) default '0.00',

  lifetot_roi float(10,5) default '0.00000',

  joindate datetime default NULL,

  lastmail date default '0000-01-01',

  minmax int(11) default NULL,

  crdsfrmallrefs float default '0',

  rbon_credits float default '0',

  rpage_credits float default '0',

  lifetime_credits float default '0',

  lifetime_pages int(11) default '0',

  sb_credits float default '0',

  sb_cash float(10,5) default '0.00000',

  ptc_clicks int(11) default '0',

  ptc_crds float default '0',

  ptc_cash float default '0',

  lastroi date default '0000-01-01',

  lastaccess datetime default NULL,

  lastsurfed datetime default '0000-01-01 00:00:00',

  upgrade_ends date default NULL,

  premregdate date default '0000-01-01',

  premmp int(11) default '0',

  toref float default '0',

  cshtoref float(10,5) default '0.00000',

  commstoref float(10,4) default '0.0000',

  allow_contact char(3) default 'yes',

  status varchar(20) default NULL,

  ip_address varchar(15) default NULL,

  ac int(11) default '0',

  PRIMARY KEY  (id),

  UNIQUE KEY email (email)

) ENGINE=MyISAM",

"INSERT INTO acctype VALUES (1, 'Free Member', 'Free account', '0.5', '0.8', '0', '15', '0.1', 10, '0', 0, 0.5, '100,50,30,20,10', '20,10,5,5,5', '20,10,5,5,5')",

"INSERT INTO acctype VALUES (2, 'Upgraded Member', '1:1 Upgraded Membership<br>\r\n1:1 Surf Ratio!<br>Purchase 1 Upgrade Credit and automatically upgrade to 1:1 Upgraded Status!', '0.8', '1', '10', '10', '0.1', 100, '1000', 365, 1, '100,50,30,20,10', '30,20,10,5,5', '20,10,5,5,5')",

"INSERT INTO admin VALUES ('login', 'admin')",

"INSERT INTO admin VALUES ('email', NULL)",

"INSERT INTO admin VALUES ('passwd', 'admin')",

"INSERT INTO admin VALUES ('lastaccess', '0')",

"INSERT INTO admin VALUES ('lastac', '0')",

"INSERT INTO admin VALUES ('lastacip', '0.0.0.0')",

"INSERT INTO admin VALUES ('lastip', '0.0.0.0')",

"INSERT INTO admin VALUES ('lstcrn', '0000-01-01')",

"INSERT INTO adminprops VALUES ('inibon', '1000')",

"INSERT INTO adminprops VALUES ('insbon', '1')",

"INSERT INTO adminprops VALUES ('reftim', '6')",

"INSERT INTO adminprops VALUES ('negact', '0')",

"INSERT INTO adminprops VALUES ('contex', '5')",

"INSERT INTO adminprops VALUES ('contey', '2')",

"INSERT INTO adminprops VALUES ('contcx', '6')",

"INSERT INTO adminprops VALUES ('contcy', '0.005')",

"INSERT INTO adminprops VALUES ('surplu', '0')",

"INSERT INTO adminprops VALUES ('csurpl', '0')",

"INSERT INTO adminprops VALUES ('sharec', '10')",

"INSERT INTO adminprops VALUES ('sharea', '1000')",

"INSERT INTO adminprops VALUES ('inact', '180')",

"INSERT INTO `adminprops` VALUES('private_sys_email', 'email@domain.com')",

"INSERT INTO `adminprops` VALUES('self_url', '$installpathurl')",

"INSERT INTO `adminprops` VALUES('default_site', 'http://www.rota.fulba.com')",

"INSERT INTO `adminprops` VALUES('title', 'YourSiteTitle')",

"INSERT INTO `adminprops` VALUES('slogan', 'Get Paid For Visiting Websites')",

"INSERT INTO `adminprops` VALUES('upgrade_title', 'Upgrade Unit')",

"INSERT INTO `adminprops` VALUES('fontface', '#CCCCCC')",

"INSERT INTO `adminprops` VALUES('admincolor', '#CCCCCC')",

"INSERT INTO `adminprops` VALUES('adminbutton', '#CCCCCC')",

"INSERT INTO `adminprops` VALUES('activation_pages', '5')",

"INSERT INTO `adminprops` VALUES('allow_rand_refs', 'no')",

"INSERT INTO `adminprops` VALUES('allow_mmax', '1')",

"INSERT INTO `adminprops` VALUES('allow_site_validation', '1')",

"INSERT INTO `adminprops` VALUES('allow_cashout_requests', '1')",

"INSERT INTO `adminprops` VALUES('allow_member_roi_upgrades', '1')",

"INSERT INTO `adminprops` VALUES('max_invest_days', '1')",

"INSERT INTO `adminprops` VALUES('min_credits_to_earn_free', '5')",

"INSERT INTO `adminprops` VALUES('roi_conversion_ratio_free', '15')",

"INSERT INTO `adminprops` VALUES('min_credits_to_earn_pro', '5')",

"INSERT INTO `adminprops` VALUES('roi_conversion_ratio_pro', '25')",

"INSERT INTO `adminprops` VALUES('surf_max_free', '50')",

"INSERT INTO `adminprops` VALUES('surf_max_pro', '50')",

"INSERT INTO `adminprops` VALUES('keep_stats', '7')",

"INSERT INTO `adminprops` VALUES('keep_site_stats', '7')",

"INSERT INTO `adminprops` VALUES('keep_refpage_stats', '7')",

"INSERT INTO `adminprops` VALUES('upgrade_member_if_buy', '1')",

"INSERT INTO `adminprops` VALUES('email_admin_if_buy', '1')",

"INSERT INTO `adminprops` VALUES('email_admin_when_roi', '1')",

"INSERT INTO `adminprops` VALUES('allow_referral_upgrades', '1')",

"INSERT INTO `adminprops` VALUES('mem_edit_special_note', 'Please ensure your payout details are correct!')",

"INSERT INTO `adminprops` VALUES('default_banner', 'http://bullebear.com/Fogo.jpg')",

"INSERT INTO `adminprops` VALUES('default_link', 'http://bullebear.com')",

"INSERT INTO `adminprops` VALUES('sendweeklymail', '1')",

"INSERT INTO banned_emails VALUES (1, '*@mail.com')",

"INSERT INTO html VALUES ('terms', 'This Terms Of Use page is edited through your administration area. Simply login, <b>click on Website Content/HTML</b>, and away you go!  The design height will automatically adjust to the amount of text you add in this area.')",

"INSERT INTO html VALUES ('priva', 'This Privacy Policy is edited through your administration area. Simply login, <b>click on Website Content/HTML</b>, and away you go!  The design height will automatically adjust to the amount of text you add in this area.

<br><br>

Very simple to do!!')",

"INSERT INTO html VALUES ('1page', 'This Home Page is edited through your administration area. Simply login, <b>click on Website Content/HTML</b>, and away you go! The design height will automatically adjust to the amount of text you add in this area.

<br><br>

Enter your program details here (signup bonuses, referral earnings etc. etc.).')",

"INSERT INTO html VALUES ('testi', '<b>Member:</b> Joe Blow<br>

Member ID#: 1<br>

<b>Comment:</b> This program is a great way to promote your urls. This place has a very responsive member base, just great!

<br><br>

<b>Member:</b> Jane Doe<br>

Member ID#: 234<br>

<b>Comment:</b> What a great site! I love surfing here!

<br>

<br>

<br>

This Testimonials page is edited through your administration area. Simply login, <b>click on Website Content/HTML</b>, and away you go!  The design height will automatically adjust to the amount of text you add in this area.

<br><br>

Very simple to do!!')",

"INSERT INTO `faq` (`id`, `quest`, `answ`) VALUES

(1, 'How Do I Add FAQs?', 'Its simple, login to your admin, click on FAQ, and add in all the frequently asked questions you want!');",

"INSERT INTO `merchant_codes` (`id`, `name`, `code`) VALUES

(1, 'PayPal', '<form action=\"https://www.paypal.com/cgi-bin/webscr\" method=\"post\">\r\n<input type=\"hidden\" name=\"cmd\" value=\"_xclick\">\r\n<input type=\"hidden\" name=\"business\" value=\"YOU@YOUREMAIL.COM\">\r\n<input type=\"hidden\" name=\"item_name\" value=\"[description] - ID#: [user] - [email]\">\r\n<input type=\"hidden\" name=\"item_number\" value=\"[description] - ID#: [user] - [email]\">\r\n<input type=\"hidden\" name=\amount\" value=\"[cost]\">\r\n<input type=\"hidden\" name=\"no_shipping\" value=\"1\">\r\n<input type=\"hidden\" name=\"return\" value=\"".$installpathurl."members/thankyou.php\">\r\n<input type=\"hidden\" name=\"cancel_return\" value=\"".$installpathurl."index.php\">\r\n<input type=\"hidden\" name=\"no_note\" value=\"1\">\r\n<input type=\"hidden\" name=\"currency_code\" value=\"USD\">\r\n<input type=\"hidden\" name=\"lc\" value=\"US\">\r\n<input type=\"hidden\" name=\"bn\" value=\"PP-BuyNowBF\">\r\n<input type=\"image\" src=\"../images/btn_buynowCC_LG.gif\" border=\"0\" name=\"submit\" alt=\"Make payments with PayPal\"></form>'),

(5, 'Alertpay', '<form method=\"post\" action=\"https://www.alertpay.com/PayProcess.aspx\" >\r\n<input type=\"hidden\" name=\"ap_purchasetype\" value=\"item\"/>\r\n<input type=\"hidden\" name=\"ap_merchant\" value=\"YOU@YOUREMAIL.COM\"/>\r\n<input type=\"hidden\" name=\"ap_itemname\" value=\"[description] - ID#: [user] - [email]\"/>\r\n<input type=\"hidden\" name=\"ap_currency\" value=\"USD\"/>\r\n<input type=\"hidden\" name=\"ap_returnurl\" value=\"".$installpathurl."members/thankyou.php\"/>\r\n<input type=\"hidden\" name=\"ap_itemcode\" value=\"[description] - ID#: [user] - [email]\"/>\r\n<input type=\"hidden\" name=\"ap_quantity\" value=\"1\"/>\r\n<input type=\"hidden\" name=\"ap_amount\" value=\"[cost]\"/>\r\n<input type=\"hidden\" name=\"ap_cancelurl\" value=\"".$installpathurl."index.php\"/>\r\n<input type=\"image\" name=\"ap_image\" src=\"../images/pay_now_11.gif\" alt=\"Pay through Alertpay\"/></form>')",

"INSERT INTO `sellcredit` (`id`, `name`, `descr`, `cost`) VALUES
(1, '1000 Surf Credits', '1000 Surf Credits', 3),

(2, '100 PTC Credits', '100 PTC Credits', 2.5),

(3, '10000 Banner Impressions', '10000 Banner Impressions', 5),

(4, '10000 Text Ad Impressions', '10000 Text Ad Impressions', 5);",

"INSERT INTO `tads` (`id`, `text`) VALUES (1, '<a href=\"http://www.earnalot.biz\" title=\"EarnALot\">Instant Online Earnings</a>'),

(2, '<a href=\"http://www.earnalot.biz\" title=\"EarnALot\">Your EarnALot Biz</a>');",

"INSERT INTO `banner` VALUES (1, '".$installpathurl."banners/ban1.gif', 'width=\"468\" height=\"60\"')"
);



if(!isset($_POST['startinstall'])){

	echo "<center><h1>New Installation</h1><br><br><br><br><form method='POST'><input type='submit' name='startinstall' value='Start Installation'></form>";

}else{

	require("vars.php");


  $mysqli = new mysqli($db_host, $db_user, $db_pwd, $db_name);
	// Check connection
  if ($mysqli -> connect_errno) {
		echo "Failed to connect to MySQL: " . $mysqli -> connect_error;
		exit();
		}

	$installstat = true;

	foreach ($sqlarray as $sql){

		$t = explode(" ",$sql);

		if($mysqli -> query($sql)){

			echo "$t[0] $t[1] $t[2] Successfull<br>";

		}else{

			$installstat = false;

			echo "$t[0] $t[1] $t[2] Failed with Reason : ".mysql_error()."<br>";

		}

	}

	if($installstat){

		echo "<br><br><br><b><h1>Installation Successfull.</h1><br>Delete the install.php file now for security reason";

	}else{

		echo "<br><br><br><b><h1>Installation Fail.</h1><br>Drop all the table from the database and try again. <a href='install.php'>Click Here</a> to Retry.";



	}

}

?>
