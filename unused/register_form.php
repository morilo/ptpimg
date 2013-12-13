<? if (!$Register) die('404/Invalid modification of request'); ?>
<form action="account.php?act=register&step=2" method="post" name="regform" onsubmit="document.regform._btn_doSubmit.disabled=true;return true;">
	Username: <br />
	<input 	type="text"
			name="username"
			value="<?=($FailedValidate)?$Form['Username']:''?>" />
			<!-- on blur add with page js /-->
			<?=($Failed['Username'])?$FailedReason['Username']:''?>
			<br />
	Password: <br />
	<input 	type="password"
			name="password"
			value="" />
			<!-- on blur add with page js /-->
			<?=($Failed['Password'])?$FailedReason['Password']:''?>
			<br />
	Email: <br />
	<input 	type="text"
			name="email"
			value="<?=($FailedValidate)?$Form['Email']:''?>" />
			<?=($Failed['Email'])?$FailedReason['Email']:''?>
			<!-- on blur add with page js /-->
			<br />
	<input	type="submit"
			name="_btn_doSubmit"
			value="Register!" />

</form>