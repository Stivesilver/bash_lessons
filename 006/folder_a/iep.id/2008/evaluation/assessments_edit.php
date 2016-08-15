<?php

	Security::init();

	$dskey      = io::get('dskey');
	$ds         = DataStorage::factory($dskey);
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$tsRefID    = io::geti('tsRefID');
	$edit       = new EditClass("edit1", io::get("RefID"));

	$edit->setSourceTable('webset.es_std_scr', 'shsdrefid');

	$edit->title      = "Add Assessment Result";
	$edit->saveLocal  = false;

	if (io::get("RefID") > 0) {
		$disabled = true;
	} else {
		$disabled = false;
	}



	$edit->addGroup("General Information");
	$edit->addControl("Area Assessed", "select")
		->disabled($disabled)
		->sqlField('screenid')
		->name('screenid')
		->sql("
			SELECT scrrefid,
                   scrdesc
              FROM webset.es_disdef_screeningtype
             WHERE vndrefid = VNDREFID
               AND (enddate IS NULL or now()< enddate)
             ORDER BY scrseq, scrdesc
        ");

	$edit->addControl("Procedure or test used", "select")
		->sqlField('hsprefid')
		->name('hsprefid')
		->disabled($disabled)
		->sql("
			SELECT hsprefid,
                   hspdesc
              FROM webset.es_scr_disdef_proc proc
                   INNER JOIN webset.es_disdef_screeningtype area ON area.scrrefid = proc.screenid
             WHERE proc.vndrefid = VNDREFID
			   AND recdeactivationdt IS NULL
			   AND proc.screenid = VALUE_01
             ORDER BY scrdesc, hspdesc
		")
		->tie('screenid');

	$edit->addControl("If other Procedure specify", "edit")
		->sqlField('test_name')
		->size(100);

	$edit->addControl("Evaluator", "edit")
		->sqlField('screener')
		->size(40);

	$edit->addControl("Title", "edit")
		->sqlField('test_usrtitle')
		->size(40);

	$edit->addControl("Date", "date")
		->sqlField('shsddate');

		$edit->addControl('Form', 'text')
			->transparent(true)
			->width('90%')
			->readOnly(true)
			->name('xml_data')
			->sqlField('xml_data')
			->css('display', 'none')
			->append(
				UIAnchor::factory('Complete Form')
					->onClick('getTitles()')
			)
			->showIf('hsprefid', db::execSQL("
					SELECT hsprefid
		              FROM webset.es_scr_disdef_proc proc
		             WHERE proc.vndrefid = VNDREFID
		               AND xml_test IS NOT NULL
                	")->indexAll())
		;

	$edit->addControl("Results (Strengths & Needs):", "textarea")
		->sqlField('shsdhtmltext')
		->css("width", "740px")
		->css("height", "150px")
		->css("font-family", "Courier")
		->css("font-size", "12px")
		->autoHeight(true);

	$edit->addUpdateInformation();

	$edit->addControl("stdIEPYear", "hidden")
		->value($stdIEPYear)
		->sqlField('iepyear');

	$edit->getButton(EditClassButton::SAVE_AND_ADD)
		->value("");

	$edit->firstCellWidth = "15%";

	$edit->printEdit();

	io::jsVar('dskey', io::get('dskey'));
	io::jsVar('RefID', io::get('RefID'));

?>

<script type="text/javascript">

	function getTitles() {
		var id = $('#hsprefid').val();
			api.ajax.post(
				'assessment_form.ajax.php',
				{id: id, dskey: dskey, xml_data: $('#xml_data').val()},
				function(result) {
					win = api.window.open(result.caption, result.url);
					win.maximize();
					win.addEventListener('formSaved', formCompleted);
					win.show();
				}

			);

	}

	function formCompleted(e) {
		$('#xml_data').val(e.param.values);
	}

</script>
