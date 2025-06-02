<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($workspace['name']); ?></title>
</head>
<body>
    <h1><?php echo htmlspecialchars($workspace['name']); ?></h1>
    <p><?php echo nl2br(htmlspecialchars($workspace['description'])); ?></p>
    <p>Език: <?php echo htmlspecialchars($workspace['language']); ?></p>
    <a href="/workspace/index">Обратно към списъка</a>
</body>
</html>