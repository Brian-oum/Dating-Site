REATE TABLE account (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    profile_photo VARCHAR(255),
    first_name VARCHAR(50),
    last_name VARCHAR(50),
    show_last_name BOOLEAN DEFAULT FALSE,
    age INT,
    location VARCHAR(100),
    gender VARCHAR(20),
    orientation VARCHAR(20),
    interests TEXT,
    bio TEXT,
    relationship_goals ENUM('casual', 'serious', 'friendship'),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
