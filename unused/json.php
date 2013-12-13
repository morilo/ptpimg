<?
require("/home/ptpimg/config.dat");
require("misc.class.php");
require("sql.class.php");
require("cache.php");
$DB=NEW DB_MYSQL;
$Cache=NEW CACHE;
if(isset($_GET['action']) && !empty($_GET['action'])) $Action=$_GET['action'];
else die("json_request");

function getImageCount() {
	global $DB, $Cache;
        if((list($Count)=$Cache->get_value('ptpimg_sql_counts'))===false) {
                $DB->query("SELECT Count(ID) FROM uploads");
                list($Count)=$DB->next_record();
                $Cache->cache_value('ptpimg_sql_counts', array($Count), 60); // 30 minutes
        }
	return $Count;
}

function getTotalSize() {
	global $DB, $Cache;
        if((list($Size)=$Cache->get_value('ptpimg_sql_size'))===false) {
                $DB->query("SELECT SUM(Size) FROM uploads");
                list($Size)=$DB->next_record();
                $Cache->cache_value('ptpimg_sql_size', array($Size), 60); // 30 minutes
        }
	return $Size;
}

switch($Action) {
	case 'random': // random

	$Count=getImageCount(); // Grab image count

	$Rand=rand(1,$Count);
	$DB->query("SELECT Code, Type, Size FROM uploads LIMIT $Rand, 1");
	list($Code, $Type, $Size)=$DB->next_record();
	echo json_encode(array(array('code'=>$Code, 'type'=>$Type, 'size'=>$Size)));
	break;

	case 'last5': // last5

        $DB->query("SELECT Code, Type, Size FROM uploads ORDER BY ID DESC LIMIT 5");
	$Data=array();
        while(list($Code, $Type, $Size)=$DB->next_record())
	       $Data[]=array('code'=>$Code, 'type'=>$Type, 'size'=>$Size);

	echo json_encode(array($Data));
        break;

	case 'stats': // stats

	$Size=getTotalSize();
	$Count=getImageCount();
	echo json_encode(array(array('size'=>$Size, 'count'=>$Count)));
}

?>
