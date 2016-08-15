<?php

    $message = '
				<b>*NOTES:</b><br>

            <b>1.) DESE Required EOC Assessments:</b> Algebra I, Biology, English II, Government, and Algebra II (if Algebra I was taken prior to grade 9 beginning with
			students graduating in 2016). All students with disabilities except those eligible for MAP-A must take the required EOC Assessments. School personnel make
			the decision regarding when a student will take the required assessments. Students generally take the assessment when they have completed the course level expectations, but students with disabilities must take the assessments prior to graduation or exiting secondary school due to age limits, whether they have
			completed course level expectations or not.
			<br><br>

            <b>2.) LEA Optional EOC Assessments:</b> Geometry, English I, American History, and Algebra II (unless Algebra I was taken prior to grade 9) are optional assessments beginning with the students graduating 2016. For students with disabilities who do not qualify for MAP-A, the IEP team will decide whether the students will participate in or will be considered exempt from the additional EOC Assessments. <br><br>

            <b>3.) NAEP (grades 4, 8, and 12)</b> is a national test administered to a statewide representative sample of students for national comparison. Thus, the NAEP
			sample includes students with disabilities and every effort must be made to ensure that selected students have an opportunity to participate in NAEP. The way
			in which students with disabilities are assessed on the NAEP should mirror as closely as possible the way they are tested on the state assessment: take NAEP 1)
			without accommodations; 2) with NAEP allowable accommodations; or 3) if assessed by the MAP-Alternate, may be excluded from taking NAEP. IEP teams
			are reminded that NAEP is not a high stakes test for students. NAEP offers most of the universal tools, designated supports and accommodations that Missouri allows on state assessments; however, a few differences exist. The NAEP accommodations, as listed, are of a general nature and may vary somewhat by year and content area being assessed. A current, more specific list of allowable NAEP accommodations will be included in the NAEP materials sent to schools selected for the NAEP sample. For additional information regarding NAEP, refer to : <a href="http://dese.mo.gov/college-career-readiness/assessment/naep">http://dese.mo.gov/college-career-readiness/assessment/naep</a>
			<br><br>

            <b>4) ACCESS FOR ELLS (Grades K-12)</b> Missouri uses ACCESS for English Language Learners (ELL) as its annual English Language Proficiency assessment.  Students who are in monitored status for ELL do not take the assessment. For additional information, refer to: <a href="http://dese.mo.gov/college-career-readiness/assessment/access-ells">http://dese.mo.gov/college-career-readiness/assessment/access-ells</a> <br><br>
             ';

    print UIMessage::factory($message, UIMessage::NOTE)
            ->textAlign('left')
            ->toHTML();
?>
