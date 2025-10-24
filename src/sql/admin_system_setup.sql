-- Admin CRUD System Database Setup
-- Run this script to create all necessary tables for the admin system

-- Events Table
CREATE TABLE IF NOT EXISTS EVENT (
    event_id INT AUTO_INCREMENT PRIMARY KEY,
    event_title VARCHAR(255) NOT NULL,
    event_description TEXT,
    event_date DATE NOT NULL,
    event_time TIME NOT NULL,
    event_location VARCHAR(255),
    event_image VARCHAR(255),
    created_by VARCHAR(100) DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Newsletter Subscribers Table
CREATE TABLE IF NOT EXISTS NEWSLETTER_SUBSCRIBER (
    subscriber_id INT AUTO_INCREMENT PRIMARY KEY,
    subscriber_email VARCHAR(255) NOT NULL UNIQUE,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Quiz Questions Table
CREATE TABLE IF NOT EXISTS QUIZ_QUESTION (
    question_id INT AUTO_INCREMENT PRIMARY KEY,
    question_text TEXT NOT NULL,
    option_a VARCHAR(255) NOT NULL,
    option_b VARCHAR(255) NOT NULL,
    option_c VARCHAR(255) NOT NULL,
    option_d VARCHAR(255) NOT NULL,
    correct_answer CHAR(1) NOT NULL CHECK (correct_answer IN ('A', 'B', 'C', 'D')),
    category VARCHAR(50) DEFAULT 'general',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admin Activity Log Table
CREATE TABLE IF NOT EXISTS ADMIN_LOG (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    admin_username VARCHAR(100) NOT NULL,
    action_type ENUM('CREATE', 'UPDATE', 'DELETE') NOT NULL,
    table_name VARCHAR(50) NOT NULL,
    record_id INT,
    action_details TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert some sample quiz questions
INSERT INTO QUIZ_QUESTION (question_text, option_a, option_b, option_c, option_d, correct_answer, category) VALUES
('What percentage of plastic waste is actually recycled globally?', 'Less than 10%', 'About 25%', 'Around 50%', 'More than 75%', 'A', 'recycling'),
('Which of the following uses the most household energy?', 'Lighting', 'Heating and cooling', 'Refrigeration', 'Electronics', 'B', 'energy'),
('How long does it take for a plastic bottle to decompose?', '50 years', '100 years', '450 years', '1000 years', 'C', 'general'),
('What is the most effective way to reduce your carbon footprint?', 'Recycling more', 'Using LED bulbs', 'Reducing meat consumption', 'Driving less', 'C', 'climate'),
('Which country produces the most renewable energy?', 'Germany', 'China', 'United States', 'Iceland', 'B', 'energy');

-- Create events directory
-- Note: Directory creation must be done manually or via PHP
-- Path: ../media/events/

SELECT 'Admin system tables created successfully!' as Status;
