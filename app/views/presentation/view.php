<?php
require_once __DIR__ . '/../../helpers/SlideRenderer.php';
?>
<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($data['presentation']['title']) ?></title>
    <link rel="stylesheet" href="/web-project/WEB_project_presentation_generator/public/assets/css/main.css">
    <link rel="stylesheet" href="/web-project/WEB_project_presentation_generator/public/assets/css/presentation.css">
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

        <div class="presentation-actions">
            <a href="<?= BASE_URL ?>/presentation/edit/<?= $data['presentation']['id'] ?>" class="btn btn-primary">Редактирай</a>
            <a href="<?= BASE_URL ?>/presentation/delete/<?= $data['presentation']['id'] ?>" class="btn btn-danger">Изтрий</a>
            <a href="<?= BASE_URL ?>/dashboard" class="btn btn-secondary">Назад</a>
        </div>

        <div class="slides-container">
            <?php if (empty($data['slides'])): ?>
                <div class="empty-state">
                    <p>Няма добавени слайдове.</p>
                    <a href="<?= BASE_URL ?>/slide/create/<?= $data['presentation']['id'] ?>" class="btn btn-primary">Добави първи слайд</a>
                </div>
            <?php else: ?>
                <?php foreach ($data['slides'] as $slide): ?>
                    <div class="slide">
                        <div class="slide-header">
                            <h3>Слайд <?= $slide['order_number'] ?></h3>
                            <div class="slide-actions">
                                <a href="<?= BASE_URL ?>/slide/edit/<?= $slide['id'] ?>" class="btn btn-sm btn-primary">Редактирай</a>
                                <a href="<?= BASE_URL ?>/slide/delete/<?= $slide['id'] ?>" class="btn btn-sm btn-danger">Изтрий</a>
                            </div>
                        </div>
                        <div class="slide-content">
                            <?php foreach ($slide['elements'] as $element): ?>
                                <div class="content-element">
                                    <?php if ($element['type'] === 'text'): ?>
                                        <p><?= nl2br(htmlspecialchars($element['content'])) ?></p>
                                    <?php elseif ($element['type'] === 'image'): ?>
                                        <img src="<?= htmlspecialchars($element['content']) ?>" alt="Slide image">
                                    <?php elseif ($element['type'] === 'image_text'): ?>
                                        <div class="image-text-container">
                                            <img src="<?= htmlspecialchars($element['content']) ?>" alt="Slide image">
                                            <p><?= nl2br(htmlspecialchars($element['text'])) ?></p>
                                        </div>
                                    <?php elseif ($element['type'] === 'image_list'): ?>
                                        <div class="image-list-container">
                                            <img src="<?= htmlspecialchars($element['content']) ?>" alt="Slide image">
                                            <ul>
                                                <?php foreach (explode("\n", $element['text']) as $item): ?>
                                                    <li><?= htmlspecialchars($item) ?></li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                    <?php elseif ($element['type'] === 'list'): ?>
                                        <ul>
                                            <?php foreach (explode("\n", $element['content']) as $item): ?>
                                                <li><?= htmlspecialchars($item) ?></li>
                                            <?php endforeach; ?>
                                        </ul>
                                    <?php elseif ($element['type'] === 'quote'): ?>
                                        <blockquote>
                                            <?= nl2br(htmlspecialchars($element['content'])) ?>
                                        </blockquote>
                                    <?php endif; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html> 