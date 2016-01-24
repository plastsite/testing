<?php
// class to send email from a contact form, from: http://coursesweb.net/
class sendEMail {
  public $re;       // store message returned by this class

  public function __construct($to) {
    // Check the session that limits to can be send only an e-mail in 5 minutes, also useful anti-refresh
    if(isset($_SESSION['limit_contact']) && $_SESSION['limit_contact']>(time()-300)) {
      $this->re = 'Error: You can send only a message at 5 minutes.<br />Wait '.($_SESSION['limit_contact']-time()+300).' seconds';
      exit;
    }

    // Check the anti-spamm code
    if(isset($_POST['anti_spam']) && isset($_POST['anti_spam1']) && $_POST['anti_spam']==$_POST['anti_spam1']) {
      // Check if all necessary data are received by post
      if(isset($_POST['nume']) && isset($_POST['email']) && isset($_POST['subject']) && isset($_POST['message'])) {
        // removes external whitespace and tags
        $_POST = array_map("trim", $_POST);
        $_POST = array_map("strip_tags", $_POST);

        // sets email data
        $cmail['to'] = $to;
        $cmail['nume'] = $_POST['nume'];
        $cmail['from'] = $_POST['email'];
        $cmail['subject'] = $_POST['subject'];
        $cmail['body'] = 'E-mail from Contact form, sent by: '. $cmail['nume'] ."\n His /Her e-mail address: ". $cmail['from']. "\n IP: ". $_SERVER['REMOTE_ADDR']. "\n\n"
          .'message: '. $_POST['message'];

        // calls the method with data to send the email
        $this->re = $this->sendMail($cmail);
      }
      else $this->re = 'Error: not all form fields.';
    }
    else $this->re = 'Error: Incorrect verification code.';
  }

  // method that receives data for mail, and accesses the function to send the email
  protected function sendMail($cmail) {
    // if GMAIL=1, uses the gmail_sender() function (of the phpmailer class) to send the email via GMail
    // Otherwise, uses the PHP mail() function
    if(GMAIL === 1) {
      // Calls the gmail_sender() function, and store its response
      $send = $this->gmailSender($cmail);
    }
    else {
      $cmail['from'] = 'From: '. $cmail['from'];
      // uses the PHP mail() function, If sending successfully, set $send='sent', otherwise defines 'Error'
      if(mail($cmail['to'], $cmail['subject'], $cmail['body'], $cmail['from']))  $send = 'sent';
      else $send = 'Error: The server could not send the email.';
    }

    // If the email is sent, display the confirmation. Otherwise returns the error
    if($send == 'sent') {
      // Set a session used to block the re-sending to an eventual refresh or in less than 5 minutes
      $_SESSION['limit_contact'] = time();

      return '<p><b>Your message was successfully sent, will be answered as soon as possible.</b></p>
      <p><b><i>Thank you for your interest.</i></b></p>
              <p>If you wish, visit: <a href="http://coursesweb.net/">CoursesWeb.net</a></p>';
    }
    else return $send;
  }

  // send email via GMail SMTP, with phpmailer class
  protected function gmailSender($cmail) {
    date_default_timezone_set('America/Toronto');

    include_once('class.phpmailer.php');
    //include("class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded

    $mail             = new PHPMailer();

    $body             = nl2br($cmail['body']);
    $body             = str_replace('\\','',$body);

    $mail->IsSMTP(); // telling the class to use SMTP
    $mail->Host       = "smtp.gmail.com"; // SMTP server
    // $mail->SMTPDebug  = 1;                     // enables SMTP debug information (for testing)
                                               // 2 = errors and messages
                                               // 1 = messages only
    $mail->SMTPAuth   = true;                  // enable SMTP authentication
    $mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
    $mail->Host       = "smtp.gmail.com";      // sets GMAIL as the SMTP server
    $mail->Port       = 465;                   // set the SMTP port for the GMAIL server
    $mail->Username   = GMAIL_USER;  // GMAIL username
    $mail->Password   = GMAIL_PASS;            // GMAIL password

    $mail->SetFrom($cmail['from'], $cmail['nume']);

    $mail->AddReplyTo($cmail['from'], $cmail['nume']);

    $mail->Subject    = $cmail['subject'];
    $mail->MsgHTML($body);

    $mail->AddAddress($cmail['to'], "Admin");

    if(!$mail->Send()) return 'Error: '. $mail->ErrorInfo;
    else return 'sent';
  }
} 