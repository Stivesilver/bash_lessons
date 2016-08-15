<?php

    $message = '<i>Updated November 2, 2015</i>';

    print UIMessage::factory($message, UIMessage::NOTE)
            ->textAlign('left')
            ->toHTML();
?>
