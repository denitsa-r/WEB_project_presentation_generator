<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Редактирай слайд</title>
</head>
<body>
    <h1>Редактирай слайд</h1>
    <form method="post" action="">
        <label>Позиция (ред):<br>
            <input type="number" name="slide_order" value="<?php echo htmlspecialchars($slide['slide_order']); ?>" required>
        </label><br><br>
        <label>Тип:<br>
            <input type="text" name="type" value="<?php echo htmlspecialchars($slide['type']); ?>" required>
        </label><br><br>
        <label>Наредба:<br>
            <input type="text" name="layout" value="<?php echo htmlspecialchars($slide['layout']); ?>">
        </label><br><br>
        <label>Стил:<br>
            <input type="text" name="style" value="<?php echo htmlspecialchars($slide['style']); ?>">
        </label><br><br>
        <label>Съдържание:<br>
            <textarea name="content" rows="6"><?php echo htmlspecialchars($slide['content']); ?></textarea>
        </label><br><br>
        <label>Навигация (JSON):<br>
            <textarea name="navigation" rows="4"><?php echo htmlspecialchars($slide['navigation']); ?></textarea>
        </label><br><br>
        <button type="submit">Запази</button>
    </form>
    <a href="/slide/view/<?php echo htmlspecialchars($slide['id']); ?>">Откажи</a>
</body>
</html>