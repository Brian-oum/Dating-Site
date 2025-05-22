REATE TABLE account (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    account_id INT NOT NULL,
    profile_photo VARCHAR(255),
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    show_last_name BOOLEAN DEFAULT 0,
    age INT,
    location VARCHAR(100),
    gender VARCHAR(10),
    bio TEXT,
    FOREIGN KEY (account_id) REFERENCES account(id) ON DELETE CASCADE
);
-- User preferences table
CREATE TABLE preferences (
    user_id INT PRIMARY KEY,
    min_age INT DEFAULT 18,
    max_age INT DEFAULT 99,
    gender_preference VARCHAR(50),
    location_preference VARCHAR(100),
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Match table
CREATE TABLE matches (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user1_id INT NOT NULL,
    user2_id INT NOT NULL,
    match_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    user1_liked BOOLEAN DEFAULT NULL,
    user2_liked BOOLEAN DEFAULT NULL,
    is_mutual BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (user1_id) REFERENCES users(id),
    FOREIGN KEY (user2_id) REFERENCES users(id),
    UNIQUE KEY unique_match (user1_id, user2_id)
);

-- User likes/dislikes
CREATE TABLE user_actions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    target_user_id INT NOT NULL,
    action ENUM('like', 'dislike', 'superlike') NOT NULL,
    action_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id),
    FOREIGN KEY (target_user_id) REFERENCES users(id),
    UNIQUE KEY unique_action (user_id, target_user_id)
);