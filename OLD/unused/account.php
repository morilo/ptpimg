<?
define('SQLHOST','localhost'); //The MySQL host ip/fqdn
define('SQLLOGIN','ptpimg');//The MySQL login
define('SQLPASS','mri34mni'); //The MySQL password
define('SQLDB','ptpimg'); //The MySQL database to use
define('SQLPORT','3306'); //The MySQL port to connect on
define('SQLSOCK','/var/run/mysqld/mysqld.sock');
require("sql.class.php");
require("cache.php");
require("class_ua.php");
require("class_user.php");
require("class_encrypt.php");

$DB=NEW DB_MYSQL;
$Cache=NEW CACHE;
$User=NEW USER;
$UA = new USER_AGENT;
$Enc = new CRYPT;
require("misc.class.php");
session_start();

if (!isset($_GET['act']) || empty($_GET['act'])) $_GET['act']="def_action";

switch($_GET['act']) {
	case 'login':
	case 'logout':
		//-------------------
		// LOGIN/LOGOUT
		//-------------------
		if (isset($_GET['act']) && $_GET['act']=="logout") logout();
		// Process the input
		if (!empty($_GET['tkl'])) {
			if (isset($_POST['username']) && preg_match('/^[a-z0-9_?]{1,20}$/iD',$_POST['username']) &&
		// strlen($_POST['password'])>6 && 
		strlen($_POST['password'])<40) {
				$DB->query("SELECT
					ID,
					Password,
					Secret,
					Enabled
					FROM users WHERE Username='".db_string($_POST['username'])."'
					AND Username<>''");
				list($UserID,$PassHash,$Secret,$Enabled)=$DB->next_record();
				if ($UserID && $PassHash==make_hash($_POST['password'],$Secret) && $Enabled == 1) {
					$User->doLogin($UserID);
					if (empty($_POST['ref_page'])) {
						header("Location: index.php");
					} else {
						$URL = base64_decode($_POST['ref_page']);
						if(preg_match('/^\/[a-zA-Z0-9]+\.php/i',$URL)) {
							header("Location: $URL");
						} else {
							header("Location: index.php");
						}
					}
					exit();
				} else {
					echo "<font color='red'><strong>BAD USERNAME/PASSWORD, try again</strong></font>";
				}
			} else {
				die("Invalid entry, <a href='account.php?act=login'>try again</a>");
			}
		}
?>
<form action="account.php?act=login&tkl=<?=time()?>" method="post" name="loginform" onsubmit="document.loginform._btn_doSubmit.disabled=true;return true;">
	Username: <br />
	<input 	type="text"
			name="username"
			value="" />
			<!-- on blur add with page js /-->
			<br />
	Password: <br />
	<input 	type="password"
			name="password"
			value="" />
			<!-- on blur add with page js /-->
			<br />
	<input	type="submit"
			name="_btn_doSubmit"
			value="Login!" />

</form>
<?
	
	break;
	
	case 'register':
		$Register=true;
		if (isset($_GET['step'])) {
			// Step 1 = register form
			// Step 2 = handle register
			// Step 3 = handle confirm
			switch ($_GET['step']) {
				case '1':
				case '2':
				case '3':
					require sprintf('register_step%d.php',$_GET['step']);
					break;
				default:
					die('404');
				}
		} else {
			require 'register_step1.php';
		}
	break;
	default:
?>
	No action!
<?
}
?>