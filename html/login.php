<?php
/* 
 * login.php
 * -Lee Hall Thu 06 Sep 2012 10:23:45 PM EDT
 * edits by Matthew Powell
 * Allow the user to login
 */
require_once('include/secure.php');
require_once('include/conf.php');

//Is there a user trying to log in?
if (array_key_exists('login', $_POST)){

    if (!array_key_exists('user_name', $_POST) || 
            !array_key_exists('password', $_POST) ){
        die("User or password not set. How did you get here?");
    }
	$UserName=strtolower($_POST['user_name']);
    // Get user info from database. Only retrieve users who have authenticated
    // their accounts.
    // If this gets slow, we can pull the quota after getting user_id so we
    // don't have to scan the whole files table, but this works for now
    $sql="SELECT user_id,password
            FROM users                                                                      
            WHERE user_name=$1;";
    $params=array($UserName);
    $results=pg_query_params($conn, $sql, $params);
    if (!$results || pg_num_rows($results) > 1){
        $msg="Unrecoverable database error.";
        trigger_error($msg);
        die($msg);
    }

    //Bail and reload the page if we didn't find a user
    $row=pg_fetch_array($results);      
    if (! $row){
        header("Location: $_SERVER[PHP_SELF]?msg=Unknown User");
        die("User not found.");
    }

    //Does the password match?
    if (md5($_POST['password']) == $row['password']){
        session_start();
        $_SESSION['user_name']=$UserName;
        $_SESSION['user_id']=$row['user_id'];
		
		header("Location: main.php");
		
		die("Done loading user.");
    } else {
        header("Location: $_SERVER[PHP_SELF]?msg=Bad Password");
        // This leaks information about whether or not a user exists on the
        // system. The ease of use is a net positive, however.
        // This problem can be alleviated with rate limiting on the login.
        die("Bad password.");
    }
}
if (array_key_exists('logout', $_GET)){

    // Make sure the session's started so we have access to the variables we
    // want to clear
    session_start();
    $_SESSION=array();
    session_destroy();

    header("Location: $_SERVER[PHP_SELF]");
    die("Reloading login page.");
}
?>
<HTML> 
<HEAD>
  <TITLE>Forager Login</TITLE>
</HEAD>
<BODY>

	<table>
		<tr><td><form action="<?php echo $_SERVER['PHP_SELF']; ?>" 
			method="post" id="login">
		<tr>
			<td>User Name:</td>
			<td><input name="user_name" type="text"></td>
		</tr>
		<tr>
			<td>Password:</td>
			<td><input name="password" type="password"></td>
		</tr>
		<tr>
			<td><input name="login" type="hidden"</td>
			<td><input value="Login" type="submit"></td>
		</tr>
		<tr>
			<td></td>
			<td>
<?php
if (array_key_exists('msg', $_GET)){
	echo "$_GET[msg]";
}   
?>
			</td>
		</tr>
	</table>
    </form>
</BODY>
</HTML>

