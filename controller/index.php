<?php

    session_start();
    $action = filter_input(INPUT_GET, "action");
    if($action == null){
        $action = "show_login";
    }

    switch($action){
        case "show_login":
            $title = "Signing In";
            require_once("../views/signing.php");
            break;
        case "login":
            print_r("dssadasdasdasd");
            break;
        case "register":
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
        case "logout":
            session_destroy();
            header("Location: controller/index.php");
            break;
    }
?>