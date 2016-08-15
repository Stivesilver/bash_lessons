<?php

	Security::init();

	$list = new listClass();

	$list->title = "FB Form Templates";

	$list->SQL = "
        SELECT stf.mfcrefid,
               mfcpdesc,
               stf.mfcdoctitle,
               CASE WHEN df.dfrefid IS NULL OR df.fb_content != stf.fb_content THEN 'Y' ELSE 'N' END AS enable_checkbox,
               dfrefid,
               to_char(df.lastupdate,'MM/DD/YYYY HH12:MI:SS am') AS ddate,
               to_char(stf.lastupdate,'MM/DD/YYYY HH12:MI:SS am') AS sdate
          FROM webset.statedef_forms AS stf
               INNER JOIN webset.def_formpurpose AS fprp ON fprp.mfcprefid  = stf.mfcprefid
               LEFT JOIN webset.disdef_forms AS df ON (stf.mfcrefid = df.mfcrefid AND df.vndrefid = " . SystemCore::$VndRefID . ")
         WHERE stf.screfid = " . VNDState::factory()->id . "
           AND stf.fb_type = 1
         ORDER BY mfcpdesc, mfcdoctitle
    ";

	$list->checkBoxColumn = true;

	$list->addSearchField('Form Title')->sqlField('mfcdoctitle');
	$list->addSearchField(FFSelect::factory('Form Purpose'))
		->sql("
			SELECT mfcprefid, mfcpdesc
			  FROM webset.def_formpurpose
	         ORDER BY mfcpdesc
		")
		->sqlField('fprp.mfcprefid');

	$list->addSearchField(FFSwitchYN::factory('Downloaded'))
		->sqlField("CASE WHEN df.dfrefid IS NULL THEN 'N' ELSE 'Y' END");

	$list->addColumn('Form Title')
		->sqlField('mfcpdesc')
		->type('group');

	$list->addColumn('Form Purpose')
		->sqlField('mfcdoctitle');

	$list->addColumn('Downloaded')
		->hint('The form is already downloaded. (The Download Date/Time)')
		->cssCallback('markDownloaded')
		->dataCallback('downloaded')
		->width('20%');

	$list->hideCheckBoxes = false;

	$list->addButton('Download', "copyEntries()")
		->width('80px');

	$list->printList();

	function downloaded($data) {
		if ($data["dfrefid"]) {
			return 'Yes (' . $data['ddate'] . ')';
		} else return 'No';
	}

	function markDownloaded($data) {
		if ($data["dfrefid"]) {
			return array(
				'background-color' => 'green'
			);
		} else {
			return array(
				'background-color' => 'red'
			);
		}
	}

?>

<script type="text/javascript">

	function copyEntries() {
		var refid = ListClass.get().getSelectedValues().values;
		if (refid.length == 0) {
			api.alert('Please select at least one record.');
			return;
		}
		api.ajax.process(
			UIProcessBoxType.DATA_UPDATE,
			api.url('./fb_copy_forms.php'),
			{RefID: refid.join(',')},
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
