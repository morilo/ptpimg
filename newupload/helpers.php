<?
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

function scaledDimensions($res, $maxSide) {
	list($w, $h) = explode("x", $res);
	if ($w > $h) {
		$factor = $w / $maxSide;
	} else {
		$factor = $h / $maxSide;
	}
	return array($w / $factor, $h / $factor);
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

function randFN($Length=6) {
	$o='';
	for ($i=0; $i<$Length; $i++) {
		$d=rand(1,30)%2;
		$o.=$d ? chr(rand(97,122)) : chr(rand(48,57));
	}
	return $o;
}
?>