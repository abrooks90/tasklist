<?php
// resume the session
session_start();

// Unset all of the session variables.
$_SESSION = array();

// Finally, destroy the session.
session_destroy();

// If it's desired to kill the session, also delete the session cookie.
// Note: This will destroy the session, and not just the session data!
if (isset($_COOKIE[session_name("PHPSESSID")])) {
    setcookie(session_name("PHPSESSID "), '', time()-42000, '/');
}

header("location:home.php");

?>
