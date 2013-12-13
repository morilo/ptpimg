<?
if(!isset($_GET['v6'])) die();
if($_GET['v6']!="zWhqd8lAyNgK8MMbsXOBjz5wjUJJpdQeJA12M98ETW8WOREc1COYkKaO82LJhQy") die();
if(!isset($_GET['nolimit'])) {
	if(!isset($_GET['id']) || !is_numeric($_GET['id'])) $ID=0;
	else $ID=$_GET['id'];
	$IDExtra="AND ID>".$ID." AND ID<".($ID+20000);
} else {
	$IDExtra="";
}
error_reporting(0);
require('../config.dat');
require("misc.class.php");
require("sql.class.php");
$DB=NEW DB_MYSQL;
$Resolution="";
if(!isset($_GET['v2'])) $v2=="def"; else $v2=$_GET['v2'];
if($v2=="def") {
	$Resolution="(resolution='1536x1180' OR
		resolution='1180x1536' OR
		resolution='1600x1200' OR
		resolution='1200x1600' OR
		resolution='2048x1536' OR
		resolution='1536x2048' OR
		resolution='2240x1680' OR
		resolution='1680x2240' OR
		resolution='2560x1920' OR
		resolution='1920x2560' OR
		resolution='3032x2008' OR
		resolution='2008x3032' OR
		resolution='3072x2304' OR
		resolution='2304x3072' OR
		resolution='3264x2448' OR
		resolution='2448x3264')";
} else {
	$Resolution="resolution='".db_string($_GET['v2'])."'";
	$IDExtra="";
}
$q=("SELECT Code, Extension
		FROM uploads
		WHERE
$Resolution
$IDExtra");
$DB->query($q);
$Data=$DB->to_array();
if($IDExtra)
	echo "<a href='?v2=def&v6=".$_GET['v6']."&id=".($ID+20000)."'>next</a><br />";
while(list($Key,list($Code, $Ext))=each($Data)) {
	if($_GET['v2']=="def")
		echo sprintf("<a href='http://ptpimg.me/%s.%s'><img src='http://ptpimg.me/%s.%s' height='500' width='500' /></a><br />",$Code,$Ext,$Code,$Ext);
	else
		echo sprintf("<a href='http://ptpimg.me/%s.%s'><img src='http://ptpimg.me/%s.%s' /></a><br />",$Code,$Ext,$Code,$Ext);
}
if($IDExtra)
	echo "<a href='?v2=def&v6=".$_GET['v6']."&id=".($ID+20000)."'>next</a><br />";
?>
