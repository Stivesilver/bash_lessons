<?php

	Security::init();

	$dskey = io::get('dskey');

	FFIDEAActionButton::factory()
		->setDsKey($dskey)
		->reorder();
?>
