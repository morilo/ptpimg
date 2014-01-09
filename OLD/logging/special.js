function CrossXHR() {
   var crossxhr = false;
   if(window.XMLHttpRequest) {
      crossxhr = new XMLHttpRequest();
   } 
   else if(window.ActiveXObject) {
      try {
         crossxhr = new ActiveXObject('Msxml2.XMLHTTP');
      } 
      catch(e) {
         try {
            crossxhr = new ActiveXObject('Microsoft.XMLHTTP');
         } 
         catch(e) {
            crossxhr = false;
         }
      }
   }
   return crossxhr;
}

function implode (glue, pieces) {
    // http://kevin.vanzonneveld.net
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Waldo Malqui Silva
    // +   improved by: Itsacon (http://www.itsacon.net/)
    // +   bugfixed by: Brett Zamir (http://brett-zamir.me)
    // *     example 1: implode(' ', ['Kevin', 'van', 'Zonneveld']);
    // *     returns 1: 'Kevin van Zonneveld'
    // *     example 2: implode(' ', {first:'Kevin', last: 'van Zonneveld'});
    // *     returns 2: 'Kevin van Zonneveld'

    var i = '', retVal='', tGlue='';
    if (arguments.length === 1) {
        pieces = glue;
        glue = '';
    }
    if (typeof(pieces) === 'object') {
        if (pieces instanceof Array) {
            return pieces.join(glue);
        }
        else {
            for (i in pieces) {
                retVal += tGlue + pieces[i];
                tGlue = glue;
            }
            return retVal;
        }
    }
    else {
        return pieces;
    }
}

function evalMasterURLUpload(element) {
	var req = CrossXHR();
	 req.onreadystatechange = function() {
            if (req.readyState == 4) { // complete
				element['v2_url'].value = '';
				element['v2_url'].enabled = false;
			 rsp = eval(req.responseText);
			 codes='';
			 exts='';
			 for (i=0;i<rsp.length;i++) {
				if (i==0) {
					codes=rsp[i].code;
					exts=rsp[i].ext;
				} else {
					codes=codes+'|'+rsp[i].code;
					exts=exts+'|'+rsp[i].ext;
				}
			}
			 iBox.showURL('index.php?type=prop_display&code='+codes+'&ext='+exts);
			 }
	      };
	element['v2_url'].enabled = false;
	req.open('POST', "index.php?type=uploadv2&key=QT5LGz7ktGFVZpfFArVHCpEvDcC3qrUZrf0kP&uid=999999&url=c_h_e_c_k_p_o_s_t", true);
	var urls = "urls="+element['v2_url'].value;
	req.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
	req.setRequestHeader("Content-length", urls.length);
	req.setRequestHeader("Connection", "close");
	req.send(urls);
}
