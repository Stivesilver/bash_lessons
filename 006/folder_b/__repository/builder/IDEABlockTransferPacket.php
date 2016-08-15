<?php
/**
 * IDEABlockTransferPacket.php
 *
 * Class for creation blocks in Transfer Packet builder(State TX).
 * @author Ganchar Danila <dganchar@lumentouch.com>
 * Created 12-02-2014
 */
class IDEABlockTransferPacket extends IDEABlockARD {

	protected $printParent           = false;
	protected $printPurposeOfMeeting = false;
	protected $typeCommitteMembers   = 'P';

	/**
	 * Generate block Transfer Packet
	 */
	public function renderTransferPacket() {
		$data      = $this->std->getTransferPacket();
		$dates     = $this->std->getRad();
		$attrDistr = $this->std->getAttDistrict();
		$container = new RCLayout();
		$layout    = new RCLayout();
		$label     = new RCStyle('[padding-top: 5px; with: 120px;]');
		$valLabel  = new RCStyle('italic [border-bottom: 1px solid black; padding-top: 5px; width: 400px;]');
		$dateStyle = new RCStyle('[width: 50px;]');
		$dateVal   = new RCStyle('italic [border-bottom: 1px solid black; margin-right: 50px; width: 150px;]');

		$this->addYN($container, $data['field0']);
		$container->addText(
			'I waive the required 5 school day waiting period between the notice of the ARD/ISP committee meeting and the meeting itself.'
			, '.padtop5'
		);

		$layout->addObject($container->newLine(), '[border: 1px solid black;]')
			->newLine()
			->addText('I. Eligibility Verification:', $this->titleStyle('width: 120px;'))
			->newLine()
			->addText('The ARD/ISP committee met to recommend that the above-named student receive special education services on a temporary basis. The parent has stated that this student received special education services in: <i>' . $attrDistr['vnd_att'] . '</i>')
			->newLine('.martop10')
			->addText('<b>A. The student\'s eligibility in former district was verified: </b>')
			->newLine('.martop10')
			->addObject($this->addCheck($data['field1_yn'])  , '.width20')
			->addText('by telephone. Staff member contacted:', $label)
			->addText((string)$data['field1_oth']            , $valLabel)
			->newLine('.martop10')
			->addObject($this->addCheck($data['field2_yn']), '.width20')
			->addText('in writing. Documents received:'    , $label)
			->addText((string)$data['field2_oth']          , $valLabel)
			->newLine('.martop10')
			->addText('Information from the parent and the former school district indicates that this student has met the eligibility criteria for special education and related services in the area of: <i>' . $data['field3'] . '</i>')
			->newLine('.martop10')
			->addText('<b>B. Verification of Full and Individual Evaluation (FIE) and Individual Education Plan (ISP):</b>')
			->newLine('.martop10')
			->addText('Date of FIE:'                               , $dateStyle)
			->addText(date('d-m-Y', strtotime($dates['stdevaldt'])), $dateVal)
			->addText('Date of ISP:'                               , $dateStyle)
			->addText(date('d-m-Y', strtotime($dates['stdevaldt'])), $dateVal)
			->newLine('.martop10')
			->addText('C. Description of services (instructional and related) provided in former school, as described by that district:')
			->newLine()
			->addText('<i>' . $data['field4'] . '</i>')
			->newLine('.martop10')
			->addObject($this->addCheck($data['field5']), '.width20')
			->addText(
				'Students identified as speech disabled will have a review of records and/or will be evaluated to establish eligibility.'
				, '.padtop5'
			)
			->newLine()
			->addObject($this->addCheck($data['field6']), '.width20')
			->addText(
				'Students identified as qualifying for special education and related services will have a review of records and/or will be reevaluated to establish eligibility.'
				, '.padtop5'
			)
			->newLine()
			->addText('II. Development of the Individual Educational Plan (ISP):', $this->titleStyle())
			->newLine()
			->addObject($this->addCheck($data['field7']), '.width20')
			->addText(
				'A current ISP from an in state school district is available, considered appropriate and remains in effect (See attached and proceed to the ' . PHP_EOL . 'signature page).'
				, '.padtop5'
			)
			->newLine()
			->addObject($this->addCheck($data['field8']), '.width20')
			->addText(
				'An interim placement has been determined. The IEP will be finalized within 30 school days (See attached goals/objectives).'
				, '.padtop5'
			);

		$this->rcDoc->addObject($layout);
	}

} 
