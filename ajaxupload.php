 <?
 	if (!isset($_GET['key']) && ($_GET['key']!="QT5LGz7ktGFVZpfFArVHCpEvDcC3qrUZrf0kP")
 							 && ($_GET['key']!="AYInBgmRgMGdEukQmjBvEWNsFlBJlebEqhGykjyk")) //darthnerdus
 		die("404/Invalid API key");

	while ($code=randFN()) {
		if (!file_exists('/home/ptpimg/public_html/raw/$code')) break;
	}
	$Data=getdata($_FILES['uploadfile']['tmp_name']);
	$originalFilename=str_replace(">", "", str_replace("<", "", $_FILES['uploadfile']['name']));
	$positionId=intval($_POST['positionid']);
	$res=$Data['res'];
	$scaledDims=scaledDimensions($res, 80);
	$width=$scaledDims[0];
	$height=$scaledDims[1];
	$ext=$Data['ext'];
	$hash=$Data['md5'];
	$size=$Data['size'];
	$ImageType=$Data['type'];
	$DB->query("SELECT Code, Extension FROM uploads WHERE NewHash='".db_string($hash)."'");
	if ($DB->record_count()>0) {
		list($Code, $Extension)=$DB->next_record();
		if (isset($_GET['tdmaker'])) {
			echo "http://ptpimg.me/".$Code.".".$Extension;
			die();
		}
		if ($_GET['resp']=="jqu") {
			$filenamestr = "http://ptpimg.me/".$Code.".".$Extension;
			$results=array("files"=>array(array("name"=>$originalFilename, "positionid"=>$positionId, "size"=>$size, "url"=>$filenamestr, "thumbnailUrl"=>$filenamestr, "height"=>$height, "width"=>$width)));
		} else {
			$results[]=array("status"=>13,"code"=>$Code, "ext"=>$Extension);
		}
		echo json_encode($results);
		die();
	}

	// Flush image contents to a temp file
	//$src=tempnam("/tmp", "ptpimg.");
	$src="/home/ptpimg/public_html/raw/$code";

	$Data=getdata($_FILES['uploadfile']['tmp_name']);

	if (!move_uploaded_file($_FILES['uploadfile']['tmp_name'], $src)) die("error");
	$results=array();

        $DB->query("INSERT INTO uploads (NewHash, UserID, Extension, Code, Resolution, Size, Type) VALUES('".db_string($hash)."', '".db_string($_GET['uid'])."', '".db_string($ext)."', '".db_string($code)."', '".db_string($res)."', '".db_string($size)."', '".db_string($ImageType)."')");
	if ($DB->affected_rows()>0) {
		// Serialized returns with status code 1
		$results[]=array("status"=>1,"code"=>$code, "ext"=>$ext);
	} else {
		die("db error?");
	}
	if(isset($_GET['tdmaker'])) {
		echo "http://ptpimg.me/".$code.".".$ext;
	} else if ($_GET['resp']=="jqu") {
		$filenamestr = "http://ptpimg.me/".$code.".".$ext;
		echo json_encode(array("files"=>array(array("name"=>$originalFilename, "positionid"=>$positionId, "size"=>$size, "url"=>$filenamestr, "thumbnailUrl"=>$filenamestr, "height"=>$height, "width"=>$width))));
	} else {
		echo json_encode($results);
	}
	die();
	?>
