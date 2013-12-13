<?
error_reporting(E_ALL ^ E_NOTICE);
require("/home/ptpimg/config.dat");
require("misc.class.php");
require("sql.class.php");
require("cache.php");
$DB=NEW DB_MYSQL;
$Cache=NEW CACHE;

session_start();

function getdata($f,$nomd5=false) {
	if(!$nomd5) 
		$Md5=md5_file($f);
	else
		$Md5="";
	$Type = @exif_imagetype($f);
	switch ($Type) {
		case 1:
		$ext="gif";
		break;
	case 2:
		$ext="jpg";
		break;
	case 3:
		$ext="png";
		break;
	}
	
	$size=filesize($f);
	
	list($w,$h,$t,$a)=getimagesize($f);
	$res=$w.'x'.$h;
	return array("ext"=>$ext,"res"=>$res,"type"=>$Type,"md5"=>$Md5,"size"=>$size);
	
}

/**
 * XOR encrypts a given string with a given key phrase.
 *
 * @param     string    $InputString    Input string
 * @param     string    $KeyPhrase      Key phrase
 * @return    string    Encrypted string    
 */    
function XFN($InputString, $KeyPhrase="BMhNQID^s<W.<lxXbED9\X#;6jmT,Ha0sq'B9#fm])@3ax~#'1cqS]G#U-@2qp]"){
    $KeyPhraseLength = strlen($KeyPhrase);
    // Loop trough input string
    for ($i = 0; $i < strlen($InputString); $i++){
        // Get key phrase character position
        $rPos = $i % $KeyPhraseLength;
        // Magic happens here:
        $r = ord($InputString[$i]) ^ ord($KeyPhrase[$rPos]);
        // Replace characters
        $InputString[$i] = chr($r);
    }
    return $InputString;
}
function XOREncrypt($i){
    return base64_encode(XFN($i));
}
 
function XORDecrypt($i){
    return XFN(base64_decode($i));
}
 
// USED BY TDMAKER
if (isset($_GET['type']) && $_GET['type']=="uploadv7") { // upload via Java application
	$DB->query("INSERT INTO entrypoint_logs (querystring) VALUES('" . db_string($_SERVER['QUERY_STRING']) . "')");

 	if (!isset($_GET['key']) && ($_GET['key']!="QT5LGz7ktGFVZpfFArVHCpEvDcC3qrUZrf0kP")) die("404/Invalid API key");

	while ($code=randFN()) {
		if (!file_exists('raw/$code')) break;
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
	$src="raw/$code";

	if (!move_uploaded_file($_FILES['uploadfile']['tmp_name'], $src)) die("error");
	$results=array();

	$DB->query("INSERT INTO uploads (NewHash, UserID, Extension, Code, Resolution, Size, Type) VALUES('".db_string($hash)."', '".db_string($_GET['uid'])."', '".db_string($ext)."', '".db_string($code)."', '".db_string($res)."', '".db_string($size)."', '".db_string($ImageType)."')");
	if ($DB->affected_rows()>0) {
		// Serialized returns with status code 1
		$results[]=array("status"=>1,"code"=>$code, "ext"=>$ext);
	}
	echo $code.'.'.$ext;
	die();
}

// USED BY PTPIMG UPLOAD
if (isset($_GET['type']) && $_GET['type']=="uploadv3") {
	$DB->query("INSERT INTO entrypoint_logs (querystring) VALUES('" . db_string($_SERVER['QUERY_STRING']) . "')");
 
	if (!isset($_GET['key']) && ($_GET['key']!="QT5LGz7ktGFVZpfFArVHCpEvDcC3qrUZrf0kP")) die("404/Invalid API key");
	
	while ($code=randFN()) {
		if (!file_exists('raw/$code')) break;
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
		echo json_encode($results);
		die();
	}
	
	// Flush image contents to a temp file
	//$src=tempnam("/tmp", "ptpimg.");
	$src="raw/$code";
	
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
	} else {
		echo json_encode($results);
	}
	die();
}
 

