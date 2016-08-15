<?php

    Security::init();

    $dskey = io::get('dskey');

    io::js('api.goto("'.CoreUtils::getURL('/apps/idea/iep.id/2013/builder/builder.php',
                                          array(
                                              'dskey' => $dskey,
                                          )
           ).'")');


	
?>