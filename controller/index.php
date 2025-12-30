<?php

session_start();
require_once("../model/database.php");
require_once("../model/user.php");
require_once("../model/task.php");

$action = filter_input(INPUT_GET, "action");
if ($action == null) {
    $action = "show_login";
}
if (!isset($_SESSION["user_id"])) {
    $action = "show_login";
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
        require_once("../views/dashboard.php");
        break;

    case "show_line_tracing_tasks":
        $command = '..\.venv\Scripts\python.exe main.py https://ie.pinterest.com/search/pins/?q=mangab%26w ' . $_SESSION["user_name"] . '_pinterest_images';
        $output = shell_exec($command);

        // Decode JSON into PHP array
        $images = json_decode($output, true);
        $title = "Line Tracing Tasks";
        $type_of_task = "line_tracing";
        require_once("../views/line_trace.php");
        break;

    case "show_object_to_drawing_tasks":
        $command = '..\.venv\Scripts\python.exe main.py https://ie.pinterest.com/search/pins/?q=objects ' . $_SESSION["user_name"] . '_pinterest_images';
        $output = shell_exec($command);

        // Decode JSON into PHP array
        $images = json_decode($output, true);
        $title = "Object to Drawing Tasks";
        $type_of_task = "object_to_drawing";
        require_once("../views/line_trace.php");
        break;

    case "show_prompt_to_picture_tasks":
        $command = '..\.venv\Scripts\python.exe prompt.py ' . $_SESSION["user_name"];
        $output = shell_exec($command);

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
        //userID + image id
        $image_id  = $_SESSION['user_id'] . "__" . filter_input(INPUT_POST, "image_id");
        $userID    = $_SESSION['user_id'];

        $year = date("Y");

        // Build folders
       $baseDir = dirname(__DIR__) . "/images/$userID/$year";
        if (!is_dir($baseDir)) {
            mkdir($baseDir, 0777, true);
        }

        // File info
        $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $filename = $image_id . "." . strtolower($ext);
        $targetPath = "$baseDir/$filename";

        move_uploaded_file($_FILES['image']['tmp_name'], $targetPath);

        // Relative path for DB
        $image_url = "images/$userID/$year/$filename";

        // Save task
        $task = new Task($db);
        $task->submitTask(
            $image_id,
            $task_type,
            "User submission",
            $image_url
        );

        //update counter
        $user->updateCounter($userID, $task_type);
        //remove json entry
        if($task_type === "prompt_to_picture"){
            $filePath = dirname(__DIR__) . "/cache/".$_SESSION["user_name"]."_prompts.json";
        } 
        else if($task_type === "line_tracing"){
            $filePath = dirname(__DIR__) . "/cache/".$_SESSION["user_name"]."_pinterest_images.json";
        }
        else{
            $filePath = dirname(__DIR__) . "/cache/".$_SESSION["user_name"]."_objects_images.json";
        }
        $task->removeJsonById($filePath, filter_input(INPUT_POST, "image_id"));

        header("Location: ../controller/index.php?action=show_" . $task_type . "_tasks");
        exit;


    case "show_profile":
        $title = "Profile";
        require_once("../views/profile.php");
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