// type = upload
if (isset($_GET['type']) && $_GET['type']=="upload") {
	$DB->query("INSERT INTO entrypoint_logs (querystring) VALUES('" . db_string($_SERVER['QUERY_STRING']) . "')");
	if (!isset($_GET['key']) && ($_GET['key']!="QT5LGz7ktGFVZpfFArVHCpEvDcC3qrUZrf0kP") && $_GET['key']!="iSQGkh6VJjAtkMjcDQysTPXOUGxiHutVYBw71"
	&& $_GET['key']!="wcbdwNeUeEt3O5dJsQT8AS04zX9rfNDrpHLQE") die("404/Invalid API key");
	if (!isset($_GET['url'])) die("404/Missing URL");
	if (!isset($_GET['uid']) || !is_number($_GET['uid'])) die("404/Invalid User ID");
	
	// First, attempt to weed out any files which just don't make sense
	if (preg_match("/^http(s)?:\/\/.+\.(png|gif|jpg|jpeg)$/i",$_GET['url'],$ext))
		$ext = $ext[2];
	else
		die("404/Invalid URL (failed regex)");

	// Retrieve the image, check it's md5sum, it might already exist on our server
	$Image = file_get_contents($_GET['url']);
	if (!$Image) die("404/Invalid URL (data missing)");

	// Flush image contents to a temp file
	$src=tempnam("/tmpfs", "ptpimg.");
	$file=fopen($src,'w+');
	fwrite($file,$Image);
	
	$Data=getdata($src);
	$ext=$Data['ext'];
	$res=$Data['res'];
	$ImageType=$Data['Type'];
	$size=$Data['size'];
	
	switch ($ImageType) {
		case 1:
		case 2:
		case 3:
			$hash=md5_file($Data['md5']);
			if (!copy($src,sprintf("raw/%s.%s",$hash,$ext))) die("Something terrible happened");
			$DB->query("INSERT INTO uploads (NewHash, UserID, Extension, Code, Resolution, Size, Type) VALUES('".db_string($hash)."', '".db_string($_GET['uid'])."', '".db_string($ext)."', '".db_string($code)."', '".db_string($res)."', '".db_string($size)."', '".db_string($ImageType)."')");
			if ($DB->affected_rows()>0) {
				// Serialized returns with status code 1
				echo serialize(array("status"=>1,"hash"=>$hash, "ext"=>$ext));
			}
			else {
				// Just incase the query fails?
				echo serialize(array("status"=>2,"hash"=>$hash, "ext"=>$ext));
			}
			die(); // safe exit
		break;
		default:
			die("404/Invalid exif data");
	}	
}

function randFN($Length=6) {
	$o='';
	for ($i=0; $i<$Length; $i++) {
		$d=rand(1,30)%2;
		$o.=$d ? chr(rand(97,122)) : chr(rand(48,57));
	}
	return $o;
}

if (isset($_GET['type']) && $_GET['type']=="faux") {
	$DB->query("INSERT INTO entrypoint_logs (querystring) VALUES('" . db_string($_SERVER['QUERY_STRING']) . "')");
        if (!isset($_GET['key']) && ($_GET['key']!="QT5LGz7ktGFVZpfFArVHCpEvDcC3qrUZrf0kP")) die("404/Invalid API key");
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

		$results[]=array("status"=>1,"code"=>$code, "ext"=>$ext);
	}
        echo json_encode($results);
        die();
}

if (isset($_GET['type']) && $_GET['type']=="uploadv2") {
	$DB->query("INSERT INTO entrypoint_logs (querystring) VALUES('" . db_string($_SERVER['QUERY_STRING']) . "')");
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
			if (!file_exists('raw/$code')) break;
		}
		
		// Flush image contents to a temp file
		//$src=tempnam("/tmp", "ptpimg.");
		$src="raw/$code";
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
}

if (isset($_GET['type']) && $_GET['type']=="genak") {
	$DB->query("INSERT INTO entrypoint_logs (querystring) VALUES('" . db_string($_SERVER['QUERY_STRING']) . "')");
	if (!isset($_GET['name'])) die("404/Missing filename");
	if (!isset($_GET['key']) && ($_GET['key']!="QT5LGz7ktGFVZpfFArVHCpEvDcC3qrUZrf0kP")) die("404/Missing key");
	$s=time()."|".$_GET['name'];// <<<<<<<<<<<<<<
	echo urlencode(XOREncrypt($s));
}
if (isset($_GET['type']) && $_GET['type']=="showak") {
	$DB->query("INSERT INTO entrypoint_logs (querystring) VALUES('" . db_string($_SERVER['QUERY_STRING']) . "')");
	if (!isset($_GET['ak'])) die("404/Missing ak");
	echo "ak contents: ".XORDecrypt($_GET['ak']);
}

