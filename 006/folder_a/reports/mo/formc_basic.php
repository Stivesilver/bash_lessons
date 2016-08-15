<?php

	Security::init();
	$tabs = new UITabs('tabs');
	$tabs->indent(3);
	$tabs->addTab('Transition Services', './formc_basic_services.php');
	$tabs->addTab('Course of Study', './formc_basic_courses.php');
	$tabs->addTab('Graduation', './formc_basic_graduation.php');
	print $tabs->toHTML();
?>
