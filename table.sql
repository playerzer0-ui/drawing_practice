CREATE TABLE users (
    id char(36) PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    line_trace_tasks_completed INT DEFAULT 0,
    object_to_drawing_tasks_completed INT DEFAULT 0,
    prompt_to_picture_tasks_completed INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE tasks (
    id char(36) PRIMARY KEY,
    task_type VARCHAR(50) NOT NULL,
    description TEXT,
    image_url TEXT,
    userID char(36),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (userID) REFERENCES users(id)
);


