<?php

	/**
	 * Contains basic read/write methods for Lumen ZDI files
	 *
	 * @copyright Lumen Touch, 2013
	 */
	class IDEAZdi {

		/**
		 * ZDI content in xml format
		 *
		 * @var SimpleXMLElement
		 */
		private $content_xml;

		/**
		 * ZDI file name
		 *
		 * @var string
		 */
		private $zdi_file_name;

		/**
		 * Initializes basic properties
		 *
		 * @param string $content_zdi
		 */
		public function __construct($zdi_file_name) {
			if ($zdi_file_name == '') throw new Exception('Please specify zdi_file_name.');
			if (is_dir($zdi_file_name)) throw new Exception('ZDI file can not be a directory');
			if (!file_exists($zdi_file_name)) throw new Exception('ZDI file does not exist');

			$this->zdi_file_name = $zdi_file_name;

			$xml = '<ZDI>' . PHP_EOL;
			$openSection = '';
			$lines = explode("\n", file_get_contents($this->zdi_file_name));
			foreach ($lines as $line) {
				if (substr($line, 0, 1) == '[') {
					if ($openSection != '') $xml .= '</' . $openSection . '>' . PHP_EOL;
					$openSection = trim(str_replace(':', '___', str_replace(']', '', str_replace('[', '', $line))));
					$xml .= '<' . $openSection . '>' . PHP_EOL;
				} else {
					$blocks[0] = trim(substr($line, 0, strpos($line, '=')));
					$blocks[1] = trim(substr($line, strpos($line, '=') + 1));
					if ($blocks[0] != '') {
						$xml .= '    <' . trim($blocks[0]) . '>' . htmlspecialchars(trim($blocks[1])) . '</' . trim($blocks[0]) . '>' . PHP_EOL;
					}
				}
			}
			if ($openSection != '') $xml .= '</' . $openSection . '>' . PHP_EOL;
			$xml .= '</ZDI>';
			$this->content_xml = new SimpleXMLElement($xml);
		}

		/**
		 * Exports desktop to XML
		 *
		 */
		public function toXML() {
			return $this->content_xml->asXML();
		}

		/**
		 * Exports desktop to TXT (ini) format
		 *
		 */
		public function toTXT() {
			$txt = '';
			foreach ($this->content_xml->children() as $section) {
				$txt .= '[' . str_replace('___', ':', $section->getName()) . ']' . PHP_EOL;
				foreach ($section->children() as $param) {
					$txt .= '  ' . $param->getName() . ' = ' . (string) $param . PHP_EOL;
				}
			}
			return $txt;
		}
		
		/**
		 * Get actual file content
		 *
		 */
		public function getActualFileContent() {			
			return file_get_contents($this->zdi_file_name);
		}
		
		/**
		 * Get actual file content
		 *
		 */
		public function getActualFileUrl() {			
			return $this->zdi_file_name;
		}
		
		/**
		 * Extracts ZDI url according backup file url
		 * 
		 * @param type $url
		 * @return string
		 */
		public static function getFileUrlFromBackup($url) {			
			preg_match_all("/~(.+)\.\d{10}.zdi/", $url, $out);
			return str_replace(basename($url), $out[1][0].'.zdi', $url);
		}

		/**
		 * Updates zdi file
		 * @return IDEAZdi
		 */
		public function updateFile() {
			$backup_file = str_replace(basename($this->zdi_file_name), '~' . str_replace('.zdi', '.' . date("mdyhi") . '.zdi', basename($this->zdi_file_name)), $this->zdi_file_name);
			if (substr($backup_file, -3) != 'zdi') throw new Exception('Backup file name "' . $backup_file . '" is wrong.');
			copy($this->zdi_file_name, $backup_file);
			file_put_contents($this->zdi_file_name, $this->toTXT());
			return $this;
		}

		/**
		 * Returns specified parameter value or array of values
		 *
		 * @param string $property
		 * @return mixed
		 */
		public function getParam($section, $property) {
			$values = $this->content_xml->$section->$property;
			if (count($values) == 0) {
				return;
			} elseif (count($values) == 1) {
				return (string) $values[0];
			} else {
				$arr = array();
				foreach ($values as $value) {
					$arr[] = (string) $value;
				}
				return $arr;
			}
		}

		/**
		 * Updates specified parameter value or array of values
		 *
		 * @param string $section
		 * @param string $property
		 * @param string $value
		 * @return IDEAZdi
		 */
		public function setParam($section, $property, $value) {
			$node = $this->content_xml->xpath('/ZDI/' . $section . '/' . $property);
			if (count($node) == 1) {
				$node[0][0] = $value;
			} elseif (count($node) == 0) {
				$node = $this->content_xml->xpath('/ZDI/' . $section);
				if (!$node) {
					$node = $this->content_xml->addChild($section);
					$node = $this->content_xml->xpath('/ZDI/' . $section);
				}
				$node[0]->addChild($property, $value);
			}
			return $this;
		}

		/**
		 * Returns icons array
		 *         
		 * @return array
		 */
		public function getIcons() {
			$values = $this->content_xml->DESKTOP->ICON;
			$arr = array();
			foreach ($values as $value) {
				$tmp = explode('; ', $value);
				$w['title'] = $tmp[0];
				$w['image'] = $tmp[1];
				$w['x'] = $tmp[2];
				$w['y'] = $tmp[3];
				$w['behavior'] = $tmp[4];
				$w['url'] = $tmp[5];
				$w['size'] = (isset($tmp[6]) ? $tmp[6] : NULL);
				$arr[] = $w;
			}
			return $arr;
		}

		/**
		 * Sets icons
		 *         
		 * @param array $icons
		 * @return IDEAZdi
		 */
		public function setIcons($icons) {
			unset($this->content_xml->DESKTOP->ICON);
			foreach ($icons as $icon) {
				$tmp = array();
				$tmp[] = $icon['title'];
				$tmp[] = $icon['image'];
				$tmp[] = $icon['x'];
				$tmp[] = $icon['y'];
				$tmp[] = $icon['behavior'];
				$tmp[] = htmlspecialchars($icon['url']);
				if (isset($icon['size'])) $tmp[] = $icon['size'];
				$value = implode('; ', $tmp);
				$this->content_xml->DESKTOP->addChild('ICON', $value);
			}
			$this->setShortcuts($this->getShortcuts()); #it is made to save correct order of ICONS/SHORTCUTS
			return $this;
		}

		/**
		 * Returns shortcuts array
		 *         
		 * @return array
		 */
		public function getShortcuts() {
			$values = $this->content_xml->DESKTOP->SHORTCUT;
			$arr = array();
			foreach ($values as $value) {
				$tmp = explode('%%', $value);
				$w['prefix'] = isset($tmp[0]) ? $tmp[0] : '';
				$w['keycode'] = isset($tmp[1]) ? $tmp[1] : '';
				$w['title'] = isset($tmp[2]) ? $tmp[2] : '';
				$w['url'] = isset($tmp[3]) ? $tmp[3] : '';
				$arr[] = $w;
			}
			return $arr;
		}

		/**
		 * Sets shortcuts
		 *         
		 * @param array $icons
		 * @return IDEAZdi
		 */
		public function setShortcuts($shortcuts) {
			unset($this->content_xml->DESKTOP->SHORTCUT);
			foreach ($shortcuts as $shortcut) {
				$tmp = array();
				$tmp[] = $shortcut['prefix'];
				$tmp[] = $shortcut['keycode'];
				$tmp[] = $shortcut['title'];
				$tmp[] = htmlspecialchars($shortcut['url']);
				$value = implode('%%', $tmp);
				$this->content_xml->DESKTOP->addChild('SHORTCUT', $value);
			}
			return $this;
		}

		/**
		 * Removed specified parameter
		 *
		 * @param string $section
		 * @param string $property		
		 * @return IDEAZdi
		 */
		public function delParam($section, $property) {
			unset($this->content_xml->$section->$property);
			return $this;
		}

		/**
		 * Creates an instance of this class
		 *
		 * @param int $content_zdi
		 * @return IDEAZdi
		 */
		public static function factory($zdi_file_name) {
			if ($zdi_file_name == '') throw new Exception('Please specify zdi_file_name.');
			return new IDEAZdi($zdi_file_name);
		}

	}

?>