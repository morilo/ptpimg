<?
if(!isset($_GET['v6'])) die();
if($_GET['v6']!="zWhqd8lAyNgK8MMbsXOBjz5wjUJJpdQeJA12M98ETW8WOREc1COYkKaO82LJhQy") die();
error_reporting(0);
require('../config.dat');
require("misc.class.php");
require("sql.class.php");
$DB=NEW DB_MYSQL;
$Resolution="exif like '%".db_string($_GET['v2'])."%'";
$q=("SELECT Code, Extension
		FROM uploads
		WHERE
$Resolution
");
$DB->query($q);
$Data=$DB->to_array();
while(list($Key,list($Code, $Ext))=each($Data)) {
		echo sprintf("<a href='http://ptpimg.me/%s.%s'><img width=200 height=200 src='http://ptpimg.me/%s.%s' /></a><br />",$Code,$Ext,$Code,$Ext);
}
?>
