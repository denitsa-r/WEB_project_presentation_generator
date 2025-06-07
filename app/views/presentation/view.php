<?php
require_once __DIR__ . '/../../helpers/SlideRenderer.php';
?>
<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/presentation.css">
    <title>Преглед на презентация</title>
</head>
<body>
    <div class="container">
        <div class="presentation-header">
            <h1><?= htmlspecialchars($data['presentation']['title']) ?></h1>
            <div class="presentation-meta">
                <span>Език: <?= htmlspecialchars($data['presentation']['language']) ?></span>
                <span>Тема: <?= htmlspecialchars($data['presentation']['theme']) ?></span>
            </div>
        </div>

        <?php if (isset($_GET['error'])): ?>
            <div class="error-message">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>

        <div class="presentation-actions">
            <a href="<?= BASE_URL ?>/presentation/edit/<?= $data['presentation']['id'] ?>" class="btn btn-primary">Редактирай</a>
            <a href="<?= BASE_URL ?>/presentation/delete/<?= $data['presentation']['id'] ?>" class="btn btn-danger">Изтрий</a>
            <a href="<?= BASE_URL ?>/slide/create/<?= $data['presentation']['id'] ?>" class="btn btn-success">Добави слайд</a>
            <a href="<?= BASE_URL ?>/dashboard" class="btn btn-secondary">Назад</a>
        </div>

        <div class="slides-container">
            <?php if (empty($data['slides'])): ?>
                <div class="empty-state">
                    <p>Няма добавени слайдове.</p>
                    <a href="<?= BASE_URL ?>/slide/create/<?= $data['presentation']['id'] ?>" class="btn btn-primary">Добави първи слайд</a>
                </div>
            <?php else: ?>
                <?php foreach ($data['slides'] as $key => $slide): ?>
                    <div class="slide">
                        <div class="slide-header">
                            <div class="slide-header-content">
                                <h2 class="slide-title"><?= htmlspecialchars($slide['title']) ?></h2>
                                <div class="slide-actions">
                                    <a href="<?= BASE_URL ?>/slide/edit/<?= $slide['id'] ?>" class="btn btn-primary">
                                        <i class="fas fa-edit"></i> Редактирай
                                    </a>
                                    <a href="<?= BASE_URL ?>/slides/delete/<?= $slide['id'] ?>" class="btn btn-danger">
                                        <i class="fas fa-trash"></i> Изтрий
                                    </a>
                                </div>
                            </div>
                        </div>
                        <div class="slide-content">
                            <?php if (!empty($slide['elements'])): ?>
                                <?php foreach ($slide['elements'] as $element): ?>
                                    <div class="content-element">
                                        <?php if ($element['type'] === 'text'): ?>
                                            <div class="element-header">
                                                <h3><?= htmlspecialchars($element['title'] ?? '') ?></h3>
                                            </div>
                                            <div class="element-content">
                                                <p><?= nl2br(htmlspecialchars($element['text'] ?? $element['content'] ?? '')) ?></p>
                                            </div>
                                        <?php elseif ($element['type'] === 'image'): ?>
                                            <div class="element-header">
                                                <h3><?= htmlspecialchars($element['title'] ?? '') ?></h3>
                                            </div>
                                            <div class="element-content">
                                                <img src="<?= htmlspecialchars($element['content'] ?? '') ?>" alt="<?= htmlspecialchars($element['title'] ?? '') ?>">
                                            </div>
                                        <?php elseif ($element['type'] === 'image_text'): ?>
                                            <div class="element-header">
                                                <h3><?= htmlspecialchars($element['title'] ?? '') ?></h3>
                                            </div>
                                            <div class="element-content">
                                                <div class="image-text-container">
                                                    <img src="<?= htmlspecialchars($element['content'] ?? '') ?>" alt="<?= htmlspecialchars($element['title'] ?? '') ?>">
                                                    <p><?= nl2br(htmlspecialchars($element['text'] ?? '')) ?></p>
                                                </div>
                                            </div>
                                        <?php elseif ($element['type'] === 'image_list'): ?>
                                            <div class="element-header">
                                                <h3><?= htmlspecialchars($element['title'] ?? '') ?></h3>
                                            </div>
                                            <div class="element-content">
                                                <div class="image-list-container">
                                                    <img src="<?= htmlspecialchars($element['content'] ?? '') ?>" alt="<?= htmlspecialchars($element['title'] ?? '') ?>">
                                                    <ul>
                                                        <?php 
                                                        $items = !empty($element['text']) ? explode("\n", $element['text']) : [];
                                                        foreach ($items as $item): 
                                                        ?>
                                                            <li><?= htmlspecialchars(trim($item)) ?></li>
                                                        <?php endforeach; ?>
                                                    </ul>
                                                </div>
                                            </div>
                                        <?php elseif ($element['type'] === 'list'): ?>
                                            <div class="element-header">
                                                <h3><?= htmlspecialchars($element['title'] ?? '') ?></h3>
                                            </div>
                                            <div class="element-content">
                                                <ul>
                                                    <?php 
                                                    $items = !empty($element['text']) ? explode("\n", $element['text']) : [];
                                                    foreach ($items as $item): 
                                                    ?>
                                                        <li><?= htmlspecialchars(trim($item)) ?></li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                        <?php elseif ($element['type'] === 'quote'): ?>
                                            <div class="element-header">
                                                <h3><?= htmlspecialchars($element['title'] ?? '') ?></h3>
                                            </div>
                                            <div class="element-content">
                                                <blockquote>
                                                    <?= nl2br(htmlspecialchars($element['text'] ?? $element['content'] ?? '')) ?>
                                                </blockquote>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="empty-content">Няма добавено съдържание</div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 