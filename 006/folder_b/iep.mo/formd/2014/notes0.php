<?php

    $message = '<i>Updated January 7, 2015</i>';

    print UIMessage::factory($message, UIMessage::NOTE)
            ->textAlign('left')
            ->toHTML();
?>
