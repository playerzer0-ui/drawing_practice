<?php include '../views/header.php'; ?>

<main>
    <div class="dashboard-options">
        <a href="../controller/index.php?action=show_line_tracing_tasks"><button><img src="../images/static/line-trace.jpg" alt=""><span class="hover-text">Line Tracing Tasks</span></button></a>
        <a href="../controller/index.php?action=show_object_to_drawing_tasks"><button><img src="../images/static/object-draw.jpg" alt=""><span class="hover-text">Object to Drawing Tasks</span></button></a>
        <a href="../controller/index.php?action=show_prompt_to_picture_tasks"><button><img src="../images/static/txt-draw.jpg" alt=""><span class="hover-text">Prompt to Picture Tasks</span></button></a>
    </div>
</main>

<script src="../js/script.js" async defer></script>
<script src="../js/task.js" async defer></script>

<?php include '../views/footer.php'; ?>