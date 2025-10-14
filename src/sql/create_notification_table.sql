CREATE TABLE NOTIFICATION (
    notification_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    notification_type ENUM('swap_request', 'swap_approved', 'swap_rejected') NOT NULL,
    swap_request_id INT,
    notification_message TEXT NOT NULL,
    is_read BOOLEAN DEFAULT FALSE,
    notification_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES USER(user_id),
    FOREIGN KEY (swap_request_id) REFERENCES SWAP(swap_request_id) ON DELETE CASCADE
);
