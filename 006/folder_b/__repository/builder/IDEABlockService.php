<?php
/**
 * IDEABlockService.php
 *
 * Class for creation blocks in Service Plan builder(State TX).
 * @author Ganchar Danila <dganchar@lumentouch.com>
 * Created 13-02-2014
 */
class IDEABlockService extends IDEABlockARD {

	protected $printParent           = false;
	protected $printPurposeOfMeeting = false;
	protected $typeCommitteMembers   = 'A';

	/**
	 * Redefine because block use unique design
	 */
	public function renderPresentCompetencies() {
		$xmlData = IDEADef::getConstructionTemplate(111);
		$values  = $this->std->getConstruction(111, true);
		$doc     = IDEADocument::factory($xmlData)
			->mergeValues(base64_decode($values['values']));

		$this->rcDoc->addObject($doc->getLayout());
	}

} 
