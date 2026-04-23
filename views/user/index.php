<section class="container-fluid mb-3">
    <h2 class="mb-3"><i class="fas fa-users mr-2"></i>User List</h2>
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center">
            <span><i class="fas fa-list mr-1"></i> All Users</span>
            <a class="btn btn-sm btn-light" href="<?= APP_URL ?>/users/create" role="button">
                <i class="fas fa-user-plus mr-1"></i> Add User
            </a>
        </div>
        <div class="card-body">
            <table class="table table-hover table-bordered" id="tabla_id" style="visibility: hidden;">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>First Name</th>
                        <th>Last Name</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Admin</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $counter = 1;
                    foreach ($users as $user) :
                    ?>
                        <tr>
                            <td class="text-center"><?= $counter++ ?></td>
                            <td><?= htmlspecialchars($user['first_name']) ?></td>
                            <td><?= htmlspecialchars($user['last_name']) ?></td>
                            <td><?= htmlspecialchars($user['username']) ?></td>
                            <td><?= htmlspecialchars($user['email']) ?></td>
                            <td class="text-center">
                                <?php if ($user['is_admin']): ?>
                                    <span class="badge badge-success badge-pill p-2">Yes</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary badge-pill p-2">No</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    <a class="btn btn-sm btn-outline-success"
                                        href="<?= APP_URL ?>/users/edit?id=<?= $user['id'] ?>" role="button">
                                        <i class="fas fa-user-edit"></i>
                                    </a>
                                    <button
                                        type="button"
                                        class="btn btn-sm btn-outline-danger js-delete-user"
                                        data-delete-url="<?= APP_URL ?>/users/delete?id=<?= $user['id'] ?>"
                                        data-name="<?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name'], ENT_QUOTES, 'UTF-8') ?>"
                                        data-username="<?= htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8') ?>">
                                        <i class="fas fa-user-minus"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</section>
