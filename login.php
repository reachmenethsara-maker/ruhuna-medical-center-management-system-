<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>User Login</title>

<style>
    body {
        font-family: Arial, sans-serif;
        background: linear-gradient(135deg, #74ebd5, #9face6);
        margin: 0;
        padding: 0;
    }

    .login-box {
        width: 350px;
        margin: 80px auto;
        padding: 25px;
        background: #fff;
        border-radius: 10px;
        box-shadow: 0 0 15px rgba(0,0,0,0.2);
    }

    h2 {
        text-align: center;
        margin-bottom: 20px;
    }

    label {
        font-weight: bold;
    }

    input[type="text"],
    input[type="password"] {
        width: 100%;
        padding: 10px;
        margin: 8px 0;
        border-radius: 5px;
        border: 1px solid #ccc;
    }

    button {
        width: 100%;
        padding: 10px;
        margin-top: 10px;
        background: #007bff;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
    }

    button:hover {
        background: #0056b3;
    }

    .error {
        color: red;
        text-align: center;
        margin-bottom: 10px;
        font-size: 14px;
    }

    .show-pass {
        font-size: 13px;
    }
</style>
</head>
<body>

<div class="login-box">
    <h2>User Login</h2>

    <!-- Error Messages -->
    <?php if (isset($_GET['error'])): ?>
        <div class="error">
            <?php
                if ($_GET['error'] == 'invalid') echo "Invalid username or password";
                elseif ($_GET['error'] == 'empty') echo "Please fill all fields";
                elseif ($_GET['error'] == 'role') echo "Role not recognized";
            ?>
        </div>
    <?php endif; ?>

    <!-- LOGIN FORM -->
    <form method="POST" action="loginproces.php">
        <label>Username</label>
        <input type="text" name="username" placeholder="Enter username" required>

        <label>Password</label>
        <input type="password" name="password" id="password" placeholder="Enter password" required>

        <div class="show-pass">
            <input type="checkbox" onclick="togglePassword()"> Show Password
        </div>

        <button type="submit">Login</button>
    </form>
</div>

<script>
function togglePassword() {
    const pass = document.getElementById("password");
    pass.type = pass.type === "password" ? "text" : "password";
}
</script>

</body>
</html>