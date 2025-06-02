<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Workspaces</title>
</head>
<body>
    <h1>Workspaces</h1>
    <a href="/workspace/create">Добави нов workspace</a>
    <ul>
        <?php foreach ($workspaces as $ws): ?>
            <li>
                <a href="/workspace/view/<?php echo $ws['id']; ?>">
                    <?php echo htmlspecialchars($ws['name']); ?>
                </a>
                [<a href="/workspace/edit/<?php echo $ws['id']; ?>">редактирай</a>]
                [<a href="/workspace/delete/<?php echo $ws['id']; ?>" onclick="return confirm('Сигурни ли сте?');">изтрий</a>]
            </li>
        <?php endforeach; ?>
    </ul>
</body>
</html>