<?php

	Security::init();
	
	$area 	 = io::get('area');
	$area_id = io::get('area_id');
	$bank    = io::get('bank');
    $add     = io::get('add');
    $goal    = new IDEAGoalDBHelper($area);

    $goal->addAriaID($area_id);

    # check if exist condition in db
    if ($add == false) {

        $exist = $goal->checkExistBank($bank);

        # if not exist condition - create message for link
        if ($exist == false) {
            $result = 'Add "' . $bank . '" to Goal Bank';
        } else {
            $result = '';
        }

    } else {
        # codition not exist - add new
        $goal->checkKsaID($area_id);

        if ($goal->addGoalBank($bank)) {
            $result = 'added';
        }

    }

	io::ajax('data', $result);

?>
