<?
define('SQLHOST','localhost'); //The MySQL host ip/fqdn
define('SQLLOGIN','');//The MySQL login
define('SQLPASS', ''); //The MySQL password
define('SQLDB',''); //The MySQL database to use
define('SQLPORT',''); //The MySQL port to connect on
define('SQLSOCK','/var/run/mysqld/mysqld.sock');
require("misc.class.php");
require("sql.class.php");
$DB=NEW DB_MYSQL;


/**
 * XOR encrypts a given string with a given key phrase.
 *
 * @param     string    $InputString    Input string
 * @param     string    $KeyPhrase      Key phrase
 * @return    string    Encrypted string    
 */    
function XFN($InputString, $KeyPhrase="blanked out"){
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
 

if (isset($_GET['type']) && $_GET['type']=="uploadv3") {
 	if (!isset($_GET['key']) && ($_GET['key']!="QT5LGz7ktGFVZpfFArVHCpEvDcC3qrUZrf0kP")) die("404/Invalid API key");
	
	while ($code=randFN()) {
		if (!file_exists('raw/$code')) break;
	}
	
	// Flush image contents to a temp file
	//$src=tempnam("/tmp", "ptpimg.");
	$src="raw/$code";
	if (!move_uploaded_file($_FILES['uploadfile']['tmp_name'], $src)) die("error");
	$results=array();
	// Read image type
	// 1-gif, 2-jpeg, 3-png
	$ImageType=exif_imagetype($src);
	switch ($ImageType) {
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
	
	$hash=md5($Image);
	$DB->query("INSERT INTO uploads (Hash, UserID, Extension, Code) VALUES('".db_string($hash)."', '".db_string($_GET['uid'])."', '".db_string($ext)."', '".db_string($code)."')");
	if ($DB->affected_rows()>0) {
		// Serialized returns with status code 1
		$results[]=array("status"=>1,"code"=>$code, "ext"=>$ext);
	}
	echo json_encode($results);
	die();
}
 

// type = upload
if (isset($_GET['type']) && $_GET['type']=="upload") {
	if (!isset($_GET['key']) && ($_GET['key']!="QT5LGz7ktGFVZpfFArVHCpEvDcC3qrUZrf0kP")) die("404/Invalid API key");
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
	$src=tempnam("/tmp", "ptpimg.");
	$file=fopen($src,'w+');
	fwrite($file,$Image);
	
	// Read image type
	// 1-gif, 2-jpeg, 3-png
	$ImageType=exif_imagetype($src);
	switch ($ImageType) {
		case 1:
		case 2:
		case 3:
			$hash=md5($Image);
			if (!copy($src,sprintf("raw/%s.%s",$hash,$ext))) die("Something terrible happened");
			$DB->query("INSERT INTO uploads (Hash, UserID, Extension) VALUES('".db_string($hash)."', '".db_string($_GET['uid'])."', '".db_string($ext)."')");
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

if (isset($_GET['type']) && $_GET['type']=="uploadv2") {
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
		
		// Retrieve the image, check it's md5sum, it might already exist on our server
		$Image = file_get_contents($url);
		if (!$Image) die("404/Invalid URL (data missing)");

		$code='';
		while ($code=randFN()) {
			if (!file_exists('raw/$code')) break;
		}
		
		// Flush image contents to a temp file
		//$src=tempnam("/tmp", "ptpimg.");
		$src="raw/$code";
		$file=fopen($src,'w+');
		fwrite($file,$Image);
		
		// Read image type
		// 1-gif, 2-jpeg, 3-png
		$ImageType=exif_imagetype($src);
		switch ($ImageType) {
			case 1:
			case 2:
			case 3:
				$hash=md5($Image);
				$DB->query("INSERT INTO uploads (Hash, UserID, Extension, Code) VALUES('".db_string($hash)."', '".db_string($_GET['uid'])."', '".db_string($ext)."', '".db_string($code)."')");
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
	if (!isset($_GET['name'])) die("404/Missing filename");
	if (!isset($_GET['key']) && ($_GET['key']!="QT5LGz7ktGFVZpfFArVHCpEvDcC3qrUZrf0kP")) die("404/Missing key");
	$s=time()."|".$_GET['name'];// <<<<<<<<<<<<<<
	echo urlencode(XOREncrypt($s));
}
if (isset($_GET['type']) && $_GET['type']=="showak") {
	if (!isset($_GET['ak'])) die("404/Missing ak");
	echo "ak contents: ".XORDecrypt($_GET['ak']);
}
if (isset($_GET['type']) && $_GET['type']=="short") {
	if (!isset($_GET['code'])) die("404/Missing code");
	else $code=substr($_GET['code'], 0, 6); // cut off the file extension
	
	if (!file_exists("raw/$code")) die("404/Image not found");
	else {
		$ImageType=exif_imagetype("raw/$code");
		$Size=filesize("raw/$code");
		switch ($ImageType) {
			case 1:
				header("Content-type: image/gif");
			break;
			case 2:
				header("Content-type: image/png");
			break;
			case 3:
				header("Content-type: image/jpeg");
			break;
			default:
				header("Content-type: application/octet-stream");
		}
		header("Content-length: $Size");
		$Contents=file_get_contents("raw/$code");
		echo $Contents;
		die(); // safe exit
	}
}
if (isset($_GET['type']) && $_GET['type']=="retrieve") {
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
			header("Content-type: image/png");
		break;
		case 3:
			header("Content-type: image/jpeg");
		break;
		default:
			header("Content-type: application/octet-stream");
	}
	header("Content-length: $Size");
	$Contents=file_get_contents("raw/$name");
	echo $Contents;
	die(); // safe exit
}
if (isset($_GET['type']) && $_GET['type']=="prop_display") {
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
		
?>
	<textarea rows="5" cols="50">
<?  for($i=0;$i<count($urls);$i++) { ?>
http://ptpimg.me/<?=$urls[$i]?>.<?=$exts[$i]?>

<? 	} ?>
</textarea>
<?
die();
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
			action: 'index.php?type=uploadv3&key=QT5LGz7ktGFVZpfFArVHCpEvDcC3qrUZrf0kP',
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
				rsp = eval(response);
				if (rsp[0].status=='1') {
					$('<li></li>').appendTo('#files').html('<img src="'+rsp[0].code+'.'+rsp[0].ext+'" alt="" /><br />'+rsp[0].code+'.'+rsp[0].ext+'<br /><input type="text" size="20" id="'+rsp[0].code+'" onclick=\'sa("'+rsp[0].code+'");\' value="http://ptpimg.me/'+rsp[0].code+'.'+rsp[0].ext+'" />').addClass('success');
				} else{
					$('<li></li>').appendTo('#files').text(file).addClass('error');
				}
			}
		});
		
	});
</script>
</head>

<body>
<p align="center"><img src="landing_border.png" width="600" height="225" /></p>
<h3 align="center">need your images hosted?</h3>
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
</center>
</body>

</html>
