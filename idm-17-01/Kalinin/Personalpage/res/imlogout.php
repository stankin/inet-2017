<?php
@require_once("x5engine.php");

$pa = new imPrivateArea();
$pa->logout();
header("Location: " . $imSettings['general']['homepage_url']);

// End of file imlogout.php
