<?
	Security::init(MODE_WS);

	$district_years = IDEADistrict::factory(1)
		->getSchoolYears();

	$table = UITable::factory()
		->border(1)
		->addRow()
		->addCell('REFID')
		->addCell('AREA')
		->addCell('VALUE ID (Optional)')
		->addCell('VALUE')
		->addCell('DISPLAY SEQUENCE');

	/**
	 * @var IDEADefValidValue $option
	 */
	foreach ($some as $option) {
		$table->addRow()
			->addCell($option->get(IDEADefValidValue::F_REFID))
			->addCell($option->get(IDEADefValidValue::F_AREA))
			->addCell($option->get(IDEADefValidValue::F_VALUE_ID))
			->addCell($option->get(IDEADefValidValue::F_VALUE))
			->addCell($option->get(IDEADefValidValue::F_SEQUENCE));
	}

	print $table->toHTML();

?>