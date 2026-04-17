CREATE DATABASE inventa_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE inventa_db;

CREATE TABLE users (
  user_id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  role ENUM('admin','staff') DEFAULT 'staff',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categories (
  category_id INT AUTO_INCREMENT PRIMARY KEY,
  category_name VARCHAR(100) NOT NULL
);

INSERT INTO categories (category_name) VALUES
('Sports'),
('Music'),
('Technology'),
('Arts & Design'),
('Leadership'),
('Innovation'),
('Volunteer'),
('Academic Competition');

CREATE TABLE students (
  student_id INT AUTO_INCREMENT PRIMARY KEY,
  student_name VARCHAR(255) NOT NULL,
  matric_no VARCHAR(50),
  program VARCHAR(100),
  year_level INT,
  email VARCHAR(150),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE talents (
  talent_id INT AUTO_INCREMENT PRIMARY KEY,
  student_id INT,
  category_id INT,
  achievement TEXT,
  level ENUM('University','State','National','International') DEFAULT 'University',
  FOREIGN KEY (student_id) REFERENCES students(student_id) ON DELETE CASCADE,
  FOREIGN KEY (category_id) REFERENCES categories(category_id) ON DELETE CASCADE
);