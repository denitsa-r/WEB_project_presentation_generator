<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Създаване на слайд</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <h1>Създайте нов слайд</h1>

    <form method="POST" action="/slide/create/<?php echo $presentationId; ?>">
        <input type="text" name="content" placeholder="Съдържание" required>
        <input type="text" name="style" value="light" required>
        <textarea name="navigation"></textarea>
        <button type="submit">Запази</button>
    </form>

    <a href="/slide/index/<?php echo $presentationId; ?>">Откажи</a>
</body>
</html>