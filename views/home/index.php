<!-- Hero -->
<section class="hero text-white text-center">
    <div class="container py-5">
        <i class="fas fa-user-circle fa-4x mb-3 text-light"></i>
        <h1 class="display-4 font-weight-bold mb-2">
            Welcome, <?= htmlspecialchars($name) ?>
        </h1>
        <p class="lead mb-4 text-light">
            Custom PHP MVC authentication system built with Composer,<br>
            a lightweight router, and role-based access control.
        </p>
        <?php if ($isAdmin): ?>
            <a href="<?= APP_URL ?>/users" class="btn btn-app-primary btn-lg px-4">
                <i class="fas fa-users mr-2"></i> Manage Users
            </a>
        <?php endif; ?>
    </div>
</section>

<!-- Feature cards -->
<section class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm text-center">
                <div class="card-body py-4">
                    <div class="feature-icon text-white mx-auto" style="background-color: var(--color-dark);">
                        <i class="fas fa-sitemap"></i>
                    </div>
                    <h5 class="card-title font-weight-bold">MVC Architecture</h5>
                    <p class="card-text text-muted">
                        PSR-4 autoloading via Composer with a custom <code>Router</code>,
                        abstract <code>Controller</code> and <code>Model</code> base classes,
                        and dedicated <code>Middleware</code> for auth and session guards.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm text-center">
                <div class="card-body py-4">
                    <div class="feature-icon text-white mx-auto" style="background-color: var(--color-dark);">
                        <i class="fas fa-lock"></i>
                    </div>
                    <h5 class="card-title font-weight-bold">Secure Authentication</h5>
                    <p class="card-text text-muted">
                        Bcrypt password hashing, persistent login via remember-me cookie,
                        automatic session timeout on inactivity, and token-based
                        password recovery sent by email via PHPMailer.
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-4">
            <div class="card h-100 shadow-sm text-center">
                <div class="card-body py-4">
                    <div class="feature-icon text-white mx-auto" style="background-color: var(--color-dark);">
                        <i class="fas fa-users-cog"></i>
                    </div>
                    <h5 class="card-title font-weight-bold">User Management</h5>
                    <p class="card-text text-muted">
                        Full admin CRUD with role-based access control, MySQLi prepared
                        statements, and a file-based cache for the users listing
                        with automatic invalidation on writes.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>