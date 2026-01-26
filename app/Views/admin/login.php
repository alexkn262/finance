<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="<?= asset_url('assets/css/admin.css') ?>">
</head>
<body class="admin">
    <main class="installer">
        <h1>Admin login</h1>
        <form method="post" action="<?= base_url('/admin/login') ?>">
            <input type="hidden" name="csrf_token" value="<?= e($csrf) ?>">
            <label>Email <input type="email" name="email" required></label>
            <label>Password <input type="password" name="password" required></label>
            <button class="btn" type="submit">Sign in</button>
        </form>
    </main>
</body>
</html>
