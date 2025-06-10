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
                <div class="presentation-actions">
                    <div class="dropdown">
                        <button class="btn btn-primary dropdown-toggle" type="button" id="exportDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Експорт
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="exportDropdown">
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/presentation/export/<?php echo $data['presentation']['id']; ?>/html">HTML</a></li>
                            <li><a class="dropdown-item" href="<?php echo BASE_URL; ?>/presentation/export/<?php echo $data['presentation']['id']; ?>/xml">XML</a></li>
                        </ul>
                    </div>
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

            <div class="presentation-preview">
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
                        <?php foreach ($data['slides'] as $index => $slide): ?>
                            <div class="slide-container">
                                <div class="slide" data-slide-id="<?php echo $slide['id']; ?>">
                                    <div class="slide-header">
                                        <div class="slide-header-content">
                                            <h2 class="slide-title">
                                                <i class="fas fa-file-alt"></i> <?php echo htmlspecialchars($slide['title']); ?>
                                            </h2>
                                            <div class="slide-actions">
                                                <a href="<?php echo BASE_URL; ?>/slide/edit/<?php echo $slide['id']; ?>" class="btn btn-primary">
                                                    <i class="fas fa-edit"></i> Редактирай
                                                </a>
                                                <a href="<?php echo BASE_URL; ?>/slides/delete/<?php echo $slide['id']; ?>" class="btn btn-danger">
                                                    <i class="fas fa-trash"></i> Изтрий
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="slide-content <?php echo htmlspecialchars($slide['layout'] ?? 'full'); ?>">
                                        <?php if (!empty($slide['elements'])): ?>
                                            <?php foreach ($slide['elements'] as $element): ?>
                                                <div class="element-container">
                                                    <?php if (!empty($element['title'])): ?>
                                                        <h3 class="element-title"><?php echo htmlspecialchars($element['title']); ?></h3>
                                                    <?php endif; ?>

                                                    <?php if ($element['type'] === 'image'): ?>
                                                        <div class="content-element image">
                                                            <div class="image-container" style="background-image: url('<?php echo $element['content']; ?>');"></div>
                                                        </div>
                                                    <?php elseif ($element['type'] === 'image_text'): ?>
                                                        <div class="content-element type-image_text">
                                                            <div class="image-text-container">
                                                                <div class="image-container" style="background-image: url('<?php echo $element['content']; ?>');"></div>
                                                                <div class="text"><p><?php echo nl2br(htmlspecialchars($element['text'])); ?></p></div>
                                                            </div>
                                                        </div>
                                                    <?php elseif ($element['type'] === 'image_list'): ?>
                                                        <div class="content-element type-image_list">
                                                            <div class="image-list-container">
                                                                <div class="image-container" style="background-image: url('<?php echo $element['content']; ?>');"></div>
                                                                <ul>
                                                                    <?php foreach (explode("\n", $element['text']) as $item): if (trim($item) !== ''): ?>
                                                                        <li><?php echo htmlspecialchars($item); ?></li>
                                                                    <?php endif; endforeach; ?>
                                                                </ul>
                                                            </div>
                                                        </div>
                                                    <?php elseif ($element['type'] === 'quote'): ?>
                                                        <div class="content-element type-quote">
                                                            <blockquote>
                                                                <?php echo nl2br(htmlspecialchars($element['content'])); ?>
                                                                <?php if (!empty($element['title'])): ?>
                                                                    <cite>— <?php echo htmlspecialchars($element['title']); ?></cite>
                                                                <?php endif; ?>
                                                            </blockquote>
                                                        </div>
                                                    <?php elseif ($element['type'] === 'list'): ?>
                                                        <div class="content-element type-list">
                                                            <ul>
                                                                <?php foreach (explode("\n", $element['content']) as $item): if (trim($item) !== ''): ?>
                                                                    <li><?php echo htmlspecialchars($item); ?></li>
                                                                <?php endif; endforeach; ?>
                                                            </ul>
                                                        </div>
                                                    <?php else: ?>
                                                        <div class="content-element type-text">
                                                            <?php echo nl2br(htmlspecialchars($element['content'])); ?>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="empty-content">Няма добавено съдържание</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="slide-controls">
                                    <?php if ($index > 0): ?>
                                        <button class="move-up" onclick="moveSlide(<?php echo $slide['id']; ?>, 'up')">
                                            <i class="fas fa-arrow-up"></i>
                                        </button>
                                    <?php endif; ?>
                                    <?php if ($index < count($data['slides']) - 1): ?>
                                        <button class="move-down" onclick="moveSlide(<?php echo $slide['id']; ?>, 'down')">
                                            <i class="fas fa-arrow-down"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <script>
        // JavaScript кодът е преместен в main.js

        // Функция за експорт на презентацията
        function exportPresentation() {
            const presentationId = <?= $data['presentation']['id'] ?>;
            
            // Изпращаме AJAX заявка към сървъра
            fetch(`${BASE_URL}/presentation/export/${presentationId}/html`, {
                method: 'GET',
                headers: {
                    'Accept': 'text/html'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Грешка при експорт на презентацията');
                }
                return response.blob();
            })
            .then(blob => {
                // Създаваме връзка за изтегляне
                const url = window.URL.createObjectURL(blob);
                const a = document.createElement('a');
                a.href = url;
                a.download = '<?= $data['presentation']['title'] ?>.html';
                document.body.appendChild(a);
                a.click();
                window.URL.revokeObjectURL(url);
                document.body.removeChild(a);
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Възникна грешка при експорт на презентацията');
            });
        }
    </script>
</body>
</html> 