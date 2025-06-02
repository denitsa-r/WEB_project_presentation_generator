<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Слайдове</title>
</head>
<body>
    <h1>Слайдове на презентация</h1>
    <a href="/slide/create/<?php echo htmlspecialchars($presentationId); ?>">Добави нов слайд</a>
    <ul>
        <?php foreach ($slides as $slide): ?>
            <li>
                <a href="/slide/view/<?php echo $slide['id']; ?>">
                    Слайд #<?php echo $slide['slide_order']; ?> (<?php echo htmlspecialchars($slide['type']); ?>)
                </a>
                [<a href="/slide/edit/<?php echo $slide['id']; ?>">редактирай</a>]
                [<a href="/slide/delete/<?php echo $slide['id']; ?>" onclick="return confirm('Сигурни ли сте?');">изтрий</a>]
            </li>
        <?php endforeach; ?>
    </ul>
    <a href="/presentation/view/<?php echo htmlspecialchars($presentationId); ?>">Обратно към презентацията</a>
</body>
</html>