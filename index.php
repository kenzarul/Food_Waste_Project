<?php

session_start();
$conn = new mysqli('localhost', 'root', '', 'food_saving');
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['mail'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    $result = $conn->query("SELECT * FROM '$role' WHERE mail='$mail' AND password='$password' AND role='$role'");
    if ($result->num_rows > 0) {
        $_SESSION['user'] = $username;
        $_SESSION['role'] = $role;
        header('Location: dashboard.php');
    } else {
        echo 'Invalid credentials';
    }
}
?>
<!DOCTYPE html>
<>
<head><title>Login</title></head>
<>

    <form method="POST">
        <input type="text" name="mail" placeholder="E-Mail" required>
        <input type="password" name="password" placeholder="Password" required>
        <select name="role">
            <option value="recipient">Recipient</option>
            <option value="donateurs">Donor</option>
        </select>
        <button type="submit">Login</button>
    </form>
</body>
</html>