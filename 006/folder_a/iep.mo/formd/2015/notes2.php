<?php

	Security::init();
	$message = '
		<h2>Note: Part 2A: Grade-Level Assessment Accommodations</h2>
		The Grade-Level Assessment features Universal Tools (available to ALL STUDENTS) and Accommodations (available only to students with an IEP/504 plan). Some Universal Tools and Accommodations are only for ELL students.<br/><br/>
		<b>Universal Tools for ALL Students</b>
		Universal tools are access features of the assessment that are either provided as digitally-delivered components of the test administration system or separate from it. Universal tools are available to ALL students based on student preference and selection. For detailed descriptions of each tool and any restrictions on the use of them, please see the Tools and Accommodations document for the current school year at <a target="_blank" href="http://dese.mo.gov/college-career-readiness/assessment/grade-level">http://dese.mo.gov/college-career-readiness/assessment/grade-level</a>.
		<br/><br/>
	<table border="1" cellpadding="5" style="border-collapse: collapse; border: 1px solid black;">
			<tr>
				<td>Break (Pause)</td>
				<td>Graphing Tool</td>
				<td>Mark For Review</td>
				<td>Scribe</td>
			</tr>
			<tr>
				<td>Calculator</td>
				<td>Highlighter</td>
				<td>Masking</td>
				<td>Separate Setting</td>
			</tr>
			<tr>
				<td>Color Contrast</td>
				<td>Keyboard Navigation</td>
				<td>Protractor</td>
				<td>Strikethrough (Cross Off)</td>
			</tr>
			<tr>
				<td>Color Overlay</td>
				<td>Line Guide</td>
				<td>Read Aloud (Not including ELA Reading Passages)</td>
				<td>Thesaurus</td>
			</tr>
			<tr>
				<td>English Dictionary</td>
				<td>Magnifier</td>
				<td>Ruler</td>
				<td>Writing Tools</td>
			</tr>
			<tr>
				<td>Grammar Handbook</td>
				<td>Magnification – Assistive Technology</td>
				<td>Scratch Paper (Sticky Notes)</td>
				<td></td>
			</tr>
			<tr>
				<td align="center" colspan="4"><b>Additional Universal Tools for ELL Students</b></td>
			</tr>
			<tr>
				<td>Bilingual Dictionary</td>
				<td>Read Aloud (Not including ELA Reading Passages) – Native Language</td>
				<td>Translation</td>
				<td>Translation – Paper/Pencil</td>
			</tr>
		</table>
		<br/>
		<b>Accommodations for Students with an IEP/504</b><br/>
		Accommodations are changes in procedures or materials that increase equitable access during the assessment.  Accommodations generate valid assessment results for students who need them and allow these students to demonstrate what they know and can do. The IEP team must determine if an accommodation will be required during the administration of the assessment to the student. For detailed descriptions of each accommodation and any restrictions on the use of them, please see the Tools and Accommodations document for the current school year at <a target="_blank" href="http://dese.mo.gov/college-career-readiness/assessment/grade-level">http://dese.mo.gov/college-career-readiness/assessment/grade-level</a>. Accommodations marked with ** modify and change the construct of the assessment affecting the validity of the score for accountability purposes. Use of these accommodations will result in the student receiving the <b>Lowest Obtainable Scaled Score (LOSS)</b>.<br/><br/>

		<h2>Note: Part 2B: End-of-Course (EOC) Assessment Accommodations</h2>
		The Grade-Level Assessment features Universal Tools (available to ALL STUDENTS) and Accommodations (available only to students with an IEP/504 plan). Some Universal Tools and Accommodations are only for ELL students.<br/><br/>
		<b>Universal Tools for ALL Students</b>
		Universal tools are access features of the assessment that are either provided as digitally-delivered components of the test administration system or separate from it. Universal tools are available to ALL students based on student preference and selection. For detailed descriptions of each tool and any restrictions on the use of them, please see the Tools and Accommodations document for the current school year at <a target="_blank" href="http://dese.mo.gov/college-career-readiness/assessment/end-course">http://dese.mo.gov/college-career-readiness/assessment/end-course</a>.
		<br/><br/>
	<table border="1" cellpadding="5" style="border-collapse: collapse; border: 1px solid black;">
			<tr>
				<td>Break (Pause)</td>
				<td>Graphing Tool</td>
				<td>Mark For Review</td>
				<td>Scribe</td>
			</tr>
			<tr>
				<td>Calculator</td>
				<td>Highlighter</td>
				<td>Masking</td>
				<td>Separate Setting</td>
			</tr>
			<tr>
				<td>Color Contrast</td>
				<td>Keyboard Navigation</td>
				<td>Protractor</td>
				<td>Strikethrough (Cross Off)</td>
			</tr>
			<tr>
				<td>Color Overlay</td>
				<td>Line Guide</td>
				<td>Read Aloud (Not including ELA Reading Passages)</td>
				<td>Thesaurus</td>
			</tr>
			<tr>
				<td>English Dictionary</td>
				<td>Magnifier</td>
				<td>Ruler</td>
				<td>Writing Tools</td>
			</tr>
			<tr>
				<td>Grammar Handbook</td>
				<td>Magnification – Assistive Technology</td>
				<td>Scratch Paper (Sticky Notes)</td>
				<td></td>
			</tr>
			<tr>
				<td align="center" colspan="4"><b>Additional Universal Tools for ELL Students</b></td>
			</tr>
			<tr>
				<td>Bilingual Dictionary</td>
				<td>Read Aloud (Not including ELA Reading Passages) – Native Language</td>
				<td>Translation</td>
				<td>Translation – Paper/Pencil</td>
			</tr>
		</table>
		<br/>
		<b>Accommodations for Students with an IEP/504</b><br/>
		Accommodations are changes in procedures or materials that increase equitable access during the assessment. Accommodations generate valid assessment results for students who need them and allow these students to demonstrate what they know and can do. The IEP team must determine if an accommodation will be required during the administration of the assessment to the student. For detailed descriptions of each accommodation and any restrictions on the use of them, please see the Tools and Accommodations document for the current school year at <a target="_blank" href="http://dese.mo.gov/college-career-readiness/assessment/end-course">http://dese.mo.gov/college-career-readiness/assessment/end-course</a>. Accommodations marked with ** modify and change the construct of the assessment affecting the validity of the score for accountability purposes. Use of these accommodations will result in the student receiving the <b>Lowest Obtainable Scaled Score (LOSS)</b>.
    ';
	print UIMessage::factory($message, UIMessage::NOTE)
		->textAlign('left')
		->toHTML();
?>
