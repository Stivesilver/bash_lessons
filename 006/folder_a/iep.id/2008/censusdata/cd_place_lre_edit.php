<?php

    Security::init();

    $dskey        = io::get('dskey');
    $area_id      = 114;
    $placement_id = io::get('placement_id');
    $ds 	      = DataStorage::factory($dskey, true);
    $tsRefID      = $ds->safeGet('tsRefID');

    $RefID = (int)db::execSQL("
        SELECT refid
          FROM webset.std_general
         WHERE stdrefid = $tsRefID
           AND area_id = $area_id
           AND int01 = $placement_id"
        )->getOne();

    $edit = new EditClass('edit1', $RefID);

    $edit->setSourceTable('webset.std_general', 'refid');

    $edit->title          = "IEP LRE Placement";
    $edit->saveAndEdit    = true;
    $edit->firstCellWidth = "30%";

    $edit->addGroup("General Information");

    $edit->addControl("Placement:", 'protected')
         ->value(db::execSQL("
             SELECT plc.spcCode || ' - ' || plc.spcdesc
               FROM webset.std_placementcode std
              INNER JOIN webset.statedef_placementcategorycode plc ON std.spcrefid = plc.spcrefid
              INNER JOIN webset.statedef_placementcategorycodetype typ ON plc.spctrefid = typ.spctrefid
              WHERE stdrefid = $tsRefID
              ORDER BY spcbeg, pcrefid DESC
         ")->getOne());

    $edit->addControl(FFSelect::factory('IEP Builder will show')
            ->name('txt01')
        )
        ->data(
            array(
                'iep'  => 'IEP LRE',
                '3_5'  => 'IEP LRE Ages 3-5',
                'sec'  => 'Secondary',
                'sp'   => 'SP LRE',
                'sp35' => 'SP LRE  Ages 3-5'
            )
        )
        ->sqlField('txt01')
        ->append(UICustomHTML::factory(
                UIAnchor::factory('Part1')
                    ->onClick('checkCategory("part1")')
                )
            ->id('category')
            ->css('padding-left', '20px')
        ->append(UICustomHTML::factory(
                UIAnchor::factory('Part2')
                    ->onClick('checkCategory("part2")')
                )
            ->id('category')
            ->css('padding-left', '20px')
        )
        );

    $edit->addControl(FFSwitchYN::factory("Include Consent for Initial Placement:"))
         ->value("N")
         ->sqlField('txt02');

    $edit->addControl(FFSwitchYN::factory("Preschool Student From Part B:"))
         ->sqlField('txt03');

    $edit->addControl("If Yes, INITIAL IEP DATE:", "date")
         ->sqlField('dat01');

    $edit->addGroup("Update Information", true);
    $edit->addControl("Last User", "protected")
         ->value(SystemCore::$userUID)
         ->sqlField('lastuser');

    $edit->addControl("Last Update", "protected")
         ->value(date("m-d-Y H:i:s"))
         ->sqlField('lastupdate');

    $edit->printEdit();

    io::jsVar('dskey', $dskey);

?>

<script type="text/javascript">

    function checkCategory(part) {
        var category = $('#txt01').val();
        var parts    = [];
        if (part == 'part1') {
            parts['iep']  = 19;
            parts['3_5']  = 23;
            parts['sec']  = 22;
            parts['sp']   = 27;
            parts['sp35'] = 52;
        } else {
            parts['iep']  = 20;
            parts['3_5']  = 24;
            parts['sec']  = 26;
            parts['sp']   = 28;
            parts['sp35'] = 53;
        }

        url = api.url('../../../iep/constructions/main.php', {'dskey': dskey, 'constr': parts[category]});
        api.window.open('Goal Bank', url);
    }

</script>