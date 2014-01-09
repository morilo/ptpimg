<?
if (!$Register) die('404/Invalid modification of request');

// Step 3
if (!isset($_GET['auth'])) {
	header("Location: account.php?act=register");
	die();
} else {
	$DB->query("SELECT ID, Username FROM users WHERE AuthKey='%s' AND Enabled='0'", db_string($_GET['auth']));
	
	if ($DB->record_count()==0) {
		header("Location: index.php");
		die();
	}
	
	list($UserID, $Username)=$DB->next_record();
	$DB->query("UPDATE users SET AuthKey='', Enabled='1' WHERE ID='$UserID'");
	if ($DB->affected_rows()>0) {
		die("Account confirmed, you may now log in. <a href='http://ptpimg.me/account.php'>back to home page</a>");
	}
}
?>