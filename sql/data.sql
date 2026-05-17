CREATE TABLE users (
	user_id INT AUTO_INCREMENT PRIMARY KEY,
	username VARCHAR(50),
	first_name VARCHAR(50),
	last_name VARCHAR(50),
	date_of_birth DATE,
	password VARCHAR(255),
	date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE customers (
    customer_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR (50),
    first_name VARCHAR (50),
    last_name VARCHAR (50),
    membership_status VARCHAR (50),
    added_by VARCHAR(50),
    updated_by VARCHAR(50),
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE rental_sessions (
    session_id INT AUTO_INCREMENT PRIMARY KEY,
    pc_number VARCHAR (50),
    hours_rented INT,
    customer_id INT,
    added_by VARCHAR(50),
    updated_by VARCHAR(50),
    date_added TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_updated TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

CREATE TABLE activity_logs (
    log_id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50),
    action_type VARCHAR(50),
    entity_type VARCHAR(50),
    record_id INT,
    field_changed VARCHAR(255),
    old_value VARCHAR(255),
    new_value VARCHAR(255),
    date_logged TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
