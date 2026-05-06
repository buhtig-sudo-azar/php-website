<?php
/*
Project Context File: project-context.php

This file describes every file in the current project structure under public_html/
and includes the full source code of each file in commented form.

Purpose:
- Provide one file that a new AI/chat can read to understand the current project.
- Explain what each file does and how it is connected.
- Include the exact contents of each file so the context is complete.

NOTE:
- This file is not meant to be executed.
- All code content is included inside comments.
*/

/*
File: public_html/.htaccess
Description:
- Apache configuration for the PHP website.
- Disables directory listing, redirects www to non-www, forces HTTPS,
  defines custom error pages, protects sensitive file extensions,
  and sets UTF-8 charset.
*/

/*
Options -Indexes

# Enable URL rewriting
RewriteEngine On

# Redirect www to non-www
RewriteCond %{HTTP_HOST} ^www\.(.+)$ [NC]
RewriteRule ^ https://%1%{REQUEST_URI} [R=301,L]

# Redirect HTTP to HTTPS
RewriteCond %{HTTPS} off
RewriteRule ^ https://%{HTTP_HOST}%{REQUEST_URI} [R=301,L]

# Custom error pages
ErrorDocument 404 /404.php
ErrorDocument 500 /500.php

# Protect sensitive files
<FilesMatch "\.(env|log|sh|sql)$">
    Order Allow,Deny
    Deny from all
</FilesMatch>

# Set default charset
AddDefaultCharset UTF-8
*/

/*
File: public_html/index.php
Description:
- Main home page script.
- Loads homepage content from public_html/home-data.json if available.
- Uses default values when JSON is missing or invalid.
- Displays site name, page title, heading, main text, and dynamic blocks.
- Outputs simple HTML with a shared stylesheet.
*/

/*
<?php
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

$siteName = $homeData['siteName'];
$pageTitle = $homeData['pageTitle'];
$mainHeading = $homeData['mainHeading'];
$mainText = $homeData['mainText'];
$blocks = is_array($homeData['blocks']) ? $homeData['blocks'] : [];
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
        <h2><?php echo htmlspecialchars($mainHeading); ?></h2>
        <p><?php echo nl2br(htmlspecialchars($mainText)); ?></p>

        <?php if (!empty($blocks)): ?>
            <section class="blocks">
                <?php foreach ($blocks as $block): ?>
                    <article class="block-card">
                        <h3><?php echo htmlspecialchars($block['title'] ?? ''); ?></h3>
                        <p><?php echo nl2br(htmlspecialchars($block['text'] ?? '')); ?></p>
                    </article>
                <?php endforeach; ?>
            </section>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> <?php echo htmlspecialchars($siteName); ?></p>
    </footer>
</body>
</html>
*/

/*
File: public_html/admin.php
Description:
- Admin panel page with login and home page editing.
- Uses PHP session storage to keep admin logged in.
- Has hardcoded credentials: admin / changeme123.
- Loads the same home data defaults and JSON as index.php.
- When logged in, shows a form to edit site name, page title,
  main heading, main text, and dynamic home page blocks.
- Saves updated content back to public_html/home-data.json.
- Contains client-side JavaScript to add/remove dynamic blocks.
*/

/*
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
*/

/*
File: public_html/contact.php
Description:
- Contact form page.
- Validates name, email, and message fields.
- Uses simple PHP validation and displays success/error messages.
- Does not actually send email; placeholder comment suggests mail() or SMTP.
*/

/*
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
*/

/*
File: public_html/style.css
Description:
- Shared styling for all pages.
- Page layout, typography, header, footer, form elements and admin editing blocks.
*/

/*
/* Reset */
*, *::before, *::after {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: Arial, sans-serif;
    font-size: 16px;
    line-height: 1.6;
    color: #333;
    background-color: #f5f5f5;
}

header {
    background-color: #2c3e50;
    color: #fff;
    padding: 1rem 2rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

header h1 {
    font-size: 1.5rem;
}

nav a {
    color: #fff;
    text-decoration: none;
    margin-left: 1.5rem;
}

nav a:hover {
    text-decoration: underline;
}

main {
    max-width: 900px;
    margin: 2rem auto;
    padding: 0 1rem;
    background: #fff;
    border-radius: 6px;
    padding: 2rem;
    box-shadow: 0 1px 4px rgba(0,0,0,0.08);
}

footer {
    text-align: center;
    padding: 1.5rem;
    color: #777;
    font-size: 0.9rem;
}

form label {
    display: block;
    margin-bottom: 0.25rem;
    font-weight: bold;
}

form input,
form textarea {
    width: 100%;
    padding: 0.5rem;
    margin-bottom: 1rem;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-size: 1rem;
}

form button {
    background-color: #2c3e50;
    color: #fff;
    border: none;
    padding: 0.6rem 1.5rem;
    border-radius: 4px;
    cursor: pointer;
    font-size: 1rem;
}

form button:hover {
    background-color: #1a252f;
}

.blocks {
    display: grid;
    gap: 1rem;
    margin-top: 2rem;
}

.block-card {
    border: 1px solid #e1e1e1;
    border-radius: 8px;
    padding: 1.25rem;
    background: #fdfdfd;
}

.block-card h3 {
    margin-bottom: 0.5rem;
}

.block-row {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
    background: #fafafa;
}

.block-row label {
    margin-top: 0.75rem;
}

.remove-block {
    background: #c0392b;
    margin-top: 0.5rem;
}

.remove-block:hover {
    background: #922b21;
}

#add-block {
    margin-right: 1rem;
}

.message {
    padding: 0.75rem 1rem;
    border-radius: 4px;
    margin-bottom: 1rem;
}

.message.success {
    background-color: #d4edda;
    color: #155724;
    border: 1px solid #c3e6cb;
}

.message.error {
    background-color: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}
*/

/*
File: public_html/home-data.json
Description:
- Stores editable homepage content used by index.php and admin.php.
- Contains site metadata, main text, and homepage block list.
- Admin panel saves updates here.
*/

/*
{
    "siteName": "My WebsiteI am az",
    "pageTitle": "Welcome",
    "mainHeading": "Welcome to our site",
    "mainText": "This is the home page of the website.",
    "blocks": [
        {
            "title": "Feature Two",
            "text": "Add another block for the home page."
        }
    ]
}
*/

/*
Current project files summary:
- public_html/.htaccess: web server rules and redirects.
- public_html/index.php: homepage renderer.
- public_html/admin.php: admin editor and login.
- public_html/contact.php: contact form page.
- public_html/style.css: shared styles.
- public_html/home-data.json: saved homepage data.
*/
