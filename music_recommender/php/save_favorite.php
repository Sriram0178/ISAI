<?php
session_start();
include 'connect.php';
header('Content-Type: application/json');
if(!isset($_SESSION['user_id'])){
  echo json_encode(['success'=>false, 'msg'=>'not_logged_in']);
  exit;
}
$user_id = intval($_SESSION['user_id']);
$track_id = $_POST['track_id'] ?? '';
$name = $_POST['name'] ?? '';
$artist = $_POST['artist'] ?? '';
$image = $_POST['image'] ?? '';
$preview = $_POST['preview'] ?? '';

if(!$track_id){
  echo json_encode(['success'=>false, 'msg'=>'track_id required']);
  exit;
}

// prevent duplicate for same user & track
$check = $conn->prepare("SELECT id FROM favorites WHERE track_id = ? AND user_id = ?");
$check->bind_param('si', $track_id, $user_id);
$check->execute();
$check->store_result();
if($check->num_rows > 0){
  echo json_encode(['success'=>false, 'msg'=>'already_saved']);
  exit;
}
$check->close();

$stmt = $conn->prepare("INSERT INTO favorites (track_id, name, artist, image_url, preview_url, user_id) VALUES (?, ?, ?, ?, ?, ?)");
$stmt->bind_param('sssssi', $track_id, $name, $artist, $image, $preview, $user_id);
$res = $stmt->execute();
if($res) echo json_encode(['success'=>true]);
else echo json_encode(['success'=>false, 'msg'=>$stmt->error]);
$stmt->close();
$conn->close();
?>