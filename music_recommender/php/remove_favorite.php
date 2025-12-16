<?php
session_start();
include 'connect.php';
header('Content-Type: application/json');
if(!isset($_SESSION['user_id'])){
  echo json_encode(['success'=>false]);
  exit;
}
$user_id = intval($_SESSION['user_id']);
$id = intval($_POST['id'] ?? 0);
$stmt = $conn->prepare("DELETE FROM favorites WHERE id = ? AND user_id = ?");
$stmt->bind_param('ii', $id, $user_id);
$res = $stmt->execute();
echo json_encode(['success' => (bool)$res]);
$stmt->close();
$conn->close();
?>