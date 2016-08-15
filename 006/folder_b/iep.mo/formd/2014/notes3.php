<?php

    $message = 'Note: All accommodations selected must match those shown on Form F. All requested accommodations must be supported by documentation submitted to ACT. Only those accommodation approved by ACT can be provided to the student by the LEA during the administration of the ACT.';
    print UIMessage::factory($message, UIMessage::NOTE)
            ->textAlign('left')
            ->toHTML();
?>
