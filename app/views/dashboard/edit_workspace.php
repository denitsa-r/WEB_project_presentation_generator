<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Редактиране на работно пространство</title>
    <link rel="stylesheet" href="/web-project/WEB_project_presentation_generator/public/assets/css/main.css">
    <link rel="stylesheet" href="/web-project/WEB_project_presentation_generator/public/assets/css/workspace.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h1>Редактиране на работно пространство</h1>
            
            <?php if (isset($data['error'])): ?>
                <div class="error"><?= htmlspecialchars($data['error']) ?></div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/dashboard/editWorkspace/<?= $data['workspace']['id'] ?>" method="POST">
                <div class="form-group">
                    <label for="name">Име на работното пространство:</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($data['workspace']['name']) ?>" required>
                </div>

                <div class="form-group">
                    <button type="submit" class="btn">Запази промените</button>
                    <a href="<?= BASE_URL ?>/dashboard/workspace/<?= $data['workspace']['id'] ?>" class="btn btn-secondary">Отказ</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html> 