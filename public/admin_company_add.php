```php
<?php
require_once('../config/config.php');

// CSRF ochrana
if(!isset($_SESSION)) { 
    session_start(); 
}
if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(!hash_equals($_SESSION['token'], $_POST['token'])) {
        die("Request forgery detected");
    }
}

// Form handling
if($_SERVER["REQUEST_METHOD"] == "POST") {
    if(isset($_POST['email']) && isset($_POST['password']) && isset($_POST['company_name']) && isset($_POST['contact_person']) && isset($_POST['participation_type'])) {
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $company_name = $_POST['company_name'];
        $contact_person = $_POST['contact_person'];
        $participation_type = $_POST['participation_type'];

        // Create user record
        $stmt = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (:email, :password, 'company')");
        $stmt->execute(['email' => $email, 'password' => $password]);
        $user_id = $pdo->lastInsertId();

        // Create company profile record
        $stmt = $pdo->prepare("INSERT INTO company_profiles (user_id, company_name, contact_person, participation_type) VALUES (:user_id, :company_name, :contact_person, :participation<｜begin▁of▁sentence｜>_type)");
        $stmt->execute(['user_id' => $user_id, 'company_name' => $company_name, 'contact_person' => $contact_person, 'participation_type' => $participation_type]);

        // Generate unique pairing_code
        $pairing_code = uniqid();
        $stmt = $pdo->prepare("UPDATE company_profiles SET pairing_code = :pairing_code WHERE user_id = :user_id");
        $stmt->execute(['pairing_code' => $pairing_code, 'user_id' => $user_id]);

        // Flash message
        $_SESSION['success'] = 'Company added successfully.';
        header('Location: admin_company_add.php');
        exit;
    }
}

// CSRF token
$_SESSION['token'] = bin2hex(random_bytes(32));

?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Company</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0-alpha1/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container">
        <h2>Add Company</h2>
        <form method="post" action="<?php echo $_SERVER["PHP_SELF"];?>">
            <div class="mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password:</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="company_name" class="form-label">Company Name:</label>
                <input type="text" class="form-control" id="company_name" name="company_name" required>
            </div>
            <div class="mb-3">
                <label for="contact_person" class="form-label">Contact Person:</label>
                <input type="text" class="form-control" id="contact_person" name="contact_person" required>
            </div>
            <div class="mb-3">
                <label for="participation_type" class="form-label">Participation Type:</label>
                <select class="form-select" id="participation_type" name="participation_type">
                    <option value="physical">Physical</option>
                    <option value="virtual">Virtual</option>
                </select>
            </div>
            <input type="hidden" name="token" value="<?php echo $_SESSION['token'];?>">
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
        <?php if(isset($_SESSION['success'])): ?>
            <div class="alert alert-success" role="alert">
                <?php echo $_SESSION['success']; unset($_SESSION['success']); ?>
            </div>
        <?php endif; ?>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/5.3.0-alpha1/js/bootstrap.bundle.min.js"></script>
</body>
</html>
```


