CREATE TABLE USER (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    user_name VARCHAR(100) NOT NULL,
    user_subscribe BOOLEAN DEFAULT FALSE,
    user_password VARCHAR(255) NOT NULL,
    user_email VARCHAR(150) UNIQUE NOT NULL,
    user_profile_image VARCHAR(255)
);