<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/presentation.css">
    <title>Изтриване на презентация</title>
</head>
<body>
    <div class="container">
        <div class="delete-container">
            <h1>Изтриване на презентация</h1>
            
            <?php if (isset($data['error'])): ?>
                <div class="error"><?= htmlspecialchars($data['error']) ?></div>
            <?php endif; ?>

            <div class="warning">
                <strong>Внимание!</strong> Сигурни ли сте, че искате да изтриете презентацията "<?= htmlspecialchars($data['presentation']['title']) ?>"?
                Това действие е необратимо и ще изтрие всички слайдове в тази презентация.
            </div>

            <form action="<?= BASE_URL ?>/presentation/delete/<?= $data['presentation']['id'] ?>" method="POST">
                <button type="submit" class="btn btn-danger">Да, изтрий презентацията</button>
                <a href="<?= BASE_URL ?>/presentation/view/<?= $data['presentation']['id'] ?>" class="btn btn-secondary">Отказ</a>
            </form>
        </div>
    </div>
</body>
</html> 