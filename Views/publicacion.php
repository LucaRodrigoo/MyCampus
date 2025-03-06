<?php
session_start();

$name = $_SESSION["usuario_nombre"];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <style>
        body {
            background-color: #f0f2f5;
            
        }
        .container {
            width: 800px !important;
            max-width: 100%;
            margin: 20px auto;
            background: #fff;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
        }
            
        .post-box {
            width: 100%;
            max-width: 500px;
            background: #fff;
            padding: 15px;
            border-radius: 10px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin: auto;
        }
        .post-header {
            display: flex;
            align-items: center;
        }
        .profile-pic {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
        }
        .post-input {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 20px;
            background: #f0f2f5;
        }
        .post-actions {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
        }
        .file-label {
            cursor: pointer;
            color: #1877f2;
        }
        .post-btn {
            background: #1877f2;
            color: white;
            border: none;
            padding: 8px 15px;
            border-radius: 20px;
            cursor: pointer;
        }
        .post-btn:disabled {
            background: #b0c4de;
            cursor: not-allowed;
        }
        .image-btn {
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: #f0f2f5;
            border-radius: 50%;
            margin-left: 10px;
        }

        .image-btn img {
            width: 20px;
            height: 20px;
        }

    </style>
</head>
<body>
    <?php include_once "menu.php"; ?>
    
    <div class="container">
    
        <h1>Hello word</h1>
    </div>


</body>
</html>
