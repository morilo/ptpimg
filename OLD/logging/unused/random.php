<?php
require 'cache.php';
$Cache=NEW CACHE;

function dirlist() {
	$d = dir("raw/");
	$contents="";
	$i=0;
	while (false !== ($entry = $d->read())) {
		if ($i>2) $contents.=$entry."\n";
		$i++;
	}
	return $contents;
}

$d=dirlist();

$a = explode("\n", $d);

$id=rand(0,count($a));

echo "<a href='/$a[$id]'><img src='/$a[$id]'></a>";
?>
