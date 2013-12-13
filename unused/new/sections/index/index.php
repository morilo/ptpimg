<?
require SERVER_ROOT.'/classes/class_image.php';

show_header('index');
?>
<style>
#progress p
{
	display: block;
	width: 240px;
	padding: 2px 5px;
	margin: 2px 0;
	border: 1px inset #446;
	border-radius: 5px;
}
#progress p.success
{
	background: #0c0 none 0 0 no-repeat;
}
#progress p.failed
{
	background: #c00 none 0 0 no-repeat;
}

</style>
<form id="upload" action="/upload.phpfile" method="POST" enctype="multipart/form-data">

<fieldset>
<legend>HTML File Upload</legend>

<input type="hidden" id="MAX_FILE_SIZE" name="MAX_FILE_SIZE" value="32000000" />

<div>
	<label for="fileselect">Files to upload:</label>
	<input type="file" id="fileselect" name="fileselect[]" multiple="multiple" />
	<div id="filedrag">or drop files here</div>

</div>

<div id="submitbutton">
	<button type="submit">Upload Files</button>
</div>
	
</fieldset>


</form>

<div id="progress"></div>

<div id="messages">

<p>Status Messages</p>

</div>
<script type="text/javascript">
/*
filedrag.js - HTML5 File Drag & Drop demonstration
Featured on SitePoint.com
Developed by Craig Buckler (@craigbuckler) of OptimalWorks.net
*/
(function() {

	// getElementById
	function $id(id) {
		return document.getElementById(id);
	}


	// output information
	function Output(msg) {
		var m = $id("messages");
		m.innerHTML = msg + m.innerHTML;
	}


	// file drag hover
	function FileDragHover(e) {
		e.stopPropagation();
		e.preventDefault();
		e.target.className = (e.type == "dragover" ? "hover" : "");
	}


	// file selection
	function FileSelectHandler(e) {

		// cancel event and hover styling
		FileDragHover(e);

		// fetch FileList object
		var files = e.target.files || e.dataTransfer.files;

		// process all File objects
		for (var i = 0, f; f = files[i]; i++) {
			ParseFile(f);
			UploadFile(f);
		}

	}


	// output file information
	function ParseFile(file) {

		Output(
			"<p>File information: <strong>" + file.name +
			"</strong> type: <strong>" + file.type +
			"</strong> size: <strong>" + file.size +
			"</strong> bytes</p>"
		);

		// display an image
		if (file.type.indexOf("image") == 0) {
			var reader = new FileReader();
			reader.onload = function(e) {
				Output(
					"<p><strong>" + file.name + ":</strong><br />"
				);
			}
			reader.readAsDataURL(file);
		}

		// display text
		if (file.type.indexOf("text") == 0) {
			var reader = new FileReader();
			reader.onload = function(e) {
				Output(
					"<p><strong>" + file.name + ":</strong></p><pre>" +
					e.target.result.replace(/</g, "&lt;").replace(/>/g, "&gt;") +
					"</pre>"
				);
			}
			reader.readAsText(file);
		}

	}


	// upload JPEG files
	function UploadFile(file) {

		var xhr = new XMLHttpRequest();
		if (xhr.upload && file.size <= $id("MAX_FILE_SIZE").value) {

			// create progress bar
			var o = $id("progress");
			var progress = o.appendChild(document.createElement("progress"));
			progress.max=100;
			progress.appendChild(document.createTextNode("upload " + file.name));


			// progress bar
			xhr.upload.addEventListener("progress", function(e) {
				var pc = parseInt((e.loaded / e.total) * 100);
				progress.value=pc;
			}, false);

			// file received/failed
			xhr.onreadystatechange = function(e) {
				if (xhr.readyState == 4) {
					progress.className = (xhr.status == 200 ? "success" : "failure");
				}
			};

			// start upload
			xhr.open("POST", "upload.php?type=1", true);
			xhr.send(file);

		}

	}


	// initialize
	function Init() {

		var fileselect = $id("fileselect"),
			filedrag = $id("filedrag"),
			submitbutton = $id("submitbutton");

		// file select
		fileselect.addEventListener("change", FileSelectHandler, false);

		// is XHR2 available?
		var xhr = new XMLHttpRequest();
		if (xhr.upload) {

			// file drop
			filedrag.addEventListener("dragover", FileDragHover, false);
			filedrag.addEventListener("dragleave", FileDragHover, false);
			filedrag.addEventListener("drop", FileSelectHandler, false);
			filedrag.style.display = "block";

			// remove submit button
			submitbutton.style.display = "none";
		}

	}

	// call initialization file
	if (window.File && window.FileList && window.FileReader) {
		Init();
	}


})();
</script>
<?
print_r($LoggedUser);

// Test the verification against getData()
$Verify = new ImageVerification('/home/ptpimg/public_html/raw/d6rkrm');
if(!$Verify->verify()) {
	// It's not valid, you say?
	// Delete image?
	// unlink($f);
} else {
	// 
	var_dump($Verify->EphemeralData);
}

show_footer();

$Image = new Image('e8219v');
print_r($Image);
//$Image->access(); //flush the contents to the screen
?>