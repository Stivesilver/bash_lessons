<?php

    $message = '
     		<b>*NOTES:</b><br>

            <b>1.) Required EOC Assessments</b>: Algebra I, Biology, English I, English II, American History, and Government beginning with students graduating in 2016.   All students with disabilities except those eligible for MAP-A must take the required EOC Assessments.  School personnel make the decision regarding when a student will take the required assessments.  Students generally take the assessment when they have completed the course level expectations, but students with disabilities must take the assessments prior to graduation or exiting secondary school due to age limits, whether they have completed course level expectations or not.<br><br>

            <b>2.) Additional EOC Assessments</b>:  Geometry and Algebra II are optional assessments beginning with the students graduating 2016. For students with disabilities who do not qualify for MAP-A, the IEP team will decide whether the students will participate in or will be considered exempt from the additional EOC Assessments.<br><br>

            <b>3.) NAEP (grades 4, 8, and 12)</b> is a national test administered to a statewide representative sample of students for national comparison. Thus, the NAEP sample includes students with disabilities and every effort must be made to ensure that selected students have an opportunity to participate in NAEP. The way in which students with disabilities are assessed on the NAEP should mirror as closely as possible the way they are tested on the state assessment: take NAEP 1) without accommodations; 2) with NAEP allowable accommodations; or 3) if assessed by the MAP-Alternate, may be excluded from taking NAEP. IEP teams are reminded that NAEP is not a high stakes test for students.<br><br>

            <b>4) ACCESS FOR ELLS (Grades K-12)</b> Missouri uses ACCESS for English Language Learners (ELL) as its annual English Language Proficiency assessment.  Students who are in monitored status for ELL do not take the assessment.  More information is available at ' . UIAnchor::factory('http://www.dese.mo.gov/divimprove/assess/documents/asmt-wida-access-faq-2013.pdf', 'http://www.dese.mo.gov/divimprove/assess/documents/asmt-wida-access-faq-2013.pdf')->toHTML() . ' <br><br>
                
            <b>5) MAP-A</b> Please refer to the alternate assessment decision making resources including the guidance document, flowchart and/or checklist when making justification for participation in the alternate assessment, ' . UIAnchor::factory('http://dese.mo.gov/se/compliance/specedguidance.html', 'http://dese.mo.gov/se/compliance/specedguidance.html')->toHTML() . '
     ';

    print UIMessage::factory($message, UIMessage::NOTE)
            ->textAlign('left')
            ->toHTML();
?>