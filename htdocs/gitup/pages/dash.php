<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
  // Redirect to login page or perform other actions if user is not logged in
  header("Location: login.php");
  exit;
}

// Retrieve user's email from session variable
$user_email = $_SESSION['email'];


$server = "localhost";
$username = "root";
$password = "";
$database = "llb";


// Establish database connection
$conn = mysqli_connect($server, $username, $password, $database);

// Check the database connection
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}
unset($error);
unset($success);
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['passc'])) {
  // Retrieve form data

  $old_password = $_POST['old_password'];
  $new_password = $_POST['new_password'];

  // Prepare and execute a query to retrieve the user's hashed password
  $stmt = $conn->prepare("SELECT password FROM users_data WHERE email = ?");
  $stmt->bind_param("s", $user_email);
  $stmt->execute();
  $stmt->store_result();

  // Bind the result variable
  $stmt->bind_result($hashed_password);

  // Fetch the result
  $stmt->fetch();
  // Verify if the old password matches the hashed password in the database
  if (password_verify($old_password, $hashed_password)) {
    // Hash the new password
    $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Prepare and execute a query to update the user's password
    $update_stmt = $conn->prepare("UPDATE users_data SET password = ? WHERE email = ?");
    $update_stmt->bind_param("ss", $hashed_new_password, $user_email);
    $update_stmt->execute();

    // Check if the password was successfully updated
    if ($update_stmt->affected_rows > 0) {
      $success = "Password updated successfully.";

    } else {
      $error = "Failed to update password. Please try again.";

    }
  } else {
    $error = "Old password is incorrect. Please try again.";

  }
}

//Experimental phase for profile photo
$sql = "SELECT profilePhoto FROM users_data WHERE email = '$user_email'";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
  // User has a profile photo
  $row = $result->fetch_assoc();
  $profilePhoto = $row['profilePhoto'];
}

// Assuming $conn is your database connection
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES['profpic'])) {
  $file = $_FILES['profpic'];

  // Debugging: Check if file is uploaded and if it contains data
  if ($file['error'] !== UPLOAD_ERR_OK) {
    $error1 = "Please select a picture.";
  } else {
    // Check file size (10MB limit)
    $maxFileSize = 10 * 1024 * 1024; // 10MB in bytes
    if ($file['size'] > $maxFileSize) {
      $error1 = "File size exceeds the maximum limit of 10MB.";
    } else {
      // Check file type (allowed: JPG, GIF, PNG)
      $allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
      $fileType = mime_content_type($file['tmp_name']);
      if (!in_array($fileType, $allowedTypes)) {
        $error1 = "Only JPG, GIF, or PNG files are allowed.";
      } else {
        $profilePhotoData = $file['tmp_name'];

        // Debugging: Check if file data is being read properly
        $profilePhotoDataContent = file_get_contents($profilePhotoData);
        if ($profilePhotoDataContent === false) {
          $error1 = "Failed to read file data.";
        } else {
          // Update profile photo in the database
          $update_sql = "UPDATE users_data SET profilePhoto = ? WHERE email = ?";
          $stmt = $conn->prepare($update_sql);
          $null = NULL; // Necessary for binding a LONGBLOB parameter
          $stmt->bind_param("bs", $null, $user_email);
          $stmt->send_long_data(0, $profilePhotoDataContent); // Send the data separately
          $stmt->execute();

          // Check if the query was executed successfully
          if ($stmt->affected_rows > 0) {
            // Redirect to dashboard after uploading photo
            header("Location: dash.php");
            exit;
          } else {
            $error1 = "Error updating profile photo.";
          }
        }
      }
    }
  }
}

$stmt = $conn->prepare("SELECT username, profilePhoto FROM users_data WHERE email = ?");
$stmt->bind_param("s", $user_email);
$stmt->execute();
$stmt->store_result();

// Bind the result variables
$stmt->bind_result($user_username, $user_profile_photo);

// Fetch the result
$stmt->fetch();

// Close the statement
$stmt->close();

// Close the database connection
$conn->close();

