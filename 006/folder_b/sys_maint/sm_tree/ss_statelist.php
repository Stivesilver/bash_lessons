<?php

    Security::init();

    $tw = new UITreeWrapper(CoreUtils::getURL('load.ajax.php', array('area' => io::get('area'))));
    $tw->addItemProcess(
        UITreeWrapperCategory::ALL_TREE_ITEMS, 'edit.php'
    );

    echo $tw->toHTML();
?>
