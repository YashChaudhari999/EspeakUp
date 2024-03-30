<?php
session_start(); // Start the session

// Check if the user is logged in
if (!isset($_SESSION['email'])) {
  // Redirect to login page or perform other actions if user is not logged in
  header("Location: login.php");
  exit;
} else {
  header("Location: card.php");
}