?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard</title>
  <link rel="stylesheet" href="../css/dash.css" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

  <!-- ===== Iconscout CSS ===== -->
  <link rel="stylesheet" href="https://unicons.iconscout.com/release/v4.0.0/css/line.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <link rel="shortcut icon" href="../assets/favicon.png" type="image/x-icon">

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      // JavaScript function for real-time password validation
      function validatePassword(passwordId, errorContainerId) {
        var password = document.getElementById(passwordId).value;
        var errorContainer = document.getElementById(errorContainerId);

        if (password.length < 8 || password.length > 20 || !/\d/.test(password)) {
          errorContainer.textContent = "Password must be 8-20 characters and include at least one number";
        } else {
          errorContainer.textContent = ""; // Clear the error message
        }
      }

      // JavaScript function to check if new password matches confirm password
      function validateConfirmPassword() {
        var newPassword = document.getElementById("new_password").value;
        var confirmPassword = document.getElementById("confirm_password").value;
        var confirmErrorContainer = document.getElementById("confirm-password-error");

        if (newPassword !== confirmPassword) {
          confirmErrorContainer.textContent = "Passwords do not match";
        } else {
          confirmErrorContainer.textContent = ""; // Clear the error message
        }
      }

      // Add event listeners for real-time password validation
      document.getElementById("old_password").addEventListener("input", function () {
        validatePassword("old_password", "old-password-error");
      });

      document.getElementById("new_password").addEventListener("input", function () {
        validatePassword("new_password", "new-password-error");
        validateConfirmPassword(); // Check if new password matches confirm password
      });

      document.getElementById("confirm_password").addEventListener("input", function () {
        validatePassword("confirm_password", "confirm-password-error");
        validateConfirmPassword(); // Check if new password matches confirm password
      });
    });
  </script>
</head>

