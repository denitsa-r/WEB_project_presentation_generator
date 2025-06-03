<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title><?= $data['title'] ?></title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .presentations {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .presentation-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .presentation-card h3 {
            margin-top: 0;
            color: #333;
        }
        .presentation-card .meta {
            color: #666;
            font-size: 0.9em;
            margin-bottom: 15px;
        }
        .btn {
            display: inline-block;
            padding: 8px 16px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            border: none;
            cursor: pointer;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .btn-secondary {
            background-color: #6c757d;
        }
        .btn-secondary:hover {
            background-color: #545b62;
        }
        .empty-state {
            text-align: center;
            padding: 40px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .workspace-info {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1><?= htmlspecialchars($data['workspace']['name']) ?></h1>
            <a href="<?= BASE_URL ?>/dashboard" class="btn btn-secondary">Назад към таблото</a>
        </div>

        <div class="workspace-info">
            <h2>Информация за работното пространство</h2>
            <p>Създадено на: <?= date('d.m.Y H:i', strtotime($data['workspace']['created_at'])) ?></p>
        </div>

        <div class="header">
            <h2>Презентации</h2>
            <a href="<?= BASE_URL ?>/presentation/create/<?= $data['workspace']['id'] ?>" class="btn">Нова презентация</a>
        </div>

        <?php if (empty($data['presentations'])): ?>
            <div class="empty-state">
                <h2>Няма презентации</h2>
                <p>Създайте първата си презентация в това работно пространство.</p>
                <a href="<?= BASE_URL ?>/presentation/create/<?= $data['workspace']['id'] ?>" class="btn">Създай презентация</a>
            </div>
        <?php else: ?>
            <div class="presentations">
                <?php foreach ($data['presentations'] as $presentation): ?>
                    <div class="presentation-card">
                        <h3><?= htmlspecialchars($presentation['title']) ?></h3>
                        <div class="meta">
                            <p>Език: <?= strtoupper($presentation['language']) ?></p>
                            <p>Тема: <?= ucfirst($presentation['theme']) ?></p>
                            <p>Създадено: <?= date('d.m.Y H:i', strtotime($presentation['created_at'])) ?></p>
                        </div>
                        <a href="<?= BASE_URL ?>/presentation/view/<?= $presentation['id'] ?>" class="btn">Отвори</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 