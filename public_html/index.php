<?php
// Home page
$siteName = "My Website";
$pageTitle = "Welcome";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle . " | " . $siteName); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($siteName); ?></h1>
        <nav>
            <a href="index.php">Home</a>
            <a href="contact.php">Contact</a>
        </nav>
    </header>

    <main>
        <h2>Welcome to our site</h2>
        <p>This is the home page of the website.</p>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> <?php echo htmlspecialchars($siteName); ?></p>
    </footer>
</body>
</html>
