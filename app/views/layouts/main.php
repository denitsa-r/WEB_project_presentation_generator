<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8" />
    <title><?php echo $title ?? 'Генератор на презентации'; ?></title>
    <link rel="stylesheet" href="/css/light.css" />
    <!-- Add other CSS files or meta tags here -->
</head>
<body>
    <header>
        <h1>Генератор на презентации</h1>
        <nav>
            <a href="/home/index">Начало</a> |
            <a href="/workspace/index">Workspaces</a>
        </nav>
        <hr>
    </header>

    <main>
        <?php echo $content; ?>
    </main>

    <footer>
        <hr>
        <p>© 2025 Presentation Generator</p>
    </footer>
</body>
</html>