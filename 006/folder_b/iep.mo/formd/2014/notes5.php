<?php

    $message = '
     	<b>Note (1):</b>
      Use of this accommodation <b>invalidates</b> the ACCESS FOR ELLS individual student scores for the shaded assessment domains.  The student will receive the Lowest Obtainable Scale Score (LOSS) for the selected shaded assessment domains.

For additional information regarding ACCESS FOR ELLS accommodations visit the WIDA web site at:
        ' . UIAnchor::factory('http://wida.us/assessment/ACCESS/', 'http://wida.us/assessment/ACCESS/')->toHTML() . '
     ';
    print UIMessage::factory($message, UIMessage::NOTE)
            ->textAlign('left')
            ->toHTML();
?>
