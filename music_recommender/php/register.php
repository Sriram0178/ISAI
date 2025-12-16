<?php
include 'connect.php';
if($_SERVER['REQUEST_METHOD'] !== 'POST') {
  header('Location: ../register.html');
  exit;
}
$username = $_POST['username'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if(!$username || !$email || !$password){
  echo 'All fields required';
  exit;
}

$hashed = password_hash($password, PASSWORD_DEFAULT);

$stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
$stmt->bind_param('sss', $username, $email, $hashed);
if($stmt->execute()){
  header('Location: ../login.html');
} else {
  echo 'Error: ' . $stmt->error;
}
$stmt->close();
$conn->close();
?>