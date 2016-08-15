<?php

    $message = '
		<b>End-of-Course (EOC) Notes</b><br/>
		<b>DESE Required EOC Assessments:</b> Algebra I (Algebra II if Algebra I was taken prior to grade 9), Biology, English II, and Government. All students with disabilities except those eligible for MAP-A must take the required EOC Assessments. School personnel make the decision regarding when a student will take the required assessments. Students generally take the assessment when they have completed the course level expectations, but students with disabilities must take the assessments prior to graduation or exiting secondary school due to age limits, whether they have completed course level expectations or not.<br/>

		<b>LEA Optional EOC Assessments:</b> Geometry, English I, American History, Physical Science and Algebra II (unless Algebra I was taken prior to grade 9) are optional assessments. For students with disabilities who do not qualify for MAP-A, the IEP team will decide whether the students will participate in or will be considered exempt from the additional EOC Assessments.<br/><br/> 

		<b>NAEP Notes</b><br/>

		NAEP is a national test administered to a statewide representative sample of students for national comparison. Thus, the NAEP sample includes students with disabilities and every effort must be made to ensure that selected students have an opportunity to participate in NAEP. The way in which students with disabilities are assessed on the NAEP should mirror as closely as possible the way they are tested on the state assessment. The NAEP accommodations, as listed, are of a general nature and may vary somewhat by year and content area being assessed. A current, more specific list of allowable NAEP accommodations will be included in the NAEP materials sent to schools selected for the NAEP sample. For additional information regarding NAEP, refer to: <a target=_blank href="http://dese.mo.gov/college-career-readiness/assessment/naep">http://dese.mo.gov/college-career-readiness/assessment/naep</a>. 
	';

    print UIMessage::factory($message, UIMessage::NOTE)
            ->textAlign('left')
            ->toHTML();
?>
