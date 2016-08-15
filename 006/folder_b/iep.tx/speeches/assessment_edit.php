<?php

	Security::init();

	$post = io::post('save');

	# ajax save
	if ($post == 1) {
		io::ajax('save', io::post('evaluator'));
		IDEAStudentRegistry::saveStdKey(
			io::post('tsRefID'),
			'tx_speech',
			'language_assessment',
			io::post('assessment'),
			io::get('stdIEPYear')
		);
		# destroy window
		io::ajax('save', 2);
	} else {
		# show form
		echo FFTextArea::factory('-The following formal assessment was administered')
			->css(
				array(
					'margin-top'  => '50px',
					'margin-left' => '20px',
					'width'       => '300px'
				)
			)
			->value(
				IDEAStudentRegistry::readStdKey(
					io::get('tsRefID'),
					'tx_speech',
					'language_assessment',
					io::get('stdIEPYear')
				)
			)
			->name('assessment')
			->toHTML();

		echo FFButton::factory('save')
			->onClick('saveAssessment()')
			->css('margin-left', '10px')
			->toHTML();

		io::jsVar('tsRefID',    io::get('tsRefID'));
		io::jsVar('stdIEPYear', io::get('stdIEPYear'));
	}
?>

<script type="text/javascript">
	function saveAssessment() {
		api.ajax.post(
			'assessment_edit.php',
			{
				'save'      : 1,
				'assessment': $('#assessment').val(),
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