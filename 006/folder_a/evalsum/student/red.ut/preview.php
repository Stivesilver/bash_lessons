<?
	Security::init();

	$dskey     = io::get('dskey');
	$RefID     = io::get('RefID');
	$ds = DataStorage::factory($dskey, true);
	$evalpoc_id = $ds->safeGet('evalproc_id');
	$tsRefID = $ds->safeGet('tsRefID');
	$typeBlock = 40;

	$edit = new EditClass('edit1', 0);

	$edit->title = 'Review of Existing Data - Preview RED';

	$doc = IDEADocumentType::factory($typeBlock);

	$edit->addGroup('General Information');

	$edit->addControl('RED Blocks', 'select_check')
		->name('blocks')
		->data($doc->getBlocksKeyedArray())
		->selectAll()
		->breakRow();

	$edit->addButton('Build RED')
		->name('btn_build')
		->css('width', '120px')
		->onClick('buildIEP()');

	$edit->saveAndAdd = false;

	io::jsVar('stdrefid', $tsRefID);
	io::jsVar('evalpoc_id', $evalpoc_id);
	io::jsVar('typeBlock', $typeBlock);

	$edit->printEdit();

?>
<script type="text/javascript">
	function buildIEP() {
		 api.ajax.process(
			UIProcessBoxType.REPORT,
			api.url('../builder/add_file.ajax.php'),
			{
				'blocks': $('#blocks').val(),
				'archive': 0,
				'tsRefID': stdrefid,
				'evalpoc_id': evalpoc_id,
				'typeBlock': typeBlock
			}
		);
    }
</script>
