<!DOCTYPE html>

<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <title><?php echo $title; ?></title>
        <meta name="description" content="">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100..900;1,100..900&display=swap" rel="stylesheet">
        <script src="https://cdn.jsdelivr.net/npm/chart.js@4.5.1/dist/chart.umd.min.js"></script>
        <link rel="stylesheet" href="../css/styles.css">
        <link rel="stylesheet" href="../css/loader.css">
    </head>
    <body>
        <nav class="main-header">
            <h1><?php echo $title; ?></h1>
            <a href="../controller/index.php?action=show_dashboard"><button class="header-btn">* Dashboard *</button></a>
            <a href="../controller/index.php?action=show_line_tracing_tasks"><button class="header-btn">& Line Tracing Tasks &</button></a>
            <a href="../controller/index.php?action=show_object_to_drawing_tasks"><button class="header-btn">$ Object to Drawing Tasks $</button></a>
            <a href="../controller/index.php?action=show_prompt_to_picture_tasks"><button class="header-btn"># Prompt to Picture Tasks #</button></a>
            <a href="../controller/index.php?action=show_profile"><button class="header-btn">% PROFILE %</button></a>
        </nav>
        <div class="loader-UI" id="loader">
            <h1>Loading...</h1>
            <div class="loader"></div>
        </div>