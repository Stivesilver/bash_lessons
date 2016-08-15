<?php
	Security::init();

	$screening_id = io::get("screening_id");
	$field = io::get("field");

	$list = new ListClass();

	$list->title = 'Assessments';

	$list->showSearchFields = true;

	$list->SQL = "
		SELECT hsprefid,
		       hspdesc
		  FROM webset.es_scr_disdef_proc proc
		 WHERE vndrefid = VNDREFID
		   AND proc.screenid = " . $screening_id . "
		   AND xml_test IS NOT NULL ADD_SEARCH
		 ORDER BY hspdesc
	";

	$list->addColumn("Form Statement");

	$list->editURL = 'javascript:addStatement("AF_COL1")';

	$list->printList();

	print FFInput::factory()
		->name('field')
		->value($field)->hide()
		->toHTML();
?>
	<script type='text/javascript'>
	function addStatement(statement) {
		api.window.dispatchEvent('assessment_selected', {stm: statement, field:  $("#field").val()});
		api.window.destroy();
	}
	</script>
