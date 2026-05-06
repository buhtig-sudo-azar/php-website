<?php
session_start();

$jsonPath = __DIR__ . '/home-data.json';
$defaultData = [
    'siteName' => 'My Website',
    'pageTitle' => 'Welcome',
    'mainHeading' => 'Welcome to our site',
    'mainText' => 'This is the home page of the website.',
    'blocks' => [
        [
            'title' => 'Feature One',
            'text' => 'Describe a feature or element here.'
        ],
        [
            'title' => 'Feature Two',
            'text' => 'Add another block for the home page.'
        ]
    ]
];

$homeData = $defaultData;
if (file_exists($jsonPath)) {
    $loaded = json_decode(file_get_contents($jsonPath), true);
    if (is_array($loaded)) {
        $homeData = array_merge($defaultData, $loaded);
    }
}

// Hardcoded credentials — replace with DB lookup + password_hash in production.
define("ADMIN_USER", "admin");
define("ADMIN_PASS_HASH", password_hash("changeme123", PASSWORD_BCRYPT));

$error = "";
$message = "";

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

// Handle home page update
if ($_SERVER["REQUEST_METHOD"] === "POST" && $isLoggedIn) {
    $siteName = trim($_POST['siteName'] ?? $homeData['siteName']);
    $pageTitle = trim($_POST['pageTitle'] ?? $homeData['pageTitle']);
    $mainHeading = trim($_POST['mainHeading'] ?? $homeData['mainHeading']);
    $mainText = trim($_POST['mainText'] ?? $homeData['mainText']);
    $blockTitles = $_POST['block_titles'] ?? [];
    $blockTexts = $_POST['block_texts'] ?? [];

    $blocks = [];
    foreach ($blockTitles as $index => $title) {
        $title = trim($title);
        $text = trim($blockTexts[$index] ?? '');
        if ($title !== '' || $text !== '') {
            $blocks[] = ['title' => $title, 'text' => $text];
        }
    }

    $homeData = [
        'siteName' => $siteName,
        'pageTitle' => $pageTitle,
        'mainHeading' => $mainHeading,
        'mainText' => $mainText,
        'blocks' => $blocks,
    ];

    if (file_put_contents($jsonPath, json_encode($homeData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
        $message = 'Home page content saved successfully.';
    } else {
        $error = 'Unable to save home page content. Check file permissions.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin | <?php echo htmlspecialchars($homeData['siteName']); ?></title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header>
        <h1><?php echo htmlspecialchars($homeData['siteName']); ?> — Admin</h1>
        <?php if ($isLoggedIn): ?>
            <nav>
                <span>Logged in as <strong><?php echo htmlspecialchars($_SESSION['admin_user']); ?></strong></span>
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
            <h2>Home Page Editor</h2>

            <?php if ($message): ?>
                <div class="message success"><?php echo htmlspecialchars($message); ?></div>
            <?php endif; ?>
            <?php if ($error): ?>
                <div class="message error"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="admin.php">
                <label for="siteName">Site Name</label>
                <input type="text" id="siteName" name="siteName" value="<?php echo htmlspecialchars($homeData['siteName']); ?>" required>

                <label for="pageTitle">Page Title</label>
                <input type="text" id="pageTitle" name="pageTitle" value="<?php echo htmlspecialchars($homeData['pageTitle']); ?>" required>

                <label for="mainHeading">Main Heading</label>
                <input type="text" id="mainHeading" name="mainHeading" value="<?php echo htmlspecialchars($homeData['mainHeading']); ?>" required>

                <label for="mainText">Main Text</label>
                <textarea id="mainText" name="mainText" rows="5" required><?php echo htmlspecialchars($homeData['mainText']); ?></textarea>

                <h3>Home Page Elements</h3>
                <div id="blocks">
                    <?php foreach ($homeData['blocks'] as $index => $block): ?>
                        <div class="block-row">
                            <label>Element Title</label>
                            <input type="text" name="block_titles[]" value="<?php echo htmlspecialchars($block['title']); ?>">

                            <label>Element Text</label>
                            <textarea name="block_texts[]" rows="3"><?php echo htmlspecialchars($block['text']); ?></textarea>
                            <button type="button" class="remove-block" onclick="removeBlock(this)">Remove</button>
                        </div>
                    <?php endforeach; ?>
                </div>

                <button type="button" id="add-block">Add Element</button>
                <button type="submit">Save Home Page</button>
            </form>

            <script>
                function removeBlock(button) {
                    const row = button.closest('.block-row');
                    if (row) {
                        row.remove();
                    }
                }

                document.getElementById('add-block').addEventListener('click', function () {
                    const container = document.getElementById('blocks');
                    const template = document.createElement('div');
                    template.className = 'block-row';
                    template.innerHTML = `
                        <label>Element Title</label>
                        <input type="text" name="block_titles[]" value="">
                        <label>Element Text</label>
                        <textarea name="block_texts[]" rows="3"></textarea>
                        <button type="button" class="remove-block" onclick="removeBlock(this)">Remove</button>
                    `;
                    container.appendChild(template);
                });
            </script>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> <?php echo htmlspecialchars($homeData['siteName']); ?></p>
    </footer>
</body>
</html>
