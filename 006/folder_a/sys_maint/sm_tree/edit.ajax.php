<?php	
	Security::init();	
	$url = io::get('RefID');
	if (file_exists(SystemCore::$physicalRoot.$url)) {
		Header('Location: ' . CoreUtils::getURL($url));	
	} else {
		$message = UILayout::factory()
					  ->addHTML('', '30%')
					  ->addHTML(UIMessage::factory('Application is not yet ready. Coming soon.', UIMessage::NOTE)->toHTML(), '40% center')					  	  
					  ->addHTML('', '30% right')
					  ->toHTML();

		echo UITable::factory(UITableAttr::factory()->css('width', '100%')->css('height', '100%'))
		->addColumns(1)
		->addRow()		
		->addCell($message, UITableAttr::factory()->align('center')->valign('middle'))
		->toHTML();
	}
?>
