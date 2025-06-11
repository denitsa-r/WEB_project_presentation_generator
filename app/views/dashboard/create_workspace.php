<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/workspace.css">
    <title>Създаване на работно пространство</title>
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h1>Ново работно пространство</h1>

            <?php if (!empty($data['error'])): ?>
                <div class="error"><?= $data['error'] ?></div>
            <?php endif; ?>

            <form method="POST" action="<?= BASE_URL ?>/dashboard/createWorkspace">
                <div class="form-group">
                    <label for="name">Име на работното пространство</label>
                    <input type="text" id="name" name="name" required>
                </div>

                <div class="actions">
                    <button type="submit" class="btn">Създай</button>
                    <a href="<?= BASE_URL ?>/dashboard" class="btn btn-secondary">Отказ</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 