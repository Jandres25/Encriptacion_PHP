<?php
$eventLabels = [
    'login_success'    => ['label' => 'Login',             'class' => 'success'],
    'login_failed'     => ['label' => 'Failed Login',      'class' => 'danger'],
    'logout'           => ['label' => 'Logout',            'class' => 'secondary'],
    'password_changed' => ['label' => 'Password Changed',  'class' => 'warning'],
    'password_reset'   => ['label' => 'Password Reset',    'class' => 'info'],
    'user_created'     => ['label' => 'User Created',      'class' => 'primary'],
    'user_updated'     => ['label' => 'User Updated',      'class' => 'primary'],
    'user_deleted'     => ['label' => 'User Deleted',      'class' => 'dark'],
];
?>
<section class="container-fluid mb-3">
    <h2 class="mb-3"><i class="fas fa-clipboard-list mr-2"></i>Activity Log</h2>
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">
            <i class="fas fa-history mr-1"></i> All Events
        </div>
        <div class="card-body">
            <table class="table table-hover table-bordered" id="activity_log_table" style="visibility: hidden;">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>User</th>
                        <th>Event</th>
                        <th>Description</th>
                        <th>IP Address</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($logs as $log): ?>
                        <?php
                        $event = $eventLabels[$log['event']] ?? ['label' => htmlspecialchars($log['event'], ENT_QUOTES, 'UTF-8'), 'class' => 'light'];
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($log['created_at'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($log['user_name'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td>
                                <span class="badge badge-<?= $event['class'] ?> badge-pill p-2">
                                    <?= $event['label'] ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($log['description'], ENT_QUOTES, 'UTF-8') ?></td>
                            <td><?= htmlspecialchars($log['ip_address'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
