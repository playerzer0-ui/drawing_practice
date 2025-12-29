<?php include '../views/header.php'; ?>

<main>
    <nav class="main-header">
        <button onclick="history.back()" class="header-btn"><- BACK</button>
        <h1>---______----^v^---__~_~_`-`--`</h1>
        <h1>__--_0-)-_-)_0--_#_)_@)_!)#$(*!)$</h1>
    </nav>
    
    <div class="profile-info">
        <h1>User Profile</h1>
        <p><?php echo $_SESSION["user_id"]; ?></p>
        <p>Name: <?php echo $_SESSION["user_name"]; ?></p>
        <p>Email: <?php echo $_SESSION["user_email"]; ?></p>
        <p>Member since: <?php echo $_SESSION["created_at"]; ?></p>
        <a href="../controller/index.php?action=logout"><button class="header-btn">Logout</button></a>
    </div>
</main>


<?php include '../views/footer.php'; ?>