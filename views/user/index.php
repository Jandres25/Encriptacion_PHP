<?php include __DIR__ . '/../../templates/header.php'; ?>

<?php if (!empty($_GET['message'])): ?>
    <div class="alert alert-success alert-dismissible fade show text-center m-auto" style="max-width:50%;" role="alert">
        <strong><?= htmlspecialchars($_GET['message']) ?></strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>
<?php if (!empty($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
        <strong><?= htmlspecialchars($_GET['error']) ?></strong>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
<?php endif; ?>

<section class="container-fluid mb-3">
    <h2>User List</h2>
    <div class="card">
        <div class="card-header">
            <a class="btn btn-outline-primary" href="<?= APP_URL ?>/?page=users/create" role="button">
                <i class="fas fa-user-plus"></i> Add User
            </a>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="tabla_id">
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
                                <td><?= $user['id'] ?></td>
                                <td><?= htmlspecialchars($user['first_name']) ?></td>
                                <td><?= htmlspecialchars($user['last_name']) ?></td>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td><?= htmlspecialchars($user['email']) ?></td>
                                <td><?= $user['is_admin'] ? 'Yes' : 'No' ?></td>
                                <td class="d-flex justify-content-center">
                                    <a class="btn btn-outline-secondary mr-3"
                                       href="<?= APP_URL ?>/?page=users/edit&id=<?= $user['id'] ?>" role="button">
                                        <i class="fas fa-user-edit"></i> Edit
                                    </a>
                                    <button type="button" class="btn btn-outline-danger"
                                            data-toggle="modal" data-target="#deleteModal<?= $user['id'] ?>">
                                        <i class="fas fa-user-minus"></i> Delete
                                    </button>
                                </td>
                            </tr>
                            <!-- Delete confirmation modal -->
                            <div class="modal fade" id="deleteModal<?= $user['id'] ?>" tabindex="-1" role="dialog">
                                <div class="modal-dialog" role="document">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <h4 class="modal-title">Delete User</h4>
                                            <button type="button" class="close" data-dismiss="modal">
                                                <span>&times;</span>
                                            </button>
                                        </div>
                                        <div class="modal-body">
                                            <h5>Are you sure you want to delete this user?</h5>
                                            <p><strong>ID:</strong> <?= $user['id'] ?></p>
                                            <p><strong>Name:</strong> <?= htmlspecialchars($user['first_name'] . ' ' . $user['last_name']) ?></p>
                                            <p><strong>Username:</strong> <?= htmlspecialchars($user['username']) ?></p>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                                            <a href="<?= APP_URL ?>/?page=users/delete&id=<?= $user['id'] ?>"
                                               class="btn btn-danger">Delete</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer text-muted"></div>
    </div>
</section>

<?php include __DIR__ . '/../../templates/footer.php'; ?>
