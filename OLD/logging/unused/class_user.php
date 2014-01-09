<?
class User {
	// User related functions
	
	public function getClassName($Class, $Color=true) {
		if ($Color) { // Color
			switch ($Class) {
				case 1:
					return 'User';
				break;
				case 2:
					return '<font color=#132600>Moderator</font>';
				break;
				case 3:
					return '<font color=#480000>Administrator</font>';
				break;
			}
		} else { // No color
			switch ($Class) {
				case 1:
					return 'User';
				break;
				case 2:
					return 'Moderator';
				break;
				case 3:
					return 'Administrator';
				break;
			}		
		}
	}
	
	public function getEnabledState($Enabled) {
		switch ($Enabled) {
			case 0:
				return 'Unconfirmed';
			break;
			case 1:
				return 'Enabled';
			break;
			case 2:
				return 'Disabled';
			break;
		}
	}
	
	// Create account and return UserID
	public function makeAccount($Username, $Password, $Email, $SendEmail=true, $Enabled=1, $Quiet=false) {
		global $DB;
		if ($this->accountNameInUse($Username) || $this->accountEmailInUse($Email))
			return -3;
		
		// Create the account
		$Secret = make_secret();
		$TS = md5(time().$Secret.time());
		$DB->query("INSERT INTO users (Username, Password, Email, Enabled, Secret, AuthKey, JoinDate) VALUES('%s', '%s', '%s', %d, '%s', '%s')", db_string($Username), db_string(make_hash($Password, $Secret)), db_string($Email), db_string($Enabled), db_string($Secret), db_string($TS), sqltime());
		$UserID=$DB->inserted_id();
		
		if ($SendEmail==true) {
			$EmailTemplate=file_get_contents(SERVER_ROOT.'/res/confirm_account.tpl');
			$EmailTemplate=str_replace('%Username%', $Username, $EmailTemplate);
			$EmailTemplate=str_replace('%AuthKey%', $TS, $EmailTemplate);
			
			$Subject = 'Redstone Mods Account Confirmation';
			$Headers = 'From: "PTPIMG Mailer" <noreply+ptpimg@nervex.net>' . PHP_EOL .
					   'X-Mailer: PHP/' . phpversion() . PHP_EOL;
			if (mail($Email, $Subject, $EmailTemplate, $Headers)) {
			  if (!$Quiet) echo "Account created! Please check your email to confirm your account. You will not be able to login until you have confirmed your email address.";
			}
			else {
			  if (!$Quiet) die("Unknown error, contact admins for additional help."); else die();
			}
		}
		if (is_number($UserID))
			return $UserID;
		else
			return -1;
	}
	
	public function accountNameInUse($Username) {
		global $DB;
		$DB->query("SELECT ID FROM users WHERE Username='%s'", db_string($Username));
			
		if ($DB->record_count()>0)
			return true;
		else
			return false;
	}

	public function accountEmailInUse($Email) {
		global $DB;
		$DB->query("SELECT ID FROM users WHERE Email='%s'", db_string($Email));
			
		if ($DB->record_count()>0)
			return true;
		else
			return false;
	}
	
	function getUsername($UserID, $Flush=false) {
		global $Cache, $DB;
		if ($Flush || !($Username=$Cache->get_value('rcm_user_'.$UserID))) {
			$DB->query("SELECT Username FROM users WHERE ID=%d", $UserID);
			list($Username)=$DB->next_record();
			$Cache->cache_value('ptpimg_user_'.$UserID, $Username, 0);
		}
		return $Username;
	}
	
	function doLogin($UserID, $KeepLogged=false) {
		global $DB, $Cache, $Enc, $Browser, $OperatingSystem;
		$SessionID = make_secret();		
		$Cookie = $Enc->encrypt($Enc->encrypt($SessionID.'|~|'.$UserID));
		
		if ($KeepLogged) {
			setcookie('session', $Cookie, 0, '/', '', false);
		} else {
			setcookie('session', $Cookie, time()+60*60*24*365, '/', '', false);
		}

		
		$DB->query("INSERT INTO sessions
                                                        (UserID, SessionID, KeepLogged, Browser, OperatingSystem, IP, LastUpdate)
                                                        VALUES ('$UserID', '".db_string($SessionID)."', '$KeepLogged', '$Browser','$OperatingSystem', '".db_string($_SERVER['REMOTE_ADDR'])."', '".sqltime()."')");

        $Cache->begin_transaction('ptpimg_sessions_'.$UserID);
        $Cache->insert_front($SessionID,array(
				'SessionID'=>$SessionID,
				'Browser'=>$Browser,
				'OperatingSystem'=>$OperatingSystem,
                'IP'=>$_SERVER['REMOTE_ADDR'],
                'LastUpdate'=>sqltime()
		));
		$Cache->commit_transaction(0);

		$DB->query("UPDATE users
				SET
				LastLogin='".sqltime()."',
				LastAccess='".sqltime()."'
				WHERE ID='".db_string($UserID)."'");

	
		return true;
	}
}
?>