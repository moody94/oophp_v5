<?php

namespace Anax\View;

/**
 * View when wining guess game
 */


?>

<h1>Guess My Number(SESSION)</h1>
<p>Your guess <?= $number ?> is CORRECT</p>
<!-- <p style="color: #4dbc4d; font-weight: 900;">congratulations, you won!</p> -->
<form method="post" action="play">
    <input type="submit" name="doInit" value="Play again">
</form>
