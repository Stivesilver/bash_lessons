<?php

	Security::init(NO_OUTPUT | MODE_WS, '2');
	
	$files_restored = 0;
	$file_total = SystemCore::$physicalRoot . "/temp/zdi_restore.txt";
	
	if (file_exists($file_total)) {
		die(file_get_contents($file_total));
	}
	
	$backup_urls = FileUtils::getFilesList(SystemCore::$physicalRoot . '/web_disk', TRUE, '~*.zdi', TRUE);

	foreach ($backup_urls as $url) {
		if ($url != '') {
			$backup_desktop = IDEAZdi::factory($url);
			$backup_icons = $backup_desktop->getIcons();
			
			if (!file_exists(IDEAZdi::getFileUrlFromBackup($url))) {print $url; continue;}
			
			$real_desktop = IDEAZdi::factory(IDEAZdi::getFileUrlFromBackup($url));
			$real_icons = $new_icons = $real_desktop->getIcons();

			foreach ($backup_icons as $key => $icon) {
				if (str_replace('WeBSAS', '', $icon['title']) == $icon['title']) {
					unset($backup_icons[$key]);
				} else {
					if (!in_array($icon, $new_icons)) {
						$new_icons[] = $icon;
					}
				}
			}
			if ($new_icons != $real_icons) {
				$real_desktop->setIcons($new_icons);
				$real_desktop->updateFile();
				$files_restored++;
			}
		}
	}
	file_put_contents($file_total, $files_restored . ' files restored.');
	print file_get_contents($file_total);
?>
