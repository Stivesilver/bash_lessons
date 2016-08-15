<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$nexttab = io::geti('nexttab');

	$edit = new EditClass('edit1', '');

	$edit->title = 'Form D - Part 2: MAP Accommodations';

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
		 WHERE (enddate IS NULL OR NOW ()< enddate)
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
				if (stristr($acc['accdesc'],'(describe)')) {
					$edit->addControl('Describe', 'text')
						->name('other_' . $acc['accrefid'])
						->width('100%')
						->value($acc['acc_oth'])
						->hideIf('acc_' . $acc['accrefid'], '');
				}
			}
		}
	}

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->setPresaveCallback('savePart', 'part2_save.inc.php', array('dskey' => $dskey));

	$edit->finishURL = 'javascript:parent.switchTab(' . $nexttab . ')';
	$edit->saveAndEdit = true;
	$edit->saveAndAdd = false;
	$edit->cancelURL = 'javascript:api.window.destroy();';

	$edit->topButtons = true;

	$edit->printEdit();

	include("notes2.php");
	include("notes0.php");
?>
