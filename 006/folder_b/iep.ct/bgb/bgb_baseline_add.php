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

	$edit->addControl(
		FFIDEABGBArea::factory('Area')
			->sqlField('blksa')
	);

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
	var recordAdded = false;

	edit1.onSaveFunc(
		function () {
			if (edit1.refid == 0) {
				recordAdded = true;
			}
		}
	)

	edit1.onSaveDoneFunc(
		function (refid) {
			if (recordAdded) {
				api.confirm("Do you want to add a Goal for this Baseline?", onOk, onNo);
				return false;
			}
		}
	)

	function onOk() {
		api.goto(api.url('<?= $url_goal; ?>', {'RefID': 0,
			'baseline_id': edit1.refid}));
	}

	function onNo() {
		api.goto(api.url('<?= $url_main; ?>', {"baseline_id": edit1.refid, "goal_id": null}));
	}
</script>
