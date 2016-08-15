<?php

/**
 * IDEABlockBRIEF.php
 *
 * Class for creation blocks in Brief/ARD builder(State TX).
 * @author Ganchar Danila <dganchar@lumentouch.com>
 * Created 07-02-2014
 */
class IDEABlockBRIEF extends IDEABlockARD {

	protected $printParent           = false;
	protected $printPurposeOfMeeting = true;
	protected $typeCommitteMembers   = 'B';

	/**
	 * Generate block IEP Updates.
	 */
	public function renderIEPUpdates() {
		$layout = RCLayout::factory()
			->newLine()
			->addText('V. IEP Updates (Address and include only the applicable attachments.):', $this->titleStyle());

		$updates    = IDEADef::getValidValues('TX_IEP_Updates');
		$allUpdates = count($updates);
		$values     = explode(
			',',
			IDEAStudentRegistry::readStdKey(
				$this->std->get('tsrefid')   ,
				'tx_ard'                     ,
				'iep_updates'                ,
				$this->std->get('stdiepyear')
			)
		);

		for ($i = 0; $i < $allUpdates; $i++) {
			$layout->newLine('.martop10')
				->addText('<b>' . $updates[$i]->get(IDEADefValidValue::F_VALUE_ID) . '</b>');

			$check = 'N';
			# if exist in array refid add checked
			if (in_array($updates[$i]->get(IDEADefValidValue::F_REFID), $values)) $check = 'Y';

			$this->addYN($layout->newLine('.martop10'), $check);
			$layout->addText($updates[$i]->get(IDEADefValidValue::F_VALUE), '.padtop5');
		}

		$this->rcDoc->addObject($layout);
	}

	/**
	 * Generate block Special Education Eligibility.
	 */
	public function renderSpedexEligibility() {
		$disabilityList = $this->std->disabilityList();
		$rowDisability  = ' <i>';
		foreach ($disabilityList as $item) {
			if ($item['dcrefid'] != '') $rowDisability .= $item['desc'];
		}
		$rowDisability .= '</i>';
		$layout         = RCLayout::factory()
			->newLine()
			->addText('IV. Special Education Eligibility:', $this->titleStyle('width: 200px;'))
			->newLine()
			->addText(
				'The student qualifies for special education services in the eligibility category(s):' . $rowDisability
			);

		$this->rcDoc->addObject($layout);
	}

} 
