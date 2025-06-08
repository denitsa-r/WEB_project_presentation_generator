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
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #000;
            overflow-x: hidden;
            width: 100%;
        }
        .review-container {
            width: 100%;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            overflow-x: hidden;
        }
        .review-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background-color: rgba(0, 0, 0, 0.8);
            color: white;
            padding: 1rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            z-index: 1000;
        }
        .review-header h1 {
            margin: 0;
            font-size: 1.5rem;
        }
        .review-controls {
            display: flex;
            gap: 1rem;
        }
        .review-controls button {
            background: none;
            border: none;
            color: white;
            cursor: pointer;
            font-size: 1.2rem;
            padding: 0.5rem;
        }
        .review-controls button:hover {
            color: #007bff;
        }
        .slides-container {
            flex: 1;
            padding: 4rem 1rem 2rem;
            display: flex;
            flex-direction: column;
            gap: 3rem;
            width: 100%;
            box-sizing: border-box;
        }
        .slide {
            background: white;
            border-radius: 12px;
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.1);
            position: relative;
            padding: 3rem;
            min-height: 600px;
            display: flex;
            flex-direction: column;
            width: 100%;
            box-sizing: border-box;
        }
        .slide-header {
            text-align: center;
            margin-bottom: 2rem;
            padding-bottom: 1rem;
            border-bottom: 2px solid #eee;
        }
        .slide-header h2 {
            font-size: 1.8rem;
            color: #333;
            margin: 0;
        }
        .slide-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }
        .content-element {
            padding: 1.5rem;
            border-radius: 8px;
            background: #f8f9fa;
            width: 100%;
            box-sizing: border-box;
        }
        .content-element h3 {
            font-size: 2rem;
            margin-bottom: 1.5rem;
        }
        .content-element p {
            font-size: 1.4rem;
            line-height: 1.6;
        }
        .content-element.type-list ul {
            font-size: 1.4rem;
            line-height: 1.6;
            padding-left: 2rem;
        }
        .content-element.type-list li {
            margin-bottom: 1rem;
        }
        .content-element.type-quote blockquote {
            font-size: 1.6rem;
            line-height: 1.6;
            padding: 2rem;
            border-left-width: 6px;
        }
        .content-element.type-quote cite {
            font-size: 1.2rem;
            margin-top: 1.5rem;
        }
        .content-element.type-image_text .image-text-container,
        .content-element.type-image_list .image-list-container {
            gap: 3rem;
            width: 100%;
            box-sizing: border-box;
        }
        .content-element.type-image_text .image-text-container img,
        .content-element.type-image_list .image-list-container img {
            max-width: 50%;
            height: auto;
            border-radius: 8px;
            box-sizing: border-box;
        }
        .content-element.type-image img {
            max-width: 100%;
            height: auto;
            border-radius: 8px;
            box-sizing: border-box;
        }
        .slide-number {
            position: absolute;
            bottom: 1.5rem;
            right: 1.5rem;
            color: #666;
            font-size: 1.2rem;
            font-weight: bold;
        }
        .fullscreen-btn {
            position: fixed;
            bottom: 1rem;
            right: 1rem;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            border: none;
            border-radius: 50%;
            width: 3rem;
            height: 3rem;
            cursor: pointer;
            z-index: 1000;
        }
        .fullscreen-btn:hover {
            background: rgba(0, 0, 0, 0.9);
        }
        .print-btn {
            position: fixed;
            bottom: 1rem;
            right: 5rem;
            background: rgba(0, 0, 0, 0.8);
            color: white;
            border: none;
            border-radius: 50%;
            width: 3rem;
            height: 3rem;
            cursor: pointer;
            z-index: 1000;
        }
        .print-btn:hover {
            background: rgba(0, 0, 0, 0.9);
        }
        @media print {
            body {
                background: white;
            }
            .review-header,
            .fullscreen-btn,
            .print-btn {
                display: none;
            }
            .slides-container {
                max-width: none;
                padding: 0;
                gap: 2rem;
            }
            .slide {
                min-height: auto;
                padding: 2rem;
                box-shadow: none;
                border: 1px solid #ddd;
                page-break-after: always;
            }
            .content-element {
                padding: 1rem;
            }
            .content-element h3 {
                font-size: 1.8rem;
            }
            .content-element p,
            .content-element.type-list ul {
                font-size: 1.2rem;
            }
            .content-element.type-quote blockquote {
                font-size: 1.4rem;
            }
        }
    </style>
</head>
<body>
    <div class="review-container">
        <div class="review-header">
            <h1><?= htmlspecialchars($data['presentation']['title']) ?></h1>
            <div class="review-controls">
                <button id="exitReview" title="Изход"><i class="fas fa-times"></i></button>
            </div>
        </div>

        <div class="slides-container" data-theme="<?= htmlspecialchars($data['presentation']['theme']) ?>">
            <?php foreach ($data['slides'] as $index => $slide): ?>
                <div class="slide" id="slide-<?= $index + 1 ?>">
                    <div class="slide-header">
                        <h2><?= htmlspecialchars($data['presentation']['title']) ?></h2>
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
                        <?php endif; ?>
                    </div>
                    <div class="slide-number"><?= $index + 1 ?> / <?= count($data['slides']) ?></div>
                </div>
            <?php endforeach; ?>
        </div>

        <button class="fullscreen-btn" id="fullscreenBtn" title="На цял екран">
            <i class="fas fa-expand"></i>
        </button>
        <button class="print-btn" id="printBtn" title="Принтирай">
            <i class="fas fa-print"></i>
        </button>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const exitBtn = document.getElementById('exitReview');
            const fullscreenBtn = document.getElementById('fullscreenBtn');
            const printBtn = document.getElementById('printBtn');

            // Exit button
            exitBtn.addEventListener('click', function() {
                window.location.href = '<?= BASE_URL ?>/presentation/viewPresentation/<?= $data['presentation']['id'] ?>';
            });

            // Fullscreen
            fullscreenBtn.addEventListener('click', function() {
                if (!document.fullscreenElement) {
                    document.documentElement.requestFullscreen();
                    fullscreenBtn.innerHTML = '<i class="fas fa-compress"></i>';
                } else {
                    document.exitFullscreen();
                    fullscreenBtn.innerHTML = '<i class="fas fa-expand"></i>';
                }
            });

            // Print
            printBtn.addEventListener('click', function() {
                window.print();
            });

            // Keyboard navigation
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape') {
                    window.location.href = '<?= BASE_URL ?>/presentation/viewPresentation/<?= $data['presentation']['id'] ?>';
                }
            });
        });
    </script>
</body>
</html> 