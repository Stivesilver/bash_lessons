<?php
    Security::init();

	$dskey = io::get('dskey');
	$RefID = io::geti('RefID');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$titles = json_decode(IDEAFormat::getIniOptions('bgb'), true);
	$esy = io::get('ESY');
	$goal_id = io::geti('goal_id');

    $goal = array_shift(db::execSQL("
                            SELECT *
                              FROM webset.std_bgb_goal
                             WHERE grefid = " . $goal_id . "
            ")->assocAll());

    if ($RefID > 0) {
        $bench = array_shift(db::execSQL("
                            SELECT *
                              FROM webset.std_bgb_benchmark
                             WHERE brefid = " . $RefID . "
            ")->assocAll());
    }

    $baseline_id = $goal['blrefid'];

    $edit = new EditClass("edit1", io::get("RefID"));

    $edit->title = 'Add/Edit ' . ($esy == 'Y' ? 'ESY ' : '') . $titles['benchmark'];

    $edit->setSourceTable('webset.std_bgb_benchmark', 'brefid');

	$edit->addControl("", "select_radio")
		->name('compose')
		->value((isset($goal['overridetext']) && $goal['overridetext'] != '' or isset($bench['overridetext']) && $bench['overridetext'] != '') ? 2 : 1)
		->data(array('1' => 'Compose ' . $titles['benchmark'], '2' => 'Own ' . $titles['benchmark']))
		->hide(!(IDEACore::disParam(1) == 'Y'));

    $edit->addGroup("General Information");
    $edit->addControl("Sentence Preface", "select")
        ->sqlField('bpreface')
        ->value($goal['gpreface'])
        ->sql("
            SELECT gsfrefid,
                   replace(gsptext,'The student', '" . $ds->safeGet('stdfirstname') . "')
              FROM webset.disdef_bgb_goalsentencepreface
             WHERE vndRefID = VNDREFID
               AND (enddate IS NULL or now()< enddate)
             ORDER BY gsptext
        ")
        ->hideIf('compose', '2');

    $edit->addControl("Sentence Verb", "select")
        ->sqlField('baction')
        ->value($goal['gaction'])
        ->sql("
            SELECT gdskgarefid,
                   gdskgaaction
              FROM webset.std_bgb_baseline std
                   INNER JOIN webset.disdef_bgb_ksaksgoalactions dis ON std.blksa = dis.gdskgrefid
             WHERE blrefid = " . $baseline_id . "
               AND (enddate IS NULL or now()< enddate)
             ORDER BY gdskgaaction
        ")
        ->hideIf('compose', '2');

    if (db::execSQL("
                    SELECT 1
                      FROM webset.std_bgb_baseline std
                           INNER JOIN webset.disdef_bgb_goaldomainscopeksa ksa ON std.blksa = ksa.gdskrefid
                           INNER JOIN webset.disdef_bgb_goaldomainscope scope ON ksa.gdsrefid = scope.gdsrefid
                     WHERE itemsbank = 1
                       AND blrefid = " . $baseline_id . "
                  ")->getOne() == '1') {

        $edit->addControl('Items List', 'textarea')
            ->sqlField('bitemslist')
            ->name('bitemslist')
            ->css('width', '100%')
            ->append(FFButton::factory('Build From Items Bank')->onClick('bitembank();'))
            ->hideIf('compose', '2');
    }

    $edit->addControl("Sentence Content", "select")
        ->sqlField('bcontent')
        ->value($goal['gcontent'])
        ->sql("
            SELECT gdskgcrefid,
                   gdskgccontent
              FROM webset.std_bgb_baseline std
                   INNER JOIN webset.disdef_bgb_scpksaksgoalcontent dis ON std.blksa = dis.gdskgrefid
             WHERE std.blrefid = " . $baseline_id . "
               AND (enddate IS NULL or now()< enddate)
             ORDER BY gdskgccontent
        ")
        ->hideIf('compose', '2');

    $edit->addControl("Condition", "select")
        ->sqlField('bconditions')
        ->value($goal['gconditions'])
        ->sql("
            SELECT crefid,
                   cdesc
              FROM webset.std_bgb_baseline std
                   INNER JOIN webset.disdef_bgb_ksaconditions dis ON std.blksa = dis.blksa
             WHERE std.blrefid = " . $baseline_id . "
               AND (enddate IS NULL or now()< enddate)
             ORDER BY cdesc
        ")
        ->hideIf('compose', '2');

    $edit->addControl("Criteria Unit", "select")
        ->sqlField('dcurefid')
        ->value($goal['dcurefid'])
        ->sql("
            SELECT dcurefid,
                   dcudesc
              FROM webset.disdef_bgb_criteriaunits
             WHERE vndRefId = VNDREFID
               AND (enddate IS NULL or now()< enddate)
             ORDER BY dcudesc
        ")
        ->hideIf('compose', '2');

    $edit->addControl("Criteria Description", "select")
        ->sqlField('bcriteria')
        ->value($goal['gcriteria'])
        ->sql("
            SELECT crrefid,
                   crdesc
             FROM webset.std_bgb_baseline std
                  INNER JOIN webset.disdef_bgb_ksacriteria dis ON std.blksa = dis.blksa
            WHERE std.blrefid = " . $baseline_id . "
              AND (enddate IS NULL or now()< enddate)
            ORDER BY crdesc
        ")
        ->hideIf('compose', '2');

    $edit->addControl("Criteria Basis", "edit")
        ->sqlField('bcriteria2')
        ->value($goal['gcriteria2'])
        ->size(55)
        ->maxlength(1000)
        ->hideIf('compose', '2');

    $edit->addControl(VNDState::factory()->code == 'KS' ? 'Timeframe' : 'Evaluation', "select")
        ->sqlField('bevaluation')
        ->value($goal['gevaluation'])
        ->sql("
            SELECT erefid,
                   edesc
              FROM webset.disdef_bgb_ksaeval
             WHERE vndrefid = VNDREFID
               AND (enddate IS NULL or now()< enddate)
             ORDER BY edesc
        ")
        ->hideIf('compose', '2');

    $edit->addControl("Measurement", "select")
        ->sqlField('bmeasure')
        ->value($goal['gmeasure'])
        ->sql("
            SELECT mrefid, mdesc
              FROM webset.disdef_bgb_measure
             WHERE vndrefid = VNDREFID
               AND (enddate IS NULL or now()< enddate)
             ORDER BY mdesc
        ")
        ->hideIf('compose', '2');

    $edit->addControl($titles['benchmark'], 'textarea')
        ->sqlField('overridetext')
        ->value($goal['overridetext'])
        ->css('WIDTH', '100%')
        ->css('HEIGHT', '200px')
        ->hideIf('compose', '1');

    $edit->addControl("Order #", "integer")
        ->sqlField('order_num')
        ->value((int) db::execSQL("
                    SELECT max(order_num)
                      FROM webset.std_bgb_benchmark
                     WHERE grefid = " . $goal_id . "
                ")->getOne() + 1
        )
        ->size(5);

	$edit->addGroup('Other Information');

	$edit->addControl('Person(s) Responsible', 'text')->sqlField('in_support')->size(55);

    $edit->addGroup('Update Information', true);
    $edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
    $edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
    $edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');
    $edit->addControl('ESY', 'hidden')->value($esy)->sqlField('esy');
    $edit->addControl('Goal ID', 'hidden')->value($goal_id)->sqlField('grefid');

    $url_bench = CoreUtils::getURL('bgb_benchmark_add.php', array_merge($_GET, array('RefID' => null, 'goal_id' => $goal_id)));

    $edit->finishURL = CoreUtils::getURL('bgb_main.php', array_merge($_GET, array('RefID' => null)));
    $edit->cancelURL = CoreUtils::getURL('bgb_main.php', array_merge($_GET, array('RefID' => null)));

    $edit->setPresaveCallback('benchCompose', 'bgb_save.inc.php');

    $edit->saveAndAdd = false;

    $edit->printEdit();

	io::js('
    var edit1 = EditClass.get();
    var recordAdded = false;

    edit1.onSaveFunc(
	    function () {
		    if (edit1.refid == 0) {
			    recordAdded = true;
		    }
	    }
    )

    edit1.onSaveDoneFunc(
        function(refid) {
            if (recordAdded) {
                api.confirm("Do you want to add another ' . $titles['benchmark'] . ' for this ' . $titles['goal'] . '?", onOk, onNo);
                return false;
            }
        }
    )

    function onOk() {
        api.goto(api.url("' . $url_bench . '", {"RefID": 0}));
    }

    function onNo() {
        edit1.cancelEdit();
    }

    function bitembank() {
        var wnd = api.window.open("Items Bank", "' . CoreUtils::getURL("iep_items_bank.php", array("baseline_id" => $baseline_id)) . '");
        wnd.resize(950, 600);
        wnd.center();
        wnd.addEventListener("items_selected", onEvent);
        wnd.show();
    }

    function onEvent(e) {
        var bgbitems = e.param.bgbitems;
        $("#bitemslist").val(bgbitems);
    }
	');

?>
<script type="text/javascript"></script>
