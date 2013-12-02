<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<? include ("vars.php"); ?>

<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><? print ("$title"); ?> : <? print ("$slogan"); ?></title>
<meta name="keywords" content="" />
<meta name="description" content="" />
<link href="<? print ("$self_url"); ?>styles.css" rel="stylesheet" type="text/css" media="screen" />
</head>

<body>

<div id="content">
<div id="main">
<div id="page">
<div id="right">	
<div id="header">
  
<div id="logo"> 
<script language="JavaScript" src="<? print ("$self_url"); ?>banner.php?style=non_ssi"></script>
</div>

<div id="menu">
<ul>
<li><a href="<? print ("$self_url"); ?>index.php<? if ($ref) {echo("?ref=$ref"); }?>" title="Home">Home</a></li>
<li><a href="<? print ("$self_url"); ?>signup.php<? if ($ref) {echo("?ref=$ref"); }?>" title="Signup">Signup</a></li>
<li><a href="<? print ("$self_url"); ?>faq.php<? if ($ref) {echo("?ref=$ref"); }?>" title="FAQs">FAQs</a></li>
<li><a href="<? print ("$self_url"); ?>signup.php?show=terms<? if ($ref) {echo("&ref=$ref"); }?>" title="Terms">Terms</a></li>
<li><a href="<? print ("$self_url"); ?>signup.php?show=privacy<? if ($ref) {echo("&ref=$ref"); }?>" title="Privacy">Privacy</a></li>
<li><a href="<? print ("$self_url"); ?>contact.php<? if ($ref) {echo("?ref=$ref"); }?>" title="Contact">Contact</a></li>
</ul>
</div>

</div>

<div id="right_page_ads">
</div>

<div id="right_page">

<div id="text">
<div class="text_s">
