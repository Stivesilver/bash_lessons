<?PHP

	Security::init();

	$res = io::geti('res', false);

	$data = array();

	for ($i = 1; $i < 21; $i++) {
		$data[$i] = $i;
	}

	$edit = new EditClass('edit1', $res);

	$edit->title = 'Create a New Measurement';

	$edit->addControl('How many copies of Measurement item do you wish to create?', 'select')
		->name('recs_num')
		->value('1')
		->data($data)
		->req(true);

	$edit->addButton('Cancel', 'procCancel();');
	$edit->addButton('Continue', 'procContinue();');

	$edit->firstCellWidth = '70%';

	$edit->printEdit();

	io::jsVar('res', $res);
?>
<script>

	function procCancel() {
		api.window.destroy();
	}

	function procContinue() {
		var recs_num = $('#recs_num').val();
		if (recs_num == '' || recs_num == 0) {
			api.alert('Please specify how many items you wish to create to continue.');
			return false;
		}
		api.ajax.process(
			UIProcessBoxType.PROCESSING,
			api.url('./bgb_measurement_copy_process.ajax.php'),
			{
				res : res,
				recs_num : recs_num
			}
		).addEventListener(
			WindowEvent.CLOSE,
			function () {
				api.window.dispatchEvent(ObjectEvent.COMPLETE);
				api.window.destroy();
			}
		);
	}
</script>