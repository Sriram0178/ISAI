<?php
// Replace these with your app's values
$client_id = "38c2552c3db84c468286fc0f24fe3fd4";
$client_secret = "0ecfbe24558547dc9507ec1f8fbeea73";

// Use client credentials flow to fetch an app-level token.
// IMPORTANT: Keep client_secret private. This file runs on your server.
$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, "https://accounts.spotify.com/api/token");
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_POST, 1);
curl_setopt($ch, CURLOPT_POSTFIELDS, "grant_type=client_credentials");
$headers = [
  "Authorization: Basic " . base64_encode("$client_id:$client_secret"),
  "Content-Type: application/x-www-form-urlencoded"
];
curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

$result = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

http_response_code($http_code);
header('Content-Type: application/json');
echo $result;
?>