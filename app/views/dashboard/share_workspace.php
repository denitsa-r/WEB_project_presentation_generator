<div class="row">
    <div class="col-md-8 offset-md-2">
        <div class="card">
            <div class="card-header">
                <h2>Споделяне на работно пространство: <?= htmlspecialchars($workspace['name']) ?></h2>
            </div>
            <div class="card-body">
                <?php if (isset($_GET['error'])): ?>
                    <div class="alert alert-danger">
                        <?= htmlspecialchars($_GET['error']) ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success">
                        <?= htmlspecialchars($_GET['success']) ?>
                    </div>
                <?php endif; ?>

                <form action="<?= BASE_URL ?>/dashboard/shareWorkspace/<?= $workspace['id'] ?>" method="POST">
                    <div class="form-group">
                        <label for="email">Имейл на потребителя:</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>

                    <button type="submit" class="btn btn-primary">Сподели</button>
                </form>

                <hr>

                <h3>Текущи членове</h3>
                <?php if (empty($members)): ?>
                    <p>Няма споделени потребители.</p>
                <?php else: ?>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Потребител</th>
                                <th>Роля</th>
                                <th>Действия</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($members as $member): ?>
                                <tr>
                                    <td><?= htmlspecialchars($member['email']) ?></td>
                                    <td><?= htmlspecialchars($member['role']) ?></td>
                                    <td>
                                        <form action="<?= BASE_URL ?>/dashboard/removeMember/<?= $workspace['id'] ?>" method="POST" class="d-inline">
                                            <input type="hidden" name="user_id" value="<?= $member['user_id'] ?>">
                                            <button type="submit" class="btn btn-danger btn-sm">Премахни</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>

                <a href="<?= BASE_URL ?>/dashboard/workspace/<?= $workspace['id'] ?>" class="btn btn-secondary">Назад</a>
            </div>
        </div>
    </div>
</div> 