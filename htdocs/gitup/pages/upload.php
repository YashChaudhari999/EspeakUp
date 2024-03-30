<?php
session_start();

$user_email = $_SESSION['email'];
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Connect to your database (replace with your actual database credentials)

    $server = "localhost";
    $username = "root";
    $password = "";
    $database = "llb";


    // Create connection
    $conn = new mysqli($server, $username, $password, $database);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Check if a file was uploaded
    if (isset($_FILES["fileToUpload"])) {
        $file = $_FILES["fileToUpload"];

        // Check if there was no error during the file upload
        if ($file["error"] === UPLOAD_ERR_OK) {
            $fileName = basename($file["name"]);
            $fileTmpName = $file["tmp_name"];

            // Read the file contents
            $fileContent = file_get_contents($fileTmpName);

            // Prepare and execute SQL statement to update the profile picture
            $sql = "UPDATE users_data SET profilePhoto= ? WHERE email =$user_email";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("b", $fileContent);
            $stmt->execute();

            // Close statement and connection
            $stmt->close();
            $conn->close();

            // Redirect back to the profile page
            header("Location: profile.php");
            exit;
        } else {
            // Handle file upload errors
            echo "Error uploading file.";
        }
    } else {
        // Handle case where no file was uploaded
        echo "No file uploaded.";
    }
}
?>