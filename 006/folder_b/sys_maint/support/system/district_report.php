<?php

	Security::init();

	$edit = new editClass("edit1", '');

	$edit->title = "District Export";

	$edit->addControl('Student Export', 'text')
		->name('student')
		->value('/home/evsc/exports/students.csv')
		->disabled(true)
		->width(350)
		->append(
			FFButton::factory('Generate')
				->onClick('getReports(1);')
				->width(80)
		);

	$edit->addControl('Parent Export', 'text')
		->name('parent')
		->value('/home/evsc/exports/parents.csv')
		->disabled(true)
		->width(350)
		->append(
			FFButton::factory('Generate')
				->onClick('getReports(2);')
				->width(80)
		);

	$edit->addControl('IEP File Export', 'text')
		->name('iepfile')
		->value('/home/evsc/exports/files')
		->disabled(true)
		->width(350)
		->append(
			FFButton::factory('Generate')
				->onClick('getReports(3);')
				->width(80)
		);

	$edit->addControl('Documentation File Export', 'text')
		->name('docfile')
		->value('/home/evsc/exports/files')
		->disabled(true)
		->width(350)
		->append(
			FFButton::factory('Generate')
				->onClick('getReports(4);')
				->width(80)
		);

	$edit->addControl('Clear Files Folder', 'text')
		->name('clear')
		->value('/home/evsc/exports/files')
		->disabled(true)
		->width(350)
		->append(
			FFButton::factory('Clear')
				->onClick('getReports(5);')
				->width(80)
		);

	$edit->printEdit();
?>

<script>
	function getReports(type) {
			api.ajax.post(
				api.url('./district_export.ajax.php'),
				{
					'type': type
				}
			);
		}
</script>
