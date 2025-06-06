<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/main.css">
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>/assets/css/slide-templates.css">
    <title>Цитат слайд</title>
</head>
<body>
    <div class="slide">
        <h2 class="slide-title"><?= htmlspecialchars($slide['title']) ?></h2>
        <div class="slide-content layout-full">
            <div class="content-element">
                <blockquote class="slide-quote">
                    <?= nl2br(htmlspecialchars($slide['content'])) ?>
                </blockquote>
            </div>
        </div>
    </div>
</body>
</html>

<style>
.quote-slide {
    padding: 2rem;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #f8f9fa;
}

.quote-slide .slide-content {
    text-align: center;
    max-width: 800px;
}

.quote-slide .quote {
    font-size: 2.5rem;
    line-height: 1.4;
    color: #333;
    margin: 0;
    padding: 0;
    font-style: italic;
    position: relative;
}

.quote-slide .quote:before,
.quote-slide .quote:after {
    content: '"';
    font-size: 4rem;
    color: #007bff;
    position: absolute;
    opacity: 0.2;
}

.quote-slide .quote:before {
    top: -2rem;
    left: -2rem;
}

.quote-slide .quote:after {
    bottom: -2rem;
    right: -2rem;
}

.quote-slide .quote-author {
    display: block;
    font-size: 1.5rem;
    color: #666;
    margin-top: 2rem;
    font-style: normal;
}
</style> 