<?php

	Security::init();

	$dskey = io::get('dskey');
	$mode = io::get('mode', true);
	$title = ($mode == 'F' ? 'FBA' : 'BIP');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$student = new IDEAStudent($tsRefID);

	io::jsVar('dskey', $dskey);
	io::jsVar('mode', $mode);

	$edit = new EditClass('edit1', io::get('RefID'));

	$edit->title = 'Add/Edit ' . $title . ' - Items';

	$edit->firstCellWidth = '30%';

	$edit->setSourceTable('webset.std_in_bipitems', 'recrefid');

	if (io::get('RefID') == 0) {
		if (io::exists('id')) {
			$birefid = db::execSQL("
				SELECT birefid
				  FROM webset.std_in_bipitems
			     WHERE recrefid = " . io::geti('id') . "
			")->getOne();
			$defId = db::execSQL("
				SELECT cat.bcrefid,
				       bcdesc
				  FROM webset.disdef_bipcat cat
					   LEFT JOIN webset.disdef_bipitems items ON items.bcrefid = cat.bcrefid
				 WHERE cat.vndrefid = VNDREFID
				   AND form_type = '" . $mode . "'
				   AND birefid = $birefid
				ORDER BY bcseq, bcdesc
			")->getOne();
		} else {
			$defId = db::execSQL("
				SELECT cat.bcrefid,
				       bcdesc
				  FROM webset.disdef_bipcat cat
				 WHERE cat.vndrefid = VNDREFID
				   AND form_type = '" . $mode . "'
		         ORDER BY bcseq, bcdesc
			")->getOne();
			$birefid = "";
		}
	} else {
		$birefid = db::execSQL("
			SELECT birefid
			  FROM webset.std_in_bipitems
		 	 WHERE recrefid = " . io::get('RefID') . "
		")->getOne();
		$defId = db::execSQL("
			SELECT cat.bcrefid,
			       bcdesc
			  FROM webset.disdef_bipcat cat
				   LEFT JOIN webset.disdef_bipitems items ON items.bcrefid = cat.bcrefid
			 WHERE cat.vndrefid = VNDREFID
			   AND form_type = '" . $mode . "'
			   AND birefid = $birefid
			ORDER BY bcseq, bcdesc
		")->getOne();
	}
	$edit->topButtons = true;

	$sql1 = "
			SELECT cat.bcrefid,
			       bcdesc
			  FROM webset.disdef_bipcat cat
			 WHERE cat.vndrefid = VNDREFID
			   AND form_type = '" . $mode . "'
	   	     ORDER BY bcseq, bcdesc
		";



	$sql2 = "
		SELECT birefid,
               bidesc || CASE EXISTS (SELECT 1 FROM webset.std_in_bipitems s WHERE items.birefid = s.birefid AND stdrefid = $tsRefID) WHEN TRUE THEN ' (Added)' ELSE '' END
          FROM webset.disdef_bipcat cat
               LEFT JOIN webset.disdef_bipitems items ON items.bcrefid = cat.bcrefid
         WHERE cat.vndrefid = VNDREFID
           AND form_type = '" . $mode . "'
           AND items.bcrefid = VALUE_01
         ORDER BY bcseq, bcdesc, biseq, bidesc
	";

	$edit->addGroup('General Information');

	$edit->addControl($title, 'select_radio')
		->name('bcrefid')
		->sql($sql1)
		->value($defId)
		->breakRow()
		->req();

	$edit->addControl('Item', 'select_radio')
		->sqlField('birefid')
		->name('birefid')
		->sql($sql2)
		->value($birefid)
		->breakRow()
		->tie('bcrefid')
		->req();

	$edit->addControl('Narrative')
		->sqlField('rectext')
		->name('rectext')
		->showIf('birefid', db::execSQL("
			SELECT birefid
			  FROM webset.disdef_bipitems
		     WHERE bistat = 'Y'
			")->indexAll()
		)
		->size(50);

	$edit->addControl('Record Value', 'select_check')
		->data(array('1' => 1, '2' => 2))
		->sqlField('recval')
		->name('recval')
		->displaySelectAllButton(false)
		->req();

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('Student ID', 'hidden')->value($tsRefID)->sqlField('stdrefid');

	$edit->finishURL = CoreUtils::getURL('items.php', array('dskey' => $dskey, 'mode' => $mode));
	$edit->cancelURL = CoreUtils::getURL('items.php', array('dskey' => $dskey, 'mode' => $mode));

	$edit->saveAndAdd = false;

	$edit->addButton(
		FFButton::factory()
			->caption('Save & Add')
			->onClick('goToNext()')
			->width(113)
	);

	$edit->printEdit();
?>

<script>
	function goToNext(id) {
		EditClass.get().save();
		EditClass.get().addEventListener(
			ObjectEvent.SAVED,
			function (e) {
				var refid = EditClass.get().refid;
				e.preventDefault();
				api.goto(
					'items_add.php',
					{
						'dskey': dskey,
						'id': refid,
						 mode : mode,
						'RefID' : 0
					}
				);
			}
		)

	}
</script>
