<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title><?= $data['title'] ?></title>
    <link rel="stylesheet" href="/web-project/WEB_project_presentation_generator/public/assets/css/main.css">
    <link rel="stylesheet" href="/web-project/WEB_project_presentation_generator/public/assets/css/workspace.css">
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><?= htmlspecialchars($data['workspace']['name']) ?></h1>
            <div class="actions">
                <?php if ($data['isOwner']): ?>
                    <a href="<?= BASE_URL ?>/dashboard/editWorkspace/<?= $data['workspace']['id'] ?>" class="btn btn-secondary">Редактирай</a>
                    <a href="<?= BASE_URL ?>/dashboard/deleteWorkspace/<?= $data['workspace']['id'] ?>" class="btn btn-danger">Изтрий</a>
                <?php endif; ?>
                <a href="<?= BASE_URL ?>/dashboard" class="btn btn-secondary">Назад към таблото</a>
            </div>
        </div>

        <div class="workspace-info">
            <h2>Информация за работното пространство</h2>
            <p>Създадено на: <?= date('d.m.Y H:i', strtotime($data['workspace']['created_at'])) ?></p>
        </div>

        <div class="header">
            <h2>Презентации</h2>
            <a href="<?= BASE_URL ?>/presentation/create/<?= $data['workspace']['id'] ?>" class="btn">Нова презентация</a>
        </div>

        <?php if (empty($data['presentations'])): ?>
            <div class="empty-state">
                <h2>Няма презентации</h2>
                <p>Създайте първата си презентация в това работно пространство.</p>
                <a href="<?= BASE_URL ?>/presentation/create/<?= $data['workspace']['id'] ?>" class="btn">Създай презентация</a>
            </div>
        <?php else: ?>
            <div class="presentations">
                <?php foreach ($data['presentations'] as $presentation): ?>
                    <div class="presentation-card">
                        <h3><?= htmlspecialchars($presentation['title']) ?></h3>
                        <div class="meta">
                            <p>Език: <?= strtoupper($presentation['language']) ?></p>
                            <p>Тема: <?= ucfirst($presentation['theme']) ?></p>
                            <p>Създадено: <?= date('d.m.Y H:i', strtotime($presentation['created_at'])) ?></p>
                        </div>
                        <a href="<?= BASE_URL ?>/presentation/viewPresentation/<?= $presentation['id'] ?>" class="btn">Отвори</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 