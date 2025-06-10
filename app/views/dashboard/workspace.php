<?php
$title = 'Работно пространство';
?>
<div class="header">
    <h1><i class="fas fa-briefcase"></i> <?= htmlspecialchars($data['workspace']['name']) ?></h1>
    <div class="actions mb-3">
        <?php if ($data['isOwner']): ?>
            <a href="<?= BASE_URL ?>/dashboard/editWorkspace/<?= $data['workspace']['id'] ?>" class="btn btn-secondary"><i class="fas fa-edit"></i> Редактирай</a>
            <a href="<?= BASE_URL ?>/dashboard/shareWorkspace/<?= $data['workspace']['id'] ?>" class="btn btn-success"><i class="fas fa-share-alt"></i> Сподели</a>
            <a href="<?= BASE_URL ?>/dashboard/deleteWorkspace/<?= $data['workspace']['id'] ?>" class="btn btn-danger"><i class="fas fa-trash"></i> Изтрий</a>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>/dashboard" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Назад към таблото</a>
    </div>
</div>

<div class="workspace-info my-2">
    <h2><i class="fas fa-info-circle"></i> Информация за работното пространство</h2>
    <p><i class="fas fa-calendar"></i> Създадено на: <?= date('d.m.Y H:i', strtotime($data['workspace']['created_at'])) ?></p>
</div>

<div class="header mb-3">
    <h2><i class="fas fa-file-powerpoint"></i> Презентации</h2>
    <?php if ($data['isOwner']): ?>
        <a href="<?= BASE_URL ?>/presentation/create/<?= $data['workspace']['id'] ?>" class="btn"><i class="fas fa-plus"></i> Нова презентация</a>
    <?php endif; ?>
</div>

<?php if (empty($data['presentations'])): ?>
    <div class="empty-state">
        <h2><i class="fas fa-folder-open"></i> Няма презентации</h2>
        <p>Създайте първата си презентация в това работно пространство.</p>
        <?php if ($data['isOwner']): ?>
            <a href="<?= BASE_URL ?>/presentation/create/<?= $data['workspace']['id'] ?>" class="btn"><i class="fas fa-plus"></i> Създай презентация</a>
        <?php endif; ?>
    </div>
<?php else: ?>
    <div class="presentations">
        <?php foreach ($data['presentations'] as $presentation): ?>
            <div class="presentation-card">
                <h3><i class="fas fa-file-powerpoint"></i> <?= htmlspecialchars($presentation['title']) ?></h3>
                <div class="meta">
                    <p><i class="fas fa-language"></i> Език: <?= strtoupper($presentation['language']) ?></p>
                    <p><i class="fas fa-palette"></i> Тема: <?= ucfirst($presentation['theme']) ?></p>
                    <p><i class="fas fa-calendar"></i> Създадено: <?= date('d.m.Y H:i', strtotime($presentation['created_at'])) ?></p>
                </div>
                <a href="<?= BASE_URL ?>/presentation/viewPresentation/<?= $presentation['id'] ?>" class="btn"><i class="fas fa-eye"></i> Отвори</a>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?> 