<?php
session_start();

$siteName = "My Website";

// Hardcoded credentials — replace with DB lookup + password_hash in production.
define("ADMIN_USER", "admin");
define("ADMIN_PASS_HASH", password_hash("changeme123", PASSWORD_BCRYPT));

$error = "";

// Handle logout
if (isset($_GET["logout"])) {
    session_destroy();
    header("Location: admin.php");
    exit;
}

// Handle login form submission
if ($_SERVER["REQUEST_METHOD"] === "POST" && !isset($_SESSION["admin"])) {
    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if ($username === ADMIN_USER && password_verify($password, ADMIN_PASS_HASH)) {
        $_SESSION["admin"] = true;
        $_SESSION["admin_user"] = $username;
        header("Location: admin.php");
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}

$isLoggedIn = isset($_SESSION["admin"]) && $_SESSION["admin"] === true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | <?php echo htmlspecialchars($siteName); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($siteName); ?> — Admin</h1>
        <?php if ($isLoggedIn): ?>
            <nav>
                <span>Logged in as <strong><?php echo htmlspecialchars($_SESSION["admin_user"]); ?></strong></span>
                <a href="admin.php?logout=1" style="margin-left:1.5rem;">Logout</a>
            </nav>
        <?php endif; ?>
    </header>

    <main>
        <?php if (!$isLoggedIn): ?>
            <h2>Admin Login</h2>

            <?php if ($error): ?>
                <div class="message error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="admin.php">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required autocomplete="username">

                <label for="password">Password</label>
                <input type="password" id="password" name="password" required autocomplete="current-password">

                <button type="submit">Login</button>
            </form>

        <?php else: ?>
            <h2>Dashboard</h2>
            <p>Welcome back, <strong><?php echo htmlspecialchars($_SESSION["admin_user"]); ?></strong>.</p>
            <p>This is the admin area. Add your management features here.</p>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> <?php echo htmlspecialchars($siteName); ?></p>
    </footer>
</body>
</html>
