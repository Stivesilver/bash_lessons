<?php

	Security::init();

	$RefID = io::geti('RefID');

	$edit = new editClass("edit1", $RefID);

	$edit->title = "Add/Edit Menu Definition";

	$edit->setSourceTable('webset.sped_menudef', 'mdrefid');

	$edit->addGroup('General Information');

	$edit->addControl("Item Name")->width(400)->sqlField('mdname');
	$edit->addControl("Menu Text")->width(400)->sqlField('mdmenutext')->req();
	$edit->addControl("Description", "textarea")->sqlField('mddesc');
	$edit->addControl("App. Link", "text")->width(400)->sqlField('mdlink');
	$edit->addControl("Icon URL", "text")->width(400)->sqlField('mdicon');
	$edit->addUpdateInformation();

	$edit->finishURL = CoreUtils::getURL('./menuDef.php', array('staterefid' => -1));
	$edit->cancelURL = CoreUtils::getURL('./menuDef.php', array('staterefid' => -1));

	$edit->printEdit();

	if ($RefID > 0) {

		$list = new listClass();

		$list->title = "Menu Manager - Active IEP Formats";

		$list->SQL = "
			SELECT mrefid,
                   shortdesc,
                   mitemgroup,
                   mitemorder,
                   mitemnewline,
                   mgroupnewline,
                   mitem_iep_req_sw,
                   displcondition
              FROM webset.sped_menu MM
                   LEFT OUTER JOIN webset.sped_menudef MD ON MM.mdrefid=MD.mdrefid
                   INNER JOIN webset.sped_menu_set mset ON mset.srefid = MM.set_refid
             WHERE MM.mdrefid = $RefID
               AND (enddate IS NULL OR now()< enddate)
             ORDER BY shortdesc, mitemgroup, mitemorder
	    ";

		$list->showSearchFields = "yes";
		$list->addSearchField("ID", "(mrefid)::varchar = ANY (STRING_TO_ARRAY(REGEXP_REPLACE('ADD_VALUE', '[^0-9,]', '', 'g'), ','))");

		$list->addColumn('ID')->sqlField('mrefid');
		$list->addColumn("Name")->sqlField('shortdesc');
		$list->addColumn("Group")->sqlField('mitemgroup');
		$list->addColumn("INL")->sqlField('mitemnewline')->type('switch');
		$list->addColumn("GNL")->sqlField('mgroupnewline')->type('switch');
		$list->addColumn("IEP")->sqlField('mitem_iep_req_sw')->type('switch');
		$list->addColumn("Include")->sqlField('displcondition');
		$list->addColumn("Seq")->sqlField('mitemorder');

		$list->addRecordsResequence(
			'webset.sped_menu',
			'mitemorder'
		);

		$list->addURL = CoreUtils::getURL('./menuDefDtl.php', array('mdrefid' => $RefID));
		$list->editURL = CoreUtils::getURL('./menuDefDtl.php', array('mdrefid' => $RefID));
		$list->deleteTableName = "webset.sped_menu";
		$list->deleteKeyField = "mrefid";

		$list->addButton(
			FFIDEAExportButton::factory()
				->setTable('webset.sped_menu')
				->setKeyField('mrefid')
				->applyListClassMode()
		);

		$list->printList();
	}

?>
