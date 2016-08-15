<?php
	function postSave($RefID) {
		MedicaidProviderTypes::factory()
			->createServicesByProviderType($RefID);
	}
?>