<?php
session_start();
include 'connect.php';
if($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ../login.html');
  exit;
}
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

$stmt = $conn->prepare("SELECT id, password FROM users WHERE email = ?");
$stmt->bind_param('s', $email);
$stmt->execute();
$stmt->bind_result($id, $hash);
if($stmt->fetch()){
  if(password_verify($password, $hash)){
    $_SESSION['user_id'] = $id;
    header('Location: ../songs.html');
    exit;
  } else {
    echo 'Invalid credentials';
  }
} else {
  echo 'Invalid credentials';
}
$stmt->close();
$conn->close();
?>