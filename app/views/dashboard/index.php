<?php
$title = 'Табло';
?>
<div class="header">
    <h1>Моите работни пространства</h1>
    <a href="<?= BASE_URL ?>/dashboard/createWorkspace" class="btn mb-3">Ново работно пространство</a>
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