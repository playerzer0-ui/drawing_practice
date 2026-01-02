<?php

require_once "database.php";

class Task
{
    private $db;

    public function __construct($db)
    {
        $this->db = $db;
    }

    public function submitTask(
        string $id,
        string $task_type,
        string $description,
        ?string $original_image_url,
        string $image_url
    ) {
        $query = "
        INSERT INTO tasks 
        (id, task_type, description, original_image_url, image_url, userID)
        VALUES
        (:id, :task_type, :description, :original_image_url, :image_url, :userID)
    ";

        $statement = $this->db->prepare($query);
        $statement->bindValue(':id', $id);
        $statement->bindValue(':task_type', $task_type);
        $statement->bindValue(':description', $description);
        $statement->bindValue(':original_image_url', $original_image_url);
        $statement->bindValue(':image_url', $image_url);
        $statement->bindValue(':userID', $_SESSION["user_id"]);
        $statement->execute();
        $statement->closeCursor();
    }


    function removeJsonById(string $filePath, string $idToRemove): ?array
    {
        if (!file_exists($filePath)) return null;

        $data = json_decode(file_get_contents($filePath), true);
        if (!is_array($data)) return null;

        $removed = null;

        $data = array_values(array_filter($data, function ($item) use ($idToRemove, &$removed) {
            if (isset($item['id']) && $item['id'] === $idToRemove) {
                $removed = $item;
                return false;
            }
            return true;
        }));

        file_put_contents($filePath, json_encode($data, JSON_PRETTY_PRINT));

        return $removed;
    }

    public function getUsedOriginalUrls(string $userID, string $taskType): array
    {
        $stmt = $this->db->prepare("
            SELECT original_image_url
            FROM tasks
            WHERE userID = :userID
            AND task_type = :taskType
        ");
        $stmt->execute([
            ':userID' => $userID,
            ':taskType' => $taskType
        ]);

        return array_column($stmt->fetchAll(PDO::FETCH_ASSOC), 'original_image_url');
    }

}
