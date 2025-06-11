<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/workspace.css">
    <title>Изтриване на работно пространство</title>
</head>
<body>
    <div class="container">
        <div class="delete-container">
            <h1>Изтриване на работно пространство</h1>
            
            <?php if (isset($data['error'])): ?>
                <div class="error"><?= htmlspecialchars($data['error']) ?></div>
            <?php endif; ?>

            <div class="warning">
                <strong>Внимание!</strong> Сигурни ли сте, че искате да изтриете работното пространство "<?= htmlspecialchars($data['workspace']['name']) ?>"?
                Това действие е необратимо и ще изтрие всички презентации в това работно пространство.
            </div>

            <form action="<?= BASE_URL ?>/dashboard/deleteWorkspace/<?= $data['workspace']['id'] ?>" method="POST">
                <button type="submit" class="btn btn-danger">Да, изтрий работното пространство</button>
                <a href="<?= BASE_URL ?>/dashboard/workspace/<?= $data['workspace']['id'] ?>" class="btn btn-secondary">Отказ</a>
            </form>
        </div>
    </div>
</body>
</html> 