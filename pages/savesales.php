<?php
session_start();
require_once('connect.php');
$a = $_POST['invoice'];
$b = $_POST['cashier'];
$c = $_POST['date'];
$d = $_POST['ptype'];
$e = $_POST['amount'];
$cname = $_POST['cname'];

$date = date('F d, Y');

$dmonth = date('F');
$dyear = date('Y');

if($d=='credit') {
	include('connect.php');
	$f = $_POST['due'];
	$sql = "INSERT INTO sales (invoice_number,cashier,date,type,total_amount,due_date,name,month,year,balance) VALUES (:a,:b,:c,:d,:e,:f,:g,:h,:i,:k)";
	$q = $db->prepare($sql);
	$q->execute(array(':a'=>$a,':b'=>$b,':c'=>$c,':d'=>$d,':e'=>$e,':f'=>$f,':g'=>$cname,':h'=>$dmonth,':i'=>$dyear,':k'=>$e));
	header("location: preview.php?invoice=$a");
	exit();
}
if($d=='cash') {
	include('connect.php');
	$f = $_POST['cash'];
	$sql = "INSERT INTO sales (invoice_number,cashier,date,type,amount,cash,name,month,year) VALUES (:a,:b,:c,:d,:e,:f,:g,:h,:i)";
	$q = $db->prepare($sql);
	$q->execute(array(':a'=>$a,':b'=>$b,':c'=>$c,':d'=>$d,':e'=>$e,':f'=>$f,':g'=>$cname,':h'=>$dmonth,':i'=>$dyear));
	header("location: preview.php?invoice=$a");
	exit();
}
// query

?>