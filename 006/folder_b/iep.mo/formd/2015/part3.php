<?php

	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$nexttab = io::geti('nexttab');

	$edit = new EditClass('edit1', '');

	$edit->title = 'Form D - Part 3: ACT Accommodations';

	$edit->firstCellWidth = '80%';

	$cats = db::execSQL("
		SELECT cactrefid,
			   catname
		  FROM webset.statedef_aa_act_cat
		 WHERE enddate IS NULL OR now()< enddate
	")->assocAll();

	$accs = db::execSQL("
		SELECT sta.actrefid AS actrefid,
		       COALESCE(sta.actsubcat || ': ','') || sta.actname AS actname,
		       sta.actcat,
		       sta.actsubcat,
		       sta.other,
		       std.refid AS srefid,
		       std.other AS val_other
		  FROM webset.statedef_aa_act_acc AS sta
		       LEFT JOIN webset.std_form_d_act AS std ON (sta.actrefid = std.actrefid AND std.stdrefid = $tsRefID AND std.syrefid = $stdIEPYear)
		 WHERE (sta.enddate IS NULL OR now()< sta.enddate)
		 ORDER BY sta.seqnum
	")->assocAll();

	foreach ($cats as $cat) {
		$edit->addGroup($cat['catname']);
		foreach ($accs as $acc) {
			if ($acc['actcat'] == $cat['cactrefid']) {
				$checked = '';
				if ($acc['srefid']) {
					$checked = 'on';
				}
				$edit->addControl(FFCheckBoxList::factory($acc['actname'])
					->data(array('on' => ''))
					->displaySelectAllButton(false)
					->value($checked)
				)->name('check_' . $acc['actrefid']);
				if ($acc['other'] == 'Y') {
					$edit->addControl('Other', "text")
						->name('oth_' . $acc['actrefid'])
						->value($acc['val_other'])
						->width('100%')
						->showIf('check_' . $acc['actrefid'], 'on');
				}
			}
		}
	}

	$edit->addButton(
		IDEAFormat::getPrintButton(array('dskey' => $dskey))
	);

	$edit->setPresaveCallback('savePart', 'part3_save.inc.php', array('dskey' => $dskey));

	$edit->finishURL = 'javascript:parent.switchTab(' . $nexttab . ')';
	$edit->saveAndAdd = false;
	$edit->saveAndEdit = true;
	$edit->cancelURL = 'javascript:api.window.destroy();';

	$edit->printEdit();

	include("notes3.php");
	include("notes0.php");
?>
