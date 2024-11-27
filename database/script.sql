CREATE TABLE IF NOT EXISTS users (
  id INT AUTO_INCREMENT PRIMARY KEY,
  first_name VARCHAR(50) NOT NULL,
  last_name VARCHAR(50) NOT NULL,
  email VARCHAR(100) NOT NULL UNIQUE,
  password VARCHAR(255) NOT NULL,
  role ENUM('student', 'admin') DEFAULT 'student',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS rooms (
  id INT AUTO_INCREMENT PRIMARY KEY,
  room_name VARCHAR(50) NOT NULL,
  room_code VARCHAR(255) NOT NULL UNIQUE,
  user_id INT NOT NULL,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  max_attempts INT NOT NULL DEFAULT -1 COMMENT '-1 to indicate unlimited',
  items_per_attempt INT NOT NULL DEFAULT 5,
  FOREIGN KEY (user_id) REFERENCES users(id)
);

CREATE TABLE IF NOT EXISTS enrolled_rooms (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  room_id INT NOT NULL,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (room_id) REFERENCES rooms(id)
    ON DELETE CASCADE
    ON UPDATE CASCADE
);

CREATE TABLE IF NOT EXISTS attempts (
  id INT AUTO_INCREMENT PRIMARY KEY,
  user_id INT NOT NULL,
  room_id INT NOT NULL,
  totalreq INT NOT NULL,
  movements INT NOT NULL DEFAULT 0,
  score FLOAT NOT NULL DEFAULT 0,
  status ENUM('completed', 'abandoned') NOT NULL DEFAULT 'abandoned',
  time TIME COMMENT 'time in the format: 00:01:35',
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (room_id) REFERENCES rooms(id)
);

CREATE TABLE IF NOT EXISTS requirements (
    id INT AUTO_INCREMENT PRIMARY KEY,
    requirementText TEXT,
    isValid BOOLEAN DEFAULT FALSE,
    feedbackText TEXT,
    room_id INT NOT NULL,
    FOREIGN KEY (room_id) REFERENCES rooms(id)
      ON DELETE CASCADE
      ON UPDATE CASCADE
)