<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/presentation.css">
    <title>Нова презентация</title>
    <link rel="stylesheet" href="/web-project/WEB_project_presentation_generator/public/assets/css/main.css">
    <link rel="stylesheet" href="/web-project/WEB_project_presentation_generator/public/assets/css/presentation.css">
</head>
<body>
    <div class="container">
        <div class="form-container">
            <h1>Нова презентация</h1>

            <?php if (!empty($data['error'])): ?>
                <div class="error"><?= $data['error'] ?></div>
            <?php endif; ?>

            <div class="workspace-info">
                <h3>Работно пространство: <?= htmlspecialchars($data['workspace']['name']) ?></h3>
            </div>

            <form method="POST" action="<?= BASE_URL ?>/presentation/create/<?= $data['workspace']['id'] ?>">
                <div class="form-group">
                    <label for="title">Заглавие на презентацията</label>
                    <input type="text" id="title" name="title" required>
                </div>

                <div class="form-group">
                    <label for="language">Език</label>
                    <select id="language" name="language" required>
                        <option value="bg">Български</option>
                        <option value="en">English</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="theme">Тема</label>
                    <select id="theme" name="theme" required>
                        <option value="light">Светла</option>
                        <option value="dark">Тъмна</option>
                    </select>
                </div>

                <div class="presentation-actions">
                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#importModal">
                        Импортиране
                    </button>
                </div>

                <div class="actions">
                    <button type="submit" class="btn">Създай</button>
                    <a href="<?= BASE_URL ?>/dashboard/viewWorkspace/<?= $data['workspace']['id'] ?>" class="btn btn-secondary">Отказ</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal за импортиране -->
    <div class="modal fade" id="importModal" tabindex="-1" aria-labelledby="importModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="importModalLabel">Импортиране на презентация</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="<?php echo BASE_URL; ?>/presentation/import" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="workspaceId" value="<?php echo $data['workspace']['id']; ?>">
                        <div class="mb-3">
                            <label for="importFile" class="form-label">Изберете файл</label>
                            <input type="file" class="form-control" id="importFile" name="importFile" accept=".html,.xml,.slim" required>
                        </div>
                        <div class="mb-3">
                            <label for="importFormat" class="form-label">Формат на файла</label>
                            <select class="form-select" id="importFormat" name="importFormat" required>
                                <option value="html">HTML</option>
                                <option value="xml">XML</option>
                                <option value="slim">SLIM</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Импортиране</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</body>
</html> 