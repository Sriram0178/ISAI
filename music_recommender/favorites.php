<?php
session_start();
include 'php/connect.php';
if(!isset($_SESSION['user_id'])){
  header('Location: login.html');
  exit;
}
$user_id = intval($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8" />
  <title>Favorites - Music Recommender</title>
  <link rel="stylesheet" href="css/style.css">
</head>
<body>
  <header class="topbar">
    <h1>❤️ My Favorites</h1>
    <nav>
      <a href="songs.html">Search</a>
      <a href="logout.php">Logout</a>
    </nav>
  </header>

  <main class="container">
    <div id="favoritesList" class="song-grid"></div>
  </main>

<script>
async function loadFavorites(){
  try{
    const res = await fetch('php/get_favorites.php');
    const data = await res.json();
    const container = document.getElementById('favoritesList');
    container.innerHTML = '';
    if(!data || data.length === 0){
      container.innerHTML = '<div class="preview-note">No favorites yet.</div>';
      return;
    }
    data.forEach(f => {
      const card = document.createElement('div');
      card.className = 'song-card';
      card.innerHTML = `
        <img src="${f.image_url || ''}" alt="${f.name}">
        <h3>${f.name}</h3>
        <p>${f.artist}</p>
        ${f.preview_url ? '<audio controls><source src="'+f.preview_url+'" type="audio/mpeg"></audio>' : '<div class="preview-note">Preview not available.</div>'}
        <button onclick="removeFav(${f.id})">Remove</button>
      `;
      container.appendChild(card);
    });
  }catch(err){
    console.error(err);
  }
}

async function removeFav(id){
  if(!confirm('Remove from favorites?')) return;
  const form = new FormData();
  form.append('id', id);
  const res = await fetch('php/remove_favorite.php', { method:'POST', body: form });
  const data = await res.json();
  if(data.success) loadFavorites();
  else alert('Remove failed');
}

document.addEventListener('DOMContentLoaded', loadFavorites);
</script>
</body>
</html>
