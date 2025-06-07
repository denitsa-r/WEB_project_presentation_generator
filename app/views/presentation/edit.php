<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/presentation.css">
    <title>Редактиране на презентация</title>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h1>Редактиране на презентация</h1>
            
            <?php if (isset($data['error'])): ?>
                <div class="error"><?= htmlspecialchars($data['error']) ?></div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/presentation/edit/<?= $data['presentation']['id'] ?>" method="POST">
                <div class="form-group">
                    <label for="title">Заглавие:</label>
                    <input type="text" id="title" name="title" value="<?= htmlspecialchars($data['presentation']['title']) ?>" required>
                </div>

                <div class="form-group">
                    <label for="language">Език:</label>
                    <select id="language" name="language" required>
                        <option value="bg" <?= $data['presentation']['language'] === 'bg' ? 'selected' : '' ?>>Български</option>
                        <option value="en" <?= $data['presentation']['language'] === 'en' ? 'selected' : '' ?>>English</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="theme">Тема:</label>
                    <select id="theme" name="theme" required>
                        <option value="light" <?= $data['presentation']['theme'] === 'light' ? 'selected' : '' ?>>Светла</option>
                        <option value="dark" <?= $data['presentation']['theme'] === 'dark' ? 'selected' : '' ?>>Тъмна</option>
                    </select>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Запази промените</button>
                    <a href="<?= BASE_URL ?>/presentation/viewPresentation/<?= $data['presentation']['id'] ?>" class="btn btn-secondary">Отказ</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 