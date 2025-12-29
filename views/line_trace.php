<?php include '../views/header.php'; ?>

<main>
    <div class="main-header">
        <button onclick="history.back()" class="header-btn"><- BACK</button>
        <?php if($action == "show_line_tracing_tasks"): ?>
        <h1>Line Tracing Tasks</h1>
        <?php elseif($action == "show_object_to_drawing_tasks"): ?>
        <h1>Object to Drawing Tasks</h1>
        <?php endif; ?>
        <button class="header-btn">% PROFILE %</button>
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
                            <button class="btn top-left">download</button>
                            <button class="btn top-right">upload</button>
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

<?php include '../views/footer.php'; ?>