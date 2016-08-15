<?php

    Security::init();

    $dskey = io::get('dskey');
    $ds    = DataStorage::factory($dskey, true);
    $tabs  = new UITabs('tabs');

    $tabs->indent(3);

    $tabs->addTab(
        'Classroom Accommodations',
        CoreUtils::getURL(
            '/apps/idea/iep.id/2008/accommodations/srv_progmod_list.php',
            array(
                'dskey'   => $dskey,
                'nexttab' => 0,
            )
        )
    );

    $tabs->addTab(
        'Testing Accommodations',
        CoreUtils::getURL(
            '/apps/idea/iep.id/2008/accommodations/srv_assessments_list.php',
            array(
                'dskey'   => $dskey,
                'nexttab' => 1,
                'top'     => 'no'
            )
        )
    );

    $tabs->addTab(
        'IAA Eligibility',
        CoreUtils::getURL(
            '/apps/idea/iep/constructions/main.php',
            array(
                'dskey'   => $dskey,
                'constr'  => '29',
                'nexttab' => 2,
                'top'     => 'no'
            )
        )
    );

    $tabs->addTab(
        'BIP',
        CoreUtils::getURL(
            '/apps/idea/iep/constructions/main.php',
            array(
                'dskey'   => $dskey,
                'constr'  => '29',
                'nexttab' => '-1',
                'top'     => 'no'
            )
        )
    );

    print $tabs->toHTML();
    print FFInput::factory()->name('screenURL')->value(CoreUtils::getURL($ds->safeGet('screenURL'), array('dskey' => $dskey)))->hide()->toHTML();

?>