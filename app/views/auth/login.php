<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <title>Вход</title>
    <style>
        body {
            font-family: sans-serif;
            background-color: #f4f4f4;
            display: flex;
            height: 100vh;
            align-items: center;
            justify-content: center;
        }
        .form-container {
            background: white;
            padding: 2rem;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            width: 300px;
        }
        h2 {
            margin-top: 0;
        }
        input {
            width: 100%;
            padding: 0.6rem;
            margin-bottom: 1rem;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            width: 100%;
            padding: 0.7rem;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            margin-bottom: 1rem;
        }
        .link {
            margin-top: 1rem;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Вход</h2>

        <?php if (!empty($data['error'])): ?>
            <div class="error"><?php echo htmlspecialchars($data['error']); ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= BASE_URL ?>/auth/login">
            <input type="email" name="email" placeholder="Имейл" required>
            <input type="password" name="password" placeholder="Парола" required>
            <button type="submit">Влез</button>
        </form>

        <div class="link">
            <a href="<?= BASE_URL ?>/auth/register">Нямаш акаунт?</a>
        </div>
    </div>
</body>
</html>