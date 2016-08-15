<?php
    Security::init();

	if (SystemCore::$coreVersion == '1') {
		io::js('api.goto(' . json_encode(CoreUtils::getURL('/applications/webset/evalsum/std_xml/desk_old.php', $_GET)) . ');', TRUE);
	} else {
		print UIMessage::factory('Old Evaluation Screen can be accessed only in PC Environment', UIMessage::NOTE)
			->textAlign('left')
			->toHTML(); 
	}
?>
