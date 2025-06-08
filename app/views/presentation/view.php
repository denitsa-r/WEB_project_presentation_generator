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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <title>Преглед на презентация</title>
</head>
<body>
    <div class="container">
        <div class="presentation-view" data-theme="<?= htmlspecialchars($data['presentation']['theme']) ?>">
            <div class="presentation-header">
                <h1><?= htmlspecialchars($data['presentation']['title']) ?></h1>
                <div class="presentation-meta">
                    <span><i class="fas fa-language"></i> Език: <?= htmlspecialchars($data['presentation']['language']) ?></span>
                    <span><i class="fas fa-palette"></i> Тема: <?= htmlspecialchars($data['presentation']['theme']) ?></span>
                </div>
            </div>

            <?php if (isset($_GET['error'])): ?>
                <div class="error-message">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($_GET['error']) ?>
                </div>
            <?php endif; ?>

            <div class="presentation-actions">
                <a href="<?= BASE_URL ?>/presentation/review/<?= $data['presentation']['id'] ?>" class="btn btn-info">
                    <i class="fas fa-eye"></i> Преглед
                </a>
                <a href="<?= BASE_URL ?>/presentation/edit/<?= $data['presentation']['id'] ?>" class="btn btn-primary">
                    <i class="fas fa-edit"></i> Редактирай
                </a>
                <a href="<?= BASE_URL ?>/presentation/delete/<?= $data['presentation']['id'] ?>" class="btn btn-danger">
                    <i class="fas fa-trash"></i> Изтрий
                </a>
                <a href="<?= BASE_URL ?>/slide/create/<?= $data['presentation']['id'] ?>" class="btn btn-success">
                    <i class="fas fa-plus"></i> Добави слайд
                </a>
                <a href="<?= BASE_URL ?>/dashboard" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Назад
                </a>
            </div>

            <div class="slides-container">
                <?php if (empty($data['slides'])): ?>
                    <div class="empty-state">
                        <i class="fas fa-file-alt fa-3x"></i>
                        <p>Няма добавени слайдове.</p>
                        <a href="<?= BASE_URL ?>/slide/create/<?= $data['presentation']['id'] ?>" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Добави първи слайд
                        </a>
                    </div>
                <?php else: ?>
                    <?php foreach ($data['slides'] as $key => $slide): ?>
                        <div class="slide" draggable="true" data-slide-id="<?= $slide['id'] ?>" data-slide-order="<?= $slide['slide_order'] ?>">
                            <div class="slide-header">
                                <div class="slide-header-content">
                                    <h2 class="slide-title">
                                        <i class="fas fa-file-alt"></i> <?= htmlspecialchars($slide['title']) ?>
                                    </h2>
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
                            <div class="slide-content <?= htmlspecialchars($slide['layout'] ?? 'full') ?>">
                                <?php if (!empty($slide['elements'])): ?>
                                <?php foreach ($slide['elements'] as $element): ?>
                                    <div class="content-element type-<?= htmlspecialchars($element['type'] ?? 'text') ?>" <?php 
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
                                            <p><?= nl2br(htmlspecialchars($element['content'] ?? '')) ?></p>
                                        <?php elseif ($element['type'] === 'image'): ?>
                                            <img src="<?= htmlspecialchars($element['content'] ?? '') ?>" alt="<?= htmlspecialchars($element['title'] ?? '') ?>">
                                        <?php elseif ($element['type'] === 'image_text'): ?>
                                            <div class="image-text-container">
                                                <img src="<?= htmlspecialchars($element['content'] ?? '') ?>" alt="<?= htmlspecialchars($element['title'] ?? '') ?>">
                                                <p><?= nl2br(htmlspecialchars($element['text'] ?? '')) ?></p>
                                            </div>
                                        <?php elseif ($element['type'] === 'list'): ?>
                                            <ul>
                                                <?php 
                                                $items = !empty($element['content']) ? explode("\n", $element['content']) : [];
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
                                                        if (trim($item) !== ''):
                                                    ?>
                                                        <li><?= htmlspecialchars(trim($item)) ?></li>
                                                    <?php 
                                                        endif;
                                                    endforeach; 
                                                    ?>
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
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const slidesContainer = document.querySelector('.slides-container');
            const slides = document.querySelectorAll('.slide');
            let draggedSlide = null;

            // Добавяме събития за drag and drop
            slides.forEach(slide => {
                slide.addEventListener('dragstart', function(e) {
                    draggedSlide = this;
                    this.classList.add('dragging');
                    e.dataTransfer.setData('text/plain', this.dataset.slideId);
                });

                slide.addEventListener('dragend', function() {
                    this.classList.remove('dragging');
                    draggedSlide = null;
                });

                slide.addEventListener('dragover', function(e) {
                    e.preventDefault();
                    const draggingSlide = document.querySelector('.dragging');
                    if (draggingSlide !== this) {
                        const rect = this.getBoundingClientRect();
                        const midY = rect.top + rect.height / 2;
                        
                        if (e.clientY < midY) {
                            this.parentNode.insertBefore(draggingSlide, this);
                        } else {
                            this.parentNode.insertBefore(draggingSlide, this.nextSibling);
                        }
                    }
                });

                slide.addEventListener('dragenter', function(e) {
                    e.preventDefault();
                    this.classList.add('drag-over');
                });

                slide.addEventListener('dragleave', function() {
                    this.classList.remove('drag-over');
                });

                slide.addEventListener('drop', function(e) {
                    e.preventDefault();
                    this.classList.remove('drag-over');
                    
                    if (draggedSlide !== this) {
                        const allSlides = [...slides];
                        const draggedIndex = allSlides.indexOf(draggedSlide);
                        const droppedIndex = allSlides.indexOf(this);

                        if (draggedIndex < droppedIndex) {
                            this.parentNode.insertBefore(draggedSlide, this.nextSibling);
                        } else {
                            this.parentNode.insertBefore(draggedSlide, this);
                        }

                        // Обновяваме реда на слайдовете
                        updateSlideOrder();
                    }
                });
            });

            function updateSlideOrder() {
                const slides = document.querySelectorAll('.slide');
                const newOrder = Array.from(slides).map((slide, index) => ({
                    id: slide.dataset.slideId,
                    order: index + 1
                }));

                // Изпращаме AJAX заявка за обновяване на реда
                fetch('<?= BASE_URL ?>/slides/updateOrder', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({
                        slides: newOrder
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Обновяваме data-slide-order атрибутите
                        slides.forEach((slide, index) => {
                            slide.dataset.slideOrder = index + 1;
                        });
                    } else {
                        console.error('Грешка при обновяване на реда на слайдовете');
                    }
                })
                .catch(error => {
                    console.error('Грешка при изпращане на заявката:', error);
                });
            }
        });
    </script>
</body>
</html> 