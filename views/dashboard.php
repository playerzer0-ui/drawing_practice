<?php include '../views/header.php'; ?>

<main>
    <div class="row">
        <div class="profile-info">
            <h1><?php echo $_SESSION["user_name"]; ?></h1>
            <p><?php echo $_SESSION["user_id"]; ?></p>
            <p>Email: <?php echo $_SESSION["user_email"]; ?></p>
            <p>Member since: <?php echo $_SESSION["created_at"]; ?></p>
            <a href="../controller/index.php?action=logout"><button class="header-btn">Logout</button></a>
        </div>
        <div class="dashboard-options">
            <a href="../controller/index.php?action=show_line_tracing_tasks"><button><img src="../images/static/line-trace.jpg" alt=""><span class="hover-text">Line Tracing Tasks</span></button></a>
            <a href="../controller/index.php?action=show_object_to_drawing_tasks"><button><img src="../images/static/object-draw.jpg" alt=""><span class="hover-text">Object to Drawing Tasks</span></button></a>
            <a href="../controller/index.php?action=show_prompt_to_picture_tasks"><button><img src="../images/static/txt-draw.jpg" alt=""><span class="hover-text">Prompt to Picture Tasks</span></button></a>
        </div>
    </div>
    <div class="row">
        <div>
            <table>
                <tr>
                    <th>Day</th>
                    <th>Line Tracing</th>
                    <th>Object → Drawing</th>
                    <th>Prompt → Picture</th>
                </tr>

                <?php foreach ($weeklyMatrix as $day => $tasks): ?>
                    <tr>
                        <td><?= $day ?></td>
                        <td><?= $tasks['line_tracing'] ?></td>
                        <td><?= $tasks['object_to_drawing'] ?></td>
                        <td><?= $tasks['prompt_to_picture'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </table>
        </div>
        <div>
            <canvas id="myChart" width="750" height="280"></canvas>
        </div>
    </div>
</main>

<script src="../js/script.js" async defer></script>
<script src="../js/task.js" async defer></script>
<script src="../js/progress.js" async defer></script>

<?php include '../views/footer.php'; ?>