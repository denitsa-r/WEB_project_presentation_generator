<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Редактиране на презентация</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <h1>Редактиране на презентация</h1>

    <form method="POST" action="/presentation/edit/<?php echo $presentation['id']; ?>">
        <label>Заглавие:<br>
            <input type="text" name="title" value="<?php echo htmlspecialchars($presentation['title']); ?>" required>
        </label><br><br>
        <label>Език:<br>
            <input type="text" name="language" value="<?php echo htmlspecialchars($presentation['language']); ?>" required>
        </label><br><br>
        <label>Тема:<br>
            <input type="text" name="theme" value="<?php echo htmlspecialchars($presentation['theme']); ?>" required>
        </label><br><br>
        <label>Версия:<br>
            <input type="text" name="version" value="<?php echo htmlspecialchars($presentation['version']); ?>" required>
        </label><br><br>
        <label>Навигация (JSON):<br>
            <textarea name="navigation"><?php echo htmlspecialchars($presentation['navigation']); ?></textarea>
        </label><br><br>
        <button type="submit">Запази</button>
    </form>

    <a href="/presentation/view/<?php echo $presentation['id']; ?>">Откажи</a>
</body>
</html>