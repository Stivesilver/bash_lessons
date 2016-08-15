<?php

	Security::init();

	$RefID = io::geti('RefID');

	$edit = new EditClass('EDIT', $RefID);

	$edit->title = 'Add/Edit Medicaid Service Providers';

	$edit->setSourceTable('webset.med_disdef_providers', 'mp_refid');

	$edit->addGroup('General Information');
	$edit->addControl('Provider ID', 'text')
		->sqlField('mp_id');

	$edit->addControl('Provider Type', 'list')
		->sqlField('mpt_refid')
		->sql("
			SELECT mpt_refid, mpt_type
			  FROM webset.med_disdef_provider_types
			 WHERE vndrefid = VNDREFID
			   AND mpt_status_sw = 'A'
			 ORDER BY mpt_type
		")
		->emptyOption(true);

	$edit->addControl(FFMultiSelect::factory('Lumen User'))
		->sqlField('umrefid')
		->name('umrefid')
		->maxRecords(1)
		->setSearchList(getUsers());

	$edit->addControl('First Name', 'text')
		->sqlField('mp_fname')
		->name('mp_fname');

	$edit->addControl('Last Name', 'text')
		->sqlField('mp_lname')
		->name('mp_lname');

	$edit->addControl(FFSwitchAI::factory('Status'))
		->sqlField('mp_status_sw')
		->value('A')
		->req(true);

	$edit->addUpdateInformation();
	$edit->addControl('vndrefid', 'hidden')
		->sqlField('vndrefid')
		->value(SystemCore::$VndRefID);

	$edit->cancelURL = CoreUtils::getURL('./providers_list.php');
	$edit->finishURL = CoreUtils::getURL('./providers_list.php');

	$edit->firstCellWidth = '25%';

	$edit->printEdit();

	function getUsers() {
		return ListClassContent::factory('Users of Lumenation System')
			->addSearchField(
				FFInput::factory()
					->caption('User Name')
					->sqlField('um_name')
			)
			->addSearchField(
				FFInput::factory()
					->caption('Login Name')
					->sqlField('umuid')
			)
			->addSearchField(
				FFSelect::factory('State Name')
					->sqlField('umsate')
					->sql("
						SELECT state, statename
						  FROM public.statemst
						 ORDER BY statename
					")
			)
			->addColumn('Login', '10%')
			->addColumn('User Name', '30%')
			->addColumn('Title', '20%')
			->addColumn('State', '5%')
			->addColumn('Phone', '15%')
			->addColumn('Email', '20%')
			->setSQL("
				SELECT umrefid,
					   umuid,
					   um_name,
					   umtitle,
					   umsate,
					   umphone,
					   umemail,
					   vndrefid
				  FROM (SELECT umrefid,
					           umuid,
							   CASE
									WHEN umlastname IS NULL OR umfirstname IS NULL OR umlastname = '' OR umfirstname = ''
									THEN umuid
									ELSE umlastname || ', ' || umfirstname
							   END AS um_name,
							   umtitle,
							   umsate,
							   umphone,
							   umemail,
							   umlastname,
							   umfirstname,
							   vndrefid
						  FROM public.sys_usermst

						) AS UM
				 WHERE vndrefid = VNDREFID
					   ADD_SEARCH
				 ORDER BY um_name
			");
	}
?>
<script>
	FFMultiSelect.get('umrefid').addEventListener(
		ObjectEvent.SELECT,
		function () {
			var userInfo = FFMultiSelect.get('umrefid').getSelectedItems();
			var mp_lname = $('#mp_lname');
			var mp_fname = $('#mp_fname');
			if (mp_lname.val() == '' && mp_fname.val() == '') {
				mp_lname.val(userInfo[0][7]);
				mp_fname.val(userInfo[0][8]);
			}
		}
	)
</script>