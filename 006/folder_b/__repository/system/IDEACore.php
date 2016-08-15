<?php
	/**
	* IDEA Core Class
	* This class provides basic IDEA methods
	*
	* @final
	* @copyright Lumen Touch, 2012
	*/
	final class IDEACore {

		/**
		 * Convert array/string to none UTF8
		 *
		 * @param mixed $input
		 * @return mixed
		 */
		public static function clearUTF8($input) {
			$input_s = serialize($input);
			if (mb_detect_encoding($input_s) != 'UTF-8') {
				return $input;
			}
			$convertables = array(
				'\u2010'	=> '-',
				'\u2011'	=> '-',
				'\u2012'	=> '-',
				'\u2013'	=> '-',
				'\u2014'	=> '-',
				'\u2015'	=> '-',
				'\u2018'	=> "'",
				'\u2019'	=> "'",
				'\u201A'	=> ',',
				'\u201B'	=> "'",
				'\u201C'	=> '"',
				'\u201D'	=> '"',
				'\u201E'	=> '"',
				'\u201F'	=> '"',
				'\u2024'	=> '.',
				'\u2025'	=> '..',
				'\u2026'	=> '...',
				'\u2032'	=> "'",
				'\u2033'	=> '"',
				'\u2035'	=> "'",
				'\u2036'	=> '"',

				'\u00C0'	=>	'A',
				'\u00C1'	=>	'A',
				'\u00C2'	=>	'A',
				'\u00C3'	=>	'A',
				'\u00C4'	=>	'A',
				'\u00C5'	=>	'A',
				'\u00C6'	=>	'A',
				'\u00C7'	=>	'C',
				'\u00C8'	=>	'E',
				'\u00C9'	=>	'E',
				'\u00CA'	=>	'E',
				'\u00CB'	=>	'E',
				'\u00CC'	=>	'I',
				'\u00CD'	=>	'I',
				'\u00CE'	=>	'I',
				'\u00CF'	=>	'I',
				'\u00D0'	=>	'D',
				'\u00D1'	=>	'N',
				'\u00D2'	=>	'O',
				'\u00D3'	=>	'O',
				'\u00D4'	=>	'O',
				'\u00D5'	=>	'O',
				'\u00D6'	=>	'O',
				'\u00D7'	=>	'x',
				'\u00D8'	=>	'O',
				'\u00D9'	=>	'U',
				'\u00DA'	=>	'U',
				'\u00DB'	=>	'U',
				'\u00DC'	=>	'U',
				'\u00DD'	=>	'Y',
				'\u00E0'	=>	'a',
				'\u00E1'	=>	'a',
				'\u00E2'	=>	'a',
				'\u00E3'	=>	'a',
				'\u00E4'	=>	'a',
				'\u00E5'	=>	'a',
				'\u00E6'	=>	'a',
				'\u00E7'	=>	'c',
				'\u00E8'	=>	'e',
				'\u00E9'	=>	'e',
				'\u00EA'	=>	'e',
				'\u00EB'	=>	'e',
				'\u00EC'	=>	'i',
				'\u00ED'	=>	'i',
				'\u00EE'	=>	'i',
				'\u00EF'	=>	'i',
				'\u00F1'	=>	'n',
				'\u00F2'	=>	'o',
				'\u00F3'	=>	'o',
				'\u00F4'	=>	'o',
				'\u00F5'	=>	'o',
				'\u00F6'	=>	'o',
				'\u00F9'	=>	'u',
				'\u00FA'	=>	'u',
				'\u00FB'	=>	'u',
				'\u00FC'	=>	'u',
				'\u00FD'	=>	'y',
				'\u00FE'	=>	'p',
				'\u00FF'	=>	'y',
				'\u0100'	=>	'A',
				'\u0101'	=>	'a',
				'\u0102'	=>	'A',
				'\u0103'	=>	'a',
				'\u0104'	=>	'A',
				'\u0105'	=>	'a',
				'\u0106'	=>	'C',
				'\u0107'	=>	'c',
				'\u0108'	=>	'C',
				'\u0109'	=>	'c',
				'\u010A'	=>	'C',
				'\u010B'	=>	'c',
				'\u010C'	=>	'C',
				'\u010D'	=>	'c',
				'\u010E'	=>	'D',
				'\u010F'	=>	'd',
				'\u0110'	=>	'D',
				'\u0111'	=>	'd',
				'\u0112'	=>	'E',
				'\u0113'	=>	'e',
				'\u0114'	=>	'E',
				'\u0115'	=>	'e',
				'\u0116'	=>	'E',
				'\u0117'	=>	'e',
				'\u0118'	=>	'E',
				'\u0119'	=>	'e',
				'\u011A'	=>	'E',
				'\u011B'	=>	'e',
				'\u011C'	=>	'G',
				'\u011D'	=>	'g',
				'\u011E'	=>	'G',
				'\u011F'	=>	'g',
				'\u0120'	=>	'G',
				'\u0121'	=>	'g',
				'\u0122'	=>	'G',
				'\u0123'	=>	'g',
				'\u0124'	=>	'H',
				'\u0125'	=>	'h',
				'\u0126'	=>	'H',
				'\u0127'	=>	'h',
				'\u0128'	=>	'I',
				'\u0129'	=>	'i',
				'\u012A'	=>	'I',
				'\u012B'	=>	'i',
				'\u012C'	=>	'I',
				'\u012D'	=>	'i',
				'\u012E'	=>	'I',
				'\u012F'	=>	'i',
				'\u0130'	=>	'I',
				'\u0131'	=>	'i',
				'\u0132'	=>	'IJ',
				'\u0133'	=>	'ij',
				'\u0134'	=>	'J',
				'\u0135'	=>	'j',
				'\u0136'	=>	'K',
				'\u0137'	=>	'k',
				'\u0138'	=>	'k',
				'\u0139'	=>	'L',
				'\u013A'	=>	'l',
				'\u013B'	=>	'L',
				'\u013C'	=>	'l',
				'\u013D'	=>	'L',
				'\u013E'	=>	'l',
				'\u013F'	=>	'L',
				'\u0140'	=>	'l',
				'\u0141'	=>	'L',
				'\u0142'	=>	'l',
				'\u0143'	=>	'N',
				'\u0144'	=>	'n',
				'\u0145'	=>	'N',
				'\u0146'	=>	'n',
				'\u0147'	=>	'N',
				'\u0148'	=>	'n',
				'\u0149'	=>	'n',
				'\u014A'	=>	'n',
				'\u014B'	=>	'n',
				'\u014C'	=>	'O',
				'\u014D'	=>	'o',
				'\u014E'	=>	'O',
				'\u014F'	=>	'o',
				'\u0150'	=>	'O',
				'\u0151'	=>	'o',
				'\u0154'	=>	'R',
				'\u0155'	=>	'r',
				'\u0156'	=>	'R',
				'\u0157'	=>	'r',
				'\u0158'	=>	'R',
				'\u0159'	=>	'r',
				'\u015A'	=>	'S',
				'\u015B'	=>	's',
				'\u015C'	=>	'S',
				'\u015D'	=>	's',
				'\u015E'	=>	'S',
				'\u015F'	=>	's',
				'\u0160'	=>	'S',
				'\u0161'	=>	's',
				'\u0162'	=>	'T',
				'\u0163'	=>	't',
				'\u0164'	=>	'T',
				'\u0165'	=>	't',
				'\u0166'	=>	'T',
				'\u0167'	=>	't',
				'\u0168'	=>	'U',
				'\u0169'	=>	'u',
				'\u016A'	=>	'U',
				'\u016B'	=>	'u',
				'\u016C'	=>	'U',
				'\u016D'	=>	'u',
				'\u016E'	=>	'U',
				'\u016F'	=>	'u',
				'\u0170'	=>	'U',
				'\u0171'	=>	'u',
				'\u0172'	=>	'U',
				'\u0173'	=>	'u',
				'\u0174'	=>	'W',
				'\u0175'	=>	'w',
				'\u0176'	=>	'Y',
				'\u0177'	=>	'y',
				'\u0178'	=>	'Y',
				'\u0179'	=>	'Z',
				'\u017A'	=>	'z',
				'\u017B'	=>	'Z',
				'\u017C'	=>	'z',
				'\u017D'	=>	'Z',
				'\u017E'	=>	'z',

				//uanis
				'\u0193'	=>	'A',
				'\u0201'	=>	'E',
				'\u0205'	=>	'I',
				'\u0211'	=>	'O',
				'\u0218'	=>	'U',
				'\u0209'	=>	'N',
				'\u0220'	=>	'U',
				'\u0225'	=>	'a',
				'\u0233'	=>	'e',
				'\u0237'	=>	'i',
				'\u0243'	=>	'o',
				'\u0250'	=>	'u',
				'\u0241'	=>	'n',
				'\u0252'	=>	'u',
			);

			$input = json_encode($input);
			foreach ($convertables as $from => $to) {
				$input = preg_replace("@\\" . $from . "@u", $to, $input);
			}
			# remove all other UTF8 chars
			$input = preg_replace('@\\\u[0-9a-f]{4}@u', '', $input);
			return json_decode($input, true);
		}

	    /**
		* Returns District Parameter 
		* 
		* @param int $id
		* @return string
		*/
		public static function disParam($id) {
			$SQL = "
				SELECT paramvalue
				  FROM webset.disdef_control
				 WHERE defrefid = $id
				   AND vndrefid = VNDREFID
				   AND (
				        EXISTS(SELECT 1
				                 FROM webset.statedef_discontrol
				                      INNER JOIN webset.glb_statemst ON webset.glb_statemst.staterefid = webset.statedef_discontrol.screfid
				                      INNER JOIN public.sys_vndmst ON public.sys_vndmst.vndstate = webset.glb_statemst.state
				                WHERE webset.statedef_discontrol.dcrefid = webset.disdef_control.defrefid
				                  AND public.sys_vndmst.vndrefid = VNDREFID)
				        OR
				        NOT EXISTS(SELECT 1 FROM webset.statedef_discontrol WHERE webset.statedef_discontrol.dcrefid = webset.disdef_control.defrefid)
				       )
			";
			return db::execSQL($SQL)->getOne();
	    }
	    
	    /**
	    * Determines whether WeBSIS/SAM installed on current server
	    * 
	    * @return bool
	    */
	    public static function websisHere() {				
            
            $filepath = SystemCore::$tempPhysicalRoot . '/' . SystemCore::$VndRefID . '_cache_websisHere.txt';                        
            
            if (file_exists($filepath)) {
	            if (file_get_contents($filepath) == '1') {
				    return true;
				} else {
					return false;
				}
			} else {
	            $std_demographics = SystemCore::$Registry->getOne('webset', 'std_demographics', 'version');
	            
		        $SQL = "
		        	SELECT 1
		              FROM pg_class, pg_namespace
		             WHERE pg_class.relnamespace = pg_namespace.oid
		               AND nspname = 'c_manager'
		               AND pg_class.relname not like '%_seq'
		               AND relkind = 'r'
		        ";
	            $result = db::execSQL($SQL);            

	            if ($result->recordCount() > 100 and $std_demographics != 'light') {
               		file_put_contents($filepath, '1');	            
	                return true;
	            } else {
               		file_put_contents($filepath, '0');
	                return false;
	            }
			}            
	    }
        
        /**
        * Creates window open javascript code
        * 
        * @return bool
        */
        public static function windowOpen($url = null, $wname = null, $quot = "'") {                
            if ($url != null) {
            	$script = 'url = ' . $quot . str_replace($quot, "\\".$quot, $url) . $quot . ';';
			} else {
				$script = '';
			}
            if (SystemCore::$isTablet) {
                $script .= 'wname = ' . $quot . ($wname === null ? '_blank' : str_replace($quot, '_', $wname)) . $quot . ';';
                $script .= 'window.open(' . $quot . str_replace($quot, "\\" . $quot, $url) . $quot . ', wname);';
                $script .= 'return;';
            } else {
                $script .= 'w = window.screen.availWidth - 100;';
                $script .= 'h = window.screen.availHeight - 150;';
                $script .= 't = 50;';                
                $script .= 'l = 50;';
                $script .= 'wname = ' . $quot . ($wname === null ? '_blank' : str_replace($quot, '_', $wname)) . $quot . ';';
                $script .= 'options = ' . $quot . 'top=' . $quot . '+t+' . $quot . ', left=' . $quot . '+l+' . $quot . ', width=' . $quot . '+w+' . $quot . ', height=' . $quot . '+h+' . $quot . ', menubar=yes, status=yes, toolbar=no, resizable=yes, scrollbars=yes' . $quot . ';';
                $script .= 'window.open(url, wname, options);';
                $script .= 'return;';
                
            }
            return $script;
        }
	} 
?>
