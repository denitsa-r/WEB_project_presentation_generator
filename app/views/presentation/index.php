<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Презентации</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <h1>Презентации в workspace</h1>

    <a href="/presentation/create/<?php echo $workspaceId; ?>">Създай нова презентация</a>

    <ul>
        <?php foreach ($presentations as $presentation): ?>
            <li>
                <a href="/presentation/view/<?php echo $presentation['id']; ?>">
                    <?php echo htmlspecialchars($presentation['title']); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>