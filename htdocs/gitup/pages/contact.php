<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

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



// Function to validate email format
function validateEmail($email)
{
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to validate phone number format
function validatePhone($phone)
{
    // Remove non-numeric characters
    $phone = preg_replace('/\D/', '', $phone);
    // Check if phone number has 10 digits
    return strlen($phone) === 10 && is_numeric($phone);
}

unset($_SESSION["error"]);
unset($_SESSION["success"]);
// Establish database connection
$con = mysqli_connect($server, $username, $password, $database);
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    if (!$con) {
        $_SESSION['error'] = ("Connection to this database failed due to" . mysqli_connect_error());
    } else {
        if (isset($_POST['firstName'], $_POST['email'], $_POST['lastName'], $_POST['phone'], $_POST['message'])) {
            $firstName = mysqli_real_escape_string($con, $_POST['firstName']);
            $lastName = mysqli_real_escape_string($con, $_POST['lastName']);
            $email = mysqli_real_escape_string($con, $_POST['email']);
            $phone = mysqli_real_escape_string($con, $_POST['phone']);
            $message = mysqli_real_escape_string($con, $_POST['message']);

            if (!validateEmail($email)) {
                $_SESSION['error'] = "Invalid email format";
            } elseif (!validatePhone($phone)) {
                $_SESSION['error'] = "Invalid phone number format";
            } else {
                $sender = 'espaekup@gmail.com';
                $senderName = 'USER MESSAGE';
                $to = 'espaekup@gmail.com';

                $name = $firstName . " " . $lastName;
                $email_template = "<h4> Name: $name <br> 
                                    Email: $email<br>
                                    Phone no.: $phone<br></h4>
                                    Message: $message<br>";

                // Verify email using ElasticEmail API
                try {
                    $result = $apiInstance->verificationsByEmailPost($email);
                    if ($result['predicted_status'] === 'LowRisk') {

                        try {
                            $apiInstance = new ElasticEmail\Api\EmailsApi(new GuzzleHttp\Client(), $config);

                            $email_message_data = new \ElasticEmail\Model\EmailMessageData([
                                "recipients" => [
                                    new \ElasticEmail\Model\EmailRecipient([
                                        "email" => $to
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
                                    "subject" => "Message from $email",
                                    "reply_to" => $sender,
                                ]),
                                "options" => new \ElasticEmail\Model\Options([
                                    "channel_name" => "User Enquiry"
                                ])
                            ]);
                            $apiInstance->emailsPost($email_message_data);
                            $sql = "INSERT INTO contact_us (first_name, last_name, email, phone, message) VALUES (?, ?, ?, ?, ?)";
                            $stmt = mysqli_prepare($con, $sql);
                            mysqli_stmt_bind_param($stmt, 'sssss', $firstName, $lastName, $email, $phone, $message);
                            mysqli_stmt_execute($stmt);
                            $_SESSION['success'] = "Form submitted successfully!";
                        } catch (Exception $e) {
                            $_SESSION['error'] = "Exception when calling EE API: . $e->getMessage(), PHP_EOL";
                        }
                    } else {
                        $_SESSION['error'] = "Email is not exists";
                    }
                } catch (Exception $e) {
                    $_SESSION['error'] = "Error validating email: " . $e->getMessage();
                }
            }
        }
        $con->close();

    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us</title>
    <!-- Bootstrap CSS -->
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">

    <link rel="stylesheet"
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="shortcut icon" href="../assets/favicon.png" type="image/x-icon">

    <link rel="icon" type="image/png" href="../assets/raybanlogo.png">
    <script src="https://smtpjs.com/v3/smtp.js"></script>
    <!-- Custom CSS -->
    <link rel="stylesheet" href="../css/contact-us.css">
    <link rel="shortcut icon" href="../assets/favicon.png" type="image/x-icon">

</head>

<body>
    <a href="http://espeakup.rf.gd/">
        <img src="../assets/logo.png" alt="Logo" class="logo">
    </a>
    <div class="container">
        <div class="center-card">
            <div class="card-body">
                <h2 class="card-title text-center">Contact Us</h2>
                <form action="contact.php" method="post">
                    <div class="form-group">
                        <label for="firstName">First Name:</label>
                        <input type="text" class="form-control" id="firstName" name="firstName"
                            placeholder="Enter First Name" required>
                    </div>
                    <div class="form-group">
                        <label for="lastName">Last Name:</label>
                        <input type="text" class="form-control" id="lastName" name="lastName"
                            placeholder="Enter Last Name" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Enter Email"
                            required>
                        <div id="emailFeedback" class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label for="phone">Phone Number:</label>
                        <input type="text" class="form-control" id="phone" name="phone"
                            placeholder="Enter Phone Number (10 digits)" required>
                        <div id="phoneFeedback" class="invalid-feedback"></div>
                    </div>
                    <div class="form-group">
                        <label for="message">Your Message:</label>
                        <textarea class="form-control" id="message" name="message" rows="3"
                            placeholder="Type your message here" required></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 back-button">
                            <button type="button" class="btn btn-secondary" onclick="history.back()">Back</button>
                        </div>
                        <div class="col-md-6 center-button">
                            <input type="submit" name="submit" class="btn btn-primary" value="Submit" />
                        </div>


                    </div>
                </form>

                <div id="errorMessage" style="display:none; margin-top:20px;" class="alert alert-danger text-center">
                </div>
                <div id="successMessage" style="display:none; margin-top:20px;" class="alert alert-success text-center">
                    <!-- Form submitted successfully! -->
                </div>

            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.0/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        $(document).ready(function () {
            // Function to validate email format
            function validateEmail(email) {
                var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                return emailPattern.test(email);
            }

            // Function to validate phone number format
            function validatePhone(phone) {
                var phonePattern = /^\d{10}$/;
                return phonePattern.test(phone);
            }

            // Function to update validation feedback
            function updateValidationFeedback(inputId, isValid, message) {
                var feedbackElement = $('#' + inputId + 'Feedback');
                if (isValid) {
                    feedbackElement.removeClass('invalid-feedback').addClass('valid-feedback');
                    feedbackElement.text(message);
                } else {
                    feedbackElement.removeClass('valid-feedback').addClass('invalid-feedback');
                    feedbackElement.text(message);
                }
                feedbackElement.css('display', 'block');
            }

            // Real-time validation for email field
            $('#email').on('input', function () {
                var email = $(this).val();
                var isValid = validateEmail(email);
                updateValidationFeedback('email', isValid, isValid ? ' ' : 'Invalid email format');
            });

            // Real-time validation for phone field
            $('#phone').on('input', function () {
                var phone = $(this).val();
                var isValid = validatePhone(phone);
                updateValidationFeedback('phone', isValid, isValid ? ' ' : 'Invalid phone number format');
            });

            // Form submission handler
            $('#contactForm').submit(function (e) {
                e.preventDefault();
                var firstName = $('#firstName').val();
                var lastName = $('#lastName').val();
                var email = $('#email').val();
                var phone = $('#phone').val();
                var message = $('#message').val();

                // Check if all fields are filled and valid
                var isValidEmail = validateEmail(email);
                var isValidPhone = validatePhone(phone);

                // $("#successMessage").show();// Show success message
                $('#contactForm')[0].reset();
            });
            <?php
            if (isset($_SESSION['error'])) {
                echo '$("#successMessage").hide();';
                echo '$("#errorMessage").text("' . (isset($_SESSION["error"]) ? $_SESSION["error"] : "") . '").show();';
                echo 'unset($_SESSION["error"]);'; // Remove the error message from the session
            } elseif (isset($_SESSION['success'])) {
                echo '$("#successMessage").text("' . (isset($_SESSION["success"]) ? $_SESSION["success"] : "") . '").show();';
                echo '$("#errorMessage").hide();';
            }
            ?>
        });
    </script>
</body>

</html>