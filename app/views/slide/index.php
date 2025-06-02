<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Слайдове на презентация</title>
</head>
<body>
    <h1>Слайдове на презентация</h1>
    <ul>
        <?php foreach ($slides as $slide): ?>
            <li>
                <a href="/slide/view/<?php echo $slide['id']; ?>">
                    Слайд #<?php echo $slide['slide_order']; ?> - <?php echo htmlspecialchars($slide['type']); ?>
                </a>
            </li>
        <?php endforeach; ?>
    </ul>
    <a href="/presentation/view/<?php echo $slides[0]['presentation_id'] ?? ''; ?>">Обратно към презентацията</a>
</body>
</html>