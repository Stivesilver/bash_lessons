<?php
	Security::init();

	$field = io::get("field");

	$list = new ListClass();

	$list->title = 'Eligibility Criteria';

	$list->showSearchFields = true;

	$list->SQL = "
		SELECT CASE WHEN elsdesc != '' THEN eldesc || ': ' || elsdesc ELSE eldesc END,
		       CASE WHEN elsdesc != '' THEN elcode || ' - ' || eldesc || ': ' || elsdesc ELSE elcode || ' - ' || eldesc END
		  FROM webset.es_statedef_eligibility AS t
		       LEFT JOIN webset.es_statedef_eligibility_sub AS s ON (s.elrefid = t.elrefid AND COALESCE(s.recdeactivationdt, now()) >= now())
		 WHERE screfid = " . VNDState::factory()->id . "
		   AND COALESCE(t.recdeactivationdt, now()) >= now()
		 ORDER BY seqnum, elcode, eldesc
    ";

	$list->addColumn("Disability");

	$list->addButton('Select', "addDisability()")
		->width('80px');

	$list->hideCheckBoxes = false;

	$list->printList();

	print FFInput::factory()->name('field')->value($field)->hide()->toHTML();

?>
<script type='text/javascript'>
	function addDisability() {
		var refids = ListClass.get().getSelectedValues().values;
		if (refids.length == 0) {
			api.alert('Please select at least one record.');
			return;
		}
		var arr = [];
		for (var i = 0; i < refids.length; i++) {
			arr[i] = refids[i].split(':');
		}

		var data = {};
		var str = '';

		for (var a = 0, l = arr.length; a < l; a++) {
			var cat = arr[a][0];
			if (data[cat] !== undefined) {
				if (arr[a][1] !== undefined) {
					str = str + ',' + arr[a][1];
					data[cat].push(arr[a][1]);
				}
			} else {
				if (str == '') {
					str = str + arr[a][0];
				} else {
					str = str + '; ' + arr[a][0];
				}
				if (arr[a][1] !== undefined) {
					data[cat] = [arr[a][1]];
					str = str + ':' + arr[a][1];
				}
			}
		}

		api.window.dispatchEvent('disability_selected', {dsb: str, field: $("#field").val()});
		api.window.destroy();
	}
</script>
