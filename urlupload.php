<?
	if (!isset($_GET['key']) && ($_GET['key']!="QT5LGz7ktGFVZpfFArVHCpEvDcC3qrUZrf0kP" && $_GET['key']!="iSQGkh6VJjAtkMjcDQysTPXOUGxiHutVYBw71")) die("404/Invalid API key");
	if (!isset($_GET['url'])) die("404/Missing URL");
	if (!isset($_GET['uid']) || !is_numeric($_GET['uid'])) die("404/Invalid User ID");
	
	if ($_GET['url']=="c_h_e_c_k_p_o_s_t") {
		$_GET['url']=$_POST['urls'];
	}

	if (strpos($_GET['url'],"\n"))
		$urls=explode("\n",$_GET['url']);
	else {
		$urls=array();
		$urls[]=$_GET['url'];
	}
	$results=array();
	foreach($urls as $url) {
		// First, attempt to weed out any files which just don't make sense
		if (preg_match("/^http(s)?:\/\/.+\.(png|gif|jpg|jpeg)$/i",$url,$ext))
			$ext = $ext[2];
		else
			continue;
		
		// Retrieve the image, check it's md5sum, it might already exist on our server
		$Image = file_get_contents($url);
		if (!$Image) die("404/Invalid URL (data missing)");
		$Unique = uniqid();
		$tmpFile=fopen("/tmpfs/ptpimg_".$Unique, 'w+');
		fwrite($tmpFile, $Image);

		$Data=getdata("/tmpfs/ptpimg_".$Unique);
		$ext=$Data['ext'];
		$res=$Data['res'];
		$ImageType=$Data['type'];
		$size=$Data['size'];
		$hash=$Data['md5'];
		$DB->query("SELECT Code, Extension FROM uploads WHERE NewHash='".db_string($hash)."'");

		if ($DB->record_count()>0) {
			unlink("/tmpfs/ptpimg_".$Unique);
                        list($Code, $Extension)=$DB->next_record();
			$results[]=array("status"=>13,"code"=>$Code, "ext"=>$Extension);
			continue;
                }

		$code='';
		while ($code=randFN()) {
			if (!file_exists('/home/ptpimg/public_html/raw/$code')) break;
		}
		
		// Flush image contents to a temp file
		//$src=tempnam("/tmp", "ptpimg.");
		$src="/home/ptpimg/public_html/raw/$code";
		rename("/tmpfs/ptpimg_".$Unique, $src);
		// Read image type
		// 1-gif, 2-jpeg, 3-png
		switch ($ImageType) {
			case 1:
			case 2:
			case 3:
				$DB->query("INSERT INTO uploads (NewHash, UserID, Extension, Code, Resolution, Size, Type) VALUES('".db_string($hash)."', '".db_string($_GET['uid'])."', '".db_string($ext)."', '".db_string($code)."', '".db_string($res)."', '".db_string($size)."', '".db_string($ImageType)."')");
				if ($DB->affected_rows()>0) {
					// Serialized returns with status code 1
					$results[]=array("status"=>1,"code"=>$code, "ext"=>$ext);
				}
			break;
		}	
	}
	echo json_encode($results);
	die();
	?>
