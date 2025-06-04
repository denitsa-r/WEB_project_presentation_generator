<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Редактиране на слайд</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <h1>Редактиране на слайд</h1>

    <form method="POST" action="/slide/edit/<?php echo $slide['id']; ?>">
        <label>Съдържание:<br>
            <textarea name="content" required><?php echo htmlspecialchars($slide['content']); ?></textarea>
        </label><br><br>
        <label>Стил:<br>
            <input type="text" name="style" value="<?php echo htmlspecialchars($slide['style']); ?>" required>
        </label><br><br>
        <label>Навигация (JSON):<br>
            <textarea name="navigation"><?php echo htmlspecialchars($slide['navigation']); ?></textarea>
        </label><br><br>
        <button type="submit">Запази</button>
    </form>

    <a href="/slide/view/<?php echo $slide['id']; ?>">Откажи</a>
</body>
</html>