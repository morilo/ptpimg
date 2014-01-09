 <?
 	if (!isset($_GET['key']) && ($_GET['key']!="QT5LGz7ktGFVZpfFArVHCpEvDcC3qrUZrf0kP")) die("404/Invalid API key");
	
	while ($code=randFN()) {
		if (!file_exists('/home/ptpimg/public_html/raw/$code')) break;
	}
	$Data=getdata($_FILES['uploadfile']['tmp_name']);
	$res=$Data['res'];
	$ext=$Data['ext'];
	$hash=$Data['md5'];
	$size=$Data['size'];
	$ImageType=$Data['type'];
	$DB->query("SELECT Code, Extension FROM uploads WHERE NewHash='".db_string($hash)."'");
	if ($DB->record_count()>0) {
		list($Code, $Extension)=$DB->next_record();
	
		$results[]=array("status"=>13,"code"=>$Code, "ext"=>$Extension);
		echo $Code.'.'.$Extension;
		die();
	}

	// Flush image contents to a temp file
	//$src=tempnam("/tmp", "ptpimg.");
	$src="/home/ptpimg/public_html/raw/$code";

	if (!move_uploaded_file($_FILES['uploadfile']['tmp_name'], $src)) die("error");
	$results=array();

	$DB->query("INSERT INTO uploads (NewHash, UserID, Extension, Code, Resolution, Size, Type) VALUES('".db_string($hash)."', '".db_string($_GET['uid'])."', '".db_string($ext)."', '".db_string($code)."', '".db_string($res)."', '".db_string($size)."', '".db_string($ImageType)."')");
	if ($DB->affected_rows()>0) {
		// Serialized returns with status code 1
		$results[]=array("status"=>1,"code"=>$code, "ext"=>$ext);
	}
	echo $code.'.'.$ext;
	die();
	?>
