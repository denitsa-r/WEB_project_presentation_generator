<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Редактиране на Workspace</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <h1>Редактиране на workspace</h1>

    <form method="POST" action="/workspace/edit/<?php echo $workspace['id']; ?>">
        <label>Име на workspace:<br>
            <input type="text" name="name" value="<?php echo htmlspecialchars($workspace['name']); ?>" required>
        </label><br><br>
        <button type="submit">Запази</button>
    </form>

    <a href="/workspace/view/<?php echo $workspace['id']; ?>">Откажи</a>
</body>
</html>