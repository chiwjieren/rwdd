CREATE TABLE ITEM (
    item_id INT AUTO_INCREMENT PRIMARY KEY,
    item_name VARCHAR(100) NOT NULL,
    item_description TEXT,
    item_image VARCHAR(255),
    user_id INT,
    FOREIGN KEY (user_id) REFERENCES USER(user_id)
);