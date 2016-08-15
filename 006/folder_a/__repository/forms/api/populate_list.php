<?php

	Security::init();

	require_once(SystemCore::$physicalRoot . "/applications/webset/includes/xmlDocs.php");

	$refID = io::get('RefID', true);

	$dskey = io::get('dskey');
	$ds = DataStorage::factory($dskey);
	$tsRefId = $ds->safeGet('tsRefID');
	$ofkey = io::get('ofkey');

	$formInfo = db::execSQL("
		SELECT frefid, stdrefid
		  FROM webset.std_forms_xml std
		 WHERE sfrefid = $refID
	")->assoc();

	$form_state = IDEAFormTemplateXML::factory($formInfo['frefid']);
	$values = IDEAStudentFormXML::factory($refID)->getValues();

	$fkey = IDEAForm::factory()
		->setTitle($form_state->getTitle())
		->setTemplate($form_state->getTemplate())
		->setValues($values)
		->setUrlCancel('')
		->setUrlSave('')
		->setUrlFinish('')
		->setParameter('dskey', $dskey)
		->setParameter('state_id', $formInfo['frefid'])
		->setParameter('std_id', $formInfo['stdrefid'])
		->getFormDSKey();

	$fds = DataStorage::factory($fkey);
	$form = IDEAForm::factory($fkey);
	$dsKey = $form->getParameter('dskey');
	$std_id = $form->getParameter('std_id');
	$state_id = $form->getParameter('state_id');
	$ds = DataStorage::factory($dsKey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');

	//PROCESS XML DOCUMENT
	$doc = new xmlDoc();
	$doc->edit_mode = 'yes';
	$doc->edit_prefix = 'form_';
	$doc->border_color = 'silver';
	$doc->includeStyle = 'no';

	$title = $fds->get('title');
	$template = $fds->get('template');
	$template_pdf = $fds->get('template_pdf');
	$values = $fds->get('values');
	$lastuser = $fds->get('lastuser');
	$lastupdate = $fds->get('lastupdate');
	$doc->xml_data = $doc->xml_merge($template, $values);

	$doc->edit_mode = "no";

	$edit = new EditClass('edit1', $fkey);

	$edit->title = $title;

	$edit->addGroup('General Information');
	$edit->addHTML($doc->getHtml());

	$edit->addGroup('Update Information', true);
	$edit->addControl("Last User", "protected")->value($_SESSION["s_userUID"])->name('lastuser');
	$edit->addControl("Last Update", "protected")->value(date("m-d-Y H:i:s"))->name('lastupdate');

	//$edit->saveLocal = false;
	$edit->firstCellWidth = '0%';

	$edit->topButtons = true;
	$param = array('ofkey' => $ofkey, 'fkey' => $fkey);
	$edit->addButton('Populate', "populate( " . json_encode($param) . ")");

	$edit->printEdit();
?>

<script type="text/javascript">
	function populate(param) {
		api.ajax.process(
			UIProcessBoxType.DATA_UPDATE,
			api.url('./populate_apply.ajax.php'),
			{
				'param': JSON.stringify(param)
			},
			true
		).addEventListener(
			ObjectEvent.COMPLETE,
			function (e) {
				api.window.dispatchEvent(ObjectEvent.COMPLETE);
				api.window.destroy();
			}
		)
	}
</script>
