<?php
	Security::init();

	$ds = DataStorage::factory(io::post('dskey', true));
	$urls = $ds->get('ids');
	$template = io::post('template', false, '', true);
	$items = new SimpleXMLElement($template);

	foreach ($urls as $url) {
		if ($url != '') {
			$desktop = IDEAZdi::factory(SystemCore::$physicalRoot . $url);
			$icons = $desktop->getIcons();

			foreach ($items as $item) {
				foreach ($icons as $key => $icon) {
					switch ($item['action']) {
						case 'rename':
							if ($item['casesensitive'] == 'yes') {
								$icon['title'] = str_replace($item['contains'], $item['to'], $icon['title']);
							} else {
								$icon['title'] = str_ireplace($item['contains'], $item['to'], $icon['title']);
							}
							break;

						case 'set_title':
							if ($item['casesensitive'] == 'yes') {
								if (str_replace($item['contains'], '', $icon['title']) != $icon['title']) {
									$icon['title'] = (string)$item['to'];
								}
							} else {
								if (str_ireplace($item['contains'], '', $icon['title']) != $icon['title']) {
									$icon['title'] = (string)$item['to'];
								}
							}
							break;

						case 'set_url':
							if ($item['casesensitive'] == 'yes') {
								if (str_replace($item['contains'], '', $icon['title']) != $icon['title']) {
									$icon['url'] = (string)$item['to'];
								}
							} else {
								if (str_ireplace($item['contains'], '', $icon['title']) != $icon['title']) {
									$icon['url'] = (string)$item['to'];
								}
							}
							break;

						case 'remove':
							if ($item['casesensitive'] == 'yes') {
								if (str_replace($item['contains'], '', $icon['title']) != $icon['title']) {
									unset($icons[$key]);
								}
							} else {
								if (str_ireplace($item['contains'], '', $icon['title']) != $icon['title']) {
									unset($icons[$key]);
								}
							}
							break;
					}
					$icons[$key] = $icon;
				}
			}

			$desktop->setIcons($icons);
			$desktop->updateFile();
		}
	}
?>
<script type='text/javascript'>
	api.window.dispatchEvent('desktops_updated');
	api.window.destroy();
</script>