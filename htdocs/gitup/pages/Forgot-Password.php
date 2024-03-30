<?php
session_start();
require_once (__DIR__ . '/vendor/autoload.php');

use ElasticEmail\Api\VerificationsApi;
use ElasticEmail\Api\EmailsApi;
use ElasticEmail\Configuration;
use GuzzleHttp\Client;

// Configure API key authorization for verifing eamil
$config = ElasticEmail\Configuration::getDefaultConfiguration()->setApiKey('X-ElasticEmail-ApiKey', 'Enter Your API key');
$apiInstance = new ElasticEmail\Api\VerificationsApi(new GuzzleHttp\Client(), $config);

$server = "localhost";
$username = "root";
$password = "";
$database = "llb";

$otp = random_int(100000, 999999);

$con = mysqli_connect($server, $username, $password, $database);
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!$con) {
        $_SESSION['error'] = ("Connection to this database failed due to" . mysqli_connect_error());
        echo "Connection to this database failed due to";
    } else {
        if (isset($_POST['login-email'])) {
            $email = isset($_POST['login-email']) ? mysqli_real_escape_string($con, $_POST['login-email']) : '';

            if (!empty($email)) {
                // Prepare and execute SQL statement
                $stmt = $con->prepare("SELECT * FROM users_data WHERE email = ?");
                $stmt->bind_param("s", $email);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows == 1) {
                    $row = $result->fetch_assoc();
                    $username = $row['username'];

                    $email_template = file_get_contents("forgot-password_template.html");

                    // Replace placeholders in the email template with actual data
                    $email_template = str_replace("[Your Username]", $username, $email_template);
                    $email_template = str_replace("[Your Code]", $otp, $email_template);
                    $sender = 'espaekup@gmail.com';
                    $senderName = 'eSpeakUp';
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
                                        "content" => $email_template
                                    ])
                                ],
                                "from" => $sender,
                                "subject" => "Password and Username Recovery - eSpeakUp.",
                                "reply_to" => $sender,
                            ]),
                            "options" => new \ElasticEmail\Model\Options([
                                "channel_name" => "eSpeakUp"
                            ])
                        ]);
                        $apiInstance->emailsPost($email_message_data);
                        $_SESSION['userotp'] = [
                            'otp' => $otp,
                            'timestamp' => time()  // Current timestamp
                        ];
                        $_SESSION['useremail'] = $email;
                        header("Location: otp-Verification.php");
                        exit();
                        // Email is sent
                    } catch (Exception $e) {
                        echo 'Exception when calling EE API: ', $e->getMessage(), PHP_EOL;
                    }
                } else {
                    // Email not found
                    $_SESSION['error'] = "Email is not registered!! Please verfiy.";
                }
            } else {
                // Email is empty
                $_SESSION['error'] = "Email field is empty";
            }
        }

    }
    // unset($_SESSION['email']);
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <!-- ===== Iconscout CSS ===== -->
    <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="shortcut icon" href="../assets/favicon.png" type="image/x-icon">
    <!-- ===== CSS ===== -->
    <link rel="stylesheet" href="../css/Forgot-Password.css">
    <script>

        document.addEventListener("DOMContentLoaded", function () {


            // JavaScript function for real-time email validation

            function validateLoginEmail() {
                var email = document.getElementById("login-email").value;
                var errorContainer = document.getElementById("login-email-error");

                if (email.length === 0) {
                    errorContainer.textContent = "Email is required";
                } else if (!/^\S+@\S+\.\S+$/.test(email)) {
                    errorContainer.textContent = "Invalid email format";
                } else {
                    errorContainer.textContent = ""; // Clear the error message
                }
                // Add event listeners for real-time validation

            }
            document.getElementById("login-email").addEventListener("input", validateLoginEmail);
        });
    </script>
    <title>Login & Registration Form</title>


</head>

<body>

    <div class="wrapper">
        <div class="container" id="container">
            <div class="forms">
                <!-- Login Form -->
                <div class="form login">
                    <span class="title">Forgot Password</span>
                    <div class="login-signup">
                        <span class="text">Please enter your registered email. We will send a password to your email.
                        </span>
                    </div>
                    <form method="post" action="Forgot-Password.php">
                        <div class="input-field">
                            <input type="email" id="login-email" name="login-email"
                                placeholder="Enter your email address">
                            <i class="uil uil-envelope icon"></i>
                        </div>
                        <small id="login-email-error" class="error-message" style=" color: red;">
                            <div class="register">
                                <?php
                                // Check if there's an error message in the session
                                if (isset($_SESSION['error'])) {
                                    echo "<p>{$_SESSION['error']}</p>";
                                    unset($_SESSION['error']); // Remove the error message from the session
                                }
                                ?>
                            </div>
                        </small>

                        <div class="input-field button">
                            <input type="submit" value="Change Password" name="signup-submit">
                        </div>
                        <i class="fas fa-arrow-left" onclick="location.href='./login.php'"
                            style="color: #4070f4; position: absolute; top: 0; left: 0; margin-top: 20px; margin-left: 20px; cursor: pointer;"></i>
                    </form>
                    <div class="login-signup">
                        <span class="text">You will receive an email shortly with password reset link. Please check your
                            inbox (and spam/junk folder, if necessary) for the email.</span>
                    </div>
                    <div class="contact-us">
                        <span class="text">Need help? <a href="./contact.php" target="_blank">Contact us</a>
                        </span>
                    </div>
                </div>
            </div>

        </div>

</body>

</html>