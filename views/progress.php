<?php include "header.php"; ?>

<main>
    <section class="gallery">
        <?php
        $currentYear = date("Y");
        $selectedYear = isset($_GET['year']) ? $_GET['year'] : $currentYear;
        ?>

        <form action="../controller/index.php" method="get" id="year-form">
            <input type="hidden" name="action" value="show_progress">
            <label for="year-select">Select Year:</label>
            <select id="year-select" name="year">
                <?php
                for ($year = $currentYear; $year >= $currentYear - 5; $year--) {
                    $selected = ($year == $selectedYear) ? 'selected' : '';
                    echo "<option value=\"$year\" $selected>$year</option>";
                }
                ?>
            </select>
            <button class="header-btn" type="submit">Go</button>
        </form>

        <a target="_blank" href="../controller/index.php?action=get_PDF&year=<?= $selectedYear ?>">
            <button class="header-btn">Download PDF</button>
        </a>

        <?php
        $currentMonth = null;
        $monthIndex = 0;

        foreach ($tasks as $task) {

            if ($currentMonth !== $task['month_year']) {

                // Close previous month
                if ($currentMonth !== null) {
                    echo "</div></div>";
                }

                $currentMonth = $task['month_year'];
                $monthIndex++;
        ?>

                <div class="month-group">
                    <button class="month-header" data-target="month-<?= $monthIndex ?>">
                        <?= htmlspecialchars($currentMonth) ?>
                        <span class="chevron">▾</span>
                    </button>

                    <div class="month-content" id="month-<?= $monthIndex ?>">
                    <?php
                }
                    ?>

                    <div class="task-card">
                        <div class="task-meta">
                            <span class="task-type"><?= $task['task_label'] ?></span>
                            <span class="task-date"><?= $task['day_label'] ?></span>
                        </div>

                        <div class="image-pair">
                            <?php if ($task['original_image_url']) { ?>
                                <img src="<?= $task['original_image_url'] ?>" alt="Original">
                            <?php } else {
                                $parts = explode("$$", $task['description']);
                                foreach ($parts as $part) {
                                    echo "<h4>" . htmlspecialchars($part) . "</h4>";
                                }
                            } ?>
                            <img src="../<?= $task['image_url'] ?>" alt="Your drawing">
                        </div>
                    </div>

                <?php } ?>

                <?php if ($currentMonth !== null) echo "</div></div>"; ?>

    </section>

</main>

<script>
$(document).ready(function() {
    $(".month-header").on("click", function() {
        const targetId = $(this).data("target");
        const content = $("#" + targetId);
        
        // Close other months
        $(".month-content").not(content).removeClass("open");
        $(".month-header").not(this).removeClass("active");
        
        // Toggle current month
        content.toggleClass("open");
        $(this).toggleClass("active");
    });
});
</script>


<?php include "footer.php"; ?>