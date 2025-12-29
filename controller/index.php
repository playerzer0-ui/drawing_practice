<?php

    session_start();
    require_once("../model/database.php");
    require_once("../model/user.php");

    $action = filter_input(INPUT_GET, "action");
    if($action == null){
        $action = "show_login";
    }

    $user = new User($db);

    switch($action){
        case "show_login":
            $title = "Signing In";
            require_once("../views/signing.php");
            break;

        case "login":
            $email = filter_input(INPUT_POST, "email");
            $password = filter_input(INPUT_POST, "password");
            $loggedInUser = $user->login($email, $password);
            if($loggedInUser){
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
            if($newUser){
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
            $command = '..\.venv\Scripts\python.exe main.py https://ie.pinterest.com/search/pins/?q=mangab%26w pinterest_images';
            $output = shell_exec($command);

            // Decode JSON into PHP array
            $images = json_decode($output, true);
            $title = "Line Tracing Tasks";
            require_once("../views/line_trace.php");
            break;

        case "show_object_to_drawing_tasks":
            $command = '..\.venv\Scripts\python.exe main.py https://ie.pinterest.com/search/pins/?q=objects pinterest_objects';
            $output = shell_exec($command);

            // Decode JSON into PHP array
            $images = json_decode($output, true);
            $title = "Object to Drawing Tasks";
            require_once("../views/line_trace.php");
            break;

        case "show_prompt_to_picture_tasks":
            break;

        case "show_profile":
            $title = "Profile";
            require_once("../views/profile.php");
            break;

        case "logout":
            session_destroy();
            header("Location: ../controller/index.php");
            break;
    }

    function session_create($user){
        $_SESSION["user_id"] = $user['id'];
        $_SESSION["user_name"] = $user['username'];
        $_SESSION["user_email"] = $user['email'];
        $_SESSION["created_at"] = $user['created_at'];

        $_SESSION["line_tracing_tasks_completed"] = $user['line_tracing_tasks_completed'] ?? 0;
        $_SESSION["object_to_drawing_tasks_completed"] = $user['object_to_drawing_tasks_completed'] ?? 0;
        $_SESSION["prompt_to_picture_tasks_completed"] = $user['prompt_to_picture_tasks_completed'] ?? 0;
    }
?>