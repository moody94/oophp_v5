<?php

namespace Anax\View;

/**
 * Render content within an article.
 */

// Show incoming variables and view helper functions
//echo showEnvironment(get_defined_vars(), get_defined_functions());


?><h1>Guess My Number(SESSION)</h1>
<p>Guess a number between 1 and 100, you have<?= $tries?> left</p>

<form method="post">
    <input type="text" name="guess">
    <input type="submit" name="doGuess" value="make a guess">
    <input type="submit" name="doInit" value="Start from the beginning">
    <input type="submit" name="doCheat" value="Cheat">
</form>
<?php if ($guess >= 1 && $guess <= 100) : ?>
    <?php if ($res) :?>
<p>Your guess <?= $guess ?> is <b><?= $res ?></b></p>
    <?php endif; ?>
<?php endif; ?>
<?php if ($doCheat) :?>
<p>CHERT: currect number is <?= $number ?>. </p>
<?php endif; ?>
