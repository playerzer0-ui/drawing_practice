<?php include "header.php"; ?>

<main>
    <div class="main-header">
        <button onclick="history.back()" class="header-btn"><- BACK</button>
        <h1>Prompts to Picture Tasks</h1>
        <button class="header-btn">% PROFILE %</button>
    </div>
    <ul class="cards">
        <?php for($i = 0; $i < count($prompts); $i++): ?>
            <div class="card">
                <strong>Theme:</strong> <?= htmlspecialchars($prompts[$i]["THEME:"]) ?><br>
                <strong>Item:</strong> <?= htmlspecialchars($prompts[$i]["ITEM:"]) ?><br>
                <strong>Character:</strong> <?= htmlspecialchars($prompts[$i]["CHARACTER:"]) ?><br>
                <strong>Color Palette:</strong> <?= htmlspecialchars($prompts[$i]["COLOR PALETTE:"]) ?><br>
                <strong>Mood:</strong> <?= htmlspecialchars($prompts[$i]["MOOD:"]) ?><br>
                <strong>Challenge:</strong> <?= htmlspecialchars($prompts[$i]["CHALLENGE:"]) ?>
                <div class="card-content">
                    <button class="btn top-right">upload</button>
                </div>
        </div>
        <?php endfor; ?>
    </ul>
</main>

<?php include "footer.php"; ?>