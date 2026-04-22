<?php if (!empty($message)): ?>
    <div class="alert alert-success alert-dismissible fade show text-center m-auto" style="max-width:50%;" role="alert">
        <strong><?= htmlspecialchars($message) ?></strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>
<?php if (!empty($error)): ?>
    <div class="alert alert-danger alert-dismissible fade show text-center m-auto" style="max-width:50%;" role="alert">
        <strong><?= htmlspecialchars($error) ?></strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

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
            <div class="table-responsive">
                <table class="table table-hover table-bordered table-striped" id="tabla_id" style="visibility: hidden;">
                    <thead class="bg-dark text-white">
                        <tr>
                            <th>ID</th>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Username</th>
                            <th>Email</th>
                            <th>Admin</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="text-center"><?= $user['id'] ?></td>
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
                                        <button type="button" class="btn btn-sm btn-outline-danger"
                                            data-toggle="modal" data-target="#deleteModal<?= $user['id'] ?>">
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
    </div>
</section>

<!-- Delete confirmation modals (outside table) -->
<?php foreach ($users as $user): ?>
    <div class="modal fade" id="deleteModal<?= $user['id'] ?>" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">Delete User</h5>
                    <button type="button" class="close" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this user?</p>
                    <p><strong>Name:</strong> <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></p>
                    <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        <i class="fas fa-times"></i> Cancel
                    </button>
                    <a href="<?= APP_URL ?>/users/delete?id=<?= $user['id'] ?>"
                        class="btn btn-danger">
                        <i class="fas fa-trash"></i> Delete
                    </a>
                </div>
            </div>
        </div>
    </div>
<?php endforeach; ?>
