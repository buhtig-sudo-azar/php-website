<?php
$siteName = "My Website";
$message = "";
$messageType = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $name    = trim($_POST["name"] ?? "");
    $email   = trim($_POST["email"] ?? "");
    $subject = trim($_POST["subject"] ?? "");
    $body    = trim($_POST["body"] ?? "");

    if (empty($name) || empty($email) || empty($body)) {
        $message = "Please fill in all required fields.";
        $messageType = "error";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "Please enter a valid email address.";
        $messageType = "error";
    } else {
        // In production, replace this with actual mail() or SMTP sending.
        $message = "Thank you, " . htmlspecialchars($name) . "! We will get back to you soon.";
        $messageType = "success";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact | <?php echo htmlspecialchars($siteName); ?></title>
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
        <h2>Contact Us</h2>

        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="contact.php">
            <label for="name">Name <span>*</span></label>
            <input type="text" id="name" name="name"
                   value="<?php echo htmlspecialchars($_POST['name'] ?? ''); ?>"
                   required>

            <label for="email">Email <span>*</span></label>
            <input type="email" id="email" name="email"
                   value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>"
                   required>

            <label for="subject">Subject</label>
            <input type="text" id="subject" name="subject"
                   value="<?php echo htmlspecialchars($_POST['subject'] ?? ''); ?>">

            <label for="body">Message <span>*</span></label>
            <textarea id="body" name="body" rows="6" required><?php echo htmlspecialchars($_POST['body'] ?? ''); ?></textarea>

            <button type="submit">Send Message</button>
        </form>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> <?php echo htmlspecialchars($siteName); ?></p>
    </footer>
</body>
</html>
