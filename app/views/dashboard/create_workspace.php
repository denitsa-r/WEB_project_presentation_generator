<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Ново работно пространство</title>
    <link rel="stylesheet" href="/web-project/WEB_project_presentation_generator/public/assets/css/main.css">
    <link rel="stylesheet" href="/web-project/WEB_project_presentation_generator/public/assets/css/workspace.css">
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