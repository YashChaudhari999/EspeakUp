<?php

session_start(); // Start the session
if (isset($_SESSION['userotp']) && isset($_SESSION['useremail'])) {
    // OTP is set, proceed with verification logic
    $user_otp = $_SESSION['userotp']['otp'];
    $timestamp = $_SESSION['userotp']['timestamp'];
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['otp-verify-submit'])) {
        if (time() - $timestamp <= 300) {
            // Compare the entered OTP with the actual OTP
            $otp_values = [
                $_POST['first'],
                $_POST['second'],
                $_POST['third'],
                $_POST['fourth'],
                $_POST['fifth'],
                $_POST['sixth']
            ];
            $entered_otp = implode("", $otp_values);
            if ($entered_otp == $user_otp) {
                // OTP is correct, proceed with authentication
                $_SESSION['Lerror'] = "OTP Verified Successfully!";
                unset($_SESSION['userotp']);
                header("Location: change-password.php?useremail" . urlencode($_SESSION['useremail']));
            } else {
                // OTP is incorrect, show error message
                $_SESSION['Lerror'] = "Invalid OTP. Please try again.";
            }
        } else {
            // OTP has expired
            $_SESSION['Lerror'] = "OTP has expired. Please generate a new OTP.";
            // Unset the expired OTP from session
            unset($_SESSION['userotp']);
        }
    }
} else {
    header("Location: Forgot-Password.php");
    exit;
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
    <link rel="stylesheet" href="../css/otp-Verification.css">
    <title>OTP Verification</title>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var otpInputs = document.querySelectorAll('.otp-input input');

            otpInputs.forEach(function (input, index, inputs) {
                input.addEventListener('input', function () {
                    if (input.value.length === 1) {
                        if (index < inputs.length - 1) {
                            inputs[index + 1].focus();
                        }
                    } else if (input.value.length === 0) {
                        if (index > 0) {
                            inputs[index - 1].focus();
                        }
                    }
                });

                // Prevent non-numeric input
                input.addEventListener('keypress', function (event) {
                    var charCode = (event.which) ? event.which : event.keyCode;
                    return !(charCode > 31 && (charCode < 48 || charCode > 57));
                });

                // Handle paste event
                input.addEventListener('paste', function (event) {
                    event.preventDefault();
                    var pasteData = (event.clipboardData || window.clipboardData).getData('text');

                    // Ensure that pasted data is numeric and of length 6
                    if (/^\d{6}$/.test(pasteData)) {
                        var pasteArray = pasteData.split('');
                        for (var i = 0; i < pasteArray.length && index + i < inputs.length; i++) {
                            inputs[index + i].value = pasteArray[i];
                        }
                    }
                });
            });
        });
    </script>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            var otpInputs = document.querySelectorAll('.otp-input input');

            otpInputs.forEach(function (input, index, inputs) {
                input.addEventListener('input', function () {
                    if (input.value.length === 1) {
                        if (index < inputs.length - 1) {
                            inputs[index + 1].focus();
                        }
                    } else if (input.value.length === 0) {
                        if (index > 0) {
                            inputs[index - 1].focus();
                        }
                    }
                });

                // Prevent non-numeric input
                input.addEventListener('keypress', function (event) {
                    var charCode = (event.which) ? event.which : event.keyCode;
                    return !(charCode > 31 && (charCode < 48 || charCode > 57));
                });

                // Handle paste event
                input.addEventListener('paste', function (event) {
                    event.preventDefault();
                    var pasteData = (event.clipboardData || window.clipboardData).getData('text');

                    // Ensure that pasted data is numeric and of length 6
                    if (/^\d{6}$/.test(pasteData)) {
                        var pasteArray = pasteData.split('');
                        for (var i = 0; i < pasteArray.length && index + i < inputs.length; i++) {
                            inputs[index + i].value = pasteArray[i];
                        }
                    }
                });
            });
        });
    </script>
</head>

<body>

    <div class="wrapper">
        <div class="container" id="container">
            <div class="forms">
                <!-- Login Form -->
                <div class="form login">
                    <span class="title">OTP Verification</span>
                    <div class="login-signup">
                        <span class="text">Please enter the one time password
                            to verify your account
                        </span>
                    </div>
                    <form method="post" action="otp-Verification.php">
                        <div id="otp" class="inputs d-flex flex-row justify-content-center mt-2 otp-input">
                            <input class="m-2 text-center form-control rounded" type="text" id="first" name="first"
                                maxlength="1" required />
                            <input class="m-2 text-center form-control rounded" type="text" id="second" name="second"
                                maxlength="1" required />
                            <input class="m-2 text-center form-control rounded" type="text" id="third" name="third"
                                maxlength="1" required />
                            <input class="m-2 text-center form-control rounded" type="text" id="fourth" name="fourth"
                                maxlength="1" required />
                            <input class="m-2 text-center form-control rounded" type="text" id="fifth" name="fifth"
                                maxlength="1" required />
                            <input class="m-2 text-center form-control rounded" type="text" id="sixth" name="sixth"
                                maxlength="1" required />
                        </div>
                        <br>
                        <small id="login-email-error" class="error-message">
                            <div class="register">
                                <?php
                                // Check if there's an error message in the session
                                if (isset($_SESSION['Lerror'])) {
                                    echo "<p>{$_SESSION['Lerror']}</p>";
                                    unset($_SESSION['Lerror']);
                                }
                                ?>
                            </div>
                        </small>

                        <div class="input-field button">
                            <input type="submit" value="Verify OTP" name="otp-verify-submit">
                        </div>
                    </form>
                    <div class="contact-us">
                        <span class="text">Need help? <a href="mailto:espaekup@gmail.com" target="_blank">Contact us</a>
                        </span>
                    </div>
                </div>
            </div>

        </div>

</body>

</html>