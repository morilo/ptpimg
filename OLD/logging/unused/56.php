<?
require("cache.php");
require("misc.class.php");
$Cache = new CACHE();
if (($lasthpm=$Cache->get_value('ptpimg_hpm_last'))===false) {
	$lasthpm = $Cache->get_value('ptpimg_hpm');
	$Cache->cache_value('ptpimg_hpm_last', $lasthpm, 60);
	$Cache->delete_value('ptpimg_hpm');
}
if (($hpm=$Cache->get_value('ptpimg_hpm'))===false) {
	$hpm = 1;
	$Cache->cache_value('ptpimg_hpm', $hpm, 0);
} else {
	$Cache->increment('ptpimg_hpm');
}
echo "Average hits in the last minute: $lasthpm";
echo "<br /><br />";
echo $Cache->get_value('ptpimg_hpm_last');
echo "<br />";
echo $Cache->get_value('ptpimg_hpm');
?>
