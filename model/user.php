<?php 

class User {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function getUserById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getUserByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function login($email, $password) {
        $user = $this->getUserByEmail($email);
        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        return null;
    }

    public function updateCounter($userID, $taskType) {
        $columnMap = [
            'line_tracing' => 'line_trace_tasks_completed',
            'object_to_drawing' => 'object_to_drawing_tasks_completed',
            'prompt_to_picture' => 'prompt_to_picture_tasks_completed'
        ];

        if (!array_key_exists($taskType, $columnMap)) {
            throw new InvalidArgumentException("Invalid task type: $taskType");
        }

        $column = $columnMap[$taskType];

        $stmt = $this->db->prepare("UPDATE users SET $column = $column + 1 WHERE id = ?");
        $stmt->execute([$userID]);
    }

    public function createUser($username, $email, $password) {
        $passwordHash = password_hash($password, PASSWORD_BCRYPT);

        // Generate UUID v4 in PHP (no MySQL functions)
        $uuid = bin2hex(random_bytes(16));
        $uuid = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split($uuid, 4));

        // Insert user
        $stmt = $this->db->prepare("
            INSERT INTO users (id, username, email, password_hash)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$uuid, $username, $email, $passwordHash]);

        // Fetch the newly created user
        $stmt = $this->db->prepare("
            SELECT 
                id,
                username,
                email,
                created_at,
                line_trace_tasks_completed,
                object_to_drawing_tasks_completed,
                prompt_to_picture_tasks_completed
            FROM users
            WHERE id = ?
            LIMIT 1
        ");
        $stmt->execute([$uuid]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

}

?>