<?
if (isset($_GET['s'])) $showimg=true; else $showimg=false;

$g=glob("raw/*");
foreach ($g as $glob) {
	$glob=substr($glob, 4); // remove the raw/
	echo "<a href='http://ptpimg.me/$glob'>";
?>
<?=($showimg==true) ? "<img src='http://ptpimg.me/$glob' />" : $glob?>
<? echo "</a><br />";
}
?>
