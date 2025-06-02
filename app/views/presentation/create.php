<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Добави презентация</title>
</head>
<body>
    <h1>Добави нова презентация</h1>
    <form method="post" action="">
        <label>Заглавие:<br>
            <input type="text" name="title" required>
        </label><br><br>
        <label>Език:<br>
            <input type="text" name="language" value="bg" required>
        </label><br><br>
        <label>Тема:<br>
            <input type="text" name="theme" value="light" required>
        </label><br><br>
        <label>Версия:<br>
            <input type="text" name="version" value="1.0" required>
        </label><br><br>
        <label>Навигация (JSON):<br>
            <textarea name="navigation"></textarea>
        </label><br><br>
        <button type="submit">Запази</button>
    </form>
    <a href="/presentation/index/<?php echo htmlspecialchars($workspaceId); ?>">Откажи</a>
</body>
</html>