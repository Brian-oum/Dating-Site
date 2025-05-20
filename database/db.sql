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
