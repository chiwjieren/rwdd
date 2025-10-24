CREATE TABLE IF NOT EXISTS ADMIN_LOG (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    admin_username VARCHAR(100) NOT NULL,
    action_type ENUM('CREATE', 'UPDATE', 'DELETE') NOT NULL,
    table_name VARCHAR(50) NOT NULL,
    record_id INT,
    action_details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
