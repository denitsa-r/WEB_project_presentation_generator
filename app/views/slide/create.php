<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Добави слайд</title>
</head>
<body>
    <h1>Добави нов слайд</h1>
    <form method="post" action="">
        <label>Позиция (ред):<br>
            <input type="number" name="slide_order" value="1" required>
        </label><br><br>
        <label>Тип:<br>
            <input type="text" name="type" value="title_text" required>
        </label><br><br>
        <label>Наредба:<br>
            <input type="text" name="layout" placeholder="title_top_text_bottom">
        </label><br><br>
        <label>Стил:<br>
            <input type="text" name="style" value="light">
        </label><br><br>
        <label>Съдържание:<br>
            <textarea name="content" rows="6"></textarea>
        </label><br><br>
        <label>Навигация (JSON):<br>
            <textarea name="navigation" rows="4"></textarea>
        </label><br><br>
        <button type="submit">Запази</button>
    </form>
    <a href="/slide/index/<?php echo htmlspecialchars($presentationId); ?>">Откажи</a>
</body>
</html>