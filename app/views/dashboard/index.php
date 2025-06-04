<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Табло</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <h1>Добре дошли в твоето табло</h1>

    <a href="/workspace/create">Създай ново работно пространство</a>

    <h2>Твоите workspaces</h2>
    <ul>
        <?php foreach ($workspaces as $workspace): ?>
            <li>
                <a href="/workspace/view/<?php echo $workspace['id']; ?>">
                    <?php echo htmlspecialchars($workspace['name']); ?>
                </a>
                - Роля: <?php echo htmlspecialchars($workspace['role']); ?>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>