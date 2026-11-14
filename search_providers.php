<?php
require_once __DIR__ . '/../config/database.php';

$service = trim($_GET['service'] ?? '');
$location = trim($_GET['location'] ?? '');

if ($service === '' || $location === '') {
    header('Location: ../index.html?search=missing');
    exit;
}

$serviceLike = '%' . $service . '%';
$locationLike = '%' . $location . '%';

$stmt = $conn->prepare(
    "SELECT u.full_name, u.phone, p.category, p.experience, p.service_area, p.bio
     FROM provider_details p
     INNER JOIN users u ON u.id = p.user_id
     WHERE u.role = 'provider'
       AND (p.category LIKE ? OR ? = '')
       AND p.service_area LIKE ?
     ORDER BY u.full_name ASC
     LIMIT 100"
);
$stmt->bind_param('sss', $serviceLike, $service, $locationLike);
$stmt->execute();
$result = $stmt->get_result();

function h($value): string {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Search Results | SkillLink</title>
<style>
*{box-sizing:border-box;margin:0;padding:0}
body{font-family:Arial,sans-serif;background:#f3f8f5;color:#173b32}
header{padding:20px 8%;background:white;border-bottom:1px solid #e2ece6;display:flex;justify-content:space-between;align-items:center}
.logo{font-size:26px;font-weight:bold;color:#087f68;text-decoration:none}
main{max-width:1050px;margin:35px auto;padding:20px}
h1{margin-bottom:10px}
.subtitle{color:#65756d;margin-bottom:25px;line-height:1.6}
.results{display:grid;grid-template-columns:repeat(auto-fit,minmax(250px,1fr));gap:18px}
.card{background:white;border:1px solid #e4eee7;border-radius:16px;padding:22px;box-shadow:0 8px 25px rgba(16,46,40,.05)}
.card h2{font-size:20px;margin-bottom:12px}
.card p{margin:9px 0;line-height:1.5;color:#587067;overflow-wrap:anywhere}
.badge{display:inline-block;background:#eaf8f1;color:#087f68;padding:6px 10px;border-radius:20px;font-size:12px;font-weight:bold;margin-bottom:10px}
.empty{background:white;padding:25px;border-radius:14px;line-height:1.8}
.btn{display:inline-block;margin-top:20px;padding:12px 17px;background:#087f68;color:white;text-decoration:none;border-radius:9px;font-weight:bold}
</style>
</head>
<body>
<header>
    <a class="logo" href="../index.html">SkillLink</a>
    <a href="../login.html">Login</a>
</header>
<main>
    <h1>Service Providers</h1>
    <p class="subtitle">
        Search: <strong><?= h($service) ?></strong>
        &nbsp; | &nbsp; Area: <strong><?= h($location) ?></strong>
    </p>

    <?php if ($result->num_rows > 0): ?>
        <div class="results">
        <?php while ($provider = $result->fetch_assoc()): ?>
            <article class="card">
                <span class="badge"><?= h($provider['category']) ?></span>
                <h2><?= h($provider['full_name']) ?></h2>
                <p><strong>Experience:</strong> <?= h($provider['experience']) ?></p>
                <p><strong>Service Area:</strong> <?= h($provider['service_area']) ?></p>
                <?php if (!empty($provider['bio'])): ?>
                    <p><?= h($provider['bio']) ?></p>
                <?php endif; ?>
                <p><strong>Phone:</strong> <?= h($provider['phone']) ?></p>
            </article>
        <?php endwhile; ?>
        </div>
    <?php else: ?>
        <div class="empty">
            <strong>No matching providers found.</strong><br>
            Try another service name or nearby area. Providers appear here after they register as Service Providers.
        </div>
    <?php endif; ?>

    <a class="btn" href="../index.html">Back to Home</a>
</main>
</body>
</html>
