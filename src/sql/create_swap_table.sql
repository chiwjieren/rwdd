CREATE TABLE SWAP (
  swap_request_id INT PRIMARY KEY AUTO_INCREMENT,
  swap_sender_id INT NOT NULL,
  swap_receiver_id INT NOT NULL,
  swap_sender_item_id INT NOT NULL,
  swap_receiver_item_id INT NOT NULL,
  swap_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
  swap_created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (swap_sender_id) REFERENCES USER(user_id),
  FOREIGN KEY (swap_receiver_id) REFERENCES USER(user_id),
  FOREIGN KEY (swap_sender_item_id) REFERENCES ITEM(item_id),
  FOREIGN KEY (swap_receiver_item_id) REFERENCES ITEM(item_id)
);
