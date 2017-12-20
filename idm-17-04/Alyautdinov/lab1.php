<!DOCTYPE html> 
<?php
 include("style.css");
 define('_DEFOUT','USD.EUR.UAH.CNY.BYN',true);
 if (isset($_GET['out'])) 
    if (ctype_alnum(str_replace('.','',$_GET['out'])))
       define('_GETOUT',$_GET['out'],true);
 define('_VALOUT',(defined('_GETOUT')?_GETOUT:_DEFOUT),true);
 $cbr_file = simplexml_load_file('http://www.cbr.ru/scripts/XML_daily.asp');
 $cbr_file_dat = array();
 foreach ($cbr_file->children() as $cbr_file_item)
 {
  $_chc = strval($cbr_file_item->CharCode);
  $_res = array(
   'name'  => strval($cbr_file_item->Name),
   'nomc'  => strval($cbr_file_item->Nominal),
   'value' => strval($cbr_file_item->Value)
  );
  $cbr_file_dat[$_chc] = $_res;
 }
?>
<html lang="ru"> 
	<head> 
		<title>Аляутдинов Артем</title> 
		<meta charset="utf-8"> 
		<meta name="viewport" content="width=device-width, initial-scale=1"> 
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"> 
	</head> 
	<body> 
	 
	<div class="container-fluid bg-2 text-center blocks"> 
		<h1>Интернет-Технологии</h1> 
		<p>&nbsp;</p>
		<h4>Лабораторная работа 1.</h4>
		<h4>Вывод данных по курсу валют на текущее время.</h4>
	</div> 
	 
	<div class="container-fluid bg-3 text-center blocks"> 
		<h1>Курс валют</h1> 
		<?
			 $_TOOUT = explode('.',_VALOUT);
			 echo '<table class="simple-little-table" cellpadding="0" >'."\n";
			  echo "<th>Название валюты</th><th>Номинал</th><th>Курс</th>";
			 foreach ($_TOOUT as $_KEY => $_VAL)
			 {
			  if (isset($cbr_file_dat[$_VAL]))
			  {
			   echo "  <tr>\n";
			   echo "    <td>".$cbr_file_dat[$_VAL]['name']."</td>\n";
			   echo "    <td>".$cbr_file_dat[$_VAL]['nomc']."</td>\n";
			   echo "    <td>".$cbr_file_dat[$_VAL]['value']."</td>\n";
			   echo "  </tr>\n";
			  }
			 }
			 echo "</table>\n";
		?>
	</div> 

</body> 
	<footer>
		<div class="container-fluid bg-1 text-center blocks"> 
			<h2>Выполнил:</h1>
			<p>&nbsp;</p>
			<h3>Аляутдинов Артем</h3>
			<h3>Студент группы ИДМ-17-04</h3> 
		</div> 
	</footer>
</html>
 
