<?php
// Definición de constantes o parámetros de funcionamiento del juego
define('MAX_INTENTOS', 5);
define('LIM_INF', 1);
define('LIM_SUP', 20);

if ($petApuesta = filter_input(INPUT_POST, 'envio_apuesta')) {
    $numOculto = filter_input(INPUT_POST, 'num_oculto', FILTER_SANITIZE_NUMBER_INT);
    $numIntentos = filter_input(INPUT_POST, 'num_intentos', FILTER_SANITIZE_NUMBER_INT);
    $apuesta = filter_input(INPUT_POST, 'apuesta', FILTER_SANITIZE_NUMBER_INT);
    $numeros = filter_input(INPUT_POST, 'numeros', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY);
    $errApuesta = empty($apuesta);
    if (!$errApuesta) {
        $numeros[] = $apuesta;
        ++$numIntentos;
        $fin = $numIntentos >= MAX_INTENTOS || $apuesta === $numOculto; // Establezco si se ha acabado la partida o no
    }
} elseif ($petNumerosJugados = filter_input(INPUT_POST, 'numeros_jugados')) {
    $numOculto = filter_input(INPUT_POST, 'num_oculto', FILTER_SANITIZE_NUMBER_INT);
    $numIntentos = filter_input(INPUT_POST, 'num_intentos', FILTER_SANITIZE_NUMBER_INT);
    $numeros = filter_input(INPUT_POST, 'numeros', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY) ?? [];
} else { // Si se arranca el juego o se solicita una nueva partida
    $numIntentos = 0;
    $numOculto = mt_rand(LIM_INF, LIM_SUP); // Genero un valor aleatorio
    $numeros = []; // Array de números jugados
}
?>

<!DOCTYPE html>
<html>
    <head>
        <title>Guess Hidden Number!</title>
        <meta name="viewport" content="width=device-width">
        <meta charset="UTF-8">
        <link rel="stylesheet" href="stylesheet.css">
    </head>
    <body>
        <div class="page">
            <h1>¡Adivina el número oculto!</h1>
            <div class="capaform">
                <form class="form" name="form_apuestanumero" 
                      action="index.php" method="POST">
                    <input type="hidden" name="num_oculto" value="<?= $numOculto ?>" /> <!-- Incluyo el número secreto en el formulario para que no se pierda -->
                    <input type="hidden" name="num_intentos" value="<?= $numIntentos ?>" /> <!-- Incluyo el número de intentos en el formulario para que no se pierda -->
                    <?php foreach ($numeros as $numero): ?> <!-- Incluyo los valores de las apuestas ya introducidas -->
                        <input type="hidden" name="numeros[]" value="<?= $numero ?>" />
                    <?php endforeach ?>                 
                    <div class="input-seccion">
                        <label for="apuesta"><?= 'Enter a numero (' . LIM_INF . '-' . LIM_SUP . '):' ?></label> 
                        <input id="apuesta" type="number" name="apuesta" min="<?= LIM_INF ?>" 
                               max="<?= LIM_SUP ?>" value="<?= ($apuesta) ?? ''; ?>" <?= !empty($fin) ? 'readonly' : '' ?> />
                    </div>
                    <?php if (isset($errApuesta)): ?>
                        <div class="error-seccion">
                            <div class="error">Introduce una apuesta</div>  
                        </div>
                    <?php endif ?>
                    <div class="submit-seccion">
                        <!-- Si se ha acabado el juego añado un botón para iniciar una nueva partida y un mensaje de fin de juego -->
                        <input class="submit" type="submit" 
                               value="Nuevo Juego" name="nuevo_juego" /> 
                    </div>
                    <?php if (isset($fin) && $fin): ?> <!-- Si no se ha acabado la partida incluyo la pista para el jugador -->
                        <p class="info-seccion"><?= ($apuesta === $numOculto) ? "Enhorabuena!!! Lo has acertado en {$numIntentos} " . (($numIntentos !== 1) ? "intentos" : "intento") : "Lo sentimos!!. El número era $numOculto"
                        ?></p> 
                    <?php else: ?>
                        <div class="submit-seccion">
                            <!-- Si es el inicio del juego o no se ha acabado el juego añado un botón para enviar apuesta -->
                            <input class="submit" type="submit" 
                                   value="Realiza una apuesta" name="envio_apuesta" /> 
                        </div>
                        <?php if (isset($fin) && !$fin): ?>
                            <div class="info-seccion">
                                <!-- Si no se ha acabado el juego añado una pista para el usuario -->
                                <p>Intentos restantes: <?= MAX_INTENTOS - $numIntentos ?></p>
                                <?php if (!isset($errApuesta)): ?>
                                    <p><?= ($apuesta <=> $numOculto) > 0 ? 'Inténtalo con un número mas bajo' : 'Inténtalo con un número mas alto' ?></p>
                                <?php endif ?>
                            </div>
                        <?php endif ?>
                    <?php endif ?> 
                    <?php if (isset($petNumerosJugados)): ?>
                        <p class="info-seccion">
                            <?= ($numeros) ? "Ya has jugado con los siguientes números: " . implode(",", $numeros) : "No hay números todavía" ?></p>
                    <?php endif ?>
                    <div class="submit-seccion">
                        <!-- Si es el inicio del juego o no se ha acabado el juego añado un botón para enviar apuesta -->
                        <input class="submit" type="submit" 
                               value="Numeros Jugados" name="numeros_jugados" /> 
                    </div>
                </form> 
            </div>
        </div>  
    </body>
</html>
