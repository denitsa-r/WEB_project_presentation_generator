<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Ново работно пространство</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        h1 {
            margin-top: 0;
            color: #333;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 5px;
            color: #666;
        }
        input[type="text"] {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
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
        .error {
            color: #dc3545;
            margin-bottom: 15px;
        }
        .actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Ново работно пространство</h1>

        <?php if (!empty($data['error'])): ?>
            <div class="error"><?= $data['error'] ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/dashboard/createWorkspace">
            <div class="form-group">
                <label for="name">Име на работното пространство</label>
                <input type="text" id="name" name="name" required>
            </div>

            <div class="actions">
                <button type="submit" class="btn">Създай</button>
                <a href="<?= BASE_URL ?>/dashboard" class="btn btn-secondary">Отказ</a>
            </div>
        </form>
    </div>
</body>
</html> 