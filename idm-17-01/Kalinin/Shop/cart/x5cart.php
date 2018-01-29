<?php

include ("../res/x5engine.php");
$ecommerce = new ImCart();
// Setup the coupon data
$couponData = array();
$couponData['products'] = array();
// Setup the cart
$ecommerce->setPublicFolder('');
$ecommerce->setCouponData($couponData);
$ecommerce->setSettings(array(
	'force_sender' => false,
	'email_opening' => 'Уважаемый клиент,<br /><br />Благодарим Вас за покупку. Здесь Вы найдете информацию о заказе.<br /><br />Ниже приведен список заказанных товаров.',
	'email_closing' => 'Обращайтесь к нам за дополнительной информацией.<br /><br />С уважением, сотрудники отдела продаж.',
	'useCSV' => true,
	'header_bg_color' => '#800000',
	'header_text_color' => '#FFFFFF',
	'cell_bg_color' => '#FFFFFF',
	'cell_text_color' => '#000000',
	'availability_reduction_type' => 1,
	'border_color' => '#D3D3D3',
	'owner_email' => 'vk-m93@yandex.ru',
	'vat_type' => 'included'
));

// Check the coupon code
if (@$_GET['action'] == 'chkcpn' && isset($_POST['coupon'])) {
	header('Content-type: application/json');
	echo $ecommerce->checkCoupon($_POST['coupon']);
	exit();
}
// Check the dynamic products status
else if (@$_GET['action'] == 'productstatus' && !isset($_POST['product_id']) && ($headers = imRequestHeaders()) !== false) {
	$token = "";
	foreach ($headers as $key => $value) {
		if (strtolower($key) == 'x-incomedia-wsx5-token') {
			$token = $value;
		}
	}
	if ($token == '662hb66fehqflx6tf296bup0k521s71wf193') {
		header('Content-type: application/json');
		echo json_encode(array('data' => $ecommerce->getDynamicProductsStatus()));
		exit();
	}
}
// Check a single dynamic products status
else if (@$_GET['action'] == 'productstatus' && isset($_POST['product_id'])) {
	header('Content-type: application/json');
	echo $ecommerce->getDynamicProductQuantity(@$_POST['product_id']);
	exit();
}
else if (@$_GET['action'] == 'sndrdr' && isset($_POST['orderData'])) {
	$orderNo = $_POST['orderData']['orderNo'];
	$ecommerce->setOrderData($_POST['orderData']);
	$ecommerce->sendOwnerEmail();
	$ecommerce->sendCustomerEmail();
	header('Content-type: application/json');
	echo '{ "status": "ok", "orderNumber": "' . $orderNo . '" }';
	exit;
}

// End of file x5cart.php
