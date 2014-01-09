    <script src="fileuploader.js" type="text/javascript"></script>
		<link href="fileuploader.css" rel="stylesheet" type="text/css">	
	<div id="zxc">		
	</div>
	
    <script>        
        function createUploader(){            
            var uploader = new qq.FileUploader({
                element: document.getElementById('zxc'),
                action: '/index.php',
				params: {
					type: 'uploadv7',
					key: 'QT5LGz7ktGFVZpfFArVHCpEvDcC3qrUZrf0kP'
				},
				allowedExtensions: ['jpg','jpeg','png','gif'],
				onComplete: function(id, fileName, responseJSON) {
					alert(id);
					alert(fileName);
					alert(responseJSON);
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