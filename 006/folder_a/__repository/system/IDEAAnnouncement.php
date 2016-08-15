<?php

    /**
     * IDEA Announcement Window class
     * This class provides link to let user open window with system annoucements
     *
     * @copyright Lumen Touch, 2014
     */
    class IDEAAnnouncement {

        /**
         * Announcement DataStorage Prefix
         *
         * @var string
         */
        private $dsprefix = 'idea_announcement_';

        /**
         * Announcement ID
         *
         * @var int
         */
        private $id;

        /**
         * Announcement Caption
         *
         * @var string
         */
        private $caption;

        /**
         * Announcement Content
         *
         * @var string
         */
        private $content;

		/**
		 * Class Constructor
		 *
		 * @param int $id
		 * @return IDEAAnnouncement
		 */
		public function __construct($id = 0) {
			$dskey = $this->dsprefix . $id;
			$this->id = $id;
			if (DataStorage::exists($dskey)) {
				$ds = DataStorage::factory($dskey);
			} else {
				$message = db::execSQL("
					SELECT acaption,
					abody
					FROM public.sys_anouncements
					WHERE arefid = " . (int)$id . "
					")->assoc();
				$ds = DataStorage::factory()
					->set('caption', $message['acaption'])
					->set('content', $message['abody'])
					->setKey($dskey);
			}
				$this->caption = $ds->get('caption');
				$this->content = $ds->get('abody');
		}

        /**
         * Creates Populate button for all available items
		 * @param string $title
         * @return UIAnchor
         */
        public function getLink($title = '') {
            /** @var UIAnchor */
			$link = UIAnchor::factory($title == '' ? $this->caption : $title)
				->onClick('alert(222)');

//            foreach ($this->items as $item) {
//                DataStorage::factory($item['dskey'])
//                    ->set('item', serialize($item));
//                $url = CoreUtils::getURL('/apps/idea/__repository/system/api/pupulate_main.php', array('dskey' => $item['dskey']));
//                $script = 'win = api.window.open("' . $item['title'] . '", "' . $url . '"); ';
//                $script .= 'win.resize(950, 600); ';
//                $script .= 'win.center(); ';
//                $script .= 'win.addEventListener("entries_populated", function(){ api.reload();});';
//                $script .= 'win.show();';
//                $button->addItem($item['title'], $script, 'wizard2_16.png');
//            }

            return $link;
        }

        /**
         * @return IDEAAnnouncement
         * @param int $id
         * @throws Exception
         */
        public static function factory($id = 0) {
            return new IDEAAnnouncement();
        }

    }

?>
