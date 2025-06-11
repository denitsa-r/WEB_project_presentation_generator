<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/presentation.css">
    <title>Изтриване на слайд</title>
</head>
<body>
    <div class="container">
        <div class="delete-container">
            <h1>Изтриване на слайд</h1>
            
            <?php if (isset($data['error'])): ?>
                <div class="error"><?= htmlspecialchars($data['error']) ?></div>
            <?php endif; ?>

            <div class="warning">
                <strong>Внимание!</strong> Сигурни ли сте, че искате да изтриете слайда "<?= htmlspecialchars($data['slide']['title']) ?>"?
                Това действие е необратимо.
            </div>

            <form action="<?= BASE_URL ?>/slides/delete/<?= $data['slide']['id'] ?>" method="POST">
                <button type="submit" class="btn btn-danger">Да, изтрий слайда</button>
                <a href="<?= BASE_URL ?>/presentation/viewPresentation/<?= $data['slide']['presentation_id'] ?>" class="btn btn-secondary">Отказ</a>
            </form>
        </div>
    </div>
</body>
</html> 