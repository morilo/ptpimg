<?
error_reporting(E_ALL ^ E_NOTICE);
require("/home/ptpimg/config.dat");
require("misc.class.php");
require("sql.class.php");
require("cache.php");
$DB=NEW DB_MYSQL;
$Cache=NEW CACHE;

session_start();


require("helpers.php");


if (isset($_GET['type']) && $_GET['type']=="uploadv7") { // upload via Java application
	require("javaupload.php");
}

if (isset($_GET['type']) && $_GET['type']=="uploadv3") {
	require("ajaxupload.php");
}


// type = upload
/*if (isset($_GET['type']) && $_GET['type']=="upload") {
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
}*/

/*if (isset($_GET['type']) && $_GET['type']=="faux") {
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
}*/

if (isset($_GET['type']) && $_GET['type']=="uploadv2") {
	require("urlupload.php");
}


if (isset($_GET['type']) && $_GET['type']=="short") {
	require("serveimg.php");
}
/*if (isset($_GET['type']) && $_GET['type']=="retrieve") {
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
}*/
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
/*if (isset($_GET['act']) && $_GET['act']=="register") {
	die("invalid ak");
}*/

?>
<html>
<head>
<title>ptpimg.me</title>
<link rel="stylesheet" href="css/bootstrap.min.css">
<!-- Generic page styles -->
<link rel="stylesheet" href="css/style.css">
<!-- blueimp Gallery styles -->
<link rel="stylesheet" href="css/blueimp-gallery.min.css">
<!-- CSS to style the file input field as button and adjust the Bootstrap progress bars -->
<link rel="stylesheet" href="css/jquery.fileupload-ui.css">
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


<script type="text/javascript" src="ibox/ibox.js"></script>
<script type="text/javascript" src="special.js"></script>
<script type="text/javascript">
iBox.padding = 50;
iBox.inherit_frames = false;

	/*$(function(){
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

	});*/
</script>
</head>

<body>
<p align="center"><img src="landing_border.png" width="300" height="112" /></p>
<h4 align="center">need your images hosted?</h4>
<center>
	<span id="url_upload">
	<p><strong>upload via url</strong> (separate each url by a new line)</p>
	<form action="index.php?action=v2&t=url" onsubmit="evalMasterURLUpload(this); return false;">
		<textarea name="v2_url" rows="5" cols="50"></textarea><br />
		<input type="submit" value="upload" />
	</form>
	</span>

<!-- ***** BEGIN MULTIUPLOADER ***** -->

<div class="container">
    <!-- The file upload form used as target for the file upload widget -->
    <form id="fileupload" action="index.php" method="POST" enctype="multipart/form-data">
        <!-- The fileupload-buttonbar contains buttons to add/delete files and start/cancel the upload -->
        <div class="row fileupload-buttonbar">
            <div class="col-lg-12">
                <!-- The fileinput-button span is used to style the file input field as button -->
                <span class="btn btn-success fileinput-button">
                    <i class="glyphicon glyphicon-plus"></i>
                    <span>Add files...</span>
                    <input type="file" name="files[]" multiple>
                </span>
                <button type="submit" class="btn btn-primary start">
                    <i class="glyphicon glyphicon-upload"></i>
                    <span>Start upload</span>
                </button>
                <button type="reset" class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Cancel upload</span>
                </button>
                <!--<button type="button" class="btn btn-danger delete">
                    <i class="glyphicon glyphicon-trash"></i>
                    <span>Delete</span>
                </button>
                <input type="checkbox" class="toggle">-->
                <!-- The loading indicator is shown during file processing -->
                <span class="fileupload-loading"></span>
            </div>
        </div>
        <div class="row fileupload-buttonbar">
            <!-- The global progress information -->
            <div class="col-lg-12 fileupload-progress fade">
                <!-- The global progress bar -->
                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
                    <div class="progress-bar progress-bar-success" style="width:0%;"></div>
                </div>
                <!-- The extended global progress information -->
                <div class="progress-extended">&nbsp;</div>
            </div>
        </div>
        <div style="padding-bottom: 15px">
        	<textarea style="width: 332px;" rows="3" onclick="this.select();" class="all-links-list" placeholder="Uploaded Image URLs..."></textarea>
        </div>
        <!-- The table listing the files available for upload/download -->
        <table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>
    </form>
</div>

