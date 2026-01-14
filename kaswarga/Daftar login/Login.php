<?php 
    include ('authentication.php')
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Kaswarga</title>
    <link rel="stylesheet" href="login_oke.css">
    <link rel="shortcut icon" href="ICONB.png" type="image/x-icon">
</head>
<body>

<div class="login-wrapper">
    <div class="login-container">
        <h2>Login Kaswarga</h2>
        <img src="ICON.png" alt="rumah" class="icon" >

        <form method="POST" action="" id="loginForm">
            <input type="username" id="nama"     name="nama"     placeholder="Masukkan username" required>
            <input type="password" id="password" name="password" placeholder="Masukkan password" required>

            <div class="button-group">
                <input type="hidden" name="login" value="1">
                <button type="submit">Login</button>

                <input type="hidden">
                <button type="button" id="register" name="register" value="register" onclick="window.location.href='../Daftar Login/register.php'">daftar</button>
            </div>
        </form>
    </div>
</div>

</body>
</html>
