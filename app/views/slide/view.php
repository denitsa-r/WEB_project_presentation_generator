<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Слайд #<?php echo $slide['slide_order']; ?></title>
</head>
<body>
    <h1>Слайд #<?php echo $slide['slide_order']; ?></h1>
    <p>Тип: <?php echo htmlspecialchars($slide['type']); ?></p>
    <p>Съдържание:</p>
    <div>
        <?php echo nl2br(htmlspecialchars($slide['content'])); ?>
    </div>
    <br>
    <a href="/slide/index/<?php echo $slide['presentation_id']; ?>">Обратно към списъка със слайдове</a>
</body>
</html>