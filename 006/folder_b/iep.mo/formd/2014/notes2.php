<?php

	$message = '
		Note: All accommodations selected must match those shown on Form F<br/><br/>
		<b>UNIVERSAL TOOLS:</b>
		Universal tools are access features of the assessment that are either provided as digitally-delivered components of the test administration system or separate from it. Universal tools are available to ALL students based on student preference and selection. For a complete description of universal tools, refer to <a href="http://dese.mo.gov/college-career-readiness/assessment">http://dese.mo.gov/college-career-readiness/assessment.</a>
		<br/><br/>
		<table border="1" cellpadding="5" style="border-collapse: collapse; border: 1px solid black; font-weight: bold;">
			<tr>
				<td>Breaks (Pause)</td>
				<td>Calculator (on allowed items)</td>
				<td>English dictionary</td>
				<td>Expandable passages</td>
				<td>Glossary (electronic)</td>
			</tr>
			<tr>
				<td>Highlighter</td>
				<td>Keyboard navigation</td>
				<td>Mark for review</td>
				<td>Notepad or scratch paper</td>
				<td>Protractor / Ruler</td>
			</tr>
			<tr>
				<td>Spell check</td>
				<td>Strikethrough</td>
				<td>Thesaurus</td>
				<td>Writing tools</td>
				<td>Zoom</td>
			</tr>
		</table>
		<br/><br/>
		<b>DESIGNATED SUPPORTS / ACCOMMODATIONS:</b>
		Designated supports are features that are available for use by ANY student when deemed appropriate by a team of educators. These features do not impact student scores for accountability purposes. For students with disabilities, the IEP team must determine if a designated support will be required during the administration of the assessment to the student.

		Accommodations are changes in procedures or materials that increase equitable access during the assessment. Accommodations generate valid assessment results for students who need them and allow these students to demonstrate what they know and can do. The IEP team must determine if an accommodation will be required during the administration of the assessment to the student.
		<br/>For a complete description of Grade-Level accommodations, refer to <a href="http://dese.mo.gov/sites/default/files/asmt-gl-accommodations-1415.pdf">http://dese.mo.gov/sites/default/files/asmt-gl-accommodations-1415.pdf</a>. For a complete description of End-of-Course accommodations, refer to <a href="http://dese.mo.gov/sites/default/files/asmt-eoc-accommodations-1415.pdf">http://dese.mo.gov/sites/default/files/asmt-eoc-accommodations-1415.pdf</a>.
        <br/><br/>
        <b>MODIFICATIONS:</b>
		Modifications are changes in procedures or materials that change the construct of the assessment affecting the validity of the scaled score for accountability purposes. <b>Modifications generate the Lowest Obtainable Scaled Score (LOSS)</b> for students who need them but do allow these students to demonstrate what they know and can do in a non-standardized way. For students with disabilities, the IEP team must determine if a modification will be required during the administration of the assessment to the student. For a complete description of modifications, refer to <a href="http://dese.mo.gov/college-career-readiness/assessment">http://dese.mo.gov/college-career-readiness/assessment</a>.
    ';
	print UIMessage::factory($message, UIMessage::NOTE)
		->textAlign('left')
		->toHTML();
?>
