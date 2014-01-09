<?
	if (!isset($_GET['code'])) die("404/Missing code");
	if ($_GET['code'] == "favicon") die();
	if (strpos($_GET['code'],'/')) {
		$code=explode('/',$_GET['code']);
		$r=rand(0,count($code)-1);
		$code=$code[$r];
	}
	else $code=$_GET['code'];
	$code=substr($code, 0, 6); // cut off the file extension
	
	// Check if they're coming from a partner site
	if(isset($_SERVER['HTTP_REFERER']) && !empty($_SERVER['HTTP_REFERER'])) {
		$Host=parse_url($_SERVER['HTTP_REFERER'], PHP_URL_HOST);
		if(array_find($Host, $AllowedSites)===FALSE) {
			$DB->query("INSERT INTO access_unauth(Code,Browser,Referer,IP) VALUES('%s', '%s', '%s', %d)", $code, $_SERVER['HTTP_USER_AGENT'], $_SERVER['HTTP_REFERER'], ip2long($_SERVER['REMOTE_ADDR']));
			header("HTTP/1.0 403 Forbidden");
			die();
		}
	}
	# 6/24

	header("Cache-Control: private, max-age=1209600, pre-check=10800");
	header("Pragma: private");
	header("Expires: " . date(DATE_RFC822,strtotime(" 14 day")));

	if(isset($_SERVER['HTTP_IF_MODIFIED_SINCE'])){
	  // if the browser has a cached version of this image, send 304
	  header('Last-Modified: '.$_SERVER['HTTP_IF_MODIFIED_SINCE'],true,304);
	  exit;
	}
	$Size=-1;
	$ImageType=-1;
	if(!($ImgData=$Cache->get_value('imgdata_'.$code))) {
		$DB->query("SELECT Size, Type FROM uploads WHERE Code='".db_string($code)."'");
		if ($DB->record_count()>0) {
			list($Size,$ImageType)=$DB->next_record();
			$Cache->cache_value('imgdata_'.$code, array('size'=>$Size, 'type'=>$ImageType), 0);
		} else {
			$Cache->cache_value('imgdata_'.$code, array('size'=>-1, 'type'=>-1), 120);
		}
	} else {
		$Size=$ImgData['size'];
		$ImageType=$ImgData['type'];
		if($Size<0 || $ImageType<0) {
			header("HTTP/1.0 404 Not Found");
			die();
		}
	}

	switch ($ImageType) {
		case 1:
			header("Content-type: image/gif");
		break;
		case 2:
			header("Content-type: image/jpeg");
		break;
		case 3:
			header("Content-type: image/png");
		break;
		default:
			header("Content-type: application/octet-stream");
	}

	if (($lasthpm=$Cache->get_value('ptpimg_hpm_last'))===false) {
				// last values
                $lasthpm = $Cache->get_value('ptpimg_hpm');
				$lastbw = $Cache->get_value('ptpimg_bw');
                
				$Cache->cache_value('ptpimg_hpm_last', $lasthpm, 60);
				$Cache->cache_value('ptpimg_bw_last', $lastbw, 60);
				
				$Cache->delete_value('ptpimg_hpm');
                $Cache->delete_value('ptpimg_bw');
				if($lasthpm>0 && $lastbw>0)
					$DB->query("INSERT into records (hits,bandwidth) value(%d, %d)", $lasthpm, $lastbw);


	}
	if (($hpm=$Cache->get_value('ptpimg_hpm'))===false) {
		$hpm = 1;
		$Cache->cache_value('ptpimg_hpm', $hpm, 0);
	} else {
		$Cache->increment('ptpimg_hpm');
	}

	if (($bwpm=$Cache->get_value('ptpimg_bw'))===false) {
		$bw = $Size;
		$Cache->cache_value('ptpimg_bw', $bw, 0);
	} else {
		$Cache->increment('ptpimg_bw', $Size);
	}
	header("Content-length: $Size");
	#$Contents=file_get_contents("raw/$code");
	#echo $Contents;
	readfile("raw/$code");
	//$DB->query("INSERT INTO access(Code,Browser,Referer,IP) VALUES('%s', '%s', '%s', %d)", $code, $_SERVER['HTTP_USER_AGENT'], $_SERVER['HTTP_REFERER'], ip2long($_SERVER['REMOTE_ADDR']));
	die(); // safe exit
	?>