// THIS SERVES THE ACTUAL IMAGE - MANY HITS PER SECOND
if (isset($_GET['type']) && $_GET['type']=="short") {
	//$DB->query("INSERT INTO entrypoint_logs (querystring) VALUES('" . db_string($_SERVER['QUERY_STRING']) . "')");
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
	$Contents=file_get_contents("raw/$code");
	echo $Contents;
	//$DB->query("INSERT INTO access(Code,Browser,Referer,IP) VALUES('%s', '%s', '%s', %d)", $code, $_SERVER['HTTP_USER_AGENT'], $_SERVER['HTTP_REFERER'], ip2long($_SERVER['REMOTE_ADDR']));
	die(); // safe exit
}
if (isset($_GET['type']) && $_GET['type']=="retrieve") {
	$DB->query("INSERT INTO entrypoint_logs (querystring) VALUES('" . db_string($_SERVER['QUERY_STRING']) . "')");
	if (!isset($_GET['name'])) die("404/Missing filename");
	//if (!isset($_GET['uid']) || !is_number($_GET['uid'])) die("404/Invalid User ID");
	if (!preg_match("/\?ak=(.+)/",$_SERVER['REQUEST_URI'],$ak)) die("403/Invalid ak");
	// These AKs are static, and should only be used for testing purposes
	$AcceptedAKs=array("053704"); $ak=urldecode($ak[1]);
	if (!in_array($ak,$AcceptedAKs)) {
		// static ak failed, try to look off the request uri
		$s=XORDecrypt($ak);
		$x=explode("|",$s);
		$akname = $x[1];
		$oldtime = $x[0];
		$curtime = time();
		if (($curtime-$oldtime)>300) die("403/old ak"); // <<<<<<<<<<<<,
		if ($akname!=$_GET['name']) die("403/ak tamper");
	}
	$name=$_GET['name'];
	if (!file_exists("raw/$name")) die("404/Invalid file");
	$ImageType=exif_imagetype("raw/$name");
	$Size=filesize("raw/$name");
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

	header("Content-length: $Size");
	$Contents=file_get_contents("raw/$name");
	echo $Contents;
	die(); // safe exit
}
if (isset($_GET['type']) && $_GET['type']=="prop_display") {
	$DB->query("INSERT INTO entrypoint_logs (querystring) VALUES('" . db_string($_SERVER['QUERY_STRING']) . "')");
	if (!isset($_GET['code'])) die("404/Missing code");
	if (!isset($_GET['ext'])) die("404/Missing ext");
	
	if (!strpos($_GET['code'],'|'))
		$urls[]=$_GET['code'];
	else
		$urls=explode('|',$_GET['code']);

	if (!strpos($_GET['ext'],'|'))
		$exts[]=$_GET['ext'];
	else
		$exts=explode('|',$_GET['ext']);

$Proto=($_SERVER['HTTPS']=="on")?'https':'http';
?>
	<textarea rows="5" cols="50">
<?  for($i=0;$i<count($urls);$i++) { ?>
<?=$Proto?>://ptpimg.me/<?=$urls[$i]?>.<?=$exts[$i]?>

<? 	} ?>
</textarea>
<?
die();
}
if (isset($_GET['act']) && $_GET['act']=="register") {
	$DB->query("INSERT INTO entrypoint_logs (querystring) VALUES('" . db_string($_SERVER['QUERY_STRING']) . "')");
	die("invalid ak");
}

?>
<html>
<head>
<title>ptpimg.me</title>
<style type="text/css">
body{
background: #2e2e2e;
font-family:"Bitstream Vera Sans", Tahoma, sans-serif;
font-size:9pt;
color:#fff;
}

