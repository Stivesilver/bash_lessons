<?php

	Security::init();

	$as_refid = io::geti('as_refid');

	$list = new ListClass('Measurement');

	$list->title = 'Measurement Items';

	$list->SQL = "
		SELECT measure.m_refid,
			   desc_measure,
			   type_measure,
			   plpgsql_recs_to_str ('
					SELECT i.ind_symbol AS column
					  FROM webset.std_bgb_indicator i
						   INNER JOIN webset.std_bgb_measurement_indicator mi ON mi.ind_refid = i.ind_refid
					 WHERE mi.m_refid = ' || measure.m_refid, ', ') AS ind_symbol
		  FROM webset.std_bgb_measurement AS measure
		 WHERE measure.as_refid = $as_refid
		   AND EXISTS (
				SELECT 1
				  FROM webset.std_bgb_measurement_indicator AS mi
				 WHERE mi.m_refid = measure.m_refid
			   )
		   AND measure.vndrefid = VNDREFID
		 ORDER BY measure.m_refid
	";

	$list->addColumn('Description')
		->sqlField('desc_measure');

	$list->addColumn('Type')
		->sqlField('type_measure');

	$list->addColumn('Indicators')
		->sqlField('ind_symbol');

	$list->addURL = CoreUtils::getURL('./bgb_measurement_edit.php', array('as_refid' => $as_refid));
	$list->editURL = CoreUtils::getURL('./bgb_measurement_edit.php', array('as_refid' => $as_refid));

	$list->deleteTableName = 'webset.std_bgb_measurement';
	$list->deleteKeyField = 'm_refid';

	$list->addButton('Copy', 'createNewMeasurement()')
		->width('90px');

	$list->printList();

	io::jsVar('as_refid', $as_refid);
?>
<script type="text/javascript">
	function createNewMeasurement() {
		res = ListClass.get().getSelectedValues().values;
		var i = 0;
		while(i < res.length) {
			i++;
		}
		switch(i) {
			case 0:
				api.alert('You need select Measurement for copy');
				break;
			case 1:
				var wnd = api.window.open(
					'Copy a Measurement',
					api.url('./bgb_measurement_copy.php', {'res' : res})
				);
				wnd.resize(740, 220);
				wnd.center();
				wnd.show();
				wnd.addEventListener(
					ObjectEvent.COMPLETE,
					function(e) {
						ListClass.get().reload();
					}
				)
				break;
			default:
				api.alert('You can select only one Measurement for copy!');
				break;
		}
		
	}
</script>