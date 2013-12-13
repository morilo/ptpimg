<html>
<head>
<title>ptpimg.me</title>
<link href="http://dl.dropbox.com/u/19570059/ptpimgdark.css" rel="stylesheet" type="text/css" />
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
<h3>Hits in last minute: 483</h3>
<h3>Bandwidth usage in last minute: 90 megabytes</h3>
</center>
</body>

</html>
