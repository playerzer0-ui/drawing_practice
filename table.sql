CREATE TABLE users (
    id char(36) PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    email VARCHAR(100) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE tasks (
    id char(60) PRIMARY KEY,
    task_type VARCHAR(50) NOT NULL,
    description TEXT,
    original_image_url TEXT,
    image_url TEXT,
    userID char(36),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (userID) REFERENCES users(id)
);

CREATE INDEX idx_tasks_user_created
ON tasks (userID, created_at);

SELECT *
FROM tasks
WHERE userID = "db27a5dc-e0bd-acfe-4186-0b0d2cfe4be2"
  AND created_at >= (
        CURDATE() - INTERVAL (DAYOFWEEK(CURDATE()) - 1) DAY
      )
  AND created_at < (
        CURDATE() - INTERVAL (DAYOFWEEK(CURDATE()) - 1) DAY
        + INTERVAL 7 DAY
      );

