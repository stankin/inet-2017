<?php

if (!isset($_POST['order_number'])) exit('An error has occurred');
$mrh_login = "1";
$mrh_pass1 = "1";
$inv_id = 0; // Leave it to 0. It will be created by Robokassa.
$inv_desc = "Номер заказа: " . @$_POST['order_number']; // The order number is recorded here as reference
$out_summ = @$_POST['amount'];
$shp_item = @$_POST['order_number'];
$in_curr = "RUB";
$culture = "ru";
$encoding = "utf-8";
$crc = md5("$mrh_login:$out_summ:$inv_id:$mrh_pass1:Shp_item=$shp_item");
echo "<html><script type=\"text/javascript\" ".
"src=\"http://test.robokassa.ru/Index.aspx?".
"MrchLogin=$mrh_login&OutSum=$out_summ&InvId=$inv_id&IncCurrLabel=$in_curr".
"&Desc=$inv_desc&SignatureValue=$crc&Shp_item=$shp_item".
"&Culture=$culture&Encoding=$encoding\"></script></html>";
