<?php include '../views/header.php'; ?>

<main class="container">
    <div class="scene">
        <div class="form-container">
            <!-- Login Form (Front) -->
            <form class="form login-form" action="../controller/index.php?action=login" method="post">
                <p><?php echo $error_message ?? ''; ?></p>
                <h1>Login</h1>
                <label for="login-email">Email:</label>
                <input type="text" id="login-email" name="email" required>
                <br>
                <label for="login-password">Password:</label>
                <input type="password" id="login-password" name="password" required>
                <br>
                <button type="submit">Login</button>
                <p class="toggle-text">
                    Don't have an account?
                    <a href="#" class="toggle-btn">Register here</a>
                </p>
            </form>

            <!-- Register Form (Back) -->
            <form class="form register-form" action="../controller/index.php?action=register" method="post">
                <h1>Register</h1>
                <label for="reg-username">Username:</label>
                <input type="text" id="reg-username" name="username" required>
                <br>
                <label for="reg-email">Email:</label>
                <input type="text" id="reg-email" name="email" required>
                <br>
                <label for="reg-password">Password:</label>
                <input type="password" id="reg-password" name="password" required>
                <br>
                <button type="submit">Register</button>
                <p class="toggle-text">
                    Already have an account?
                    <a href="#" class="toggle-btn">Login here</a>
                </p>
            </form>
        </div>
    </div>
</main>

<script src="../js/script.js"></script>

<?php include '../views/footer.php'; ?>