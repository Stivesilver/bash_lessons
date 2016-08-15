<?php
	Security::init();

	$screening_id = io::get("screening_id");
	$field = io::get("field");
	$area = io::get("area");

	$list = new ListClass();

	$list->title = $area . ' Form Statements';

	$list->showSearchFields = true;

	$list->SQL = "
        SELECT ssgirefid,
               ssgitext
          FROM webset.es_formdisselections AS stm
         WHERE (1=1) ADD_SEARCH
           AND stm.vndrefid = VNDREFID
           AND screening_id = " . $screening_id . "
           AND area = 'E'
         ORDER BY seq_ord, lower(ssgitext)
    ";

	$list->addSearchField('Statement', "LOWER(ssgitext)  like '%' || LOWER('ADD_VALUE') || '%'");

	$list->addColumn("Form Statement")->dataCallback('makeReadable');

	$list->editURL = 'javascript:addStatement("AF_REFID")';

	$list->printList();

	function makeReadable($data, $col) {
		return UILayout::factory()
				->addHTML($data[$col], '[white-space: pre;]')
				->toHTML();
	}

	print FFInput::factory()->name('field')->value($field)->hide()->toHTML();
?>
<script type='text/javascript'>
	
		function addStatement(id) {
			url = api.url('statements.ajax.php');
			api.ajax.post(
				url,
				{'id': id},
				function(answer) {				
					api.window.dispatchEvent('statetment_selected', {stm: answer.statement, field: $("#field").val()});
					api.window.destroy();				
				}
			);
		}
</script>
