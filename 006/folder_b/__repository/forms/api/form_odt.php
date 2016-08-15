<?php
	Security::init();

	if (io::exists('xml')) {
		$xml = io::vpost('xml', DataValidator::factory('string')
			->setHTMLEntitiesPermit(true)
			->setHTMLPermit(true));
	} else {
		$fkey = io::get('fkey');
		$ds = DataStorage::factory($fkey);
		$name = $ds->get('name');
		$xml = $ds->get('xml');
	}

	io::jsVar('xml', $xml);

	print FFButton::factory('Downlodad')
		->css('margin', '10px 10px 10px 10px')
		->onClick('redirect();')
		->toHTML();
?>

<script>
	function redirect() {
		var url = api.url('./form_tab.php');
		api.ajax.process(UIProcessBoxType.REPORT,
			url,
			{
				'xml' : xml,
				'format' : 'odt'
			}
		);
	}
</script>
