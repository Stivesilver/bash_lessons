<?php

	Security::init();

	$staterefid = io::get('staterefid');

	$edit = new EditClass('edit1', io::geti('RefID'));
	$edit->title = 'Add/Edit Form Template';

	$edit->setSourceTable('webset.statedef_forms', 'mfcrefid');

	$edit->addGroup('General Information');

	$edit->addControl(FFSelect::factory("Form Purpose"))
		->sql("
			SELECT mfcprefid, mfcpdesc
			  FROM webset.def_formpurpose
	         ORDER BY mfcpdesc
		")->sqlField('mfcprefid');

	$edit->addControl('Form Title', 'text')
		->width('400px')
		->req()
		->sqlField('mfcdoctitle');

	$edit->addControl('This District only (District Name)', 'text')
		->width('400px')
		->sqlField('onlythisip');

	$edit->addControl('state', 'hidden')
		->value($staterefid)
		->sqlField('screfid');

	$edit->addControl('type', 'hidden')
		->value(1)
		->sqlField('fb_type');

	$edit->addControl(FFFormBuilder::factory())
		->settings(FBIDEASettings::factory())
		->caption('Form Template')
		->sqlField('fb_content');

	$edit->addControl(FFMultiSelect::factory('Area')
		->maxRecords(1)
		->sqlField('xmlform_id')
		->setSearchList(ListClassContent::factory('Form Manager')
				->addColumn("Form Purpose", "1%", "GROUP", "", "", "")
			    ->addColumn("State", "", "text", "", "", "")
			    ->addColumn("Form Title", "", "text", "", "", "")
			    ->addColumn("Default Fields", "", "text", "", "", "")
			    ->addColumn("Form Size", "", "text", "", "", "")
			    ->addColumn("Status", "", "text", "", "", "")
			    ->addColumn("User", "", "text", "", "", "")
			    ->addColumn("Date", "", "text", "", "", "")
				->addSearchField("Form Title", "lower(form_name)  like '%' || lower(ADD_VALUE)|| '%'", "TEXT", "", "", "", "")
			    ->addSearchField("Form Body", "lower(encode(decode(form_xml, 'base64'),'escape'))  like '%' || lower(ADD_VALUE)|| '%'", "TEXT", "", "", "", "")
			    ->addSearchField("Form Purpose", "xml.form_purpose", "LIST", "", "", "", "")
			    ->addSearchField("District Only", "lower(onlythisvnd)  like '%' || lower(ADD_VALUE)|| '%'", "TEXT", "", "", "", "")
			    ->addSearchField('Status', '(CASE end_date<now() WHEN true THEN 2 ELSE 1 END)', 'LIST', '1', '', '')
				->setSQL("
					SELECT frefid,
	                       mfcpdesc,
	                       state,
	                       form_name,
	                       length(form_xml)/1000 || ' Kb',
	                       onlythisvnd,
	                       CASE WHEN file_defaults IS NOT NULL THEN 'File' END,
	                       CASE NOW() >= end_date WHEN TRUE THEN 'In-Active' ELSE 'Active' END as status,
	                       xml.lastuser,
	                       xml.lastupdate
	                FROM webset.statedef_forms_xml xml
	                    INNER JOIN webset.def_formpurpose ON  webset.def_formpurpose.mfcprefid  = xml.form_purpose
	                    INNER JOIN webset.glb_statemst ON  webset.glb_statemst.staterefid = xml.screfid
	                WHERE screfid = " . VNDState::factory()->id . "
	                ORDER BY status, mfcpdesc, form_name
				")
		));

	$edit->addUpdateInformation();

	$edit->saveAndEdit = true;

	$edit->printEdit();
?>
