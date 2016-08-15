<?php

    $message = '
        NOTE: Please refer to the alternate assessment decision making resources including the guidance document, flowchart and/or checklist when making justification for participation in the alternate assessment, '. UIAnchor::factory('http://dese.mo.gov/se/compliance/specedguidance.html', 'http://dese.mo.gov/se/compliance/specedguidance.html')->toHTML().'.
     ';

    print UIMessage::factory($message, UIMessage::NOTE)
            ->textAlign('left')
            ->toHTML();
?>