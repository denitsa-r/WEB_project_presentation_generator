<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Презентации в workspace</title>
</head>
<body>
    <h1>Презентации в workspace</h1>
    <ul>
        <?php foreach ($presentations as $presentation): ?>
            <li>
                <a href="/presentation/view/<?php echo $presentation['id']; ?>">
                    <?php echo htmlspecialchars($presentation['title']); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
    <a href="/workspace/index">Обратно към workspaces</a>
</body>
</html>