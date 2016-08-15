<?php

	/**
	 * IDEA Document class allows create PDF, HTML documents using IDEA XML schema
	 *
	 * @author Nick Ignatushko
	 * @copyright Lumen Touch, 2012
	 */
	final class IDEADocument implements IOTraceableInterface {

		/**
		 * XML source of document
		 *
		 * @var text
		 */
		private $source;

		/**
		 * CheckBox Selected RC element
		 *
		 * @var text
		 */
		private static $elem_checkbox_yes = null;

		/**
		 * CheckBox None-Selected RC element
		 *
		 * @var text
		 */
		private static $elem_checkbox_no = null;

		/**
		 * Initializes basic properties
		 *
		 * @param string $xml
		 */
		public function __construct($xml = '') {
			if ($xml != '') {
				$this->source = $xml;
			}
		}

		/**
		 * Sets source of documents
		 *
		 * @param string $xml
		 * @return IDEADocument|$this
		 */
		public function setSource($xml) {
			$this->source = $xml;
			return $this;
		}

		/**
		 * Gets source of documents
		 *
		 * @return string
		 */
		public function getSource() {
			return $this->source;
		}

		/**
		 * Gets XML-validated source
		 * Out lumen templates often consists of open-closed tags with blank inside.
		 * For example <section></section> or <field name="fld_01"></field>
		 * This class validates it to shortened version <section/> and <field/>
		 *
		 * @return string
		 */
		public function getSourceValidated() {
			try {
				$xmlDoc = new SimpleXMLElement($this->source);
				$source = $xmlDoc->asXML();
			} catch (Exception $e) {
				throw new Exception($e->getMessage());
			}
			return $source;
		}

		/**
		 * Merges FIELD and CHECKBOX elements
		 * 1. If array given it should be associative array("..."=>"...", "..."=>"...")
		 * 2. If xml given it should have such format
		 * <values>
		 *      <value name="field1">value 1</value>
		 *      <value name="field2">value 2</value>
		 * </values>
		 *
		 * @param array|string $values
		 * @return IDEADocument|$this
		 */
		public function mergeValues($values) {
			if (!is_array($values)) {
				$values = self::convertValuesXmlToArray($values);
			}
			$xmlDoc = new SimpleXMLElement($this->source);
			//Processing FIELD elements
			foreach ($xmlDoc->xpath("//field") as $field_node) {
				$field_name = (string)$field_node['name'];
				if (array_key_exists($field_name, $values)) {
					$field_node[0] = $values[$field_name];
				}
			}

			//Processing CHECKBOX elements
			foreach ($xmlDoc->xpath("//checkbox") as $field_node) {
				$field_name = (string)$field_node['name'];
				if (array_key_exists($field_name, $values) && $values[$field_name] == 'on') {
					$field_node['value'] = '1';
				}
			}

			//Processing SELECT elements
			foreach ($xmlDoc->xpath("//select") as $select_node) {
				$field_name = (string)$select_node['name'];
				$select_xml = $select_node->asXML();
				if (array_key_exists($field_name, $values)) {
					# replace <select> with <i>
					$selected_option = $select_node->xpath("//option[@value='" . $values[$field_name] . "']");
					$selected_text = (string)$selected_option[0];
					$this->source = str_replace($select_xml, '<i>' . $selected_text . '</i>', $xmlDoc->asXML());
				} else {
					# remove unchanged drop dawn
					$this->source = str_replace($selected_text, '', $xmlDoc->asXML());
				}
				# reload tree after xml/text replace
				$xmlDoc = new SimpleXMLElement($this->source);
			}

			//Processing DISPLAY property
			foreach ($xmlDoc->xpath("//*[@saveprop]") as $node) {
				$field_name = (string)$node['name'] . "_display";
				$node_xml = $node->asXML();
				if (array_key_exists($field_name, $values)) {
					# update DISPLAY property with field value
					$node['display'] = $values[$field_name];
					$this->source = str_replace($node_xml, $node->asXML(), $xmlDoc->asXML());
				}
				# reload tree after xml/text replace
				$xmlDoc = new SimpleXMLElement($this->source);
			}

			$this->source = $xmlDoc->asXML();
			return $this;
		}

		/**
		 * Checks whether text is encoded with base64
		 *
		 * @param string $str
		 * @return bool
		 */
		public static function is_base64($str) {
			return (bool)base64_decode($str);
		}

		/**
		 * converts special chars inside xml
		 *
		 * @param string $txt
		 * @return array
		 */
		public static function cleanValues($txt) {

			$lines = explode("</value>", $txt);
			$newValues = "<values>\n";
			for ($i = 0; $i < count($lines); $i++) {
				preg_match_all("/<value name=\"(.+?)\">(.+)/s", $lines[$i], $out);
				if (isset($out[1][0]) and isset($out[2][0])) {
					$newValues .= "<value name=\"" . $out[1][0] . "\">" . htmlspecialchars($out[2][0]) . "</value>\n";
				}
			}
			$newValues .= "</values>\n";
			return $newValues;
		}

		/**
		 * Remove XML Comments
		 *
		 * @param string $xml
		 * @return string
		 */
		public static function cleanComments($xml) {
			$dom = new DOMDocument;
			libxml_use_internal_errors(true);
			$dom->loadHtml($xml);
			libxml_clear_errors();
			$xpath = new DOMXPath($dom);
			foreach ($xpath->query('//comment()') as $comment) {
				$comment
					->parentNode
					->removeChild($comment);
			}
			$body = $xpath->query('//doc')
				->item(0);
			return $body instanceof DOMNode ? $dom->saveXml($body) : se($body);
		}

		/**
		 * Converts xml values structure into associative array
		 * xml should have such format
		 * <values>
		 *      <value name="field1">value 1</value>
		 *      <value name="field2">value 2</value>
		 *      ...
		 * </values>
		 *
		 * @param string $xml
		 * @return array
		 */
		public static function convertValuesXmlToArray($xml = '<values/>') {
			$values = array();
			$valuesElem = new SimpleXMLElement(self::cleanValues($xml));
			foreach ($valuesElem as $value) {
				$values[(string)$value['name']] = (string)($value);
			}
			return $values;
		}

		/**
		 * Converts associative array into XML
		 * XML will have such format
		 * <values>
		 *      <value name="field1">value 1</value>
		 *      <value name="field2">value 2</value>
		 *      ...
		 * </values>
		 *
		 * @param string $xml
		 */
		public static function convertValuesArrayToXml($values) {
			$xml = '<values>' . PHP_EOL;
			if (is_array($values)) {
				foreach ($values as $key => $value) {
					$xml .= "\t" . '<value name="' . $key . '">' . htmlspecialchars($value) . '</value>' . PHP_EOL;
				}
			}
			$xml .= '</values>' . PHP_EOL;
			return $xml;
		}

		/**
		 * Outputs document to needed format
		 *
		 * @param int $format
		 */
		function output($format = IDEADocumentFormat::PDF) {
			$source = $this->source ? $this->source : '<doc/>';
			$source = $this->cleanComments($source);
			$xmlDoc = new SimpleXMLElement($source);
			switch ($format) {
				case IDEADocumentFormat::PDF:
					$rcDoc = $this->rcDocProcess($xmlDoc, true, RCDocumentFormat::PDF);
					$rcDoc->output();
					break;
				case IDEADocumentFormat::HTML:
					$rcDoc = $this->rcDocProcess($xmlDoc, true, RCDocumentFormat::HTML);
					$rcDoc->output();
					break;
			}
		}

		/**
		 * @return RCLayout
		 */
		public function getLayout() {
			$source = $this->source ? $this->source : '<doc/>';
			$source = $this->cleanComments($source);
			$xmlDoc = new SimpleXMLElement($source);
			return $this->rcDocProcess($xmlDoc, RCDocumentFormat::PDF);
		}

		/**
		 * Parses xml and creates RCDoc
		 *
		 * @param $rootNode
		 * @param bool $returnDoc if true return RCDoc, false - RClayout
		 * @param $pageType
		 * @internal param \RCDocument $rcDoc
		 * @internal param \SimpleXMLElement $node
		 * @return \RCDocument|\RCLayout
		 */
		private function rcDocProcess($rootNode, $returnDoc = true, $pageType = null) {
			$pageFormat = $this->getOrientation($rootNode);
			$attr = $this->getHeaderFooter($rootNode);
			if ($returnDoc === true) {
				$rcDoc = RCDocument::factory($pageFormat, $pageType);
				$header = RCLayout::factory();

				$hParam = '';
				if ($attr["hColor"] != null) {
					$hParam .= 'color: #' . $attr["hColor"] . ';';
				}
				if ($attr["hSize"] != null) {
					$hParam .= 'font-size: #' . $attr["hSize"] . ';';
				}

				$header->newLine()
					->addText($attr['hLeftPart'], 'left [' . $hParam . ']')
					->addText($attr['hRightPart'], 'right [' . $hParam . ']')
					->newLine('[border-top: 1.5px solid grey;]')
					->addText('');

				$footer = RCLayout::factory();

				$fParam = '';
				if ($attr["fAlign"] != null) {
					$fParam .= $attr["fAlign"];
				}
				if ($attr["fSize"] != null) {
					$fParam .= '[font-size: #' . $attr["fSize"] . ';';
				}
				if ($attr["fColor"] != null) {
					$fParam .= 'color: #' . $attr["fColor"] . ';]';
				}

				$footer->newLine()
					->addText($attr['fText'], $fParam);

				$rcDoc->setPageHeader($header);
				$rcDoc->setPageFooter($footer);
			} else {
				$rcDoc = RCLayout::factory($pageFormat, $pageType);
			}

			foreach ($rootNode->children() as $node) {
				if ($node->attributes()->{'display'} == "0") continue;
				switch ($node->getName()) {
					case 'line':
						$rcDoc->newLine();
						$style = $this->rcStyleProcess($node);
						$rcDoc->addObject($this->rcLineProcess($node), $style->padding(0));
						break;

					case 'table':
						$rcDoc->newLine();
						$rcDoc->addObject($this->rcTableProcess($node), $this->rcStyleProcess($node));
						break;

					case 'border':
						$rcDoc->newLine();
						$style = $this->rcStyleProcess($node);
						$rcDoc->addObject($this->rcBorderProcess($node), $style->border('1px solid #000')->padding('1'));
						break;

					case 'watermark':
						$rcDoc->setWatermark(
							(string)$node
						);
						break;

					case 'pagebreak':
						$rcDoc->startNewPage();
						break;

					case 'bookmark':
						$rcDoc->addBookmark(current($node));
						break;
				}
			}
			return $rcDoc;
		}

		/**
		 * Parses LINE elements
		 *
		 * @param SimpleXMLElement $node
		 */
		private function rcLineProcess($node) {
			$tbl = RCLayout::factory();
			foreach ($node->children() as $section) {
				$style = $this->rcStyleProcess($section);
				$tbl->addText($this->rcTextProcess($section), $style);
			}
			return $tbl;
		}

		/**
		 * Parses TABLE elements
		 *
		 * @param SimpleXMLElement $node
		 */
		private function rcTableProcess($node) {

			$tbl = RCTable::factory()
				->border();

			foreach ($node->children() as $tr) {
				if ($tr->attributes()->{'display'} == "0") continue;
				$tbl->addRow();
				foreach ($tr->children() as $td) {
					$colspan = $td['colspan'] ? $td['colspan'] : 1;
					if ($colspan > 1) unset($td['width']);
					$tbl->addCell(
						$this->rcTextProcess($td) . ' ',
						$this->rcStyleProcess($td, array('align' => 'left')),
						$td['colspan'] ? $td['colspan'] : 1
					);
				}
			}

			return $tbl;
		}

		/**
		 * Parses BORDER elements
		 *
		 * @param SimpleXMLElement $node
		 */
		private function rcBorderProcess($node) {

			$brd = RCLayout::factory('');
			foreach ($node->children() as $object) {
				$element = $object->getName();
				switch ($element) {
					case 'line':
						$brd->newLine();
						$style = $this->rcStyleProcess($object);
						$brd->addObject($this->rcLineProcess($object), $style->padding(0));
						break;

					case 'table':
						$brd->newLine();
						$brd->addObject($this->rcTableProcess($object), $this->rcStyleProcess($object));
						break;
				}
			}

			return $brd;
		}

		/**
		 * Parses simple_area elements such as section and td
		 *
		 * @param SimpleXMLElement $node
		 */
		private function rcTextProcess($node) {

			$nodeName = $node->getName();
			$text = $node->asXML();
			$nodeTag = '<' . $nodeName;
			foreach ($node->attributes() as $key => $val) {
				$nodeTag .= ' ' . $key . '="' . $val . '"';
			}
			$nodeTag .= '>';

			foreach ($node->xpath("//field") as $element) {
				$newElem = str_replace("\n", "<br/>", (string)$element);
				$newElem = str_replace("\t", "    ", $newElem);
				$text = str_replace($element->asXML(), ($newElem == '' ? '' : '<i>' . $newElem . '</i>'), $text);
			}

			if (isset($node->attributes()->{'font'}) && $node->attributes()->{'font'} != 'Courier') {
				$text = str_replace("\t", " ", $text);
				$text = str_replace("\n", " ", $text);
				$text = preg_replace("/ {2,}/", " ", $text);
			}
			$text = preg_replace('<<br/>>', "\n", $text);

			//Removing start/end tages
			$text = substr($text, strlen($nodeTag));
			$text = substr($text, 0, -1 * (strlen($nodeName) + 3));
			$text = ltrim($text, " ");

			//Replace FIELD/BR/SPACE/IMG tage
			foreach ($node->children() as $element) {

				if ($element->getName() == 'img') {
					$attributes = $element->attributes();
					$img = SystemCore::$physicalRoot . (string)$attributes['src'];
					$width = (string)$attributes['width'];
					$imgbinary = fread(fopen($img, "r"), filesize($img));
					$img_base64 = base64_encode($imgbinary);
					$text = str_replace($element->asXML(), '<img>' . $img_base64 . '</img>', $text);
				}

				if ($element->getName() == 'checkbox') {
					if (self::$elem_checkbox_no == null) {
						$check_path = SystemCore::$physicalRoot . '/apps/idea/img/reportComposer/';
						self::$elem_checkbox_yes = '<img>' . base64_encode(file_get_contents($check_path . 'reduced_check.jpg')) . '</img>';
						self::$elem_checkbox_no = '<img>' . base64_encode(file_get_contents($check_path . 'reduced_uncheck.jpg')) . '</img>';
					}
					$text = str_replace($element->asXML(), ($element['value'] == '1' ? self::$elem_checkbox_yes : self::$elem_checkbox_no), $text);
				}
				if ($element->getName() == 'space') {
					$text = str_replace($element->asXML(), str_pad('', ((int)$element['repeat'] > 0 ? (int)$element['repeat'] : 1)), $text);
				}
			}

			return (strlen($text) > 0 ? $text : '');
		}

		/**
		 * Parses basic styles for simple_area elements such as section and td
		 *
		 * @param SimpleXMLElement $node
		 * @param array $default_props
		 * @return type RCStyle
		 */
		private function rcStyleProcess($node, $default_props = null) {
			$style = '';
			$attributes = ($default_props == null ? array() : $default_props);
			foreach ($node->attributes() as $key => $val) {
				$attributes[$key] = $val;
			}
			foreach ($attributes as $key => $val) {
				switch ($key) {
					case 'width':
						$style .= 'width: ' . $val . ';';
						break;

					case 'size':
						$style .= 'font-size: ' . $val . 'px;';
						break;

					case 'align':
						$style .= 'text-align: ' . $val . ';';
						break;

					case 'under':

						if ($val == '1') {
							$style .= 'border-bottom: solid 1px black;';
						}
						break;

					case 'font':
						$style .= 'font-family:' . $val . ';';
						break;

					case 'bgcolor':
						$style .= 'background-color: ' . $val . ';';
						break;

					default:
						break;
				}
			}
			$style = '[' . $style . ']';
			return RCStyle::factory($style);
		}

		/**
		 * Creates an instance of this class
		 *
		 * @param string $xml
		 * @return IDEADocument
		 */
		public static function factory($xml = '') {
			return new IDEADocument($xml);
		}

		/**
		 * Returns summarized info about the object.
		 * Implementation of interface IOTraceableInterface.
		 * This method helps to correct output the object using the class IOTrace (static alias is io::trace() )
		 *
		 * @return mixed
		 */
		public function trace() {
			$props = CoreUtils::getClassProperties($this, true, false, true);
			foreach ($props as $key => $val) {
				$props[$key] = $this->$key;
			}

			return $props;
		}

		/**
		 * Get Page Orientation

		 */
		public function getOrientation($rootNode) {
			$attributes = $rootNode->attributes();
			$orient = (string)$attributes['orient'];
			if ($orient == 'landscape') {
				return RCPageFormat::LANDSCAPE;
			} else {
				return null;
			}
		}

		/**
		 * Get Page Header/Footer

		 */
		public function getHeaderFooter($rootNode) {
			$attributes = $rootNode->attributes();
			$attr = array();
			//header
			$attr['hLeftPart'] = (string)$attributes['headerleft'];
			$attr['hRightPart'] = (string)$attributes['headerright'];
			$attr['hColor'] = (string)$attributes['headercolor'];
			$attr['hSize'] = (string)$attributes['headersize'];

			//footer
			$attr['fText'] = (string)$attributes['footer'];
			$attr['fAlign'] = (string)$attributes['footeralign'];
			$attr['fColor'] = (string)$attributes['footercolor'];
			$attr['fSize'] = (string)$attributes['footersize'];

			return $attr;
		}


	}

?>
