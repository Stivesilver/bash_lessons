<?php
	Security::init(MODE_WS | NO_OUTPUT, 1);

    $url = CryptClass::factory()->decode(base64_decode(io::get('url')));
    $url .= '/apps/idea/sys_maint/support/system/check.client.php';
    
    print IDEAIntegrity::DownloadUrl($url, io::post('xml'), false);
?>