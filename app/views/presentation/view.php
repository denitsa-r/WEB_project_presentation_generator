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
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/slides.css">
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
                        <div class="slide-content <?= htmlspecialchars($slide['layout']) ?>">
                            <?php if (!empty($slide['elements'])): ?>
                            <?php foreach ($slide['elements'] as $element): ?>
                                <div class="content-element type-<?= htmlspecialchars($element['type']) ?>" <?php 
                                    if (!empty($element['style'])) {
                                        $style = json_decode($element['style'], true);
                                        $styleString = '';
                                        if (!empty($style['color'])) $styleString .= 'color: ' . htmlspecialchars($style['color']) . ';';
                                        if (!empty($style['fontSize'])) $styleString .= 'font-size: ' . htmlspecialchars($style['fontSize']) . ';';
                                        if (!empty($style['textAlign'])) $styleString .= 'text-align: ' . htmlspecialchars($style['textAlign']) . ';';
                                        if ($styleString) echo 'style="' . $styleString . '"';
                                    }
                                ?>>
                                    <?php if (!empty($element['title'])): ?>
                                        <h3><?= htmlspecialchars($element['title']) ?></h3>
                                    <?php endif; ?>
                                    
                                    <?php if ($element['type'] === 'text'): ?>
                                        <p><?= nl2br(htmlspecialchars($element['content'])) ?></p>
                                    <?php elseif ($element['type'] === 'image'): ?>
                                        <img src="<?= htmlspecialchars($element['content']) ?>" alt="<?= htmlspecialchars($element['title']) ?>">
                                    <?php elseif ($element['type'] === 'image_text'): ?>
                                        <div class="image-text-container">
                                            <img src="<?= htmlspecialchars($element['content']) ?>" alt="<?= htmlspecialchars($element['title']) ?>">
                                            <p><?= nl2br(htmlspecialchars($element['text'])) ?></p>
                                        </div>
                                    <?php elseif ($element['type'] === 'list'): ?>
                                        <ul>
                                            <?php 
                                            $items = explode("\n", $element['content']);
                                            foreach ($items as $item):
                                                if (trim($item) !== ''):
                                            ?>
                                                <li><?= htmlspecialchars(trim($item)) ?></li>
                                            <?php 
                                                endif;
                                            endforeach;
                                            ?>
                                        </ul>
                                    <?php elseif ($element['type'] === 'image_list'): ?>
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
                                    <?php elseif ($element['type'] === 'quote'): ?>
                                        <blockquote>
                                            <?= nl2br(htmlspecialchars($element['content'] ?? '')) ?>
                                            <?php if (!empty($element['title'])): ?>
                                                <cite>— <?= htmlspecialchars($element['title']) ?></cite>
                                            <?php endif; ?>
                                        </blockquote>
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