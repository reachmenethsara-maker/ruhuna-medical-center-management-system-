<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Login</title>

<style>

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family: Arial, Helvetica, sans-serif;
}

body{
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
    background:linear-gradient(135deg,#1e3c72,#2a5298);
}

.login-box{
    background:white;
    padding:40px;
    width:350px;
    border-radius:10px;
    box-shadow:0 10px 25px rgba(0,0,0,0.2);
}

.login-box h2{
    text-align:center;
    margin-bottom:25px;
    color:#333;
}

.login-box label{
    font-weight:bold;
    display:block;
    margin-bottom:5px;
}

.login-box input{
    width:100%;
    padding:10px;
    margin-bottom:20px;
    border:1px solid #ccc;
    border-radius:5px;
    font-size:14px;
}

.login-box input:focus{
    border-color:#2a5298;
    outline:none;
}

.login-box button{
    width:100%;
    padding:10px;
    border:none;
    background:#2a5298;
    color:white;
    font-size:16px;
    border-radius:5px;
    cursor:pointer;
}

.login-box button:hover{
    background:#1e3c72;
}

</style>
</head>

<body>

<div class="login-box">

<h2>User Login</h2>

<form action="loginprocess.php" method="POST">

<label>Username</label>
<input type="text" name="username" required>

<label>Password</label>
<input type="password" name="password" required>

<button type="submit" name="login">Login</button>

</form>

</div>

</body>
</html> 