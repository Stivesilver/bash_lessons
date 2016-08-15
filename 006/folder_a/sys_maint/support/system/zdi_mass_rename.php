<?php

	Security::init(NO_OUTPUT | MODE_WS, '2');

	$file_total = SystemCore::$physicalRoot . "/temp/zdi_total.txt";
	$file_details = SystemCore::$physicalRoot . "/temp/zdi_details.txt";
	
	if (file_exists($file_total)) {
		die(file_get_contents($file_total));
	}
	
	$urls = FileUtils::getFilesList(SystemCore::$physicalRoot . '/web_disk', TRUE, '*.zdi', TRUE);

	foreach ($urls as $key => $url) {
		if (substr(basename($url), 0, 1) == '~') {
			//unlink($urls[$key]);
			unset($urls[$key]);
		}
	}
	
//	io::trace($urls);
//	die();
	
	$desktop = IDEAZdi::factory($urls[0]);

	$template = '<template>
				<item contains="IDEA" action="rename" to="SPEDEX" casesensitive="yes"/>
				<item contains="WeBSET" action="rename" to="SPEDEX" casesensitive="yes"/>
				<item contains="WEBSET" action="rename" to="SPEDEX" casesensitive="yes"/>
				<item contains="WeBSIS" action="rename" to="AXSIS" casesensitive="yes"/>
				<item contains="WEBSIS" action="rename" to="AXSIS" casesensitive="yes"/>
				<item contains="SAM" action="rename" to="AXSIS" casesensitive="yes"/>
				<item contains="WeBSAS - " action="rename" to="" casesensitive="yes"/>
				<item contains="WeBSAS-" action="rename" to="" casesensitive="yes"/>
			</template>';
	$items = new SimpleXMLElement($template);

	foreach ($urls as $url) {
		if ($url != '') {
			$desktop = IDEAZdi::factory($url);
			$icons = $desktop->getIcons();

			foreach ($items as $item) {
				foreach ($icons as $key => $icon) {
					switch ($item['action']) {
						case 'rename':
							if ($item['casesensitive'] == 'yes') {
								$icons[$key]['title'] = str_replace($item['contains'], $item['to'], $icons[$key]['title']);
							} else {
								$icons[$key]['title'] = str_ireplace($item['contains'], $item['to'], $icons[$key]['title']);
							}
							break;
							
						case 'remove': 
							if ($item['casesensitive'] == 'yes') {																
								if (str_replace($item['contains'], '', $icon['title']) !=  $icon['title']) {
									unset($icons[$key]);
								}
							} else {
								if (str_ireplace($item['contains'], '', $icon['title']) !=  $icon['title']) {
									unset($icons[$key]);
								}
							}
							break;
					}
				}
			}

			$desktop->setIcons($icons);
			$desktop->updateFile();			
		}
	}
	
	file_put_contents($file_total, count($urls) . ' files updated.');
	file_put_contents($file_details, print_r($file_details, true));
	
	print file_get_contents($file_total);
?>
