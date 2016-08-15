<?php
    Security::init();

    $dskey = io::get('dskey');
    $RefID = io::geti('RefID');
    $ds = DataStorage::factory($dskey);
    $tsRefID = $ds->safeGet('tsRefID');
    $stdIEPYear = $ds->safeGet('stdIEPYear');
    $esy = io::get('ESY');

    $edit = new EditClass('edit1', io::get('RefID'));

    $edit->title = 'Add/Edit Baseline';

    $edit->setSourceTable('webset.std_bgb_baseline', 'blrefid');

    $edit->addGroup("General Information");
    $edit->addControl("Order #", "integer")
        ->sqlField('order_num')
        ->value((int) db::execSQL("
                    SELECT max(order_num)
                      FROM webset.std_bgb_baseline
                     WHERE stdrefid = " . $tsRefID . "
                       AND siymrefid = " . $stdIEPYear . "
                       AND esy = '" . $esy . "'
                ")->getOne() + 1
        )
        ->size(5);
    $edit->addControl('Area', 'select')
        ->sqlField('blksa')
        ->sql("
            SELECT ksa.gdskrefid,
                   " . IDEAParts::get('baselineArea') . "
              FROM webset.disdef_bgb_goaldomainscopeksa ksa
                   INNER JOIN webset.disdef_bgb_goaldomainscope scope ON ksa.gdsrefid = scope.gdsrefid
                   INNER JOIN webset.disdef_bgb_goaldomain domain ON scope.gdrefid = domain.gdrefid
             WHERE domain.vndrefid = VNDREFID
               AND (domain.enddate IS NULL or now()< domain.enddate)
               AND (scope.enddate IS NULL or now()< scope.enddate)
               AND (ksa.enddate IS NULL or now()< ksa.enddate)
             ORDER BY 2
        ");

    $edit->addControl("Narrative", "textarea")
		->sqlField('blbaseline')
		->css("width", "100%");

    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
    $edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
    $edit->addControl("IEP Year", "hidden")->value($stdIEPYear)->sqlField('siymrefid');
    $edit->addControl("ESY", "hidden")->value($esy)->sqlField('esy');

    $url_goal = CoreUtils::getURL('bgb_goal_add.php', array_merge($_GET, array('RefID' => null, 'baseline_id' => null)));
    $url_main = CoreUtils::getURL('bgb_main.php', array_merge($_GET, array('RefID' => null, 'baseline_id' => ($RefID > 0 ? $RefID : null))));

    $edit->finishURL = $url_main;
    $edit->cancelURL = $url_main;

    $edit->saveAndAdd = false;

    $edit->printEdit();
    //require_once("bgb_standart.php");
?>
<script type="text/javascript">
    var edit1 = EditClass.get();
    edit1.onSaveDoneFunc(
        function(refid) {
            if ($('input[name="RefID"]').val() == 0) {
                $('input[name="RefID"]').val(refid);
                api.confirm("Do you want to add a Goal for this Baseline?", onOk, onNo);
                return false;
            }
        }
    )

    function onOk() {
        api.goto(api.url('<?= $url_goal; ?>', {'RefID': 0,
            'baseline_id': $('input[name="RefID"]').val()}));
    }

    function onNo() {
        api.goto(api.url('<?= $url_main; ?>', {'baseline_id': $('input[name="RefID"]').val()}, "goal_id": null}));
    }
</script>
