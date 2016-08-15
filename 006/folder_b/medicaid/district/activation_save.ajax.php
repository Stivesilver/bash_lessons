<?php

	Security::init();

	$activation = io::post('activation');
	SystemRegistry::factory(SystemRegistry::SR_ENTERPRISE)
		->updateKey('webset', 'medicaid', 'activated', $activation, SystemRegistry::SR_ENTERPRISE);

	if ($activation == 'Y') {
		MedicaidServiceProviders::factory()
			->createServiceProviders();
	}
	io::ajax('res', 1);
?>