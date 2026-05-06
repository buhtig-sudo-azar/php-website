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
            <a href="rce-eval-injection/">RCE Eval Injection Demo</a>
        </nav>
    </header>

    <section class="vulnerabilities-intro">
        <h2>Демонстрация уязвимостей и их устранения</h2>
        <p>Здесь вы можете изучить распространённые уязвимости в PHP-сайтах, посмотреть примеры заражённого кода и процесс очистки. Всё безопасно — только образовательные демо.</p>
        <div class="vulnerability-card">
            <h3>RCE через Eval-инъекцию</h3>
            <p>Remote Code Execution: злоумышленник внедряет код в eval(), что позволяет выполнять произвольные команды на сервере.</p>
            <p><strong>Пример:</strong> eval(base64_decode('...')) — может красть данные или перенаправлять на казино.</p>
            <a href="rce-eval-injection/" class="demo-link">Посмотреть демо и очистку</a>
        </div>
    </section>

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
