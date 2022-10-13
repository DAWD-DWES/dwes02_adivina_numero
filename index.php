<?php
define("MAX_TRIES", 5);
define("LOWER_BOUND", 1);
define("UPPER_BOUND", 10);

if (empty($_POST) || isset($_POST['newgamebutton'])) {
    $numTries = 0;
    $numHidden = mt_rand(LOWER_BOUND, UPPER_BOUND);
} else {
    $numHidden = filter_input(INPUT_POST, 'num_hidden');
    $numTries = filter_input(INPUT_POST, 'num_tries');
    $guess = filter_input(INPUT_POST, 'guess');
    $numTries++;
    $end = $numTries >= MAX_TRIES || $guess === $numHidden;
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
            <div class="capaform">
                <form class="form-font" name="form_guessnumber" 
                      action="index.php" method="POST">
                    <input type="hidden" name="num_hidden" value="<?= ($numHidden) ?>" />
                    <input type="hidden" name="num_tries" value="<?= ($numTries) ?>" />
                    <div class="form-section">
                        <div class="input-section">
                            <label for="guess"><?= "Enter a number (" . constant("LOWER_BOUND") . "-" . constant("UPPER_BOUND") . "):" ?></label> 
                            <input id="guess" type="number"  required name="guess" min="<?= LOWER_BOUND ?>" 
                                   max="<?= UPPER_BOUND ?>" value="<?= ($guess) ?? '' ?>" <?= (!empty($end) ? "readonly" : "") ?> />
                        </div>
                        <div class="submit-section">
                            <input class="submit" type="submit" 
                                   value="<?= empty($end) ? "Guess" : "New Game" ?>" name="<?= empty($end) ? "guessbutton" : "newgamebutton" ?>" />
                        </div>
                    </div>
                    <?php if (empty($end)): ?>
                        <p class="info-section">Tries left: <?= (MAX_TRIES - $numTries) ?></p>
                        <?php if (isset($guess)): ?>
                            <p class="info-section"><?= ($guess <=> $numHidden) > 0 ? "Try with a lower number" : "Try with a higher number" ?></p>
                        <?php endif ?>
                    <?php else: ?>
                        <p class="info-section"><?= ($guess === $numHidden) ? "Well done!!! Got it in $numTries tries" : "You lost!!" ?></p>
                    <?php endif ?>
                </form> 
            </div>
        </div>  
    </div>
</div>
</body>
</html>
