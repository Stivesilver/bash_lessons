<?php
	Security::init(MODE_WS | NO_OUTPUT, 1);

    $xml = CryptClass::factory()->decode(base64_decode(io::post('xml')));
    
    print IDEAIntegrity::clientProcess($xml);
?>