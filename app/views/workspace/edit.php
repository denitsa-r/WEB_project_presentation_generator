<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Редактирай workspace</title>
</head>
<body>
    <h1>Редактирай workspace</h1>
    <form method="post" action="/workspace/edit/<?php echo $workspace['id']; ?>">
        <label>Име:<br>
            <input type="text" name="name" value="<?php echo htmlspecialchars($workspace['name']); ?>" required>
        </label><br><br>
        <label>Описание:<br>
            <textarea name="description"><?php echo htmlspecialchars($workspace['description']); ?></textarea>
        </label><br><br>
        <label>Език:<br>
            <input type="text" name="language" value="<?php echo htmlspecialchars($workspace['language']); ?>" required>
        </label><br><br>
        <button type="submit">Запази</button>
    </form>
    <a href="/workspace/index">Откажи</a>
</body>
</html>