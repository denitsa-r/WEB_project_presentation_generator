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
        .workspaces {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 20px;
        }
        .workspace-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .workspace-card h3 {
            margin-top: 0;
            color: #333;
        }
        .workspace-card .meta {
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
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Моите работни пространства</h1>
            <a href="<?= BASE_URL ?>/dashboard/createWorkspace" class="btn">Ново работно пространство</a>
        </div>

        <?php if (empty($data['workspaces'])): ?>
            <div class="empty-state">
                <h2>Нямате работни пространства</h2>
                <p>Създайте първото си работно пространство, за да започнете да създавате презентации.</p>
                <a href="<?= BASE_URL ?>/dashboard/createWorkspace" class="btn">Създай работно пространство</a>
            </div>
        <?php else: ?>
            <div class="workspaces">
                <?php foreach ($data['workspaces'] as $workspace): ?>
                    <div class="workspace-card">
                        <h3><?= htmlspecialchars($workspace['name']) ?></h3>
                        <div class="meta">
                            <p>Роля: <?= ucfirst($workspace['role']) ?></p>
                            <p>Създадено: <?= date('d.m.Y H:i', strtotime($workspace['created_at'])) ?></p>
                        </div>
                        <a href="<?= BASE_URL ?>/dashboard/viewWorkspace/<?= $workspace['id'] ?>" class="btn">Отвори</a>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</body>
</html> 