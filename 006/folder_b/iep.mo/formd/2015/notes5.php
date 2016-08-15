<?php

    $message = '
<table border="1" cellpadding="5" style="border-collapse: collapse; border: 1px solid black;">
<tr>
<td size="10" colspan="2">ACCESS for ELLs 2.0 was designed to incorporate Universal Design principles in order to provide greater accessibility for all ELLs. WIDA provides Accessibility Tools (available to all ELLs), Test Administration Procedures (available to all ELLs) and Accommodations (available only to those with an IEP/504). Note the accomodations listed for ACCESS are not the same for Alternate ACCESS.<br/>
For more information regarding tools, procedures, and accommodations, please read the following documents: http://dese.mo.gov/college-career-readiness/assessment/access-ells.<br/>
- ACCESS 2.0 Accessibility and Accommodations Matrix<br/>
- ACCESS 2.0 Accessibility and Accommodations Descriptions<br/>
- ACCESS 2.0 Accessibility and Accommodations Guidelines<br/>
- Considerations When Choosing Appropriate Accommodations for Alternate ACCESS: https://www.wida.us/get.aspx?id=574
</td>
</tr>
<tr>
<td align="center" size="12" colspan="2">Accessibility Tools for ALL ELL Students</td>
</tr>
<tr>
<td size="10" colspan="2">Accessibility tools are available to all ELLs taking ACCESS for ELLs 2.0.</td>
</tr>
<tr>
<td size="10" width="50%"><b>Online</b></td>
<td size="10" width="50%"><b>Paper-Based</b></td>
</tr>
<tr>
<td size="10" width="50%">
- Audio aids<br/>
- Highlight tool<br/>
- Line guide<br/>
- Screen magnifier<br/>
- Sticky Notes<br/>
- Color contrast<br/>
- Color overlay<br/>
- Keyboard shortcuts/equivalents<br/>
- Scratch/blank paper (including lined or graph paper)
</td>
<td size="10" width="50%">
- Audio aids<br/>
- Highlighters, colored pencils, or crayons<br/>
- Place marker or tracking device<br/>
- Low-vision aids or magnification devices<br/>
- Color overlay<br/>
- Equipment or technology that the student uses for other tests and school work<br/>
- Scratch/blank paper (including lined or graph paper)
</td>
</tr>
<tr>
<td align="center" size="12" colspan="2">Test Administration Procedures</td>
</tr>
<tr>
<td size="10" colspan="2">In addition to the accessibility tools, test administrators may employ a range of test administration procedures to provide flexibility to schools and districts in determining the conditions under which ACCESS for ELLs 2.0 can be administered most effectively.</td>
</tr>
</table>
<table border="1" cellpadding="5" style="border-collapse: collapse; border: 1px solid black;">
<tr>
<td size="10" width="33%"><b>Presentation</b></td>
<td size="10" width="34%"><b>Response/Flexible Timing/Scheduling</b></td>
<td size="10" width="33%"><b>Test Environment/Setting</b></td>
</tr>
<tr>
<td size="10" width="33%">
- Read test directions by test administrator<br/>
- Repeat test directions by test administrator<br/>
- Explain/clarify test directions in English by test administrator<br/>
- Clarify test directions in student\'s native language by test administrator (per availability and local policy)<br/>
- Provide verbal praise or tangible reinforcement to a student<br/>
- Verbally redirect student\'s attention to test, in English or in student\'s native language<br/>
- Allow student to take the paper-based test based on policy outlined by the state education agency
</td>
<td size="10" width="34%">
- Student reads test aloud to self (but must not disturb or interfere with other test takers)<br/>
- Test administrator monitors placement of responses onscreen or in test booklet<br/>
- Student provides hand written response to the online Writing test instead of a keyboarded response, based on the student\'s inexperience, unfamiliarity, or discomfort with keyboarding<br/>
- This is only applicable for the online Writing test for grades 4-12<br/>
- The student would view the writing prompt on the computer screen and handwrite his or her response in a paper test booklet<br/>
- Frequent or additional supervised breaks<br/>
- Test administered in short segments (i.e., administer brief section of each test at a time)
</td>
<td size="10" width="33%">
Test administered:<br/>
- By school personnel familiar to student<br/>
- By school personnel other than student\'s teacher, including special educator<br/>
- In a small group<br/>
- In a separate room<br/>
- With preferential or adaptive seating<br/>
- In study carrel<br/>
- In a space with special lighting<br/>
- In a space with special acoustics<br/>
- With adaptive or specialized furniture or equipment<br/>
- Using tools to minimize distractions or maintain focus (e.g., stress ball); for paper-based test administration only, use noise-reducing headphones or instrumental music played through an individual student\'s headphones or ear buds
</td>
</tr>
</table>
     ';
    print UIMessage::factory($message, UIMessage::NOTE)
            ->textAlign('left')
            ->toHTML();
?>
