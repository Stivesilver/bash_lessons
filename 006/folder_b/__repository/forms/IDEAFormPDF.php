<?php

	define("TOKEN_START", "ssssssstarttttttt");
	define("TOKEN_FINISH", "ffffffinishhhhh");

	/**
	 * IDEA PDF Form Class
	 * This class provides PDF Form utilities
	 *
	 * @final
	 * @copyright Lumen Touch, 2012
	 */
	final class IDEAFormPDF {

		/**
		 * PDF file binary content
		 *
		 * @var binary
		 */
		private $pdf_content;

		/**
		 * PDF Archived property
		 *
		 * @var bool
		 */
		private $pdf_archived;

		/**
		 * Merged PDF file binary content
		 *
		 * @var binary
		 */
		private $pdf_merged;

		/**
		 * PDF save file
		 *
		 * @var string
		 */
		private $pdf_savefile;

		/**
		 * FDF file content
		 *
		 * @var string
		 */
		private $fdf_content;

		/**
		 * FDF file path
		 *
		 * @var string
		 */
		private $path;

		/**
		 * Initializes $content property
		 *
		 * @param binary $pdf_content
		 */
		public function __construct($pdf_content = '') {
			$this->pdf_content = $pdf_content;
			$this->pdf_savefile = SystemCore::$virtualRoot . '/applications/webset/iep/evalforms/frm_docSave.php';
		}

		/**
		 * Initializes PDF Save File property
		 *
		 * @param string
		 * @return IDEAFormPDF
		 */
		public function setSaveFile($pdf_savefile = '') {
			$this->pdf_savefile = $pdf_savefile;
			return $this;
		}

		/**
		 * Initializes PDF Archived property
		 *
		 * @param bool
		 * @return IDEAFormPDF
		 */
		public function setArchived($pdf_archived = false) {
			$this->pdf_archived = $pdf_archived;
			return $this;
		}

		/**
		 * Merges field in PDF content
		 *
		 * @param string $fieldname
		 * @param string $fieldvalue
		 * @return void
		 */
		private function mergeField($fieldname, $fieldvalue) {
			#Removing slash from fieldname
			$fieldname = str_replace("/", "\/", $fieldname);

			#If field is empty
			if (trim($fieldvalue) == "") return;

			#If field does not exist in PDF - return

			preg_match_all("/\(" . trim($fieldname) . "\)/", $this->pdf_merged, $out, PREG_PATTERN_ORDER);
			if (count($out[0]) == 0) return;

			#Checking if field is strUrlEnd
			if ($fieldname == "strUrlEnd") {
				$blockPos = strpos($this->pdf_merged, "strUrlEnd");
				$fieldmask = "/obj[^j]+?strUrlEnd[^j]+?endobj/s";
				preg_match_all($fieldmask, substr($this->pdf_merged, $blockPos - 250, 500), $out, PREG_PATTERN_ORDER);

				if (count($out[0]) > 0) {
					$templateBlock = $out[0][0];
					$fieldvalue = str_replace("999999", $fieldvalue, $templateBlock);
					$this->pdf_merged = str_replace($templateBlock, $fieldvalue, $this->pdf_merged);
				}
				return;
			}

			$fieldmask = "/\r\/T \($fieldname\).+?endobj\r([0-9]{1,4})/s";
			preg_match_all($fieldmask, $this->pdf_merged, $out, PREG_PATTERN_ORDER);
			$obj_id = $out[1][0] - 1;
			$fieldmask = "/\r$obj_id 0 obj.+?endobj/s";

			#Checking if field is checkbox
			if ($fieldvalue == "Yes") {
				preg_match_all($fieldmask, $this->pdf_merged, $out, PREG_PATTERN_ORDER);
				if (strpos($out[0][0], "AS ") > 0) {
					$fieldvalue = preg_replace("/\/AS \/[\w]{3}/", "/AS /Yes\r/V /Yes", $out[0][0], 1);
					$this->pdf_merged = preg_replace($fieldmask, $fieldvalue, $this->pdf_merged, 1);
					return;
				}
			}

			if ($fieldvalue == "Off") return;

			#Merging field which has values layer
			preg_match_all($fieldmask, $this->pdf_merged, $out, PREG_PATTERN_ORDER);

			if (count($out[0]) > 0) {
				#calculating Kids line
				preg_match_all("/Kids (\[.+?\])/", $out[0][0], $kid_obj, PREG_PATTERN_ORDER);

				#calculating Kids  numbers
				if (isset($kid_obj[0][0]) && $kid_obj[0][0] != '') {
					preg_match_all("/(\d{1,4}) 0 R/", $kid_obj[0][0], $kid_num, PREG_PATTERN_ORDER);
				}

				if (isset($kid_num[1]) && count($kid_num[1]) > 0) {
					for ($i = 0; $i < count($kid_num[1]); $i++) {

						$kid_parent = "/\r" . $kid_num[1][$i] . " 0 obj.+?endobj/s";
						preg_match_all($kid_parent, $this->pdf_merged, $kk_obj, PREG_PATTERN_ORDER);
						#calculating AP line
						preg_match_all("/AP <<.+0 R >>/", $kk_obj[0][0], $ap_obj, PREG_PATTERN_ORDER);
						#calculating AP number
						preg_match_all("/\d{1,4}/", $ap_obj[0][0], $ap_num, PREG_PATTERN_ORDER);

						$realkidobj = $ap_num[0][0];
						$thirdmask = "/\r$realkidobj 0 obj.+?endobj/s";
						preg_match_all($thirdmask, $this->pdf_merged, $kids_obj, PREG_PATTERN_ORDER);
						$thirdblock = $kids_obj[0][0];

						if ($thirdblock <> "") {

							$fieldvalue = trim($fieldvalue);
							if (strlen($fieldvalue) == 5) $fieldvalue .= " ";
							if (strlen($fieldvalue) == 4) $fieldvalue .= "  ";
							if (strlen($fieldvalue) == 3) $fieldvalue .= "   ";
							if (strlen($fieldvalue) == 2) $fieldvalue .= "    ";
							if (strlen($fieldvalue) == 1) $fieldvalue .= "     ";

							$f = preg_replace("/999999/", $fieldvalue, $thirdblock);
							$this->pdf_merged = preg_replace($thirdmask, $f, $this->pdf_merged);
						}
					}
				}

				if (isset($out[0][0])) {
					preg_match_all("/AP <<.+0 R >>/", $out[0][0], $ap_obj, PREG_PATTERN_ORDER);
				}

				#calculating AP number
				if (isset($ap_obj[0][0])) {
					preg_match_all("/\d{1,4}/", $ap_obj[0][0], $ap_num, PREG_PATTERN_ORDER);
				}

				if ($ap_num[0][0] > 0) {
					$secondmask = "/\r" . $ap_num[0][0] . " 0 obj.+?endobj/s";
					preg_match_all($secondmask, $this->pdf_merged, $ap_obj, PREG_PATTERN_ORDER);
					$secondblock = $ap_obj[0][0];
				} else {
					$secondblock = "";
				}

				if ($secondblock <> "") {
					preg_match_all("/(Ti.+ )(\d{1,2})( Tf)/", $secondblock, $fnt_size, PREG_PATTERN_ORDER);
					$fnt = $fnt_size[2][0];

					preg_match_all("/(\/BBox \[ 0 0 )(.{1,9}) (.{1,9}) ]/", $secondblock, $width_size, PREG_PATTERN_ORDER);
					$height = 0;
					$width = 0;
					$width = $width_size[2][0];
					$height = $width_size[3][0];

					//THIS block developed to avoid "()" messup
					$etalon = $fieldvalue;
					$fieldvalue = preg_replace("/\(/", TOKEN_START, $fieldvalue, 2);
					$fieldvalue = preg_replace("/\)/", TOKEN_FINISH, $fieldvalue, 2);

					$multifield = $this->get_str_width($fieldvalue, "Times-Italic", $fnt, $width, $height);
					$fieldvalue = $etalon;
					$multifield = preg_replace("/" . TOKEN_START . "/", "\(", $multifield, 2);
					$multifield = preg_replace("/" . TOKEN_FINISH . "/", "\)", $multifield, 2);

					$field = preg_replace("/999999\) Tj/", $multifield, $secondblock);

					preg_match_all("/(\d{1,3})\.\d{1,4} 0 Td/", $secondblock, $margin_size, PREG_PATTERN_ORDER);
					$margin = isset($margin_size[1][0]) ? $margin_size[1][0] : 0;
					if ($margin > 0) {
						$symb_width = ($width - 2 * $margin) / 6.4;
						$new_margin = round(($width - strlen($fieldvalue) * $symb_width) / 2, 0);
						$field = preg_replace("/(\d{1,3})(\.\d{1,4} 0 Td)/", "$new_margin\\2", $field);
					}
					$this->pdf_merged = preg_replace($secondmask, $field, $this->pdf_merged, 1);

				}

				#Processing 0 R cases
				if (preg_match("/\/V.(\d{1,4}).0 R/", $out[0][0], $or_arr)) {
					$this->pdf_merged = preg_replace("/^" . $or_arr[1] . ".0.obj(.+?)endobj/", $or_arr[1] . " 0 obj\r(" . $fieldvalue . ")\rendobj", $this->pdf_merged);
				}

				$field = preg_replace("/999999/", $fieldvalue, $out[0][0]);
				$field = str_replace("\r\n", "\\r", $field);
				$this->pdf_merged = preg_replace("/\r$obj_id 0 obj.+?endobj/s", $field, $this->pdf_merged, 1);
			}
		}

		/**
		 * Devides long values into lines
		 *
		 * @param string $str
		 * @param string $fnt_n
		 * @param int $fnt_s
		 * @param int $w
		 * @param int $h
		 */
		private function get_str_width($str, $fnt_n, $fnt_s, $w, $h) {
			$str = trim($str);
			require_once(SystemCore::$physicalRoot . "/uplinkos/classes/pdfClass.v2.0.php");
			if ($fnt_s == "") $fnt_s = "10";
			$fl = format_line($str, $fnt_n, $fnt_s, $w / 595 * 100 + 3);
			if ($fnt_s == "8") $fl = format_line($str, $fnt_n, $fnt_s, $w / 595 * 100 + 1);
			$res = explode("\n", $fl);

			if ($h < 20) return $str . ") Tj\r";
			$resultvalue = $res[0] . ") Tj\r";

			for ($i = 1; $i < count($res); $i++) {
				$resultvalue = $resultvalue . "\r(" . $res[$i] . ") '";
			}

			return $resultvalue;
		}

		/**
		 * Merges fdf content into PDF file template
		 *
		 * @param string $fdf_content
		 * @return IDEAFormPDF
		 */
		public function mergeFDF($fdf_content = '') {

			# this paragraph uses old engine to create PDF. New one never worked ideally
			$this->fdf_content = $fdf_content;
			$this->pdf_merged = $this->pdf_content;
			$g_physicalRoot = SystemCore::$physicalRoot; 
			include(SystemCore::$physicalRoot . "/applications/webset/iep/evalforms/frm_include.php");
			$this->pdf_content = gen_pdf($this->fdf_content);
			$this->pdf_content = $this->repair_structure($this->pdf_content);
			return $this;

			//Mergeing fields
			preg_match_all("/<< \/T \([\w\W\s\S\d\D\n\r\t]{1,}?(\)>>)/", $this->fdf_content, $out, PREG_PATTERN_ORDER);

			for ($i = 0; $i < count($out[0]); $i++) {
				$fdf_field = substr($out[0][$i], 7, strpos($out[0][$i], ")/V") - 7);
				$fdf_field = str_replace("\(", "\\\(", $fdf_field);
				$fdf_field = str_replace("\)", "\\\)", $fdf_field);
				$fdf_field = str_replace(" ", "_", $fdf_field);
				$fdf_value = substr($out[0][$i], strpos($out[0][$i], ")/V") + 5, strlen($out[0][$i]) - strpos($out[0][$i], ")/V") - 8);
				$fdf_value = str_replace(")", "\)", str_replace("\)", ")", $fdf_value));
				$fdf_value = str_replace("(", "\(", str_replace("\(", "(", $fdf_value));
				$fdf_value = preg_replace("/\\\\\\\\$/", "", $fdf_value);
				if ($fdf_value != "") $this->mergeField($fdf_field, $fdf_value);
			}

			//If field is Save Form button
			if ($this->pdf_archived) {
				$this->pdf_merged = preg_replace("/4194304/", "4194305", $this->pdf_merged);
				$this->pdf_merged = preg_replace("/4198400/", "4194305", $this->pdf_merged);
				$hideSave = "T (Save Form)\r/F 6";
				$this->pdf_merged = preg_replace("/T \(Save Form\)/", $hideSave, $this->pdf_merged);
			}

			//Determine to what save file PDF should post
			$this->pdf_merged = preg_replace("/\/applications\/webset\/iep\/evalforms\/frm_docSave.php/", $this->pdf_savefile, $this->pdf_merged);

			$oldLimit = ini_get('pcre.backtrack_limit');
			ini_set('pcre.backtrack_limit', 1024 * 1024 * 10);
			$this->pdf_merged = preg_replace_callback("/obj.+?endobj/s", "set_blank", $this->pdf_merged);
			ini_set('pcre.backtrack_limit', $oldLimit);

			//set blanks kids
			preg_match_all("/\/AP << \/N (\d{1,5}) 0 R >>/", $this->pdf_merged, $out, PREG_PATTERN_ORDER);
			for ($i = 0; $i < count($out[1]); $i++) {
				preg_match_all("/\r" . $out[1][$i] . " 0 obj.+?endobj/s", $this->pdf_merged, $s, PREG_PATTERN_ORDER);
				if (strpos($s[0][0], "999999") > 0) {
					$this->pdf_merged = preg_replace("/\/AP << \/N " . $out[1][$i] . " 0 R >>/", "/AP << >>", $this->pdf_merged);
				}
			}

			//Adjust Metadata
			$this->pdf_merged = $this->adjustMeta($this->pdf_merged);

			//To avoid 109 error we make sure size of template is not equal to resulting form
			if (strlen($this->pdf_content) == strlen($this->pdf_merged) or strlen($this->pdf_content) == (strlen($this->pdf_merged) - 1))
				$this->pdf_merged = preg_replace("/\/L " . strlen($this->pdf_content) . "/", "/L " . strlen($this->pdf_content) . "  ", $this->pdf_merged);

			$this->pdf_content = $this->pdf_merged;
			$this->pdf_content = $this->repair_structure($this->pdf_content);
			$this->pdf_merged = '';
			return $this;
		}

		/**
		 * Adjusts PDF Meta Data
		 *
		 * @param binary $txt
		 * @return binary
		 */
		private function adjustMeta($txt) {
			$username = explode(",", trim(SystemCore::$userName));
			$txt = preg_replace("/\/Creator \(.*?\)\r/", "/Creator (" . $_SERVER["SERVER_NAME"] . ")\r", $txt);
			$txt = preg_replace("/\/CreationDate \(.*?\)\r/", "/CreationDate (D:" . date("YmdHis") . "Z)\r", $txt);
			$txt = preg_replace("/\/Title \(.*?\)/", "/Title (IDEA Form)", $txt);
			$txt = preg_replace("/\/Author \(.*?\)/", "/Author (" . (isset($username[1]) ? $username[1] . " " . $username[0] : '') . ")", $txt);
			$txt = preg_replace("/\/Producer \(.*?\)\r/", "/Producer (Lumen Touch Forms)\r", $txt);
			$txt = preg_replace("/\/ModDate \(.*?\)\r/", "/ModDate (D:" . date("YmdHis") . "z)\r", $txt);
			return $txt;
		}

		/**
		 * Replaces native XML fields with PDF equivalents ON TEMPLATE
		 *
		 * @param string $template
		 * @param string $new_ids
		 * @return string
		 */
		public static function replace_id($template, $new_ids) {
			$tpl_fields = array();
			$rel_fields = array();
			$new_ids = str_replace("<values>", "", $new_ids);
			$new_ids = str_replace("</values>", "", $new_ids);
			$lines = explode("name=\"", $template);

			//create all existing in template fields array
			for ($i = 1; $i < count($lines); $i++) {
				$xml_field = substr($lines[$i], 0, strpos($lines[$i], "\""));
				$tpl_fields[] = $xml_field;
			}

			$lines = explode("</value>", $new_ids);
			for ($i = 0; $i < count($lines) - 1; $i++) {
				$xml_field = substr($lines[$i], strpos($lines[$i], "name=\"") + 6, strpos($lines[$i], ">") - strpos($lines[$i], "name=\"") - 7);
				$pdf_field = substr($lines[$i], strpos($lines[$i], ">") + 1, strlen($lines[$i]));
				$pdf_field = str_replace("_", " ", $pdf_field);
				$template = str_replace("name=\"" . $xml_field . "\"", "NAME=\"" . $pdf_field . "\"", $template);
				$rel_fields[] = $xml_field;
			}
			if (str_replace("</doc>", "", $template) != $template) {
				$template = str_replace("</doc>", "<line display=\"0\"><section><field name=\"strUrlEnd\"></field></section></line>\n</doc>", $template);
			} else {
				$template = "<line display=\"0\"><section><field name=\"strUrlEnd\"></field></section></line>" . $template;
			}

			//disable all none-linked fields
			for ($i = 0; $i < count($tpl_fields); $i++) {
				if (!in_array($tpl_fields[$i], $rel_fields)) {
					$template = str_replace("name=\"" . $tpl_fields[$i] . "\"", "NAME=\"" . $tpl_fields[$i] . "\" display=\"0\"", $template);
				}
			}

			return $template;
		}

		/**
		 * Replaces native XML fields with PDF equivalents on VALUES
		 *
		 * @param string $values
		 * @param string $new_ids
		 * @return string
		 */
		public static function replace_id_vals($values, $new_ids) {
			$tpl_fields = array();
			$rel_fields = array();
			$new_ids = str_replace("<values>", "", $new_ids);
			$new_ids = str_replace("</values>", "", $new_ids);
			$lines = explode("name=\"", $values);

			//create all existing in values fields array
			for ($i = 1; $i < count($lines); $i++) {
				$xml_field = substr($lines[$i], 0, strpos($lines[$i], "\""));
				$tpl_fields[] = $xml_field;
			}

			$lines = explode("</value>", $new_ids);
			for ($i = 0; $i < count($lines) - 1; $i++) {
				$xml_field = substr($lines[$i], strpos($lines[$i], "name=\"") + 6, strpos($lines[$i], ">") - strpos($lines[$i], "name=\"") - 7);
				$pdf_field = substr($lines[$i], strpos($lines[$i], ">") + 1, strlen($lines[$i]));
				$pdf_field = str_replace("_", " ", $pdf_field);
				$values = str_replace("name=\"" . $xml_field . "\"", "name=\"" . $pdf_field . "\"", $values);
				$rel_fields[] = $xml_field;
			}

			return $values;
		}

		/**
		 * Converts FDF into XML
		 *
		 * @param string $fdf
		 * @param string $template
		 * @return string
		 */
		public static function fdf2xml($fdf, $template) {
			//Create array with checkboxes names
			$chk_array = array();
			$lines = explode("<checkbox ", $template);
			for ($i = 1; $i < count($lines); $i++) {
				$lines[$i] = str_replace("NAME=\"", "", $lines[$i]);
				$field = substr($lines[$i], 0, strpos($lines[$i], "\""));
				$chk_array[] = trim($field);
			}
			$fdf = substr($fdf, strpos($fdf, "2 0 obj") + 15, strlen($fdf));
			$fdf = substr($fdf, 0, strlen($fdf));
			$lines = explode(")>>", $fdf);
			$xml = "<values>\n";
			for ($i = 0; $i < count($lines); $i++) {
				$field = substr($lines[$i], strpos($lines[$i], "<< /T (") + 7, strpos($lines[$i], ")/V (") - strpos($lines[$i], "<< /T (") - 7);
				$field = str_replace("_", " ", $field);
				$value = htmlspecialchars(trim(substr($lines[$i], strpos($lines[$i], ")/V (") + 5, strlen($lines[$i]))));
				//If field is checkbox we should replace Yes with on
				if (in_array($field, $chk_array) and $value == "Yes") $value = "on";
				if ($field != "" and $value != "") $xml .= "<value name=\"" . $field . "\">" . $value . "</value>\n";
			}
			$xml .= "</values>";
			return $xml;
		}

		/**
		 * Creates FDF format file
		 *
		 * @param array $data
		 * @param string $filename
		 * @param int $mfcrefid
		 * @param int $template_pdf
		 * @return string
		 */
		public static function fdf_prepare($data, $filename = null, $mfcrefid = null, $template_pdf = null) {

			$fields = '';
			$pdf_file = '';

			if (!empty($mfcrefid)) {
				$pdf_file = 'http://' . $_SERVER['SERVER_NAME'] . SystemCore::$virtualRoot . '/applications/webset/iep/evalforms/docs/';
				$pdf_file .= db::execSQL("
                             SELECT mfcfilename
                               FROM webset.statedef_forms
                              WHERE mfcrefid = " . $mfcrefid . "
                         ")->getOne();
			}

			if (!empty($template_pdf)) {
				$pdf_file = 'http://' . $_SERVER['SERVER_NAME'] . SystemCore::$virtualRoot . CoreUtils::getAbstractPath($template_pdf);
			}

			foreach ($data as $key => $val) {
				if ($key == 'strUrlEnd' && !empty($filename)) {
					$fields .= '<< /T (' . str_replace('_', ' ', $key) . ')/V (' . '?tsRefID=-1&mfcrefid=' . $filename . ')>>';
				} else {
					if ($val == 'on') $val = 'Yes';
					$fields .= '<< /T (' . str_replace('_', ' ', $key) . ')/V (' . $val . ')>>';
				}
			}

			return '%FDF-1.2
                        %����
                        1 0 obj
                        <<
                        /FDF << /Fields 2 0 R /F (' . $pdf_file . ')>>
                        >>
                        endobj
                        2 0 obj
                        [
                        ' . $fields . '
                        ]
                        endobj
                        trailer
                        <<
                        /Root 1 0 R

                        >>
                        %%EOF ';
		}

		/**
		 * Restore xref and other problem using great gs command
		 * @param string $content
		 * @return void
		*/
		public static function repair_structure($content) {
			$tmpDir = '/tmp';

			$temp_in_path = tempnam($tmpDir, 'pdf');
			$temp_out_path = tempnam($tmpDir, 'pdf');

			file_put_contents($temp_in_path, $content);

			$command = '
				marksfile=""
				if grep -q "UseOutlines" "' . $temp_in_path . '"; then
					marksfile="/tmp/pdfmarks"
					echo "[/PageMode /UseOutlines /DOCVIEW pdfmark" > "$marksfile"
				fi
				gs -q -dNOPAUSE -dBATCH -dSAFER -sDEVICE=pdfwrite -sOutputFile="' . $temp_out_path . '" "' . $temp_in_path . '" $marksfile
			';
			exec($command);

			$content = file_get_contents($temp_out_path);

			if (is_file($temp_in_path) && file_exists($temp_in_path)) unlink($temp_in_path);
			if (is_file($temp_out_path) && file_exists($temp_out_path)) unlink($temp_out_path);

			return $content;
		}

		/**
		 * Forces all linebreaks to be \r\n overwise old pdf class badly work with multiline
		 * @param string $str
		 * @return string
		*/
		public static function prepare_linebreaks($str) {
			$str = str_replace("\r\n", '%%%%somthing%%%%', $str);
			$str = str_replace(array("\r", "\n"), array("\r\n", "\r\n"), $str);
			$str = str_replace('%%%%somthing%%%%', "\r\n", $str);
			return $str;
		}

		/**
		 * Sends PDF content into browser
		 *
		 * @return void
		 */
		public function show() {
			header('Content-type: application/pdf');
			print $this->pdf_content;
		}

		/**
		 * Save pdf content to file
		 *
		 * @param $path
		 * @return string
		 */
		public function toFile($path) {
			$this->path = $path;
			$fp = fopen($this->path, "w");
			fputs($fp, $this->pdf_content);
			fclose($fp);
			return $this->path;
		}

		/**
		 * Returns PDF content
		 *
		 * @return bin
		 */
		public function getPDFContent() {
			return $this->pdf_content;
		}

		/**
		 * Creates an instance of this class
		 *
		 * @param binary $content
		 * @return IDEAFormPDF
		 */
		public static function factory($pdf_content = '') {
			return new IDEAFormPDF($pdf_content);
		}
	}

?>
