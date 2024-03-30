<?php
session_start();
if (isset($_SESSION['email'])) {
    header("Location: card.php"); // Change "dash.php" to the desired page
    exit(); // Stop executing further code
}
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



// Establish database connection
$con = mysqli_connect($server, $username, $password, $database);
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!$con) {
        $_SESSION['error'] = ("Connection to this database failed due to" . mysqli_connect_error());
        echo "Connection to this database failed due to";
    } else {
        if (isset($_POST['login-email'], $_POST['login-password'])) {
            session_start();
            // Retrieve form data
            $username = isset($_POST['login-email']) ? mysqli_real_escape_string($con, $_POST['login-email']) : '';
            $password = isset($_POST['login-password']) ? mysqli_real_escape_string($con, $_POST['login-password']) : '';
            $stmt = null;
            if (!empty($username) && !empty($password)) {
                // Prepare and execute SQL statement
                $stmt = $con->prepare("SELECT * FROM users_data WHERE email = ?");
                $stmt->bind_param("s", $username);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows == 1) {
                    $row = $result->fetch_assoc();

                    // Verify password
                    if (password_verify($password, $row['password'])) {
                        // User is authenticated
                        $_SESSION['username'] = $username;
                        $_SESSION['password'] = $password;
                        $_SESSION['email'] = $username;
                        if (isset($_POST['remember'])) {
                            // Set a cookie with a long expiration time (7 days)
                            setcookie('remember_me', '1', time() + (7 * 24 * 60 * 60), '/');
                        }
                        header("location: card.php");  // Redirect to card page or user's dashboard
                        // echo "Data reached";
                        exit;
                    } else {
                        // Invalid credentials
                        $_SESSION['Lerror'] = "Invalid username or password";
                    }
                } else {
                    // Invalid username
                    $_SESSION['Lerror'] = "Invalid Email";
                }
            } else {
                $_SESSION['Lerror'] = "Please enter both email and password";
            }

        } elseif (isset($_POST['name'], $_POST['email'], $_POST['password'], $_POST['confirmPassword'])) {
            if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['signup-submit'])) {

                $username = mysqli_real_escape_string($con, $_POST['name']);
                $email = mysqli_real_escape_string($con, $_POST['email']);
                $password = mysqli_real_escape_string($con, $_POST['password']);
                $confirmpassword = mysqli_real_escape_string($con, $_POST['confirmPassword']);

                $checkUsernameQuery = "SELECT * FROM users_data WHERE email = '$email'";
                $result = mysqli_query($con, $checkUsernameQuery);
                $checkUsernameQuery1 = "SELECT * FROM users_data WHERE username = '$username'";
                $resultUsername = mysqli_query($con, $checkUsernameQuery1);
                $hashedPassword = password_hash($confirmpassword, PASSWORD_DEFAULT);

                if (mysqli_num_rows($result) > 0) {
                    $_SESSION['error'] = "Email already exists. Please choose a different username.";

                } elseif (mysqli_num_rows($resultUsername) > 0) {
                    $_SESSION['error'] = "Username already exists. Please choose a different username.";
                } else {
                    // Verify email using ElasticEmail API
                    try {
                        $result = $apiInstance->verificationsByEmailPost($email);
                        if ($result['predicted_status'] === 'LowRisk') {
                            $sql = "INSERT INTO`llb`.`users_data` (username, email, password, profilePhoto) VALUES ('$username', '$email', '$hashedPassword', '')";
                            $_SESSION['email'] = $email;
                            header("Location: EmailVerification.php?email=" . urlencode($email));
                            // echo "$sql";

                            if ($con->query($sql) === true) {
                            } else {
                                $_SESSION['error'] = "ERROR: $sql <br /> $con->error";
                                // echo "ERROR: $sql <br /> $con->error";
                            }
                        } else {
                            $_SESSION['error'] = "Email is not exists";
                        }
                    } catch (Exception $e) {
                        $_SESSION['error'] = "Error validating email: " . $e->getMessage();
                    }
                }
                $con->close();
            }
        }
    }
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
    <link rel="stylesheet" href="../css/login.css">

    <title>Login</title>


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

            // JavaScript function for real-time password validation
            function validateLoginPassword() {
                var password = document.getElementById("login-password").value;
                var errorContainer = document.getElementById("login-password-error");

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


            // Add event listeners for real-time validation


            // JavaScript function for real-time username validation
            function validateName() {
                var name = document.getElementById("name").value;
                var errorContainer = document.getElementById("name-error");

                if (name.length < 5) {
                    errorContainer.textContent = "Name must be at least 5 characters long";
                } else {
                    errorContainer.textContent = ""; // Clear the error message
                }
            }

            // JavaScript function for real-time email validation
            function validateEmail() {
                var email = document.getElementById("email").value;
                var errorContainer = document.getElementById("email-error");

                if (email.length === 0) {
                    errorContainer.textContent = "Email is required";
                } else if (!/^\S+@\S+\.\S+$/.test(email)) {
                    errorContainer.textContent = "Invalid email format";
                } else {
                    errorContainer.textContent = ""; // Clear the error message
                }
            }

            // JavaScript function for real-time password validation
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
            document.getElementById("login-email").addEventListener("input", validateLoginEmail);
            document.getElementById("login-password").addEventListener("input", validateLoginPassword);
            document.getElementById("name").addEventListener("input", validateName);
            document.getElementById("email").addEventListener("input", validateEmail);
            document.getElementById("password").addEventListener("input", validatePassword);
            document.getElementById("confirmPassword").addEventListener("input", validateConfirmPassword);
        });
    </script>