<body>
  <div id="nav">
    <div id="nleft">
      <a href="./card.php"><img src="../assets/logo.png" alt=""></a>
    </div>
    <div id="nright">
      <a href="./logout.php" class="btn btn-outline-light" id="sign-out-btn">Sign Out</a>
      </a>
    </div>
  </div>
  </div>
  <div class="wrapper">
    <div class="container light-style flex-grow-1 container-p-y">
      <h4 class="font-weight-bold py-3 mb-4" style="color: white;">Account settings</h4>
      <div class="card overflow-hidden">
        <div class="row no-gutters row-bordered row-border-light">
          <div class="col-md-3 pt-0">
            <div class="list-group list-group-flush account-settings-links">
              <a class="list-group-item list-group-item-action active" data-toggle="list"
                href="#account-general">General</a>
              <a class="list-group-item list-group-item-action" data-toggle="list"
                href="#account-change-password">Change password</a>
            </div>
          </div>
          <div class="col-md-9">
            <div class="tab-content">
              <div class="tab-pane fade active show" id="account-general">
                <div class="card-body media align-items-center">
                  <div>
                    <!-- Display profile photo -->
                    <!-- If no photo found, it will display the default image -->
                    <?php
                    if ($result->num_rows > 0 && !empty($profilePhoto)) {
                      echo '<img src="data:image/jpeg;base64,' . base64_encode($profilePhoto) . '" alt="Profile Photo" style="width: 100px; height: 100px;" >';
                    } else {
                      echo '<img src="https://bootdey.com/img/Content/avatar/avatar1.png" alt="Default Profile Photo"style="width: 100px; height: 100px;">';
                    }
                    ?>
                  </div>
                  <div class="media-body ml-4">
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post"
                      enctype="multipart/form-data">
                      <label for="profpic">Upload New Profile Photo:</label><br>
                      <input type="file" id="profpic" name="profpic"><br>
                      <input class="btn btn-primary" type="submit" value="Upload" id="btnUpload">
                    </form>
                    &nbsp;

                    <div class="text-dark small mt-1">
                      Allowed JPG, GIF or PNG. Max size of 10MB
                    </div>
                    <?php
                    // Display error or success message with JavaScript setTimeout function
                    if (!empty($error1)) {
                      echo '<div id="errorDiv" class="alert alert-danger mt-3">' . $error1 . '</div>';
                      echo '<script>
                      setTimeout(function(){
                        document.getElementById("errorDiv").innerHTML = "";
                        document.getElementById("errorDiv").style.display = "none";
                      }, 7000);
                    </script>';
                    }
                    ?>
                  </div>
                </div>
                <hr class="border-light m-0" />
                <div class="card-body">
                  <div class="form-group">
                    <label class="form-label">Name</label>
                    <input type="text" class="form-control" value="<?php echo $user_username ?>" />
                  </div>
                  <div class="form-group">
                    <label class="form-label">E-mail</label>
                    <input type="text" class="form-control mb-1" value="<?php echo $user_email ?>" />
                  </div>
                </div>
              </div>
              <div class="tab-pane fade" id="account-change-password">
                <div class="card-body pb-2">
                  <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" name="passc">
                    <div class="form-group">
                      <label for="old_password" class="form-label">Old Password</label>
                      <div class="input-group">
                        <input type="password" class="form-control" id="old_password" name="old_password" required>
                        <div class="input-group-append">
                          <span class="input-group-text">
                            <i class="uil uil-eye-slash showHidePw" style=" cursor: pointer;"
                              onclick="togglePasswordVisibility('old_password','toggleOldPassword')"></i>
                          </span>
                        </div>
                      </div>
                      <span id="old-password-error" class="error-message"></span>
                    </div>
                    <div class="form-group">
                      <label for="new_password" class="form-label">New Password</label>
                      <div class="input-group">
                        <input type="password" class="form-control" id="new_password" name="new_password" required>
                        <div class="input-group-append">
                          <span class="input-group-text">
                            <i class="uil uil-eye-slash showHidePw" style=" cursor: pointer;"
                              onclick="togglePasswordVisibility('new_password','toggleNewPassword')"></i>
                          </span>
                        </div>
                      </div>
                      <span id="new-password-error" class="error-message"></span>
                    </div>
                    <div class="form-group">
                      <label for="confirm_password" class="form-label">Confirm New Password</label>
                      <div class="input-group">
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                          required>
                        <div class="input-group-append">
                          <span class="input-group-text">
                            <i class="uil uil-eye-slash showHidePw" style=" cursor: pointer;"
                              onclick="togglePasswordVisibility('confirm_password','toggleConfirmPassword')"></i>
                          </span>
                        </div>
                      </div>
                      <span id="confirm-password-error" class="error-message"></span>
                    </div>
                    <button type="submit" name="passc" class="btn btn-primary">Change Password</button>
                  </form>
                  <?php
                  // Display error or success message
                  if (!empty($error)) {
                    echo '<div class="alert alert-danger mt-3">' . $error . '</div>';
                    unset($error);

                  } elseif (!empty($success)) {
                    echo '<div class="alert alert-success mt-3">' . $success . '</div>';
                    unset($success);
                  }
                  ?>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="row">
        <div class="col-md-6 text-left mt-3">
          <a href="./card.php" class="btn btn-secondary">
            Back
          </a>
        </div>
      </div>
    </div> <!-- closing container div -->


  </div>

  <footer class="footer">
    <div class="container-fo">
      <div class="row-fo">
        <div class="footer-col">
          <h4>company</h4>
          <ul>
            <li><a href="./about.html">about us</a></li>
            <li><a href="./contact.php">Contact US</a></li>
            <li><a href="./terms.html">Terms and Conditions</a></li>

          </ul>
        </div>
        <div class="footer-col">
          <h4>get help</h4>
          <ul>
            <li><a href="#">FAQ</a></li>

          </ul>
        </div>

        <div class="footer-col">
          <h4>follow us</h4>
          <div class="social-links">
            <a href="#"><i class="fa-brands fa-facebook"></i></a>

            <a href="#"><i class="fa-brands fa-instagram"></i></a>

          </div>
        </div>
      </div>
    </div>
  </footer>

  <script data-cfasync="false" src="/cdn-cgi/scripts/5c5dd728/cloudflare-static/email-decode.min.js"></script>
  <script src="https://code.jquery.com/jquery-1.10.2.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.0/dist/js/bootstrap.bundle.min.js"></script>

  <script>
    function togglePasswordVisibility(passwordId) {
      var passwordInput = document.getElementById(passwordId);
      var icon = passwordInput.parentElement.querySelector('.showHidePw');

      if (passwordInput.type === "password") {
        passwordInput.type = "text";
        icon.classList.remove('uil-eye-slash');
        icon.classList.add('uil-eye');
        console.log("Changing type to text");
      } else {
        passwordInput.type = "password";
        icon.classList.remove('uil-eye');
        icon.classList.add('uil-eye-slash');
        console.log("Changing type to password");
      }
    }
  </script>

</body>

</html>