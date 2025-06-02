<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Слайд #<?php echo $slide['slide_order']; ?></title>
</head>
<body>
    <h1>Слайд #<?php echo $slide['slide_order']; ?></h1>
    <p>Тип: <?php echo htmlspecialchars($slide['type']); ?></p>
    <p>Наредба: <?php echo htmlspecialchars($slide['layout']); ?></p>
    <p>Стил: <?php echo htmlspecialchars($slide['style']); ?></p>

    <div>
        <?php echo nl2br(htmlspecialchars($slide['content'])); ?>
    </div>

    <p>Навигация:</p>
    <pre><?php echo htmlspecialchars($slide['navigation']); ?></pre>

    <a href="/slide/index/<?php echo htmlspecialchars($slide['presentation_id']); ?>">Обратно към списъка със слайдове</a>
</body>
</html>