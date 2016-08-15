<?php

	Security::init();

	require_once(SystemCore::$physicalRoot . "/applications/webset/includes/xmlDocs.php");

	$refID = io::get('RefID', true);
	$table = io::get('table', true);
	$constr = io::get('constr', true);
	$tsRefId = io::get('tsrefid');

	//PROCESS XML DOCUMENT
	$doc = new xmlDoc();
	$doc->includeStyle = 'no';
	$doc->edit_mode = "yes";

	$bp = IDEABackup::factory($tsRefId, $table, $constr);
	$doc->xml_data = $doc->xml_merge($bp->getTamplate(), $bp->getValues($refID));
	$html_curr = $doc->getHtml();


	$edit = new EditClass('edit1', '');

	$edit->addGroup('General Information');
	$edit->addHTML($html_curr);

	if (substr(SystemCore::$userUID, 0, 8) == 'gsupport') {
		/**
		 * Obtain Diff info
		*/
		$doc = new xmlDoc();
		$doc->includeStyle = 'no';
		$doc->edit_mode = "no";
		$doc->xml_data = $doc->xml_merge($bp->getTamplate(), $bp->getValues($bp->getPreviousID($refID)));
		$html_prev = $doc->getHtml();
		$clean_curr = preg_replace('/<[^>]*>/', '', preg_replace('/<br\/>/i', "\n", preg_replace('/&nbsp;/', "", $html_curr)));
		$clean_prev = preg_replace('/<[^>]*>/', '', preg_replace('/<br\/>/i', "\n", preg_replace('/&nbsp;/', "", $html_prev)));
		$diffs = IDEADiff::factory()
			->setCurrentVersion($clean_curr)
			->setPreviousVersion($clean_prev)
			->getDiff();
		$edit->addGroup('Diff Information');
		$edit->addControl('Changes since previous saving', 'textarea')
			->value($diffs)
			->css('width', '100%')
			->css('height', '250px')
			->autoHeight(true);
	}

	//$edit->saveLocal = false;
	$edit->firstCellWidth = '0%';

	$edit->topButtons = true;

	$param = array('refid' => $refID, 'constr' => $constr, 'tsrefid' => $tsRefId);
	$edit->addButton('Revert', "revert( " . json_encode($param) . ")", '' , 'wizard2_16.png');

	$edit->printEdit();
?>

<script type="text/javascript">
	function revert(param) {
		api.window.dispatchEvent(ObjectEvent.COMPLETE, param);
		api.window.destroy();
	}
</script>
