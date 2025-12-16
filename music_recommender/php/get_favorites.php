<?php
session_start();
include 'connect.php';
header('Content-Type: application/json');
if(!isset($_SESSION['user_id'])){
  echo json_encode([]);
  exit;
}
$user_id = intval($_SESSION['user_id']);
$stmt = $conn->prepare("SELECT id, track_id, name, artist, image_url, preview_url FROM favorites WHERE user_id = ? ORDER BY added_at DESC");
$stmt->bind_param('i', $user_id);
$stmt->execute();
$res = $stmt->get_result();
$rows = $res->fetch_all(MYSQLI_ASSOC);
echo json_encode($rows);
$stmt->close();
$conn->close();
?>