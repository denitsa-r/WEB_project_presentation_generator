<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($presentation['title']); ?></title>
</head>
<body>
    <h1><?php echo htmlspecialchars($presentation['title']); ?></h1>
    <p>Език: <?php echo htmlspecialchars($presentation['language']); ?></p>
    <p>Тема: <?php echo htmlspecialchars($presentation['theme']); ?></p>

    <a href="/slide/index/<?php echo $presentation['id']; ?>">Виж слайдове</a>
    <br>
    <a href="/presentation/index/<?php echo $presentation['workspace_id']; ?>">Обратно към презентации</a>
</body>
</html>