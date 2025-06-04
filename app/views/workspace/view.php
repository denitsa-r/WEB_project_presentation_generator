<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Преглед на Workspace</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <h1><?php echo htmlspecialchars($workspace['name']); ?></h1>

    <h2>Презентации в това workspace</h2>
    <ul>
        <?php foreach ($presentations as $presentation): ?>
            <li>
                <a href="/presentation/view/<?php echo $presentation['id']; ?>">
                    <?php echo htmlspecialchars($presentation['title']); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>

    <a href="/workspace/edit/<?php echo $workspace['id']; ?>">Редактирай workspace</a>
</body>
</html>