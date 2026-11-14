
<?php

require_once __DIR__ . '/../config/database.php';

// Only allow form submissions
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../register.html');
    exit;
}

// Redirect back with an error message
function failRegistration($message)
{
    header('Location: ../register.html?error=' . urlencode($message));
    exit;
}

// Get form values safely
$fullName = isset($_POST['fullName']) ? trim($_POST['fullName']) : '';
$email = isset($_POST['email']) ? strtolower(trim($_POST['email'])) : '';
$phone = isset($_POST['phone']) ? trim($_POST['phone']) : '';
$password = isset($_POST['password']) ? $_POST['password'] : '';
$confirmPassword = isset($_POST['confirmPassword']) ? $_POST['confirmPassword'] : '';
$role = isset($_POST['role']) ? $_POST['role'] : 'customer';
$terms = isset($_POST['terms']) ? $_POST['terms'] : '';

$category = isset($_POST['category']) ? trim($_POST['category']) : '';
$experience = isset($_POST['experience']) ? trim($_POST['experience']) : '';
$serviceArea = isset($_POST['serviceArea']) ? trim($_POST['serviceArea']) : '';

// Allowed values
$allowedRoles = array('customer', 'provider');

$allowedCategories = array(
    'Plumbing',
    'Electrical',
    'Cleaning',
    'Painting',
    'Carpentry',
    'Beauty',
    'Tutoring',
    'Gardening',
    'Repair',
    'Other'
);

$allowedExperience = array(
    'Fresher',
    '1-2 years',
    '3-5 years',
    '5+ years'
);

// Validate name
if (strlen($fullName) < 2 || strlen($fullName) > 100) {
    failRegistration('Enter a name between 2 and 100 characters.');
}

// Validate email
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 150) {
    failRegistration('Enter a valid email address.');
}

// Validate phone
if (!preg_match('/^[6-9][0-9]{9}$/', $phone)) {
    failRegistration('Enter a valid 10-digit Indian mobile number.');
}

// Validate role
if (!in_array($role, $allowedRoles, true)) {
    failRegistration('Choose a valid account type.');
}

// Validate password
if (strlen($password) < 8) {
    failRegistration('Password must contain at least 8 characters.');
}

if ($password !== $confirmPassword) {
    failRegistration('Passwords do not match.');
}

// Validate terms
if ($terms !== 'accepted') {
    failRegistration('Please accept the Terms and Conditions.');
}

// Validate provider information
if ($role === 'provider') {

    if (!in_array($category, $allowedCategories, true)) {
        failRegistration('Choose a valid service category.');
    }

    if (!in_array($experience, $allowedExperience, true)) {
        failRegistration('Choose a valid experience level.');
    }

    if ($serviceArea === '' || strlen($serviceArea) > 150) {
        failRegistration('Enter your service area.');
    }
}

// Check database connection
if (!isset($conn) || !$conn) {
    die('Database connection failed. Please check config/database.php.');
}

// Check whether email already exists
$check = $conn->prepare(
    'SELECT id FROM users WHERE email = ? LIMIT 1'
);

if (!$check) {
    error_log('SkillLink email-check query error: ' . $conn->error);
    failRegistration('Database query failed. Please check your database tables.');
}

$check->bind_param('s', $email);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    $check->close();
    failRegistration('An account with this email already exists. Please log in.');
}

$check->close();

// Hash password
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

// Start database transaction
$conn->begin_transaction();

try {

    // Insert user details
    $insertUser = $conn->prepare(
        'INSERT INTO users (full_name, email, phone, password, role)
         VALUES (?, ?, ?, ?, ?)'
    );

    if (!$insertUser) {
        throw new Exception('Could not prepare user registration query.');
    }

    $insertUser->bind_param(
        'sssss',
        $fullName,
        $email,
        $phone,
        $hashedPassword,
        $role
    );

    $insertUser->execute();

    $userId = (int) $conn->insert_id;

    $insertUser->close();

    // Insert provider details when the account type is provider
    if ($role === 'provider') {

        $insertProvider = $conn->prepare(
            'INSERT INTO provider_details
             (user_id, category, experience, service_area)
             VALUES (?, ?, ?, ?)'
        );

        if (!$insertProvider) {
            throw new Exception('Could not prepare provider details query.');
        }

        $insertProvider->bind_param(
            'isss',
            $userId,
            $category,
            $experience,
            $serviceArea
        );

        $insertProvider->execute();
        $insertProvider->close();
    }

    // Save changes
    $conn->commit();

    // Registration successful
    header('Location: ../login.html?registered=success');
    exit;

} catch (Exception $e) {

    $conn->rollback();

    error_log('SkillLink registration error: ' . $e->getMessage());

    failRegistration(
        'Registration failed. Please check the database and table structure.'
    );
}
?>
```