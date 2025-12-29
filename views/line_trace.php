<?php include '../views/header.php'; ?>

<main>
    <h1>Line Tracing Tasks</h1>
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