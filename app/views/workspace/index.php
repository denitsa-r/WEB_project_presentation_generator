<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Workspaces</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <h1>Твоите workspaces</h1>

    <a href="/workspace/create">Създай ново workspace</a>

    <ul>
        <?php foreach ($workspaces as $workspace): ?>
            <li>
                <a href="/workspace/view/<?php echo $workspace['id']; ?>">
                    <?php echo htmlspecialchars($workspace['name']); ?>
                </a>
                <a href="/workspace/edit/<?php echo $workspace['id']; ?>">Редактиране</a>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>