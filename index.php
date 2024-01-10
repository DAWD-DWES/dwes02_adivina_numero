<?php
session_start();

// Definición de constantes o parámetros de funcionamiento del juego
define('MAX_INTENTOS', 5);
define('LIM_INF', 1);
define('LIM_SUP', 20);

if (filter_has_var(INPUT_POST, 'envio_apuesta')) { // SI se está enviando una apuesta  
    $apuesta = filter_input(INPUT_POST, 'apuesta', FILTER_VALIDATE_INT);
    $_SESSION['numeros'][] = $apuesta;
    $numeros = $_SESSION['numeros'];
    ++$_SESSION['num_intentos'];
    $numIntentos = $_SESSION['num_intentos'];
    $numOculto = $_SESSION['num_oculto'];
    $fin = $numIntentos >= MAX_INTENTOS || $apuesta === $numOculto; // Establezco si se ha acabado la partida o no// Si se arranca el juego o se solicita una nueva partida
} else { // Si estoy al comienzo del juego o se solicita un nuevo juego
    $_SESSION['num_intentos'] = $numIntentos = 0;
    $_SESSION['num_oculto'] = $numOculto = mt_rand(LIM_INF, LIM_SUP); // Genero un valor aleatorio
    $_SESSION['numeros'] = $numeros = []; // Array de números jugados
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Guess Hidden Number!</title>
        <meta name="viewport" content="width=device-width">
        <meta charset="UTF-8">
        <link rel="stylesheet" href="stylesheet.css">
        <!-- css para usar Bootstrap -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" 
              integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" 
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    </head>
    <body>
        <div class="page">
            <h1>¡Adivina el número oculto!</h1>
            <div class="border border-4 border-warning">
                <form name="form_apuestanumero" action="index.php" method="POST">
                    <input type="hidden" name="num_oculto" value="<?= $numOculto ?>" /> <!-- Incluyo el número secreto en el formulario para que no se pierda -->
                    <input type="hidden" name="num_intentos" value="<?= $numIntentos ?>" /> <!-- Incluyo el número de intentos en el formulario para que no se pierda -->
                    <?php foreach ($numeros as $numero): ?> <!-- Incluyo los valores de las apuestas ya introducidas -->
                        <input type="hidden" name="numeros[]" value="<?= $numero ?>" />
                    <?php endforeach ?>                 
                    <div class="input-seccion">
                        <label for="apuesta"><?= 'Enter a numero (' . LIM_INF . '-' . LIM_SUP . '):' ?></label> 
                        <input id="apuesta" type="number"  required name="apuesta" min="<?= LIM_INF ?>" 
                               max="<?= LIM_SUP ?>" value="<?= ($apuesta) ?? ''; ?>" <?= !empty($fin) ? 'readonly' : '' ?> />
                    </div>
                    <?php if (isset($fin) && $fin): ?> <!-- Si se ha acabado el juego -->
                        <div class="submit-seccion">
                            <!-- Añado un botón para iniciar una nueva partida y un mensaje de fin de juego -->
                            <!-- <input class="submit" type="submit" value="Nuevo Juego" name="nuevo_juego" /> -->
                            <!-- <input class="submit" type="submit" formmethod="GET" value="Nuevo Juego" name="nuevo_juego"> -->
                            <a href="<?= "{$_SERVER['PHP_SELF']}?nuevo_juego" ?>"><input class="submit" value="Nuevo Juego"></a>
                        </div>
                        <p class="info-seccion"><?= ($apuesta === $numOculto) ? "Enhorabuena!!! Lo has acertado en {$numIntentos} " . (($numIntentos !== 1) ? "intentos" : "intento") : 'Lo sentimos!!' ?></p> 
                    <?php else: ?> <!-- Si no se ha acabado el juego o es el inicio de un nuevo juego-->
                        <div class="submit-seccion">
                            <!-- Añado un botón para enviar apuesta -->
                            <input class="submit" type="submit" 
                                   value="Apuesta" name="envio_apuesta" /> 
                        </div>
                        <?php if (isset($fin) && !$fin): ?> <!-- Si no se ha acabado el juego -->
                            <div class="info-seccion">
                                <!-- Añado una pista para el usuario -->
                                <p>Intentos restantes: <?= MAX_INTENTOS - $numIntentos ?></p>
                                <p><?= ($apuesta <=> $numOculto) > 0 ? 'Inténtalo con un número mas bajo' : 'Inténtalo con un número mas alto' ?></p>
                                <p>Ya has jugado con los siguientes números: <?= implode(",", $numeros) ?></p>
                            </div>
                        <?php endif ?> 
                    <?php endif ?>                 
                </form> 
            </div>
        </div>  
    </body>
</html>