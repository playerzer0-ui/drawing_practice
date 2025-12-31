<?php include "header.php"; ?>

<main>
    <ul class="cards">
        <?php for ($i = 0; $i < count($prompts); $i++): ?>
            <div class="card">
                <strong>ID:</strong> <?= htmlspecialchars($prompts[$i]["id"]) ?><br>
                <strong>Theme:</strong> <?= htmlspecialchars($prompts[$i]["THEME:"]) ?><br>
                <strong>Item:</strong> <?= htmlspecialchars($prompts[$i]["ITEM:"]) ?><br>
                <strong>Character:</strong> <?= htmlspecialchars($prompts[$i]["CHARACTER:"]) ?><br>
                <strong>Color Palette:</strong> <?= htmlspecialchars($prompts[$i]["COLOR PALETTE:"]) ?><br>
                <strong>Mood:</strong> <?= htmlspecialchars($prompts[$i]["MOOD:"]) ?><br>
                <strong>Challenge:</strong> <?= htmlspecialchars($prompts[$i]["CHALLENGE:"]) ?>
                <div class="card-content">
                    <form
                        action="../controller/index.php?action=submit_task"
                        method="POST"
                        enctype="multipart/form-data"
                        >

                        <input type="hidden" name="task_type" value="<?= htmlspecialchars($type_of_task) ?>">
                        
                        <!-- PROMPT DATA -->
                        <input type="hidden" name="image_id" value="<?= htmlspecialchars($prompts[$i]["id"]) ?>">
                        <input type="hidden" name="theme" value="<?= htmlspecialchars($prompts[$i]["THEME:"]) ?>">
                        <input type="hidden" name="item" value="<?= htmlspecialchars($prompts[$i]["ITEM:"]) ?>">
                        <input type="hidden" name="character" value="<?= htmlspecialchars($prompts[$i]["CHARACTER:"]) ?>">
                        <input type="hidden" name="palette" value="<?= htmlspecialchars($prompts[$i]["COLOR PALETTE:"]) ?>">
                        <input type="hidden" name="mood" value="<?= htmlspecialchars($prompts[$i]["MOOD:"]) ?>">
                        <input type="hidden" name="challenge" value="<?= htmlspecialchars($prompts[$i]["CHALLENGE:"]) ?>">

                        <input type="file" name="image" accept="image/*" hidden>

                        <button type="button" class="btn top-right"
                            onclick="$(this).prev('input[type=file]').click()">
                            upload
                        </button>
                    </form>
                </div>
            </div>
        <?php endfor; ?>
    </ul>
</main>

<script src="../js/script.js" async defer></script>
<script src="../js/task.js" async defer></script>

<?php include "footer.php"; ?>