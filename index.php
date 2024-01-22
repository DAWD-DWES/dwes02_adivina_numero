<?php
session_start();

$bd = require "conexion.php";
require_once "funciones.php";

// Expresión regular para comprobación de name
// Cadena entre tres y 25 caracteres
define("REGEXP_NAME", "/^\w{3,25}$/");
// Expresión regular para comprobación de password
// Cadena de 4 a 8 caracteres con al menos 1 digito
define("REGEXP_PASSWORD", "/^(?=.*\d).{4,8}$/");
// Expresión regular para comprobación de Email
define("REGEXP_EMAIL", "/^.+@[^\.].*\.[a-z]{2,}$/");

if (isset($_SESSION['usuario'])) { // Si el usuario ya se ha autenticado
// Si es una petición de cierre de sesión
    if (filter_has_var(INPUT_GET, 'logout')) { // Si es una petición de logout
// destruyo la sesión
        session_unset();
        session_destroy();
        setcookie(session_name(), '', 0, '/');
    } elseif (filter_has_var(INPUT_GET, 'baja')) { // Si es una petición de baja
        $usuarioId = $_SESSION['usuario_id'];
        borraUsuarioPorId($bd, $usuarioId);
        session_unset();
        session_destroy();
        setcookie(session_name(), '', 0, '/');
    } else { // En otro caso
        header('Location:juego.php');
        die();
    }
} else { // Si el usuario no se ha autenticado
    if (filter_has_var(INPUT_POST, 'login')) { // Si es una petición de login
        $usuario = trim(filter_input(INPUT_POST, 'nombre', FILTER_UNSAFE_RAW));
        $password = trim(filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW));
        $usuarioObject = recuperaUsuarioPorNombrePassword($bd, $usuario, $password);
        if ($usuarioObject) {
            $_SESSION['usuario'] = $usuarioObject->name;
            $_SESSION['usuario_id'] = $usuarioObject->id;
            header('Location:juego.php');
            die();
        } else {
            $errorLogin = true;
        }
    } elseif (filter_has_var(INPUT_POST, 'registro')) { // Si es una petición de registro
        $usuario = trim(filter_input(INPUT_POST, 'nombre', FILTER_UNSAFE_RAW));
        $usuarioValido = !(filter_var($usuario, FILTER_VALIDATE_REGEXP,
                        ['options' => ['regexp' => REGEXP_NAME]]) === false);
        $password = trim(filter_input(INPUT_POST, 'password', FILTER_UNSAFE_RAW));
        $passwordValido = !(filter_var($password, FILTER_VALIDATE_REGEXP,
                        ['options' => ['regexp' => REGEXP_PASSWORD]]) === false);
        $email = trim(filter_input(INPUT_POST, 'email', FILTER_UNSAFE_RAW));
        $emailValido = empty($email) || !(filter_var($email, FILTER_VALIDATE_REGEXP,
                        ['options' => ['regexp' => REGEXP_EMAIL]]) === false);
        if ($usuarioValido === false || $passwordValido === false || $emailValido === false) {
            $errorRegistro = true;
        } else {
            $resultado = insertaUsuario($bd, $usuario, $password, $email);
            if ($resultado) {
                $_SESSION['usuario'] = $usuario;
                $_SESSION['usuario_id'] = (int) $bd->lastInsertId();
                header('Location:juego.php?nuevo_juego');
                die();
            }
        }
    } elseif (filter_has_var(INPUT_GET, 'pet_registro')) { // Si es una petición de formulario de registro
        $petRegistro = true;
    }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Login/Registro</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" 
              integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" 
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    </head>
    <body>
        <div class= "container-fluid">
            <nav class="navbar navbar-light bg-light d-flex justify-content-around">
                <div>Adivina Número</div><div></div>
                <div>
                    <?php if (isset($petRegistro) || isset($errorRegistro)): ?>
                        <a class="btn btn-primary p-2" role="button" href="index.php?pet_login">Login</a>
                    <?php else: ?>
                        <a class="btn btn-primary p-2" role="button" href="index.php?pet_registro">Registro</a>
                    <?php endif ?>
                </div>
            </nav>
            <div class= "d-flex flex-column">
                <div class="row justify-content-center mt-5">
                    <div class="col-md-8">
                        <?php if (isset($errorLogin)): ?>
                            <div class="alert alert-danger" role="alert">Error de login</div>
                        <?php endif ?>
                        <?php if (isset($errorRegistro)): ?>
                            <div class="alert alert-danger" role="alert">Error de Registro</div>
                        <?php endif ?>
                        <div class="card">
                            <?php if (isset($petRegistro) || isset($errorRegistro)): ?>
                                <div class="card-header">Registro</div>
                            <?php else: ?>
                                <div class="card-header">Login</div>
                            <?php endif ?>
                            <div class="card-body mt-3">
                                <form class="form-horizontal" method="POST" action="index.php" id='formloginregistro' novalidate>
                                    <div class="row m-2">                            
                                        <label for="inputNombre" class="col-sm-2 col-form-label">Name:</label>
                                        <div class="col-sm-10">
                                            <?php if (isset($petRegistro) || isset($errorRegistro)): ?>
                                                <input id="inputNombre" type="text" value="<?= (isset($usuario) && $usuario) ? htmlspecialchars($usuario) : "" ?>"
                                                       class="form-control  col-sm-10 <?= (isset($usuarioValido) ? ($usuarioValido ? "is-valid" : "is-invalid") : "") ?>" 
                                                       id="inputNombre" placeholder="Nombre" name="nombre">
                                                <div class="col-sm-10 invalid-feedback">
                                                    Nombre es requerido. Longitud entre 3 y 25 caracteres
                                                </div>
                                            <?php else: ?>
                                                <input id="inputNombre" type="text" value="<?= (isset($usuario) && $usuario) ? htmlspecialchars($usuario) : "" ?>"
                                                       class="form-control  col-sm-10" 
                                                       id="inputNombre" placeholder="Nombre" name="nombre">
                                                   <?php endif ?>
                                        </div>
                                    </div>
                                    <div class="row m-2">
                                        <label for="inputPassword" class="col-sm-2 col-form-label">Password:</label>
                                        <div class="col-sm-10">
                                            <?php if (isset($petRegistro) || isset($errorRegistro)): ?>
                                                <input type="password" 
                                                       class="form-control  col-sm-10 <?= (isset($passwordValido) ? ($passwordValido ? "is-valid" : "is-invalid") : "") ?>" id="inputPassword" placeholder="Password" name="password">
                                                <div class="col-sm-10 invalid-feedback">
                                                    Password es requerido. Longitud de 4 a 8 caracteres. Contiene un 1 número
                                                </div>
                                            <?php else: ?>
                                                <input type="password" class="form-control col-sm-10"
                                                       id="inputPassword" placeholder="Password" name="password">
                                                   <?php endif ?>
                                        </div>        
                                    </div>
                                    <?php if (isset($petRegistro) || isset($errorRegistro)): ?>
                                        <div class="row m-2">
                                            <label for="inputEmail" class="col-sm-2 col-form-label">Email:</label>
                                            <div class="col-sm-10">
                                                <input type="text" value="<?= (isset($email) && $email) ? htmlspecialchars($email) : "" ?>"
                                                       class="form-control  col-sm-10 <?= (isset($emailValido) ? ($emailValido ? "is-valid" : "is-invalid") : "") ?>" 
                                                       id="inputEmail" placeholder="Email" name="email">
                                                <div class="col-sm-10 invalid-feedback">
                                                    Email no tiene el formato correcto
                                                </div>
                                            </div>
                                        </div>
                                    <?php endif ?>
                                    <div class ="d-flex justify-content-end m-2">
                                        <?php if (isset($petRegistro) || isset($errorRegistro)): ?>
                                            <input type="submit" class="btn btn-warning" name="registro" value="registro" formaction="index.php?registro">
                                        <?php else: ?>
                                            <input type="submit" class="btn btn-warning" name="login" value="login" formaction="index.php?login">
                                        <?php endif ?>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>

