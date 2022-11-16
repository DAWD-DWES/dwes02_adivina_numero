<?php
// Definición de constantes o parámetros de funcionamiento del juego
define('MAX_TRIES', 5);
define('LOWER_BOUND', 1);
define('UPPER_BOUND', 20);

if (empty($_POST) || isset($_POST['newgamebutton'])) { // Si se arranca el juego o se solicita una nueva partida
    $numTries = 0;
    $numHidden = mt_rand(LOWER_BOUND, UPPER_BOUND); // Genero un valor aleatorio
    $numeros = []; // Array de números jugados
} else { // Si estoy en mitad del juego leo los valores del formulario
    $numHidden = filter_input(INPUT_POST, 'num_hidden', FILTER_SANITIZE_NUMBER_INT);
    $numTries = filter_input(INPUT_POST, 'num_tries', FILTER_SANITIZE_NUMBER_INT);
    $guess = filter_input(INPUT_POST, 'guess', FILTER_SANITIZE_NUMBER_INT);
    $numeros = filter_input(INPUT_POST, 'numeros', FILTER_SANITIZE_NUMBER_INT, FILTER_REQUIRE_ARRAY);
    $numeros[] = $guess;
    ++$numTries;
    $end = $numTries >= MAX_TRIES || $guess === $numHidden; // Establezco si se ha acabado la partida o no
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
            <h1>Guess Hidden Number!</h1>

            <form class="form" name="form_guessnumber" 
                  action="index.php" method="POST">
                <input type="hidden" name="num_hidden" value="<?= $numHidden ?>" /> <!-- Incluyo el número secreto en el formulario para que no se pierda -->
                <input type="hidden" name="num_tries" value="<?= $numTries ?>" /> <!-- Incluyo el número de intentos en el formulario para que no se pierda -->
                <div class="form-section">
                    <div class="input-section">
                        <label for="guess"><?= 'Enter a number (' . constant('LOWER_BOUND') . '-' . constant('UPPER_BOUND') . '):' ?></label> 
                        <input id="guess" type="number"  required name="guess" min="<?= LOWER_BOUND ?>" 
                               max="<?= UPPER_BOUND ?>" value="<?= ($guess) ?? ''; ?>" <?= !empty($end) ? 'readonly' : '' ?> />

                        <div class="submit-section">
                            <!-- Si no se ha acabado el juego añado un botón para enviar apuesta, si se ha acabado añado un botón para iniciar una nueva partida -->
                            <input class="submit" type="submit" 
                                   value="<?= empty($end) ? 'Guess' : 'New Game' ?>" name="<?= empty($end) ? 'guessbutton' : 'newgamebutton'; ?>" /> 
                        </div>
                    </div>

                    <div class="input-section">Guessed Number List: <?= implode(',', $numeros) ?></div>
                    <?php foreach ($numeros as $numero): ?>
                        <input type="hidden" name="numeros[]" value=<?= "$numero" ?>>
                    <?php endforeach ?>
                </div>
                <?php if (isset($end) && !$end): ?> <!-- Si no se ha acabado la partida incluyo la pista para el jugador -->
                    <p class="info-section">Tries left: <?= MAX_TRIES - $numTries ?></p>
                    <?php if (isset($guess)): ?>
                        <p class="info-section"><?= ($guess <=> $numHidden) > 0 ? 'Try with a lower number' : 'Try with a higher number' ?></p>
                    <?php endif ?> 
                <?php elseif (isset($end) && $end): ?>
                    <p class="info-section"><?= ($guess === $numHidden) ? "Well done!!! Got it in {$numTries} tries" : 'You lost!!' ?></p>
                <?php endif ?>
            </form> 
        </div>
    </div>
</div>
</body>
</html>
