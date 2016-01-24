<?php
// Contact Form - http://coursesweb.net/php-mysql/
if(!isset($_SESSION)) session_start();        // starts session, if not already started
if(!headers_sent()) header('Content-type: text/html; charset=utf-8');            // sets header for UITF-8 encoding

/** HERE ADD YOUR DATA **/

$to = 'your_email@domain.mail';			// Receiver e-mail address (to which the email will be send)

// If you want to use the SMTP server from GMail, set the value 1 at GMAIL constant
// Add your GMail address at the GMAIL_USER, and add the password for this e-mail at GMAIL_PASS
// If you want to use the local mail server, let GMAIL to 0
define('GMAIL', 0);
define('GMAIL_USER', 'your_account@gmail.com');
define('GMAIL_PASS', 'gmail_password');

// include the class that sends the email
include('sendmail/class.sendemail.php');

// create an object of sendEMail class that sends the email
$obEMail = new sendEMail($to);
echo $obEMail->re;           // output the resulted message