</head>

<body>
    <div id="nav">
        <div id="nleft">
            <a href="../../index.php"><img src="../assets/logo.png" alt=""></a>
        </div>
    </div>
    <div class="wrapper">
        <div class="container" id="container">
            <div class="forms">
                <!-- Login Form -->
                <div class="form login">
                    <span class="title">Login</span>

                    <form method="post" action="login.php">
                        <div class="input-field">
                            <input type="email" id="login-email" name="login-email" placeholder="Enter your email"
                                required>
                            <i class="uil uil-envelope icon"></i>
                        </div>
                        <small id="login-email-error" class="error-message"></small>
                        <!-- Error message for email validation -->

                        <div class="input-field">
                            <input type="password" id="login-password" name="login-password"
                                placeholder="Enter your password" required>
                            <i class="uil uil-lock icon"></i>
                            <i class="uil uil-eye-slash showHidePw" id="toggleLoginPassword"
                                onclick="togglePasswordVisibility('login-password','toggleLoginPassword')"></i>
                        </div>
                        <small id="login-password-error" class="error-message"></small>

                        <div class="checkbox-text">
                            <div class="checkbox-content">
                                <input type="checkbox" id="logCheck" name="remember">
                                <label for="logCheck" class="text">Remember me</label>
                            </div>

                            <a href="./Forgot-Password.php" class="text">Forgot password?</a>
                        </div>

                        <div class="input-field button">
                            <input type="submit" value="Login">
                        </div>
                        <div class="register">
                            <?php
                            // Check if there's an error message in the session
                            if (isset($_SESSION['Lerror'])) {
                                echo "<p>{$_SESSION['Lerror']}</p>";
                                unset($_SESSION['Lerror']); // Remove the error message from the session
                            }
                            ?>
                        </div>
                    </form>

                    <div class="login-signup">
                        <span class="text">Not a member?
                            <a href="#" class="text signup-link">Signup Now</a>
                        </span>
                    </div>
                </div>


                <!-- Registration Form -->
                <div class="form signup">
                    <span class="title">Registration</span>

                    <form method="post" action="login.php">
                        <div class="input-field">
                            <input type="text" id="name" name="name" placeholder="Enter your name" required>
                            <i class="uil uil-user"></i>
                        </div>
                        <small id="name-error" class="error-message"></small> <!-- Error message for name validation -->
                        <div class="input-field">
                            <input type="email" id="email" name="email" placeholder="Enter your email" required>
                            <i class="uil uil-envelope icon"></i>
                        </div>
                        <small id="email-error" class="error-message"></small>
                        <!-- Error message for email validation -->
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
                        <!-- Error message for confirm password validation -->

                        <div class="checkbox-text">
                            <div class="checkbox-content">
                                <input type="checkbox" id="termCon" required>
                                <label for="termCon" class="text">I accepted all terms and conditions</label>
                            </div>
                        </div>

                        <div class="input-field button">
                            <input type="submit" value="Signup" name="signup-submit">
                        </div>
                        <div class="register">
                            <?php
                            // Check if there's an error message in the session
                            if (isset($_SESSION['error'])) {
                                echo "<p>{$_SESSION['error']}</p>";
                                unset($_SESSION['error']); // Remove the error message from the session
                            }
                            ?>
                        </div>
                    </form>

                    <div class="login-signup">
                        <span class="text">Already a member?
                            <a href="#" class="text login-link" onclick="toggleFormHeight()">Login Now</a>
                        </span>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <script> function togglePasswordVisibility(passwordId, iconId) {
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
    <script src="../script/login.js"></script>
</body>

</html>