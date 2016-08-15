<?php

    /**
     * IDEA Populate Window class
     * This class provides Populate button to let user open windows a select needed entries
     *
     * @copyright Lumen Touch, 2013
     */
    class IDEAPopulateWindow {

        /**
         * Items Array
         *
         * @var array
         */
        private $items;

        /**
         * Index of Items array
         * @var type integer
         */
        private $index = NULL;

        /**
         * Adds new item to Populate Button
         * @return IDEAPopulateWindow
         */
        public function addNewItem() {
            if ($this->index === NULL) {
                $this->index = 0;
            } else {
                $this->index++;
            }
            $this->items[$this->index]['dskey'] = DataStorage::factory()->getKey();
            return $this;
        }

        /**
         * Sets title to current Item
         * @param type $title
         * @return IDEAPopulateWindow
         */
        public function setTitle($title = NULL) {
            $this->items[$this->index]['title'] = $title;
            return $this;
        }

        /**
         * Sets SQL to current Item
         *
         * @param null $SQL
         * @return IDEAPopulateWindow
         */
        public function setSQL($SQL = NULL) {
            $this->items[$this->index]['SQL'] = $SQL;
            return $this;
        }

        /**
         * Sets Source Table to current Item
         * @param string $table
         * @return IDEAPopulateWindow
         * @throws Exception
         */
        public function setSourceTable($table = NULL) {
            if ($table === NULL) throw new Exception('Please specify table.');
            $this->items[$this->index]['source_table'] = $table;
            return $this;
        }

        /**
         * Sets Source Table Refid to current Item
         * @param string $refid
         * @return IDEAPopulateWindow
         * @throws Exception
         */
        public function setSourceTableKeyField($refid = NULL) {
            if ($refid === NULL) throw new Exception('Please specify table key field.');
            $this->items[$this->index]['source_refid'] = $refid;
            return $this;
        }

        /**
         * Sets Destination Table to current Item
         * @param string $table
         * @return IDEAPopulateWindow
         * @throws Exception
         */
        public function setDestinationTable($table = NULL) {
            if ($table === NULL) throw new Exception('Please specify table.');
            $this->items[$this->index]['destination_table'] = $table;
            return $this;
        }

        /**
         * Sets Destination Table Refid to current Item
         * @param string $refid
         * @return IDEAPopulateWindow
         * @throws Exception
         */
        public function setDestinationTableKeyField($refid = NULL) {
            if ($refid === NULL) throw new Exception('Please specify destination table key field.');
            $this->items[$this->index]['destination_refid'] = $refid;
            return $this;
        }

        /**
         * Add search field to current Item Windows
         *
         * @param mized $title
         * @param null $sqlField
         * @param string $type
         * @return IDEAPopulateWindow
         */
        public function addSearch($title = NULL, $sqlField = NULL, $type = NULL) {
            $this->items[$this->index]['searches'][] = array('title' => $title, 'sqlField' => $sqlField, 'type' => $type);
            return $this;
        }

        /**
         * Add column to current Item Windows
         * @param string $title
         * @param string $width
         * @param string $type
         * @return IDEAPopulateWindow
         */
        public function addColumn($title = NULL, $width = NULL, $type = NULL) {
            $this->items[$this->index]['columns'][] = array('title' => $title, 'width' => $width, 'type' => $type);
            return $this;
        }

        /**
         * Sets Pairs Source Table Field -> Destination Table Field for final DB Import
         * @param string $destination_column
         * @param string $source_column
         * @param bool $as_sql
         * @return IDEAPopulateWindow
         */
        public function addPair($destination_column = NULL, $source_column = NULL, $as_sql = TRUE) {
            $this->items[$this->index]['pairs'][] = array('to' => $destination_column, 'from' => $source_column, 'as_sql' => $as_sql);
            return $this;
        }

        /**
         * Creates Populate button for all available items
         * @return FFMenuButton
         */
        public function getPopulateButton() {
            /** @var FFMenuButton */
            $button = FFMenuButton::factory('Populate')
                ->leftIcon('wizard2_16.png');

            foreach ($this->items as $item) {
                DataStorage::factory($item['dskey'])
                    ->set('item', serialize($item));
                $url = CoreUtils::getURL('/apps/idea/__repository/system/api/pupulate_main.php', array('dskey' => $item['dskey']));
                $script = 'win = api.window.open("' . $item['title'] . '", "' . $url . '"); ';
                $script .= 'win.resize(950, 600); ';
                $script .= 'win.center(); ';
                $script .= 'win.addEventListener("entries_populated", function(){ api.reload();});';
                $script .= 'win.show();';
                $button->addItem($item['title'], $script, 'wizard2_16.png');
            }
            return $button;
        }

        /**
         * @return IDEAPopulateWindow
         * @throws Exception
         */
        public static function factory() {
            return new IDEAPopulateWindow();
        }

    }

?>
