<?php include '../views/header.php'; ?>

<main>
    <nav class="main-header">
        <h1>Dashboard</h1>
        <a href="../controller/index.php?action=show_profile"><button class="header-btn">% PROFILE %</button></a>
    </nav>
    <div class="dashboard-options">
        <a href="../controller/index.php?action=show_line_tracing_tasks"><button><img src="../images/line-trace.jpg" alt=""><span class="hover-text">Line Tracing Tasks</span></button></a>
        <a href="../controller/index.php?action=show_object_to_drawing_tasks"><button><img src="../images/object-draw.jpg" alt=""><span class="hover-text">Object to Drawing Tasks</span></button></a>
        <a href="../controller/index.php?action=show_prompt_to_picture_tasks"><button><img src="../images/txt-draw.jpg" alt=""><span class="hover-text">Prompt to Picture Tasks</span></button></a>
    </div>
</main>

<?php include '../views/footer.php'; ?>