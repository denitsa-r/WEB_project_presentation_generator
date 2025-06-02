<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Добави workspace</title>
</head>
<body>
    <h1>Добави нов workspace</h1>
    <form method="post" action="/workspace/create">
        <label>Име:<br>
            <input type="text" name="name" required>
        </label><br><br>
        <label>Описание:<br>
            <textarea name="description"></textarea>
        </label><br><br>
        <label>Език:<br>
            <input type="text" name="language" value="bg" required>
        </label><br><br>
        <button type="submit">Запази</button>
    </form>
    <a href="/workspace/index">Откажи</a>
</body>
</html>