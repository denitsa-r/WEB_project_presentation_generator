<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Създаване на презентация</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <h1>Създайте нова презентация</h1>

    <form method="POST" action="/presentation/create/<?php echo $workspaceId; ?>">
        <input type="text" name="title" placeholder="Заглавие" required>
        <input type="text" name="language" value="bg" required>
        <input type="text" name="theme" value="light" required>
        <input type="text" name="version" value="1.0" required>
        <textarea name="navigation"></textarea>
        <button type="submit">Запази</button>
    </form>

    <a href="/presentation/index/<?php echo $workspaceId; ?>">Откажи</a>
</body>
</html>