<?php
/* @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@  ENTER RECPATCHA FOLDER INFO  @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */
/*                                                            also change line 15 in contact.php                                                        */
/* Require ReCaptcha class    => FILL IN WITH CORRECT INFO                                                                                              */           
require('###########/assets/recaptcha/recaptcha-master/src/autoload.php');
/*     Example =>   require('/home/finetechnology/fineonline.com/assets/recaptcha/recaptcha-master/src/autoload.php');                                  */
/*                                                                                                                                                      */
/* @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@  ENTER RECPATCHA FOLDER INFO  @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */

// Configure email address in the FROM field
$from = 'efine@fineonline.com';

// Email address that receives email with output of the form
$sendTo = 'efine@fineonline.com';

// Email Subject
$subject = "New message from edithfine.com";

// Form field variable names => Text to appear in the email
$fields = array('name' => 'Name', 'email' => 'Email', 'phone' => 'Phone', 'message' => 'Message');

// Message to display when all OK
$okMessage = 'Message sent!; <br> Thanks! I will contact you soon.';

// Message to display if not OK
$errorMessage = 'There was an error while submitting the form. <br> Please try again later';

/* @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@  SECRET_RECPATCHA_SITEKEY  @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */
/*                                                                                                                                                      */
/*           RECAPTCHA-----ENTER_SECRET_RECPATCHA_SITEKEY-----from https://www.google.com/recaptcha/admin                                               */
/*           $recaptchaSecret = 'ENTER_SECRET_RECPATCHA_SITEKEY';                                                                                       */
$recaptchaSecret = '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe';
/*                                                                                                                                                      */
/* @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@  SECRET_RECPATCHA_SITEKEY  @@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@@ */

// DEBUG -- To turn error reporting off => error_reporting(0);
error_reporting(E_ALL & ~E_NOTICE);

try {
    if (!empty($_POST)) {

        // ReCaptcha validation: Trhow error if something wrong
        // Code stops executing and goes to catch() block
        if (!isset($_POST['g-recaptcha-response'])) {
            throw new \Exception('ReCaptcha is not set.');
        }

        // Secret Key should have been enetered above        
        $recaptcha = new \ReCaptcha\ReCaptcha($recaptchaSecret, new \ReCaptcha\RequestMethod\CurlPost());
        
        // Validate ReCaptcha with user's IP address        
        $response = $recaptcha->verify($_POST['g-recaptcha-response'], $_SERVER['REMOTE_ADDR']);

        if (!$response->isSuccess()) {
            throw new \Exception('ReCaptcha was not validated.');
        }
        
        // All OK: Compose Message        
        $emailText = "Contact Form Message (edithfine.com);\n=================================================\n";

        foreach ($_POST as $key => $value) {
            // If $fields array field exists it is included in email
            if (isset($fields[$key])) {
                $emailText .= "$fields[$key]: $value\n";
            }
        }
    
        // Neccessary headers for the email
        $headers = array('Content-Type: text/plain; charset="UTF-8";',
            'From: ' . $from,
            'Reply-To: ' . $from,
            'Return-Path: ' . $from,
        );
        
        // Send email
        mail($sendTo, $subject, $emailText, implode("\n", $headers));

        $responseArray = array('type' => 'success', 'message' => $okMessage);
    }
    // Catch errors
} catch (\Exception $e) {
    $responseArray = array('type' => 'danger', 'message' => $e->getMessage());
}

if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    $encoded = json_encode($responseArray);

    header('Content-Type: application/json');

    echo $encoded;
} else {
    echo $responseArray['message'];
}
