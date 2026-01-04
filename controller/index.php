<?php

session_start();
require_once("../model/database.php");
require_once("../model/user.php");
require_once("../model/task.php");

$action = filter_input(INPUT_GET, "action") ?? "show_login";
$publicActions = ["show_login", "login", "register"];

if (!isset($_SESSION["user_id"]) && !in_array($action, $publicActions)) {
    $action = "show_login";
}

if (isset($_SESSION["user_id"])) {
    $lineCommand = '..\.venv\Scripts\python.exe main.py https://ie.pinterest.com/search/pins/?q=mangab%26w '
        . $_SESSION["user_name"] . '_pinterest_images';

    $objectCommand = '..\.venv\Scripts\python.exe main.py https://ie.pinterest.com/search/pins/?q=objects '
        . $_SESSION["user_name"] . '_pinterest_objects';

    $promptCommand = '..\.venv\Scripts\python.exe prompt.py '
        . $_SESSION["user_name"];
}

$user = new User($db);
$task = new Task($db);


switch ($action) {
    case "show_login":
        $title = "Signing In";
        require_once("../views/signing.php");
        break;

    case "login":
        $email = filter_input(INPUT_POST, "email");
        $password = filter_input(INPUT_POST, "password");
        $loggedInUser = $user->login($email, $password);
        if ($loggedInUser) {
            session_create($loggedInUser);
            header("Location: ../controller/index.php?action=show_dashboard");
        } else {
            $error_message = "Invalid email or password.";
            require_once("../views/signing.php");
        }
        break;

    case "register":
        $username = filter_input(INPUT_POST, "username");
        $email = filter_input(INPUT_POST, "email");
        $password = filter_input(INPUT_POST, "password");
        $newUser = $user->createUser($username, $email, $password);
        if ($newUser) {
            session_create($newUser);
            header("Location: ../controller/index.php?action=show_login");
        } else {
            $error_message = "Registration failed. Please try again.";
            require_once("../views/signing.php");
        }
        break;

    case "show_dashboard":
        $title = "Dashboard";
        $weeklyMatrix = $task->getWeeklyTaskMatrix($_SESSION['user_id']);
        require_once("../views/dashboard.php");
        break;

    case "show_line_tracing_tasks":
        $output = shell_exec($lineCommand);
        $images = json_decode($output, true) ?? [];

        // Fetch already-used images
        $usedUrls = $task->getUsedOriginalUrls(
            $_SESSION['user_id'],
            'line_tracing'
        );

        // Filter them out
        $images = array_values(array_filter($images, function ($img) use ($usedUrls) {
            return !in_array($img['url'], $usedUrls, true);
        }));

        // OPTIONAL: shuffle for randomness
        shuffle($images);
        $title = "Line Tracing Tasks";
        $type_of_task = "line_tracing";
        require_once("../views/line_trace.php");
        break;

    case "show_object_to_drawing_tasks":
        $output = shell_exec($objectCommand);
        $images = json_decode($output, true) ?? [];

        // Fetch already-used images
        $usedUrls = $task->getUsedOriginalUrls(
            $_SESSION['user_id'],
            'object_to_drawing'
        );

        // Filter them out
        $images = array_values(array_filter($images, function ($img) use ($usedUrls) {
            return !in_array($img['url'], $usedUrls, true);
        }));

        // OPTIONAL: shuffle for randomness
        shuffle($images);

        $title = "Object to Drawing Tasks";
        $type_of_task = "object_to_drawing";
        require_once("../views/line_trace.php");
        break;


    case "show_prompt_to_picture_tasks":
        $output = shell_exec($promptCommand);

        $prompts = json_decode($output, true);

        $title = "Prompt to Picture Tasks";
        $type_of_task = "prompt_to_picture";
        require_once("../views/prompts.php");
        break;

    case "submit_task":
        if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
            die("Upload failed");
        }

        $task_type = filter_input(INPUT_POST, "task_type");
        $userID    = $_SESSION['user_id'];
        $task_id   = uuidv4();
        $year      = date("Y");

        $original_image_url = filter_input(INPUT_POST, "original_image_url", FILTER_SANITIZE_URL);

        // Build folders
        $baseDir = dirname(__DIR__) . "/images/$userID/$year";
        if (!is_dir($baseDir)) {
            mkdir($baseDir, 0777, true);
        }

        // File info
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $filename = $task_id . "." . $ext;
        $targetPath = "$baseDir/$filename";

        move_uploaded_file($_FILES['image']['tmp_name'], $targetPath);

        // Relative path for DB
        $image_url = "images/$userID/$year/$filename";

        // Description handling
        if ($task_type === "prompt_to_picture") {
            $description = implode("$$", [
                "THEME:" . filter_input(INPUT_POST, "theme"),
                "ITEM:" . filter_input(INPUT_POST, "item"),
                "CHARACTER:" . filter_input(INPUT_POST, "character"),
                "COLOR PALETTE:" . filter_input(INPUT_POST, "palette"),
                "MOOD:" . filter_input(INPUT_POST, "mood"),
                "CHALLENGE:" . filter_input(INPUT_POST, "challenge"),
            ]);
        } else {
            $description = "User submission";
        }

        // Save task
        $task->submitTask(
            $task_id,
            $task_type,
            $description,
            $original_image_url,
            $image_url
        );

        // remove cache entry (still uses source image ID)
        $sourceImageId = filter_input(INPUT_POST, "image_id");

        if ($task_type === "prompt_to_picture") {
            $filePath = dirname(__DIR__) . "/cache/{$_SESSION["user_name"]}_prompts_cache.json";
            ensureCacheFresh($filePath, $promptCommand);
        } elseif ($task_type === "line_tracing") {
            $filePath = dirname(__DIR__) . "/cache/{$_SESSION["user_name"]}_pinterest_images.json";
            ensureCacheFresh($filePath, $lineCommand);
        } else {
            $filePath = dirname(__DIR__) . "/cache/{$_SESSION["user_name"]}_pinterest_objects.json";
            ensureCacheFresh($filePath, $objectCommand);
        }

        $task->removeJsonById($filePath, $sourceImageId);

        header("Location: ../controller/index.php?action=show_" . $task_type . "_tasks");
        break;

    case "get_year_task_matrix":
        $year = filter_input(INPUT_GET, "year", FILTER_VALIDATE_INT) ?? date("Y");
        $matrix = $task->getYearTaskMatrix($_SESSION['user_id'], $year);
        header('Content-Type: application/json');
        echo json_encode($matrix);
        break;

    case "show_loading":
        $title = "Loading...";
        require_once("../views/loading.php");
        break;

    case "logout":
        session_destroy();
        header("Location: ../controller/index.php");
        break;
}