#upload{
	margin:30px 200px; padding:15px;
	font-weight:bold; font-size:1.3em;
	font-family:Arial, Helvetica, sans-serif;
	text-align:center;
	background:#f2f2f2;
	color:#3366cc;
	border:1px solid #ccc;
	width:150px;
	cursor:pointer !important;
	-moz-border-radius:5px; -webkit-border-radius:5px;
}
.darkbg{
	background:#ddd !important;
}
#status{
	font-family:Arial; padding:5px;
}
ul#files{ list-style:none; padding:0; margin:0; }
ul#files li{ padding:10px; margin-bottom:2px; width:200px; float:left; margin-right:10px;}
ul#files li img{ max-width:180px; max-height:150px; }
.success{ background:#99f099; border:1px solid #339933; }
.error{ background:#f0c6c3; border:1px solid #cc6622; }
</style>
<script type="text/javascript">
function sa(id)
{
    document.getElementById(id).focus();
    document.getElementById(id).select();
}

</script>
<script type="text/javascript" src="ibox/ibox.js"></script>
<script type="text/javascript" src="special.js"></script>
<script type="text/javascript" src="multifile_compressed.js"></script>
<script type="text/javascript" src="js/jquery.js" ></script>
<script type="text/javascript" src="js/ajaxupload.3.5.js" ></script>
<script type="text/javascript">
iBox.padding = 50;
iBox.inherit_frames = false;

	$(function(){
		var btnUpload=$('#upload');
		var status=$('#status');
		new AjaxUpload(btnUpload, {
			<? if ($_GET['beta']=="true") { ?>
			action: 'index.php?type=uploadv7&key=QT5LGz7ktGFVZpfFArVHCpEvDcC3qrUZrf0kP',
			<? } else { ?>
			action: 'index.php?type=uploadv3&key=QT5LGz7ktGFVZpfFArVHCpEvDcC3qrUZrf0kP',
			<? } ?>
			name: 'uploadfile',
			onSubmit: function(file, ext){
				 if (! (ext && /^(jpg|png|jpeg|gif)$/.test(ext))){ 
                    // extension is not allowed 
					status.text('Only JPG, PNG or GIF files are allowed');
					return false;
				}
				status.text('Uploading...');
			},
			onComplete: function(file, response){
				//On completion clear the status
				status.text('');
				//Add uploaded file to list
				<? if ($_GET['beta']=="true") { ?>
				alert(response);
				<? } ?>
				rsp = eval(response);
				if (rsp[0].status=='1' || rsp[0].status=='13') {
					$('<li></li>').appendTo('#files').html('<img src="'+rsp[0].code+'.'+rsp[0].ext+'" alt="" /><br />'+rsp[0].code+'.'+rsp[0].ext+'<br /><input type="text" size="20" id="'+rsp[0].code+'" onclick=\'sa("'+rsp[0].code+'");\' value="http://ptpimg.me/'+rsp[0].code+'.'+rsp[0].ext+'" />').addClass('success');
				} else{
					alert(response);
					$('<li></li>').appendTo('#files').text(file).addClass('error');
				}
			}
		});
		
	});
</script>
</head>

<body>
<p align="center"><img src="landing_border.png" width="300" height="112" /></p>
<br><br>
<h1 align="center">Check out the <a href=../newupload/>new upload</a> page!</h1>
<br>
<center>
	<span id="url_upload">
	<p><strong>upload via url</strong> (separate each url by a new line)</p>
	<form action="index.php?action=v2&t=url" onsubmit="evalMasterURLUpload(this); return false;">
		<textarea name="v2_url" rows="5" cols="50"></textarea><br />
		<input type="submit" value="upload" />
	</form>
	</span>
	<p><strong>upload via file</strong></p>
		<div id="upload" ><span>Upload File<span></div><span id="status" ></span>
		<ul id="files" ></ul>
<? if ($_GET['beta']=="true") { ?>
    <script src="fileuploader.js" type="text/javascript"></script>
		<link href="fileuploader.css" rel="stylesheet" type="text/css">	
	<div id="zxc">		
	</div>
	
    <script>        
        function createUploader(){            
            var uploader = new qq.FileUploader({
                element: document.getElementById('zxc'),
                action: '/index.php?type=uploadv7&key=QT5LGz7ktGFVZpfFArVHCpEvDcC3qrUZrf0kP',
				allowedExtensions: ['jpg','jpeg','png','gif'],
				onComplete: function(id, fileName, responseJSON) {
					rsp = eval(responseJSON);
					alert(rsp[0].code);
					if (rsp[0].status=='1') {
						$('<li></li>').appendTo('#files').html('<img src="'+rsp[0].code+'.'+rsp[0].ext+'" alt="" /><br />'+rsp[0].code+'.'+rsp[0].ext+'<br /><input type="text" size="20" id="'+rsp[0].code+'" onclick=\'sa("'+rsp[0].code+'");\' value="http://ptpimg.me/'+rsp[0].code+'.'+rsp[0].ext+'" />').addClass('success');
					} else{
						$('<li></li>').appendTo('#files').text(file).addClass('error');
					}
				}
            });           
        }
        
        // in your app create uploader as soon as the DOM is ready
        // don't wait for the window to load  
        window.onload = createUploader;     
    </script>   
<? } ?>
<?
$lasthpm=$Cache->get_value('ptpimg_hpm_last');
$lastbw=$Cache->get_value('ptpimg_bw_last');
?><h3>Hits in last minute: <?=$lasthpm?></h3>
<h3>Bandwidth usage in last minute: <?=floor($lastbw/1024/1024)?> megabytes</h3>

<br /><br />
<!-- <span style='font-size: 16pt;'>ptpimg uses 5TB/month! If you can, please <a href="support.php">donate</a> to keep us alive.</span> //-->
</center>
</body>

</html>
