// Updated frontend script: adds favorite button behavior
let accessToken = "";
let tokenExpires = 0;
let tokenFetchInProgress = false;
const tokenURL = "php/spotify_token.php";

async function getAccessToken(){
  if(accessToken && Date.now() < tokenExpires - 5000) return accessToken;
  if(tokenFetchInProgress) return accessToken;
  tokenFetchInProgress = true;
  try{
    const res = await fetch(tokenURL);
    if(!res.ok) throw new Error("Token fetch failed: " + res.status);
    const data = await res.json();
    accessToken = data.access_token;
    tokenExpires = Date.now() + (data.expires_in || 3600) * 1000;
    tokenFetchInProgress = false;
    return accessToken;
  }catch(err){
    console.error(err);
    tokenFetchInProgress = false;
    return null;
  }
}

let searchTimer = null;
document.addEventListener("DOMContentLoaded", () => {
  const input = document.getElementById("searchInput");
  const clearBtn = document.getElementById("clearBtn");
  input && input.addEventListener("input", () => {
    clearTimeout(searchTimer);
    searchTimer = setTimeout(() => searchSpotify(input.value.trim()), 300);
  });
  clearBtn && clearBtn.addEventListener("click", () => {
    input.value = "";
    document.getElementById("results").innerHTML = "";
  });
});

async function searchSpotify(query){
  const resultsDiv = document.getElementById("results");
  if(!query){
    resultsDiv.innerHTML = "";
    return;
  }

  const token = await getAccessToken();
  if(!token){
    resultsDiv.innerHTML = "<div class='preview-note'>Unable to fetch token. Check php/spotify_token.php and your client credentials.</div>";
    return;
  }

  try{
    const url = `https://api.spotify.com/v1/search?q=${encodeURIComponent(query)}&type=track&limit=20`;
    const res = await fetch(url, {
      headers: { Authorization: 'Bearer ' + token }
    });
    if(!res.ok){
      const txt = await res.text();
      throw new Error('Spotify API error: ' + res.status + ' - ' + txt);
    }
    const data = await res.json();
    const tracks = data.tracks && data.tracks.items ? data.tracks.items : [];
    renderTracks(tracks);
  }catch(err){
    console.error(err);
    resultsDiv.innerHTML = "<div class='preview-note'>Error fetching from Spotify. See console for details.</div>";
  }
}

function renderTracks(tracks){
  const resultsDiv = document.getElementById("results");
  resultsDiv.innerHTML = "";
  if(!tracks || tracks.length === 0){
    resultsDiv.innerHTML = "<div class='preview-note'>No tracks found.</div>";
    return;
  }

  tracks.forEach(track => {
    const card = document.createElement("div");
    card.className = "song-card";

    const img = document.createElement("img");
    img.src = track.album.images && track.album.images[0] ? track.album.images[0].url : "";
    img.alt = track.name;

    const title = document.createElement("h3");
    title.textContent = track.name;

    const artists = document.createElement("p");
    artists.textContent = track.artists.map(a=>a.name).join(", ");

    const audioContainer = document.createElement("div");
    if(track.preview_url){
      const audio = document.createElement("audio");
      audio.controls = true;
      const src = document.createElement("source");
      src.src = track.preview_url;
      src.type = "audio/mpeg";
      audio.appendChild(src);
      audioContainer.appendChild(audio);
    } else {
      const note = document.createElement("div");
      note.className = "preview-note";
      note.textContent = "Preview not available for this track.";
      audioContainer.appendChild(note);
    }

    const favBtn = document.createElement("button");
    favBtn.textContent = "Add to Favorites";
    favBtn.onclick = () => saveFavorite(track);

    card.appendChild(img);
    card.appendChild(title);
    card.appendChild(artists);
    card.appendChild(audioContainer);
    card.appendChild(favBtn);

    resultsDiv.appendChild(card);
  });
}

async function saveFavorite(track){
  // send POST to php/save_favorite.php
  const form = new FormData();
  form.append('track_id', track.id);
  form.append('name', track.name);
  form.append('artist', track.artists.map(a=>a.name).join(', '));
  form.append('image', track.album.images && track.album.images[0] ? track.album.images[0].url : '');
  form.append('preview', track.preview_url || '');

  const res = await fetch('php/save_favorite.php', { method:'POST', body: form });
  const data = await res.json();
  if(data.success){
    alert('Saved to favorites!');
  } else {
    if(data.msg === 'not_logged_in'){
      if(confirm('You must be logged in to save favorites. Go to login page?')){
        window.location.href = 'login.html';
      }
    } else if(data.msg === 'already_saved'){
      alert('Already in favorites.');
    } else {
      alert('Save failed: ' + (data.msg || 'unknown'));
    }
  }
}
