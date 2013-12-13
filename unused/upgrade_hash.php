<?
if (isset($_SERVER['REMOTE_ADDR'])) die("nah");
define('SQLHOST','localhost'); //The MySQL host ip/fqdn
define('SQLLOGIN','ptpimg');//The MySQL login
define('SQLPASS','mri34mni'); //The MySQL password
define('SQLDB','ptpimg'); //The MySQL database to use
define('SQLPORT','3306'); //The MySQL port to connect on
define('SQLSOCK','/var/run/mysqld/mysqld.sock');
require("misc.class.php");
require("sql.class.php");
$DB=NEW DB_MYSQL;

$DB->query("SELECT ID, Code FROM uploads WHERE NewHash=''");
$Results = $DB->to_array('', MYSQLI_NUM, false);
$UBound=count($Results);
$QueryCount=0;
$Query=array();
while(list($Key,list($ID, $Code))=each($Results)) {
	if ($QueryCount>500) {
		echo "500 queries, flushing.";
		$x=microtime(); $x=explode(" ",$x); $x=$x[1]+$x[0];
		foreach($Query as $q) {
			$DB->query($q);
		}
	        $y=microtime(); $y=explode(" ",$y); $y=$y[1]+$y[0];
	        $e=$y-$x; $e=floor($e*1000);
		echo "... done (".$DB->affected_rows()." affected) (".($e)." ms)\n";
		$QueryCount=0;
		unset($x,$y,$e,$Query);
	}
	$NewHash=md5_file("raw/$Code");
	if (!$NewHash && !file_exists("raw/$Code")) {
		$DB->query("DELETE FROM uploads WHERE ID='".$ID."'");
		continue;
	}
	echo "$Key / $UBound Updating hash: $Code -> $NewHash\n";
	$Query[]="UPDATE uploads SET NewHash='".$NewHash."' WHERE ID='".$ID."'\n";
	$QueryCount++;
	unset($Key,$ID,$Code);
}
if ($QueryCount>0) {
	foreach($Query as $q) {
		$DB->query($q);
        }
}
die();
?>
