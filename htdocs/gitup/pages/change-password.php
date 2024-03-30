<?php
session_start();
require_once (__DIR__ . '/vendor/autoload.php');

use ElasticEmail\Api\VerificationsApi;
use ElasticEmail\Api\EmailsApi;
use ElasticEmail\Configuration;
use GuzzleHttp\Client;

$config = ElasticEmail\Configuration::getDefaultConfiguration()->setApiKey('X-ElasticEmail-ApiKey', 'Enter Your API key');
$apiInstance = new ElasticEmail\Api\VerificationsApi(new GuzzleHttp\Client(), $config);

$server = "localhost";
$username = "root";
$password = "";
$database = "llb";


$email_template = file_get_contents("password-changed_template.html");
$sender = 'espaekup@gmail.com';
$senderName = 'eSpeakUp';
$email = $_SESSION['useremail'];
$con = mysqli_connect($server, $username, $password, $database);
if (isset($_SESSION['useremail'])) {
    if (!$con) {
        $_SESSION['error'] = ("Connection to this database failed due to" . mysqli_connect_error());
    } else {
        if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup-submit'])) {
            $password = mysqli_real_escape_string($con, $_POST['password']);
            $confirmpassword = mysqli_real_escape_string($con, $_POST['confirmPassword']);

            $stmt = $con->prepare("SELECT * FROM users_data WHERE email = ?");
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows == 1) {
                $row = mysqli_fetch_assoc($result);
                $hashedPassword = password_hash($confirmpassword, PASSWORD_DEFAULT);

                $updatePasswordQuery = "UPDATE users_data SET password = '$hashedPassword' WHERE email = '$email'";
                $updateResult = mysqli_query($con, $updatePasswordQuery);
                if ($updateResult) {
                    $_SESSION['error'] = 'Password has been successfully changed';
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
                                "subject" => "🥳Password has been successfully changed - eSpeakUp.",
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
                        unset($_SESSION['userotp']);
                        header("Location: login.php");
                        exit();
                        // Email is sent
                    } catch (Exception $e) {
                        $_SESSION['error'] = "Exception when calling EE API: . $e->getMessage(), PHP_EOL";
                    }
                    header("Location: login.php");
                    exit(); // Make sure to exit after redirection
                } else {
                    $_SESSION['error'] = "Failed to update password";
                }
            } else {
                $_SESSION['error'] = "Email does not exist";
            }
        }
        $con->close();
    }
} else {
    header("Location: Forgot-Password.php");
}
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
    <link rel="shortcut icon" href="../assets/favicon.png" type="image/x-icon">
    <!-- ===== CSS ===== -->
    <link rel="stylesheet" href="../css/change-password.css">

    <script>

        document.addEventListener("DOMContentLoaded", function () {
            // JavaScript function for real-time email validation
            function validatePassword() {
                var password = document.getElementById("password").value;
                var errorContainer = document.getElementById("password-error");

                if (
                    password.length < 8 ||
                    password.length > 20 ||
                    !/\d/.test(password)
                ) {
                    errorContainer.textContent =
                        "Password must be 8-20 characters and include at least one number";
                } else {
                    errorContainer.textContent = ""; // Clear the error message
                }
            }

            // JavaScript function for real-time confirm password validation
            function validateConfirmPassword() {
                var confirmPassword = document.getElementById("confirmPassword").value;
                var password = document.getElementById("password").value;
                var errorContainer = document.getElementById("confirmPassword-error");

                if (password !== confirmPassword) {
                    errorContainer.textContent = "Passwords do not match";
                    console.log("Passwords do not match");
                } else {
                    errorContainer.textContent = "";
                    console.log("Passwords match");

                }
            }
            document.getElementById("password").addEventListener("input", validatePassword);
            document.getElementById("confirmPassword").addEventListener("input", validateConfirmPassword);
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
                    <span class="title">Change Password</span>
                    <div class="login-signup">
                        <span class="text">Please enter your new Password.
                        </span>
                    </div>
                    <form method="post" action="change-password.php">
                        <div class="input-field">
                            <input type="password" id="password" name="password" placeholder="Create a password"
                                required>
                            <i class="uil uil-lock icon"></i>
                            <i class="uil uil-eye-slash showHidePw" id="togglepassword"
                                onclick="togglePasswordVisibility('password','togglepassword')"></i>
                        </div>
                        <small id="password-error" class="error-message"></small>
                        <!-- Error message for password validation -->
                        <div class="input-field">
                            <input type="password" id="confirmPassword" name="confirmPassword"
                                placeholder="Confirm password" required>
                            <i class="uil uil-lock icon"></i>
                            <i class="uil uil-eye-slash showHidePw" id="toggleconfirmpassword"
                                onclick="togglePasswordVisibility('confirmPassword', 'toggleconfirmpassword')"></i>
                        </div>
                        <small id="confirmPassword-error" class="error-message"></small>
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

                    </form>
                    <div class="contact-us">
                        <span class="text">Need help? <a href="mailto:espaekup@gmail.com" target="_blank">Contact us</a>
                        </span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script>
        function togglePasswordVisibility(passwordId, iconId) {
            var passwordInput = document.getElementById(passwordId);
            var icon = document.getElementById(iconId);
            if (passwordInput.type === "password") {
                passwordInput.type = "text";
                icon.className = "uil uil-eye showHidePw";
                console.log("Changing type to text");
            } else {
                passwordInput.type = "password";
                icon.className = "uil uil-eye-slash showHidePw";
                console.log("Changing type to password");
            }
        }</script>
</body>

</html>