<?php

	Security::init();

	require_once(SystemCore::$physicalRoot . "/uplinkos/classes/pdfClass.v2.0.php");
	require_once(SystemCore::$physicalRoot . "/uplinkos/classes/lib_sysparam.php");
	require_once(SystemCore::$physicalRoot . "/applications/webset/includes/xmlDocs.php");
	require_once(SystemCore::$physicalRoot . "/applications/webset/iep.mo/documentation/builder_core/IEPDates.php");
	require_once(SystemCore::$physicalRoot . "/applications/webset/iep.id/2008/builder/builder_core/IEPDates.php");
	require_once(SystemCore::$physicalRoot . "/applications/webset/iep.id/2008/builder/builder_core/IEPTemplates.php");

	$dskey = io::get('dskey');
	$id = io::geti('id');
	$ds = DataStorage::factory($dskey);
	$tsRefID = $ds->safeGet('tsRefID');
	$stdIEPYear = $ds->safeGet('stdIEPYear');
	$stitle = 'Outcomes_EC_Goals';
	$format = 'pdf';

	$a = ec_outcome_list($id);

	$content = '
		<doc>
			<line>
				<section align="center"><b>IEP Goals with Early Childhood Outcomes<br/>Students Ages 3-5</b></section>
			</line>' . "\n";
	$content .= '<line><section>Document date: <field>' . $a['docdate'] . '</field></section></line>' . "\n";

	$content .= stdDemoPrint($tsRefID);

	$content .= '
	<line><section></section></line>
	<line>
	<section><b>' . $a['otitle'] . '</b></section>
	</line>
	<line>
	<section></section></line>
	<line>
	<section width="5%"></section>
	<section width="95%"><b>1. Present Level of Performance:</b></section>
	</line>
	<line>
	<section></section></line>
	<line>
	<section width="5%"></section>
	<section width="95%"><b>a. Parent Input:</b> (Related to strengths and concerns in child\'s functioning in this outcome area)
	<br/><field>' . $a['txt01'] . '</field></section>
	</line>
	<line>
	<section width="5%"></section>
	<section width="95%"><b>b. State Approved Anchor Assessment and date completed:</b>
	<br/><field>' . $a['txt02'] . '</field></section>
	</line>
	<line><section width="5%"></section>
	<section width="95%"><b>c. Summarize the specific skills this child has that are age-appropriate, immediate foundational, and/or foundational skills based on assessments, observations and interviews: </b></section>
	</line>
	<line>
	<section width="10%"></section>
	<section width="90%"><b>Age Appropriate Skills (same age child): </b>
	<br/><field>' . $a['txt03'] . '</field>
	</section>
	</line>	
	<line>
	<section width="10%"></section>
	<section width="90%"><b>Immediate Foundational Skills (younger child): </b>
	<br/><field>' . $a['txt04'] . '</field>
	</section>
	</line>
	<line>
	<section width="10%"></section>
	<section width="90%"><b>Foundational Skills (much younger child): </b>
	<br/><field>' . $a['txt05'] . '</field>
	</section>
	</line>
	<line>
	<section width="5%"></section>
	<section width="95%"><b>d. Early Childhood Outcome Entry, Exit, and Progress Data Collection</b> (for State reporting purposes only) <field name="d11"></field></section>
	</line>
	<line><section></section></line>
	<table>
	<tr>
	<td width="20%">Outcome Area</td>
	<td width="16%" align="center">ECO Entry Rating</td>
	<td width="16%" align="center">* Annual ECO Rating and Date</td>
	<td width="16%" align="center">* Annual ECO Rating and Date</td>
	<td width="16%" align="center">ECO Exit Rating</td>
	<td width="16%" align="center">Progress at exit?
	<br/>Yes/No
	</td>
	</tr>
	<tr>
	<td width="20%">' . $a['area'] . '</td>
	<td width="16%"><field>' . $a['txt06'] . '</field></td>
	<td width="16%"><field>' . $a['txt07'] . '</field></td>
	<td width="16%"><field>' . $a['txt08'] . '</field></td>
	<td width="16%"><field>' . $a['txt09'] . '</field></td>
	<td width="16%"><field>' . $a['txt10'] . '</field></td>
	</tr>
	</table>
	<line>
	<section><i>*Enter updated ECO rating and date at the annual review.</i></section>
	</line>';

	$content .= '
	<line><section></section></line>
	<line><section><b>Check one of the following.</b></section></line>
	<line><section></section></line>';

	$d = ValidValuesGen('ID_EC_Outcomes_Progress');
	for ($n = 0; $n < count($d); $n++) {
		$content .= '
			<line>
			<section width="5%"></section>
			<section width="95%"><checkbox ' . ($d[$n]["id"] == $a['int02'] ? 'value="1"' : '' ) . '/> ' . $d[$n]["name"] . ' 
			</section>
			</line>';
	}

	//GOALS
	$goals = ec_outcome_goals($id);
	for ($i = 0; $i < count($goals); $i++) {
		if ($i == 0) {
			$content .= '<line><section></section></line>';
		} else {
			$content .= '<pagebreak/>';
		}
		$content .= '
			<line><section><b>e. Describe the child\'s baseline performance for the annual goal (s) and how participation in pre-academic and non-academic activities and routines is adversely affected.</b></section>
			</line>    
			<line><section></section></line>
			<line>
			<section><b>2. General Education Content Standard(s):</b> (List all of the Idaho eGuidelines standards that related to the Annual goal(s) of need) 
			<br/><field>' . $goals[$i]['txt01'] . '</field>
			</section>
			</line>
			<line><section></section></line>
			<line>
			<section><b>3. Annual goal:</b> (Specific measurable skill(s) and the condition that would indicate improved functioning in general education curriculum and setting related to this outcome.)
			<br/><field>' . $goals[$i]['txt02'] . '</field></section></line>
			<line><section></section></line>
			<line>
			<section><b>4. Evaluation Procedure: </b> (criteria, procedure, and schedule): 
			<br/><field>' . $goals[$i]['txt03'] . '</field>
			</section></line>    
			<line><section></section></line>
			<line>
			<section><b>5. Assistive Technology </b> (if needed): 
			<br/><field>' . $goals[$i]['txt04'] . '</field></section>
			</line>    
			<line>
			<section></section></line>
			<line>
			<section><b>6. How and When Progress Toward Goal Is Reported:</b>
			<br/><field>' . $goals[$i]['txt05'] . '</field></section>
			</line>
			';
		
		$xml_temlate = constr_def(155);
		$xml_values = constr_iep(155, $tsRefID, $stdIEPYear, $goals[$i]['refid']);

		$doc = new xmlDoc();
		$content .= $doc->xml_merge($xml_temlate, $xml_values);
				
		$content .= '<line><section></section></line>';

		$objectives = ec_outcome_objectives($goals[$i]['refid']);
		if (count($objectives) > 0) {
			$content .= '<line><section><b>7. Objectives</b> (required if student takes the IAA):</section></line>';

			for ($j = 0; $j < count($objectives); $j++) {
				$content .= '
				<line><section><b>Objective ' . $objectives[$j]['num'] . ':</b> <field>' . $objectives[$j]['txt01'] . '</field></section></line>
				<line><section><b>Expected Progress:</b> <field>' . $objectives[$j]['txt02'] . '</field></section></line>
				<line><section><b>Target Date:</b> <field>' . $objectives[$j]['targetdate'] . '</field></section></line>
				';
			}
			$content .= '<line><section></section></line>';
		}
		
		$content .= '
			<line>
			<section><b>*Note: If the student is not progressing according to target dates, parents will be informed.</b></section>			
			</line>';
	}
	$content .= '</doc>';

	//die($content);

	$doc = new xmlDoc();
	$doc->edit_mode = 'no';
	$doc->xml_data = $content;
	$file_name = $doc->getPdf();

	$nice_file_name = ucfirst(strtolower($ds->safeGet('stdlastname')));
	$nice_file_name .= '_';
	$nice_file_name .= ucfirst(strtolower($ds->safeGet('stdfirstname')));
	$nice_file_name .= '_';
	$nice_file_name .= str_replace(' ', '_', $stitle);
	$nice_file_name .= '_';
	$nice_file_name .= date('m_d_Y');
	$nice_file_name .= '.';
	$nice_file_name .= strtolower($format);

	rename($_SERVER['DOCUMENT_ROOT'] . $file_name, SystemCore::$tempPhysicalRoot . '/' . basename($nice_file_name));

	io::download(SystemCore::$tempVirtualRoot . '/' . basename($nice_file_name));
?>