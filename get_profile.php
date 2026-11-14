<?php
require_once __DIR__ . '/../config/database.php';

header('Content-Type: text/html; charset=utf-8');

if (empty($_SESSION['user_id'])) {
    header('Location: ../login.html?error=' . urlencode('Please log in to view your profile.'));
    exit;
}

$userId = (int)$_SESSION['user_id'];

$stmt = $conn->prepare(
    'SELECT u.id, u.full_name, u.email, u.phone, u.role, u.created_at,
            p.category, p.experience, p.service_area, p.bio
     FROM users u
     LEFT JOIN provider_details p ON p.user_id = u.id
     WHERE u.id = ? LIMIT 1'
);
$stmt->bind_param('i', $userId);
$stmt->execute();
$user = $stmt->get_result()->fetch_assoc();
$stmt->close();

if (!$user) {
    session_destroy();
    header('Location: ../login.html?error=' . urlencode('Account not found. Please log in again.'));
    exit;
}

function e($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Profile | SkillLink</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:Arial,sans-serif;background:#f3f8f5;color:#173b32;min-height:100vh}
header{background:#fff;padding:20px 8%;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid #e2ece6}
.logo{font-size:26px;font-weight:bold;color:#087f68;text-decoration:none}
a{color:#087f68}
main{max-width:760px;margin:45px auto;padding:20px}
.card{background:white;border:1px solid #e4eee7;border-radius:18px;padding:30px;box-shadow:0 10px 30px rgba(16,46,40,.06)}
h1{margin-bottom:8px}
.sub{color:#718078;margin-bottom:25px}
.row{padding:14px 0;border-bottom:1px solid #edf2ee;display:grid;grid-template-columns:180px 1fr;gap:15px;overflow-wrap:anywhere}
.row:last-child{border-bottom:0}
.label{font-weight:bold;color:#587067}
.actions{display:flex;gap:12px;flex-wrap:wrap;margin-top:25px}
.btn{display:inline-block;padding:12px 17px;border-radius:9px;background:#087f68;color:white;text-decoration:none;font-weight:bold}
.btn.secondary{background:#eaf8f1;color:#087f68}
@media(max-width:520px){main{margin:20px auto}.card{padding:22px}.row{grid-template-columns:1fr;gap:6px}header{padding:18px 5%}}
</style>
</head>
<body>
<header>
    <a class="logo" href="../index.html">SkillLink</a>
    <a href="logout.php">Log out</a>
</header>
<main>
    <section class="card">
        <h1>Welcome, <?= e($user['full_name']) ?>!</h1>
        <p class="sub">Your SkillLink account profile</p>

        <div class="row"><div class="label">Full Name</div><div><?= e($user['full_name']) ?></div></div>
        <div class="row"><div class="label">Email</div><div><?= e($user['email']) ?></div></div>
        <div class="row"><div class="label">Mobile Number</div><div><?= e($user['phone']) ?></div></div>
        <div class="row"><div class="label">Account Type</div><div><?= e(ucfirst($user['role'])) ?></div></div>

        <?php if ($user['role'] === 'provider'): ?>
            <div class="row"><div class="label">Service Category</div><div><?= e($user['category'] ?? '') ?></div></div>
            <div class="row"><div class="label">Experience</div><div><?= e($user['experience'] ?? '') ?></div></div>
            <div class="row"><div class="label">Service Area</div><div><?= e($user['service_area'] ?? '') ?></div></div>
        <?php endif; ?>

        <div class="row"><div class="label">Member Since</div><div><?= e($user['created_at']) ?></div></div>

        <div class="actions">
            <a class="btn" href="../index.html">Home</a>
            <a class="btn secondary" href="logout.php">Log out</a>
        </div>
    </section>
</main>
</body>
</html>
