<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$nexttab = io::geti('nexttab');

	$edit = new EditClass('edit1', '');

	$edit->firstCellWidth = '50%';

	$cats = db::execSQL("
		SELECT catrefid,
			   catdesc
		  FROM webset.statedef_aa_cat
		 WHERE (enddate IS NULL OR NOW ()< enddate)
		   AND screfid = " . VNDState::factory()->id . "
	")->assocAll();

	$accs = db::execSQL("
		SELECT sta.accrefid,
			   accdesc,
			   acccat,
			   std.acc_subjects,
			   acc_oth,
			   cat
		  FROM webset.statedef_aa_acc AS sta
		  LEFT JOIN webset.std_form_d_acc AS std ON (std.accrefid = sta.accrefid AND std.stdrefid = $tsRefID  AND std.syrefid = $stdIEPYear)
         INNER JOIN webset.statedef_aa_cat AS cat ON (cat.catrefid = sta.acccat)
		 WHERE (sta.enddate IS NULL OR NOW ()< sta.enddate)
           AND screfid = " . VNDState::factory()->id . "
		 ORDER BY seq_num
	")->assocAll();

	foreach ($cats as $cat) {
		$edit->addGroup($cat['catdesc']);
		foreach ($accs as $acc) {
			if ($acc['acccat'] == $cat['catrefid']) {
				$progs = ListClassContent::factory($acc['accdesc'])
					->addColumn('Desc')
					->setSQL("
						SELECT progrefid,
							   progdesc
						  FROM webset.statedef_aa_prog AS prog
						 WHERE screfid = " . VNDState::factory()->id . "
						  AND progrefid IN (" .$acc['cat']  .")
						 ORDER BY seqnum
				");
				$edit->addControl(FFMultiSelect::factory($acc['accdesc']))
					->setSearchList($progs)
					->value($acc['acc_subjects'])
					->name('acc_' . $acc['accrefid']);
				if (substr($acc['accdesc'], 0, 5) == 'Other') {
					$edit->addControl('Other', 'text')
						->name('other_' . $acc['accrefid'])
						->width('100%')
						->value($acc['acc_oth']);
				}
			}
		}
	}

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->setPresaveCallback('savePart', './testing_cmt_save.inc.php', array('dskey' => $dskey));

	$edit->finishURL = 'javascript:parent.switchTab(' . $nexttab . ')';
	$edit->saveAndEdit = true;
	$edit->saveAndAdd = false;
	$edit->cancelURL = 'javascript:parent.switchTab();';

	$edit->topButtons = true;

	$edit->printEdit();
?>
