<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if(!empty($_POST['renttype']) && !empty($_POST['pickuploc']) && !empty($_POST['dropoffloc'])&& !empty($_POST['pickdate']) && !empty($_POST['picktime']) && !empty($_POST['dropdate']) && !empty($_POST['droptime']) && !empty($_POST['rentemail'])) {
	$to = 'your@email.com'; // Your e-mail address here.
	$body = "\nCar Type: {$_POST['renttype']}\n\n\nPick-Up Location: {$_POST['pickuploc']}\nPick-Up Date: {$_POST['pickdate']} {$_POST['picktime']}\n\n\nDrop-Off Location: {$_POST['dropoffloc']}\nDrop-Off Date: {$_POST['dropdate']} {$_POST['droptime']}\n\n\nEmail: {$_POST['rentemail']}\n\n";
	mail($to, "Сar rental order from yoursite.com", $body, "From: {$_POST['rentemail']}"); // E-Mail subject here.
    }
}
?>