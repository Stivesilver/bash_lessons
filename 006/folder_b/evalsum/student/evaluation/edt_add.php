<?php
	Security::init();

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$RefID = io::geti('RefID');
	$evalproc_id = $ds->safeGet('evalproc_id');
	$tsRefID = $ds->safeGet('tsRefID');

	//GET ATTENDANCE TYPE DROPDOWN
	$edit = new EditClass('edit1', $RefID);

	$edit->title = 'Add/Edit Eligibility Meeting Participants';
	$edit->firstCellWidth = "30%";

	$edit->setSourceTable('webset.es_std_er_participants', 'erpa_refid');

	$edit->addGroup('General Information');

	$edit->addControl(FFInputDropList::factory('Participant')
		->name('part_name')
		->sqlField('part_name')
		->dropListSQL("
        SELECT COALESCE(stdfnm,'') || ' ' || COALESCE(stdlnm,'')
          FROM webset.dmg_studentmst dmg
               INNER JOIN webset.sys_teacherstudentassignment ts ON dmg.stdrefid = ts.stdrefid
         WHERE tsrefid = " . $tsRefID . "
         UNION ALL
       (SELECT COALESCE(gdfnm,'') || ' ' || COALESCE(gdlnm,'')
          FROM webset.dmg_guardianmst grd
               INNER JOIN webset.def_guardiantype ON grd.gdtype = webset.def_guardiantype.gtrefid
               INNER JOIN webset.sys_teacherstudentassignment ts ON grd.stdrefid = ts.stdrefid
         WHERE tsrefid = " . $tsRefID . "
         ORDER BY gtrank)
         UNION ALL
       (SELECT umfirstname || ' ' || umlastname
          FROM sys_usermst
         WHERE vndrefid = VNDREFID
           AND COALESCE(um_internal, TRUE) IS TRUE
         ORDER BY 1)
        "))
		->highlightField(false)
		->width('400px')
		->append(FFButton::factory('Find Teacher or Guardian')->onClick('selectUser();'));

	$edit->addControl(FFSelect::factory('Role'))
		->name('part_role_id')
		->emptyOption(true)
		->sqlField('part_role_id')
		->sql("
			SELECT refid,
			       role
			  FROM webset.es_statedef_red_part
			 ORDER BY seq, role
        ")
		->req(true);

	$edit->addControl('Specify Role')
		->sqlField('part_role_oth')
		->showIf('part_role_id', db::execSQL("SELECT refid FROM webset.es_statedef_red_part WHERE role ILIKE '%Other%'")->indexAll())
		->size(50);

	$edit->addGroup('Update Information', true);
	$edit->addControl('Last User', 'protected')->value(SystemCore::$userUID)->sqlField('lastuser');
	$edit->addControl('Last Update', 'protected')->value(date('m-d-Y H:i:s'))->sqlField('lastupdate');
	$edit->addControl("evalproc_id", "hidden")->value($evalproc_id)->sqlField('eprefid');

	$edit->addControl('Data Storage Key', 'hidden')->name('dskey')->value($dskey);

	$edit->finishURL = CoreUtils::getURL('./edt_list.php', array('dskey' => $dskey));
	$edit->cancelURL = CoreUtils::getURL('./edt_list.php', array('dskey' => $dskey));

	$edit->printEdit();
?>
<script type="text/javascript">
	function selectUser() {
		var wnd = api.window.open('Find Teacher or Guardian', api.url('../../../iep/iepmeeting/iep_participants_users.php', {'dskey': $("#dskey").val()}));
		wnd.resize(950, 600);
		wnd.center();
		wnd.addEventListener('user_selected', onEvent);
		wnd.show();
	}

	function onEvent(e) {
		var name = e.param.name;
		$("#part_name").val(name);
	}
</script>
