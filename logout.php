<?php
session_start();
include("vars.php");
$_SESSION = array();
session_destroy();
header("Location: $self_url?logged-out");
exit;
?>
