<?php

    /**
     * Contains basic student Demo and Sp Ed data
     *
     * @copyright Lumen Touch, 2012
     */
    abstract class IDEAStudentCaption {

        /**
         * Initializes basic properties
         *
         * @param int $tsRefID
         * @return string
         */
        static public function get($tsRefID = 0) {
	        $stdid_header = '';
	        $cmname = '';
	        $iepyear = '';
	        $iepdates = '';

            $student = new IDEAStudent($tsRefID);
            $set_ini = IDEAFormat::getIniOptions();

            if (IDEACore::disParam(11) == 'Y' && $student->get('stdschid') != '') {
	            $stdid_header = 'ID: <i>' . $student->get('stdschid') . '</i>';
            }

	        $stdname = $student->get('stdname');

            if ($student->get('cmnamelf') != '') {
	            $cmname = 'Case Manager: <i>' . $student->get('cmnamelf') . '</i>';
            }

            if ($student->get('stdiepyearbgdt') != '' || $student->get('stdiepyearendt') != '') {
	            $iepyear = 'Current ' . $set_ini['iep_year_title'] . ': <i>' . $student->get('stdiepyearbgdt') . '-' . $student->get('stdiepyearendt') . '</i>';
            }

	        if ($student->get('stdiepmeetingdt') != '' || $student->get('stdcmpltdt') != '') {
		        $iepdates = 'IEP Meeting/Annual Review: <i>' . $student->get('stdiepmeetingdt') . '-' . $student->get('stdcmpltdt') . '</i>';
	        }

	        $replace_lbl = array('%D', '%S', '%C', '%Y', '%I');
	        $replace_val = array($stdid_header, $stdname, $cmname, $iepyear, $iepdates);
			$caption = $set_ini['iep_screen_caption'];
	        $caption = str_replace($replace_lbl, $replace_val, $caption);
	        $caption = trim($caption,'-, ');

            return $caption;
        }
    }
?>
