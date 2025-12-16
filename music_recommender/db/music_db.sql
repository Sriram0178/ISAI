-- SQL schema for music_recommender (updated with favorites per user)
CREATE DATABASE IF NOT EXISTS music_db;
USE music_db;

CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) NOT NULL,
  email VARCHAR(150) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS favorites (
  id INT AUTO_INCREMENT PRIMARY KEY,
  track_id VARCHAR(100) NOT NULL,
  name VARCHAR(255),
  artist VARCHAR(255),
  album VARCHAR(255),
  image_url VARCHAR(512),
  preview_url VARCHAR(512),
  user_id INT DEFAULT 0,
  added_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
