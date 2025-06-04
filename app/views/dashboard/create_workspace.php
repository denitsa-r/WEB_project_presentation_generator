<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Създаване на Workspace</title>
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <h1>Създайте ново workspace</h1>

    <?php if (!empty($data['error'])): ?>
        <div class="error"><?= htmlspecialchars($data['error']) ?></div>
    <?php endif; ?>

    <form method="POST" action="/workspace/create">
        <input type="text" name="name" placeholder="Име на workspace" required>
        <button type="submit">Създай</button>
    </form>

    <a href="/dashboard">Обратно към таблото</a>
</body>
</html>