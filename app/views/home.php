<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Начало - Генератор на презентации</title>
</head>
<body>
    <h1>Workspaces</h1>
    <ul>
        <?php foreach ($workspaces as $ws): ?>
            <li><a href="/workspace/view/<?php echo $ws['id']; ?>"><?php echo htmlspecialchars($ws['name']); ?></a></li>
        <?php endforeach; ?>
    </ul>
</body>
</html>