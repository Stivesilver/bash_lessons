<?php
/**
 * IDEABlockARDAmnt.php
 *
 * Class for creation blocks in ARD/IEP Amendment builder(State TX).
 * @author Ganchar Danila <dganchar@lumentouch.com>
 * Created 07-02-2014
 */
class IDEABlockARDAmnt extends IDEABlockIEP {

	public function __construct() {
		parent::__construct();
		RCStyle::defineStyleClass('martop10', '[margin-top: 10px;]');
	}

	/**
	 * Generate block Cover Page
	 */
	public function renderCoverPage() {
		$bordBot = new RCStyle('[border-bottom: 1px solid black;]');
		$layout  = RCLayout::factory()
			->newLine()
			->addText(SystemCore::$VndName, 'bold center')
			->newLine()
			->addText('INDIVIDUAL EDUCATION PLAN (IEP) AMENDMENT/MODIFICATION', 'bold center')
			->newLine('.martop10')
			->addText('Student: ', new RCStyle('[width: 45px;]'))
			->addText(
				'<i>' . $this->std->get('stdname') . '</i>',
				new RCStyle('[width: 150px; border-bottom: 1px solid black;]')
			)
			->addText('ID#: ', new RCStyle('[width: 25px;]'))
			->addText('<i>' . (string)$this->std->get('stdschid') . '</i>', '[width: 100px; border-bottom: 1px solid black;]')
			->newLine('.martop10')
			->addText(
				'In making changes to a child\'s IEP after the annual IEP meeting for a school year, the parent of a child with a disability and the local educational agency may agree not to convene an IEP meeting for the purposes of making such changes, and instead may develop a written document to amend or modify the child\'s current IEP.'
				, '[background: #c0c0c0;]'
			)
			->newLine()
			->addText('I. Parental Consent:', $this->titleStyle())
			->newLine()
			->addText('I have received a copy of my procedural safeguards. At the annual ARD/IEP meeting an individual educational program was developed for my child. My signature below indicates that I have agreed not to convene an ARD/IEP meeting to make changes to my child\'s IEP.  My signature below further indicates that I agree that the changes in this document amend and/or modify my child\'s current IEP.')
			->newLine()
			->addText('Annual IEP Date:', 'bold [width: 300px; margin-left: 230px;]')
			->addText('<i>' . $this->std->get('stdiepmeetingdt') . '</i>', '[border-bottom: 1px solid black; width: 50px;]')
			->newLine('.martop10')
			->addObject(
				RCLayout::factory()
					->addText('', $bordBot)
					->newLine()
					->addText('Signature of Parent')
					->newLine('.martop10')
					->addText('', $bordBot)
					->newLine()
					->addText('Signature of School Personnel')
					->newLine('.martop10')
					->addText('', $bordBot)
					->newLine()
					->addText('Signature of Interpreter <i>(if applicable)</i>')
				, '[width: 300px; margin-right: 50px;]'
			)
			->addObject(
				RCLayout::factory()
					->addText('', $bordBot)
					->newLine()
					->addText('Date')
					->newLine('.martop10')
					->addText('', $bordBot)
					->newLine()
					->addText('Date')
					->newLine('.martop10')
					->addText('', $bordBot)
					->newLine()
					->addText('Date')
				, '[width: 250px;]'
			)
			->newLine()
			->addText('I. IEP Amendments/Modifications:', $this->titleStyle())
			->newLine()
			->addText('The student\'s IEP will be amended or modified to reflect the following changes <i>(include only the applicable attachments and/or any relevant reports):</i>');

		$selected = explode(
			',',
			IDEAStudentRegistry::readStdKey(
				$this->std->get('tsrefid')   ,
				'tx_amm'                     ,
				'modifications'              ,
				$this->std->get('stdiepyear')
			)
		);

		$labels    = $this->getAmendmentsLabels();
		$sumLabels = count($labels);
		for ($i = 0; $i < $sumLabels; $i++) {
			$this->addYN(
				#if first row add margin-top
				$layout->newLine($i == 0 ? '.martop10' : null),
				in_array($i + 1, $selected) ? 'Y' : 'N'
			);

			$layout->addText($labels[$i], '.padtop5');
		}

		$this->rcDoc->addObject($layout);
	}

	/**
	 * Return labels for Amendments checkboxes
	 *
	 * @return array
	 */
	private function getAmendmentsLabels() {
		return array(
			'Behavior <i>(Attachment A)</i>'                                              ,
			'Goals and Objectives <i>(Attachment B)</i>'                                  ,
			'Program Interventions, Accommodations or Modifications <i>(Attachment C)</i>',
			'Assistive Technology (AT) <i>(Attachment D)</i>'                             ,
			'Texas Assessment Program Decision Making <i>(Attachment E)</i>'              ,
			'Schedule Changes <i>(Attachment F)</i>'                                      ,
			'Related Services-including transportation <i>(Attachment G)</i>'             ,
			'ESY <i>(Attachment H)</i>'                                                   ,
			'Request Evaluation <i>(Attachment I)</i>'
		);
	}

}

?>
