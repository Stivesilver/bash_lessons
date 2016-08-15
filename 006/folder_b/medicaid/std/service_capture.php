<?php
	Security::init();

	//if (db::execSQL("SELECT 1 FROM webset.med_disdef_providers WHERE umrefid = " . SystemCore::$userID)->EOF)
	//	io::err("You are not assigned in Service Provider. ");

	$mp_refid = io::geti('mp_refid');
	if ($mp_refid == 0)
		$mp_refid = db::execSQL("
			 SELECT mp_refid
			 FROM
			((SELECT mp_refid
			  FROM webset.med_disdef_providers
			 WHERE umrefid = " . SystemCore::$userID . ")
		     UNION ALL
           (SELECT mp_refid
			  FROM webset.med_disdef_providers
			 WHERE vndrefid = VNDREFID
			 ORDER BY mp_lname, mp_fname)) AS tt
			 LIMIT 1
		")->getOne();

	if (!$mp_refid)
		io::err("Please add Service Provider. ");

	$SQL = "
				SELECT mp_refid,  mp_lname || ', ' || mp_fname || COALESCE(' (' || mp_id || ')', '') AS mp_name
			      FROM webset.med_disdef_providers
			     WHERE vndrefid = VNDREFID
			     ORDER BY LOWER(mp_lname), LOWER(mp_fname)
			";
	$rs = db::execSQL($SQL)
		->assocAll();

	$tbl = new UITable('100% .zBox3');
	$tbl->cellPadding(4);
	$tbl->cellSpacing(1);
	$tbl->addColumn('100%');
	$tbl->addRow('.zBox4');
	$tbl->addCell('Service Provider');
	foreach ($rs as $key => $value) {
		$tblAttr = UITableAttr::factory($mp_refid == $value['mp_refid'] ? '.zHLightBox' : '.zLightBox')
			->id('index_row_' . (string)$value['mp_refid'])
			->onClick("
					api.goto(
						'./service_capture.php',
						{'mp_refid' : " . $value['mp_refid'] . "}
					)
				");
		if ($mp_refid != $value['mp_refid']) {
			$tblAttr->onMouseOut("$(this).removeClass('zHLightBox').addClass('zLightBox')")
				->onMouseOver("$(this).removeClass('zLightBox').addClass('zHLightBox')");
		}
		$tbl->addRow($tblAttr);
		$tbl->addCell($value['mp_name']);
	}


	echo UIFrameSet::factory('37, auto')
		->className('zBox10 zDarkLines')
		->addFrame(
			UIFrame::factory()
				->css('overflow', 'visible')
				->indent(2, 0, 2, 0)
				->addObject(
					UIRollBox::factory(UIRollBox::LEFT, 'caption_rollbox')
						->autoClose(true) # issue with scrollbar inside
						->attachBox(
							UIRollBoxItem::factory()
								->name('servise_providers_conttent')
								->width(250)
								->height(0)
								->caption('Service Provider &nbsp;')
								->icon('./img/serv_prov_16.png')
								->addObject(
									UIFrame::factory()
										->className('zBox1 zRound6')
										->scrollable(true)
										->css('height', '100%')
										->scrollable(true)
										->addObject($tbl)
								)
						)
				)
		)
		->addFrame(
			UIFrame::factory()
				->className('zRound6 zResetTopRightRound zResetBottomRightRound')
				->id('time_track_content')
				->addObject(
					UISCTimeScale::factory(SCTimeScale::MEDICAID_SERVICE, $mp_refid)
				)
		)
		->toHTML();
?>
