<?php
require_once (__DIR__ . '/vendor/autoload.php');

use ElasticEmail\Api\VerificationsApi;
use ElasticEmail\Api\EmailsApi;
use ElasticEmail\Configuration;
use GuzzleHttp\Client;

// Configure API key authorization for verifing eamil
$config = ElasticEmail\Configuration::getDefaultConfiguration()->setApiKey('X-ElasticEmail-ApiKey', 'Enter Your API key');
$apiInstance = new ElasticEmail\Api\VerificationsApi(new GuzzleHttp\Client(), $config);

$email = isset($_GET['email']) ? urldecode($_GET['email']) : '';
session_start();
$sender = 'espaekup@gmail.com';
$senderName = 'eSpeakUp';

// Concatenate CSS styles with the HTML template
$htmlWithStyles = file_get_contents('email_template.html');
try {
    $apiInstance = new ElasticEmail\Api\EmailsApi(new GuzzleHttp\Client(), $config);

    $email_message_data = new \ElasticEmail\Model\EmailMessageData([
        "recipients" => [
            new \ElasticEmail\Model\EmailRecipient([
                "email" => $email
            ])
        ],
        "content" => new \ElasticEmail\Model\EmailContent([
            "body" => [
                new \ElasticEmail\Model\BodyPart([
                    "content_type" => "HTML",
                    "content" => $htmlWithStyles
                ])
            ],
            "from" => $sender,
            "subject" => "🎉 Welcome to eSpeakUp.",
            "reply_to" => $sender,
        ]),
        "options" => new \ElasticEmail\Model\Options([
            "channel_name" => "eSpeakUp"
        ])
    ]);
    $apiInstance->emailsPost($email_message_data);
    // Email is sent
} catch (Exception $e) {
    echo 'Exception when calling EE API: ', $e->getMessage(), PHP_EOL;
}
// Check if email is set in session

unset($_SESSION['email']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- ===== Iconscout CSS ===== -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- ===== CSS ===== -->
    <link rel="shortcut icon" href="../assets/favicon.png" type="image/x-icon">

    <link rel="stylesheet" href="../css/EmailVerification.css">
    <title>Login & Registration Form</title>


</head>

<body>

    <div class="wrapper">
        <div class="container" id="container">
            <div class="forms">
                <!-- Login Form -->
                <div class="form login">
                    <span class="title">Verify Your Email</span>
                    <div class="login-signup">
                        <span class="text">For verification purposes, we've sent an email to
                        </span>
                    </div>
                    <form method="post" action="login.php">
                        <div class="input-field">
                            <input type="email" id="login-email" name="login-email" value="<?php echo $email; ?>"
                                readonly>
                            <i class="uil uil-envelope icon"></i>
                        </div>
                        <small id="login-email-error" class="error-message"></small>

                    </form>
                    <div class="login-signup">
                        <span class="text">You will receive an email with instaructions on sign up. If you don't see it,
                            be sure to check your junk or spam folder.
                        </span>
                    </div>
                    <div class="contact-us">
                        <span class="text">Need help? <a href="./contact.php">Contact us</a>
                        </span>
                    </div>
                </div>
            </div>

        </div>

</body>

</html>