<!-- The template to display files available for upload -->
<script id="template-upload" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-upload fade">
        <td>
            <span class="preview"></span>
        </td>
        <td>
        	<input type="hidden" class="positionid input" name="positionid" value="0">
            <p class="name">{%=file.name%}</p>
            {% if (file.error) { %}
                <div><span class="label label-danger">Error</span> {%=file.error%}</div>
            {% } %}
        </td>
        <td>
            <p class="size">{%=o.formatFileSize(file.size)%}</p>
            {% if (!o.files.error) { %}
                <div class="progress progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="progress-bar progress-bar-success" style="width:0%;"></div></div>
            {% } %}
        </td>
        <td>
            {% if (!o.files.error && !i && !o.options.autoUpload) { %}
                <button class="btn btn-primary start">
                    <i class="glyphicon glyphicon-upload"></i>
                    <span>Start</span>
                </button>
            {% } %}
            {% if (!i) { %}
                <button class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Cancel</span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>
<!-- The template to display files available for download -->
<script id="template-download" type="text/x-tmpl">
{% for (var i=0, file; file=o.files[i]; i++) { %}
    <tr class="template-download fade">
        <td>
            <span class="preview">
                {% if (file.thumbnailUrl) { %}
                    <a href="{%=file.url%}" title="{%=file.name%}" target="_blank"><img width="{%=file.width%}" height="{%=file.height%}" src="{%=file.thumbnailUrl%}"></a>
                {% } %}
            </span>
        </td>
        <td>
        	<input type="hidden" class="positionid" name="positionid" value="{%=file.positionid %}">
            <p class="name">{%=file.name%}</p>
            <p><input type="text" class="dlurl" onClick="this.select();" value="{%=file.url%}"></p>
            {% if (file.error) { %}
                <div><span class="label label-danger">Error</span> {%=file.error%}</div>
            {% } %}
        </td>
        <td>
            <span class="size">{%=o.formatFileSize(file.size)%}</span>
        </td>
        <td>
            {% if (file.deleteUrl) { %}
                <button class="btn btn-danger delete" data-type="{%=file.deleteType%}" data-url="{%=file.deleteUrl%}"{% if (file.deleteWithCredentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
                    <i class="glyphicon glyphicon-trash"></i>
                    <span>Delete</span>
                </button>
                <input type="checkbox" name="delete" value="1" class="toggle">
            {% } else if (true == false) { /* never show cancel after upload */ %}
                <button class="btn btn-warning cancel">
                    <i class="glyphicon glyphicon-ban-circle"></i>
                    <span>Cancel</span>
                </button>
            {% } %}
        </td>
    </tr>
{% } %}
</script>

<!-- ***** END MULTIUPLOADER ***** -->

<?
$lasthpm=$Cache->get_value('ptpimg_hpm_last');
$lastbw=$Cache->get_value('ptpimg_bw_last');
?><h5>Hits in last minute: <?=$lasthpm?></h5>
<h5>Bandwidth usage in last minute: <?=floor($lastbw/1024/1024)?> megabytes</h5>

<br /><br />
<!-- <span style='font-size: 16pt;'>ptpimg uses 5TB/month! If you can, please <a href="support.php">donate</a> to keep us alive.</span> //-->
</center>

<script src="//ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js"></script>
<!-- The jQuery UI widget factory, can be omitted if jQuery UI is already included -->
<script src="js/vendor/jquery.ui.widget.js"></script>
<!-- The Templates plugin is included to render the upload/download listings -->
<script src="js/tmpl.min.js"></script>
<!-- The Load Image plugin is included for the preview images and image resizing functionality -->
<script src="js/load-image.min.js"></script>
<!-- The Canvas to Blob plugin is included for image resizing functionality -->
<!--<script src="http://blueimp.github.io/JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js"></script>-->
<!-- Bootstrap JS is not required, but included for the responsive demo navigation -->
<!--<script src="//netdna.bootstrapcdn.com/bootstrap/3.0.0/js/bootstrap.min.js"></script>-->
<!-- blueimp Gallery script -->
<script src="js/jquery.blueimp-gallery.min.js"></script>
<!-- The Iframe Transport is required for browsers without support for XHR file uploads -->
<script src="js/jquery.iframe-transport.js"></script>
<!-- The basic File Upload plugin -->
<script src="js/jquery.fileupload.js"></script>
<!-- The File Upload processing plugin -->
<script src="js/jquery.fileupload-process.js"></script>
<!-- The File Upload image preview & resize plugin -->
<script src="js/jquery.fileupload-image.js"></script>
<!-- The File Upload audio preview plugin -->
<!--<script src="js/jquery.fileupload-audio.js"></script>-->
<!-- The File Upload video preview plugin -->
<!--<script src="js/jquery.fileupload-video.js"></script>-->
<!-- The File Upload validation plugin -->
<script src="js/jquery.fileupload-validate.js"></script>
<!-- The File Upload user interface plugin -->
<script src="js/jquery.fileupload-ui.js"></script>
<!-- The main application script -->
<script src="js/main.js"></script>
<!-- The XDomainRequest Transport is included for cross-domain file deletion for IE 8 and IE 9 -->
<!--[if (gte IE 8)&(lt IE 10)]>
<script src="js/cors/jquery.xdr-transport.js"></script>
<![endif]-->
</body>

</html>
