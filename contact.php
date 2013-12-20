<?php
/********************************************************************************************************************
* This script is brought to you by Vasplus Programming Blog by whom all copyrights are reserved.
* Website: www.vasplus.info
* Email: info@vasplus.info
* Please, do not remove this information from the top of this page.
*********************************************************************************************************************/
session_start();
ob_start();
//ini_set('error_reporting', E_NONE);

if(isset($_POST["submitted"]) && $_POST["submitted"] == 1)
{
	//Read POST request params into global vars
	$to_email          = "ptpimg@gmail.com"; // Replace this email field with your email address or your company email address
	$from_fullname     = trim(strip_tags($_POST['vpbfullname']));
	$from_email        = trim(strip_tags($_POST['email']));
	$email_subject     = trim(strip_tags($_POST['subject']));
	$email_message     = nl2br(trim(strip_tags($_POST['message'])));
	$security_code     = trim(strip_tags($_POST['vpb_captcha_code']));
	
	//Set up the email headers
    $headers      = "From: $from_fullname <$from_email>\r\n";
    $headers   .= "Content-type: text/html; charset=iso-8859-1\r\n";
    $headers   .= "Message-ID: <".time().rand(1,1000)."@".$_SERVER['SERVER_NAME'].">". "\r\n";   
	
	if($from_fullname == "")
	{
		$submission_status = '<div class="vpb_info" align="left">Please enter your fullname in the required field to proceed. Thanks.</div>';
	}
	elseif($from_email == "")
	{
		$submission_status = '<div class="vpb_info" align="left">Please enter your email address in the required email field to proceed.</div>';
	}
	elseif(!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/", $from_email))
	{
		$submission_status = '<div class="vpb_info" align="left">Sorry, your email address is invalid. Please enter a valid email address to proceed. Thanks.</div>';
	}
	elseif($email_subject == "")
	{
		$submission_status = '<div class="vpb_info" align="left">Please enter the subject of your message in the required field to proceed.</div>';
	}
	elseif($email_message == "")
	{
		$submission_status = '<div class="vpb_info" align="left">Please enter your message in the required message field to proceed. Thanks.</div>';
	}
	elseif($security_code == "")
	{
		$submission_status = '<div class="vpb_info" align="left">Please enter the security code in its field to send us your message. Thanks.</div>';
	}
	elseif(!isset($_SESSION['vpb_captcha_code']))
	{
		$submission_status = '<div class="vpb_info" align="left">Sorry, there is no session created on this page at the moment, please refresh this page and try again.</div>';
	}
	else
	{
		if(empty($_SESSION['vpb_captcha_code']) || strcasecmp($_SESSION['vpb_captcha_code'], $_POST['vpb_captcha_code']) != 0)
		{
			//Note: the captcha code is compared case insensitively. If you want case sensitive match, update the check above to strcmp()
			$submission_status = '<div class="vpb_info" align="left">Sorry, the security code you provided was incorrect, try again.</div>';
		}
		else
		{
			$vasplus_mailer_delivers_greatly = @mail($to_email, $email_subject, $email_message, $headers);
					
			if ($vasplus_mailer_delivers_greatly) 
			 {
				//Displays the success message when email message is sent
				  $submission_status = "<div align='left' class='vpb_success'>Congrats $from_fullname, your email message has been sent successfully!<br>We will get back to you as soon as possible. Thanks.</div>";
			 } 
			 else 
			 {
				 //Displays an error message when email sending fails
				  $submission_status = "<div align='left' class='vpb_info'>Sorry, your email could not be sent at the moment. <br>Please try again or contact this website admin to report this error message if the problem persist. Thanks.</div>";
			 }
		}
	}
}
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>vasPLUS Programming Blog - Simple PHP Anti-Spam Captcha Contact Form Submission</title>



<!-- This is the CSS file for this tutorial -->
<link href="css/style.css" rel="stylesheet" type="text/css">

<!-- This function refreshes the security or captcha code when clicked on the refresh link -->
<script type="text/javascript">
function vpb_refresh_aptcha()
{
	return document.getElementById("vpb_captcha_code").value="",document.getElementById("vpb_captcha_code").focus(),document.images['captchaimg'].src = document.images['captchaimg'].src.substring(0,document.images['captchaimg'].src.lastIndexOf("?"))+"?rand="+Math.random()*1000;
}
</script>




</head>
<body>
<br clear="all">
<center>
<div style="font-family:Verdana, Geneva, sans-serif; font-size:24px;">Simple PHP Anti-Spam Captcha Contact Form Submission</div><br clear="all" />










<!-- Code Begins Here -->
<div class="vasplus_programming_blog_wrapper" align="left">

<center>
<div style="font-family:Verdana, Geneva, sans-serif; font-size:16px; float:left; width:120px;" align="left">Contact Form</div>
<div style="font-family:Verdana, Geneva, sans-serif; font-size:11px; float:left; padding-top:4px;" align="left">Please complete the form below to reach us...</div><br clear="all" /><br clear="all" />

<div style="width:430px; font-family:Verdana, Geneva, sans-serif; font-size:12px;padding:10px;" align="left">

<form method="post" action="index.php" enctype="multipart/form-data">

<div style="width:100px;padding-top:10px;float:left;font-family:Verdana, Geneva, sans-serif; font-size:11px;" align="left">Your Fullname:</div>
<div style="width:300px; float:left;" align="left"><input type="text" id="vpbfullname" name="vpbfullname" value="<?php echo strip_tags($_POST["vpbfullname"]); ?>" class="vpb_input_fields"></div><br clear="all"><br clear="all">


<div style="width:100px;padding-top:10px;float:left;font-family:Verdana, Geneva, sans-serif; font-size:11px;" align="left">Email Address:</div>
<div style="width:300px; float:left;" align="left"><input type="text" id="email" name="email" value="<?php echo strip_tags($_POST["email"]); ?>" class="vpb_input_fields"></div><br clear="all"><br clear="all">

<div style="width:100px;padding-top:10px;float:left;font-family:Verdana, Geneva, sans-serif; font-size:11px;" align="left">Email Subject:</div>
<div style="width:300px; float:left;" align="left"><input type="text" id="subject" name="subject" value="<?php echo strip_tags($_POST["subject"]); ?>" class="vpb_input_fields"></div><br clear="all"><br clear="all">


<div style="width:100px;padding-top:10px;float:left;font-family:Verdana, Geneva, sans-serif; font-size:11px;" align="left">Your Message:</div>
<div style="width:300px; float:left;" align="left"><textarea id="message" name="message" style="width:280px; height:80px; padding:10px;" class="vpb_input_fields"><?php echo strip_tags($_POST["message"]); ?></textarea></div><br clear="all"><br clear="all">


<div style="width:100px;padding-top:10px;float:left;font-family:Verdana, Geneva, sans-serif; font-size:11px;" align="left">Security Code:</div>
<div style="width:300px; float:left;" align="left">
<div class="vpb_captcha_wrappers"><input type="text" id="vpb_captcha_code" name="vpb_captcha_code" style="border-bottom: solid 2px #cbcbcb;" class="vpb_input_fields"></div></div><br clear="all">
<div style="width:100px; float:left;" align="left">&nbsp;</div>
<div style="width:300px; float:left;" align="left"><div class="vpb_captcha_wrapper"><img src="vasplusCaptcha.php?rand=<?php echo rand(); ?>" id='captchaimg' ></div><br clear="all">
<div style=" padding-top:5px;" align="left"><font style="font-family:Verdana, Geneva, sans-serif; font-size:11px;">Can't read the above security code? <a href="javascript:void(0);" style="text-decoration:none;" onClick="vpb_refresh_aptcha();">Refresh</a></font></div>

</div>
<br clear="all"><br clear="all">

<div style="width:420px; float:left;" align="left"><?php echo $submission_status; ?></div><!-- Display success or error messages -->
<div style="width:100px; float:left;" align="left">&nbsp;</div>
<div style="width:300px; float:left;" align="left">
<input type="hidden" id="submitted" name="submitted" value="1">
<input type="submit" class="vpb_general_button"  value="Submit">
</div>


</form>
</div>
<br clear="all">
</center>
</div>
<!-- Code Ends Here -->





<p style="margin-bottom:200px;">&nbsp;</p>
</center>
</body>
</html>