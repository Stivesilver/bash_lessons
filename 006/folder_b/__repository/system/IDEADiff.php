<?php

	/**
	 * Class IDEADiff
	 * This class allows to create diff between two texts using Linux command diff, sdiff and similar
	 *
	 * @author Nick Ignatushko
	 * @copyright LumenTouch, 2015
	 */
	class IDEADiff extends RegularClass {

		/**
		 * @var string $content_curr
		 */
		private $content_curr;

		/**
		 * @var string $content_prev
		 */
		private $content_prev;

		/**
		 * @var string $diff_command
		 */
		private $diff_command;

		/**
		 * @var string $diff_params
		 */
		private $diff_params;

		/**
		 * @var string $result
		 */
		private $result;

		/**
		 * @var string $result
		 */
		private $result_url;

		/**
		 * Class Constructor
		 *
		 * @param string $content_curr
		 * @param string $content_prev
		 * @param string $diff_command
		 * @param string $diff_params
		 * @return IDEADiff
		 */
		public function __construct($content_curr = '', $content_prev = '', $diff_command = 'diff', $diff_params = '-w') {
			parent::__construct();
			$this->content_curr = $content_curr;
			$this->content_prev = $content_prev;
			$this->diff_command = $diff_command;
			$this->diff_params = $diff_params;
		}

		/**
		 * Creates and returns an instance of this class.
		 *
		 * @param string $content_curr
		 * @param string $content_prev
		 * @param string $diff_command
		 * @param string $diff_params
		 * @return IDEADiff
		 */
		public static function factory($content_curr = '', $content_prev = '', $diff_command = 'diff', $diff_params = '') {
			return new IDEADiff($content_curr, $content_prev, $diff_command, $diff_params);
		}

		/**
		 * Set Current Text Version
		 *
		 * @param string $txt
		 * @return IDEADiff
		 */
		public function setCurrentVersion($txt) {
			$this->content_curr = $txt;
			return $this;
		}


		/**
		 * Set Previous Text Version
		 *
		 * @param string $txt
		 * @return IDEADiff
		 */
		public function setPreviousVersion($txt) {
			$this->content_prev = $txt;
			return $this;
		}

		/**
		 * Get diff command result
		 *
		 * @return string
		 */
		public function getDiff() {
			$file_current = FileUtils::createTmpFile($this->content_curr);
			$file_previos = FileUtils::createTmpFile($this->content_prev);
			$file_tempory = FileUtils::createTmpFile('', 'patch');

			$this->result = CLIRequest::factory($this->diff_command)
				->params($this->diff_params . ' ' . $file_previos . ' ' . $file_current . ' > ' . $file_tempory . '; cat ' . $file_tempory)
				->execute()
				->result;

			return $this->result;
		}

		/**
		 * Get url with diff result
		 *
		 * @return string
		 */
		public function getDiffUrl() {
			$dskey = DataStorage::factory()
				->set('diff', $this->getDiff())
				->getKey();
			$this->result_url = CoreUtils::getVirtualPath('./api/diff_viewer.php');
			return CoreUtils::getURL('./api/diff_viewer.php', array('dskey' => $dskey)) ;
		}

	}
