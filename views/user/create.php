<?php include __DIR__ . '/../../templates/header.php'; ?>

<?php if (!empty($_GET['error'])): ?>
    <div class="alert alert-danger text-center m-auto" style="max-width:60%;">
        <?= htmlspecialchars($_GET['error']) ?>
    </div>
<?php endif; ?>

<section class="container mb-3" style="max-width: 720px;">
    <h2 class="mb-3"><i class="fas fa-user-plus mr-2"></i>Create User</h2>
    <div class="card shadow-sm">
        <div class="card-header bg-dark text-white">User Details</div>
        <form action="<?= APP_URL ?>/?page=users/create" method="post">
            <div class="card-body">
                <div class="mb-3">
                    <label for="first_name" class="form-label">First Name</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" name="first_name" id="first_name"
                            placeholder="e.g. John" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="last_name" class="form-label">Last Name</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" name="last_name" id="last_name"
                            placeholder="e.g. Doe" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        </div>
                        <input type="email" class="form-control" name="email" id="email"
                            placeholder="e.g. john@gmail.com">
                    </div>
                    <small class="form-text text-muted">Required for password recovery</small>
                </div>
                <div class="mb-3">
                    <label for="username" class="form-label">Username</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" name="username" id="username"
                            placeholder="e.g. john10" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        </div>
                        <input type="password" class="form-control" name="password" id="password"
                            placeholder="Password" required>
                    </div>
                </div>
                <div class="form-check">
                    <input type="checkbox" class="form-check-input" name="is_admin" id="is_admin" value="1">
                    <label class="form-check-label" for="is_admin">Administrator</label>
                </div>
            </div>

            <div class="card-footer">
                <div class="row">
                    <div class="col-12 col-sm-auto mb-2 mb-sm-0">
                        <button type="submit" name="add_user" class="btn btn-outline-primary w-100">
                            <i class="fas fa-user-plus"></i> Add User
                        </button>
                    </div>
                    <div class="col-12 col-sm-auto">
                        <a class="btn btn-outline-secondary w-100" href="<?= APP_URL ?>/?page=users" role="button">
                            <i class="fas fa-undo"></i> Cancel
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>

<?php include __DIR__ . '/../../templates/footer.php'; ?>