function session_create($user)
{
    $_SESSION["user_id"] = $user['id'];
    $_SESSION["user_name"] = $user['username'];
    $_SESSION["user_email"] = $user['email'];
    $_SESSION["created_at"] = $user['created_at'];

    $_SESSION["line_tracing_tasks_completed"] = $user['line_tracing_tasks_completed'] ?? 0;
    $_SESSION["object_to_drawing_tasks_completed"] = $user['object_to_drawing_tasks_completed'] ?? 0;
    $_SESSION["prompt_to_picture_tasks_completed"] = $user['prompt_to_picture_tasks_completed'] ?? 0;
}

function uuidv4(): string
{
    return sprintf(
        '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
        random_int(0, 0xffff),
        random_int(0, 0xffff),
        random_int(0, 0xffff),
        random_int(0, 0x0fff) | 0x4000,
        random_int(0, 0x3fff) | 0x8000,
        random_int(0, 0xffff),
        random_int(0, 0xffff),
        random_int(0, 0xffff)
    );
}

function ensureCacheFresh(
    string $jsonPath,
    string $pythonCommand,
    bool $forceRefresh = false
): array {

    $needsRefresh = $forceRefresh;

    if (!$forceRefresh) {
        // Only auto-check if not forcing
        if (!file_exists($jsonPath)) {
            $needsRefresh = true;
        } else {
            $content = trim(file_get_contents($jsonPath));

            if ($content === '') {
                $needsRefresh = true;
            } else {
                $decoded = json_decode($content, true);
                if (!is_array($decoded) || count($decoded) === 0) {
                    $needsRefresh = true;
                }
            }
        }
    }

    if ($needsRefresh) {
        if (file_exists($jsonPath)) {
            unlink($jsonPath);
        }

        shell_exec($pythonCommand);

        if (!file_exists($jsonPath)) {
            throw new Exception("Cache refresh failed: $jsonPath not created");
        }

        $decoded = json_decode(file_get_contents($jsonPath), true);

        if (!is_array($decoded)) {
            throw new Exception("Cache refresh failed: invalid JSON");
        }

        return $decoded;
    }

    // No refresh needed
    return json_decode(file_get_contents($jsonPath), true);
}
