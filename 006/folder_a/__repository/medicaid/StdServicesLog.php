<?php
	/**
	 * This class generates transactions for Medicaid Student Service
	 *
	 * @author Oleg Bychkovski
	 */
	class StdServicesLog extends RecordLog {

		/**
		 * Class Constructor
		 *
		 */
		public function __construct() {
			parent::__construct();
		}

		/**
		 * Returns an instance of this class
		 *
		 */
		public static function factory() {
			return new StdServicesLog();
		}

		/**
		 * Saves Log
		 *
		 * @return void
		 */
		public function save() {
			$fields = $this->fields();
			for ($i = 0; $i < count($fields); $i++) {
				/** @var RecordLogField */
				$field = $fields[$i];

				switch ($this->actionType()) {
					case RecordLog::RECORD_ADDED:
						$type = 'I';
						break;
					case RecordLog::RECORD_UPDATED:
						$type = 'U';
						break;
					case RecordLog::RECORD_REMOVED:
						$type = 'D';
						break;
				}

				DBImportRecord::factory('webset.med_std_services_log', 'msl_refid')
					->set('mss_refid', $this->id())
					->set('msl_name', $field->name())
					->set('msl_caption', $field->caption())
					->set('msl_type', $type)
					->set('msl_value_old', $field->value1())
					->set('msl_value_new', $field->value2())
					->set('msl_text_value_old', $field->valueText1())
					->set('msl_text_value_new', $field->valueText2())
					->set('umrefid', SystemCore::$userID)
					->setUpdateInformation()
					->import();
			}
		}

	}
?>
