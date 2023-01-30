<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<?php include ("vars.php"); ?>

<head>
<meta http-equiv="content-type" content="text/html; charset=utf-8" />
<title><?= $title; ?> : <?= $slogan; ?></title>
<meta name="keywords" content="" />
<meta name="description" content="" />
<link href="<?= SITE_URL; ?>/styles.css" rel="stylesheet" type="text/css" media="screen" />
</head>

<body>

<div id="content">
<div id="main">
<div id="page">
<div id="right">	
<div id="header">
  
<div id="logo"> 
<script language="JavaScript" src="<?= SITE_URL; ?>/banner.php?style=non_ssi"></script>
</div>

<div id="menu">
<ul>
<li><a href="<?= SITE_URL; ?>/index.php<?php if ($ref) {echo '?ref=$ref'; }?>" title="Home">Home</a></li>
<li><a href="<?= SITE_URL; ?>/signup.php<?php if ($ref) {echo '?ref=$ref'; }?>" title="Signup">Signup</a></li>
<li><a href="<?= SITE_URL; ?>/faq.php<?php if ($ref) {echo '?ref=$ref'; }?>" title="FAQs">FAQs</a></li>
<li><a href="<?= SITE_URL; ?>/signup.php?show=terms<?php if ($ref) {echo '&ref=$ref'; }?>" title="Terms">Terms</a></li>
<li><a href="<?= SITE_URL; ?>/signup.php?show=privacy<?php if ($ref) {echo '&ref=$ref'; }?>" title="Privacy">Privacy</a></li>
<li><a href="<?= SITE_URL; ?>/contact.php<?php if ($ref) {echo '?ref=$ref'; }?>" title="Contact">Contact</a></li>
</ul>
</div>

</div>

<div id="right_page_ads">
</div>

<div id="right_page">

<div id="text">
<div class="text_s">
