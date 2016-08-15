<?php

    $message = '
     	<b>Note (1):</b>
        Use of this accommodation invalidates the ACCESS FOR ELLS assessment student scores and the student receives the Lowest Obtainable Scale Score.

        <br><br>

        For additional information regarding ACCESS FOR ELLS accommodations visit the WIDA web site at:
        ' . UIAnchor::factory('http://wida.wceruw.org/assessment/ACCESS/accommodations.aspx', 'http://wida.wceruw.org/assessment/ACCESS/accommodations.aspx')->toHTML() . '
     ';
    print UIMessage::factory($message, UIMessage::NOTE)
            ->textAlign('left')
            ->toHTML();
?>