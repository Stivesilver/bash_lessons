<?php

	Security::init();

	$post = io::post('save');

	# ajax save
	if ($post == 1) {
		io::ajax('save', io::post('evaluator'));
		IDEAStudentRegistry::saveStdKey(
			io::post('tsRefID'),
			'tx_speech',
			'therapist',
			io::post('evaluator'),
			io::get('stdIEPYear')
		);
		# destroy window
		io::ajax('save', 2);
	} else {
		# show form

		$edit = new EditClass('edit1', 0);

		$edit->title = 'Professional Evaluator';

		$SQL = "
			SELECT validvalue, validvalue
	          FROM webset.glb_validvalues
	         WHERE valuename = 'TX_FIE_Titles'
	         ORDER BY sequence_number
	        ";

		$result   = db::execSQL($SQL)->assocAll();
		foreach ($result as $option) {
			$data[$option['validvalue']] = $option['validvalue'];
		}

		$edit->addGroup('General Information');

		$edit->addControl(
			FFSelect::factory('-Professional Evaluator')
				->data($data)
				->value(
					IDEAStudentRegistry::readStdKey(
						io::get('tsRefID'),
						'tx_speech',
						'therapist',
						io::get('stdIEPYear')
					)
				)
				->name('evaluator')
		);

		$edit->addButton(
			FFButton::factory('Save & Finish')
				->onClick('saveEvaluator()')
				->css('margin-left', '10px')
		);

		$edit->printEdit();

		io::jsVar('tsRefID',    io::get('tsRefID'));
		io::jsVar('stdIEPYear', io::get('stdIEPYear'));
	}
?>

<script type="text/javascript">
	function saveEvaluator() {
		api.ajax.post(
			'evaluator_edit.php',
			{
				'save'      : 1,
				'evaluator' : $('#evaluator').val(),
				'tsRefID'   : tsRefID,
				'stdIEPYear': stdIEPYear,
			},
			function(answer) {
				if (answer.save == 2) {
					api.window.destroy();
				}
			}
		);
	}
</script>