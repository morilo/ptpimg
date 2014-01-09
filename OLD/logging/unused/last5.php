<?php
function myscandir($dir, $exp, $how='name', $desc=0) 
{ 
    $r = array(); 
    $dh = @opendir($dir); 
    if ($dh) { 
        while (($fname = readdir($dh)) !== false) { 
            if (preg_match($exp, $fname)) { 
                $stat = stat("$dir/$fname"); 
                $r[$fname] = ($how == 'name')? $fname: $stat[$how]; 
            } 
        } 
        closedir($dh); 
        if ($desc) { 
            arsort($r); 
        } 
        else { 
            asort($r); 
        } 
    } 
    return(array_keys($r)); 
} 

$a=myscandir("raw/", "/.+/", "ctime", 1);

$r=0;
for($i=0;$i<5;$i++) {
	if (!preg_match('/^([a-zA-Z0-9]+)$/',$a[$r])) { $i--; $r++; continue; }
	echo "<a href='/$a[$r]'><img src='/$a[$r]'></a><br />";
	$r++;
}
?>
