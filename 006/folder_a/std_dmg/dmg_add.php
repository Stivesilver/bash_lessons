<?php

	Security::init();
	$titles = json_decode(IDEAFormat::getIniOptions('demo_titles'), true);

	$RefID = io::geti('RefID');

	if ($RefID > 0) {
		$std_title = db::execSQL(
				"SELECT stdfnm || ' ' || stdlnm
			FROM webset.dmg_studentmst
			WHERE stdrefid = $RefID")->getOne() . ', Lumen ID: ' . $RefID . ' - ' . $titles['main_title'];
	} else {
		$std_title = 'Add New ' . $titles['student'];
	}

	$edit = new EditClass('edit1', $RefID);

	$edit->setSourceTable('webset.dmg_studentmst', 'stdrefid');

	$edit->addTab($titles['student']);
	$edit->addControl('First Name')->sqlField('stdfnm')->req();
	$edit->addControl('Middle Name')->sqlField('stdmnm');
	$edit->addControl('Last Name')->sqlField('stdlnm')->req();
	$edit->addControl('Nickname')->sqlField('stdnickname');
	$edit->addControl(FFSwitchAI::factory($titles['student'] . ' Status'))->sqlField('stdstatus')->value('A');
	$edit->addControl('Date of Birth', 'date')->sqlField('stddob');

	$edit->addControl('Gender', 'select_radio')
		->sqlField('stdsex')
		->sql("SELECT validvalueid, validvalue
                 FROM webset.glb_validvalues
                WHERE valueName = 'UKTGender'");

	$edit->addControl('Race/ Ethnicity', 'select')
		->sqlField('stdeth')
		->emptyOption(true)
		->sql("SELECT ethRefID,
                      TRIM(TRIM(COALESCE(ethcode, '')) || '/' || COALESCE(replace(ethdesc,'/',' '), ''), '/')
                 FROM webset.statedef_ethniccode
                WHERE scdrefid = " . VNDState::factory()->id . "
                ORDER BY 2");

	$edit->addTab('Numbers');
	$edit->addSQLConstraint(
		'This External ID Number already belongs to somebody',
		"
        SELECT 1
	      FROM webset.dmg_studentmst
	     WHERE vndrefid = VNDREFID
           AND COALESCE(std_deleted_sw, 'N') = 'N'
           AND externalid = '[externalid]'
	       AND COALESCE(externalid, '')!=''
	       AND stdrefid!=AF_REFID
    ");
	$edit->addControl('External ID Number')
		->sqlField('externalid')
		->name('externalid')
		->append(UICustomHTML::factory('')->id('externalid_div'))
		->onChange('api.ajax.get(api.url("dmg_check.ajax.php", {id: this.value,
                                                                stdrefid: ' . $RefID . ',
                                                                field: "externalid"}),
                                 function (answer) {
                                     if (answer.data) {
                                         EditClass.get().lockButtons(true);
                                         $("#externalid_div").html(answer.data);
                                     } else {
                                         EditClass.get().lockButtons(false);
                                         $("#externalid_div").html("");
                                     }
                                 }
                                 )');

	$edit->addSQLConstraint(
		'This ID Number (Ext2) already belongs to somebody',
		"
        SELECT 1
	      FROM webset.dmg_studentmst
	     WHERE vndrefid = VNDREFID
            AND COALESCE(std_deleted_sw, 'N') = 'N'
            AND stdschid = '[stdschid]'
	        AND COALESCE(stdschid, '')!=''
	        AND stdrefid!=AF_REFID
    ");
	$edit->addControl($titles['student'] . ' ID Number (Ext2)')
		->sqlField('stdschid')
		->name('stdschid')
		->append(UICustomHTML::factory('')->id('stdschid_div'))
		->onChange('api.ajax.get(api.url("dmg_check.ajax.php", {id: this.value,
                                                                stdrefid: ' . $RefID . ',
                                                                field: "stdschid"}),
                                 function (answer) {
                                     if (answer.data) {
                                         EditClass.get().lockButtons(true);
                                         $("#stdschid_div").html(answer.data);
                                     } else {
                                         EditClass.get().lockButtons(false);
                                         $("#stdschid_div").html("");
                                     }
                                 }
                                 )');

	$edit->addSQLConstraint(
		'This Federal ID Number already belongs to somebody',
		"
        SELECT 1
	      FROM webset.dmg_studentmst
	     WHERE vndrefid = VNDREFID
           AND COALESCE(std_deleted_sw, 'N') = 'N'
           AND stdfedidnmbr = '[stdfedidnmbr]'
	       AND COALESCE(stdfedidnmbr, '')!=''
	       AND stdrefid!=AF_REFID
    ");
	$edit->addControl('Federal ID Number')
		->sqlField('stdfedidnmbr')
		->name('stdfedidnmbr')
		->append(UICustomHTML::factory('')->id('stdfedidnmbr_div'))
		->onChange('api.ajax.get(api.url("dmg_check.ajax.php", {id: this.value,
                                                                stdrefid: ' . $RefID . ',
                                                                field: "stdfedidnmbr"}),
                                 function (answer) {
                                     if (answer.data) {
                                         EditClass.get().lockButtons(true);
                                         $("#stdfedidnmbr_div").html(answer.data);
                                     } else {
                                         EditClass.get().lockButtons(false);
                                         $("#stdfedidnmbr_div").html("");
                                     }
                                 }
                                 )');

	$edit->addSQLConstraint(
		'This State ID Number already belongs to somebody',
		"
        SELECT 1
	      FROM webset.dmg_studentmst
	     WHERE vndrefid = VNDREFID
           AND COALESCE(std_deleted_sw, 'N') = 'N'
           AND stdstateidnmbr = '[stdstateidnmbr]'
	       AND COALESCE(stdstateidnmbr, '')!=''
	       AND stdrefid!=AF_REFID
    ");
	$edit->addControl('State ID Number')
		->sqlField('stdstateidnmbr')
		->name('stdstateidnmbr')
		->append(UICustomHTML::factory('')->id('stdstateidnmbr_div'))
		->onChange('api.ajax.get(api.url("dmg_check.ajax.php", {id: this.value,
                                                                stdrefid: ' . $RefID . ',
                                                                field: "stdstateidnmbr"}),
                                 function (answer) {
                                     if (answer.data) {
                                         EditClass.get().lockButtons(true);
                                         $("#stdstateidnmbr_div").html(answer.data);
                                     } else {
                                         EditClass.get().lockButtons(false);
                                         $("#stdstateidnmbr_div").html("");
                                     }
                                 }
                                 )');

	$edit->addControl('Medicaid Number')->sqlField('stdmedicatenum');

	//General Tab
	$edit->addTab('General');

	$edit->addControl('Attending School', 'list')
		->sqlField('vourefid')
		->req(true)
		->sql("
            SELECT vourefid,
                   vouname
              FROM sys_voumst
             WHERE vndrefid = VNDREFID
             ORDER BY 2
    ");

	$edit->addControl('Resident School', 'list')
		->sqlField('vourefid_res')
		->req()
		->emptyOption(true)
		->sql("
            SELECT vourefid,
                   vouname
              FROM sys_voumst
             WHERE vndrefid = VNDREFID
             ORDER BY 2
    ");

	$edit->addControl('Grade Level', 'select')
		->sqlField('gl_refid')
		->req()
		->emptyOption(true)
		->sql("
            SELECT gl_refid,
                   gl_code
              FROM c_manager.def_grade_levels
             WHERE vndrefid = VNDREFID
             ORDER BY gl_numeric_value
    ");

	$edit->addControl(FFSwitchYN::factory('504 Student'))->value('N')->sqlField('student504');
	$edit->addControl(FFSwitchYN::factory('Gifted Program'))->value('N')->sqlField('giftedprogram');
	$edit->addControl(FFSwitchYN::factory('Title I'))->value('N')->sqlField('stdtitle_i');
	$edit->addControl(FFSwitchYN::factory('Migrant'))->value('N')->sqlField('stdimmigrant');

	$edit->addControl('Primary Language', 'select')
		->value(SystemCore::$DBUtils->execSQL("
                    SELECT refid
                      FROM webset.statedef_prim_lang
                     WHERE screfid = " . VNDState::factory()->id . "
                       AND LOWER(adesc) = LOWER('English')
                       AND (recdeactivationdt IS NULL OR now()< recdeactivationdt)
                ")->getOne())
		->sqlField('splrefid')
		->emptyOption(true)
		->sql("
            SELECT refid,
                   adesc
              FROM webset.statedef_prim_lang
             WHERE screfid = " . VNDState::factory()->id . "
               AND (recdeactivationdt IS NULL OR now()< recdeactivationdt)
             ORDER BY 2
        ");

	//Address Tab
	$edit->addTab('Address');
	$edit->addControl('Home Phone', 'phone')->sqlField('stdhphn');
	$edit->addControl('Mobile Phone', 'phone')->sqlField('stdhphnmob');
	$edit->addControl('Address 1')->sqlField('stdhadr1')->size(30);
	$edit->addControl('Address 2')->sqlField('stdhadr2')->size(30);
	$edit->addControl('City')->sqlField('stdhcity');
	$edit->addControl('State')
		->value(VNDState::factory()->code)
		->sqlField('stdhstate')
		->size(2);

	$edit->addControl('Zip Code')->sqlField('stdhzip')->size(10);
	$edit->addControl('County', 'select')
		->sqlField('stdcounty')
		->emptyOption(true)
		->sql("
            SELECT sc.refid,
                   trim(COALESCE(sc.countcode,'') || ' ' || sc.countname)
              FROM webset.statedef_counties AS sc
             WHERE sc.screfid = " . VNDState::factory()->id . "
             ORDER BY 2
	");

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl('vndrefid', 'hidden')->value(SystemCore::$VndRefID)->sqlField('vndrefid');
	$edit->addControl('vndrefid_res', 'hidden')->value(SystemCore::$VndRefID)->sqlField('vndrefid_res');

	//$edit->saveLocal = 'no';
	$edit->finishURL = 'dmg_add.php';
	$edit->cancelURL = 'javascript:api.window.destroy()';

	$edit->saveAndEdit = true;

	$edit->getButton(EditClassButton::CANCEL)->value('To List');
	$edit->getButton(EditClassButton::SAVE_AND_FINISH)->hide();

	$edit->firstCellWidth = '30%';
	//$edit->topButtons = true;

	$edit->printEdit();

	if ($RefID > 0) {
		$tabs = new UITabs('tabs');
		$tabs->addTab('Guardians')->url(CoreUtils::getURL('guard_list.php', array('stdrefid' => $RefID)))->name('guardians');
		$tabs->addTab('Emergency')->url(CoreUtils::getURL('emer_list.php', array('stdrefid' => $RefID)))->name('emergency');
		$tabs->addTab('Groupings')->url(CoreUtils::getURL('group_list.php', array('stdrefid' => $RefID)))->name('grouping');
		print $tabs->toHTML();
	}

?>
<script type="text/javascript">
	if (SystemCore.getUserInterface().coreVersion == 1) {
		parent.zWindow.changeCaption(<?=json_encode($std_title);?>);
		parent.zWindow.changeSystemBarCaption(<?=json_encode($std_title);?>);
	} else {
		api.window.changeTitle(<?= json_encode($std_title) ?>);
	}
</script>
