<?php include '../views/header.php'; ?>

<main>
    <div class="main-header">
        <button onclick="history.back()" class="header-btn"><- BACK</button>
                <?php if ($action == "show_line_tracing_tasks"): ?>
                    <h1>Line Tracing Tasks</h1>
                <?php elseif ($action == "show_object_to_drawing_tasks"): ?>
                    <h1>Object to Drawing Tasks</h1>
                <?php endif; ?>
                <a href="../controller/index.php?action=show_profile"><button class="header-btn">% PROFILE %</button></a>
    </div>

    <div class="cards">
        <?php
        if (isset($images) && is_array($images)) {
            foreach ($images as $image) {
                $id = $image['id'];
                $url = $image['url']; ?>
                <div class="card">
                    <img src="<?php echo htmlspecialchars($url); ?>" alt="Avatar">
                    <div class="card-content">
                        <button class="btn top-left" onclick="downloadImage('<?php echo $url; ?>', '<?php echo $id; ?>')">Download</button>
                        <form
                            action="../controller/index.php?action=submit_task"
                            method="POST"
                            enctype="multipart/form-data">

                            <input type="hidden" name="task_type" value="<?= htmlspecialchars($type_of_task) ?>">
                            <input type="hidden" name="image_id" value="<?= htmlspecialchars($id) ?>">

                            <input type="file" name="image"
                                accept="image/*"
                                hidden
                                onchange="this.form.submit()">

                            <button type="button" class="btn top-right"
                                onclick="this.previousElementSibling.click()">
                                upload
                            </button>
                        </form>

                        <h2 class="bottom-id"><?php echo htmlspecialchars($id); ?></h2>
                    </div>
                </div>
        <?php }
        } else {
            echo '<p>No images to display.</p>';
        }
        ?>
    </div>
</main>

<script src="../js/task.js" async defer></script>

<?php include '../views/footer.php'; ?>