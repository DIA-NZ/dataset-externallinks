<?php

/**
 * Represents the status of a track run
 *
 * @method DataList TrackedItems()
 * @method DataList BrokenLinks()
 * @property int $TotalItems Get total items count
 * @property int $CompletedItems Get completed items count
 */
class BrokenExternalItemTrackStatus extends DataObject {

	private static $db = array(
		'Status' => 'Enum("Completed, Running", "Running")',
		'JobInfo' => 'Varchar(255)'
	);

	private static $has_many = array(
		'TrackedItems' => 'BrokenExternalItemTrack',
		'BrokenLinks' => 'BrokenExternalLink'
	);

	/**
	 * Get the latest track status
	 *
	 * @return self
	 */
	public static function get_latest() {
		return self::get()
			->sort('ID', 'DESC')
			->first();
	}

	/**
	 * Gets the list of Items yet to be checked
	 *
	 * @return DataList
	 */
	public function getIncompleteItemList() {
		$itemIDs = $this
			->getIncompleteTracks()
			->column('ItemID');
		if($itemIDs) return DataSet::get()
			->byIDs($itemIDs);
	}

	/**
	 * Get the list of incomplete BrokenExternalItemTrack
	 *
	 * @return DataList
	 */
	public function getIncompleteTracks() {
		return $this
			->TrackedItems()
			->filter('Processed', 0);
	}

	/**
	 * Get total items count
	 */
	public function getTotalItems() {
		return $this->TrackedItems()->count();
	}

	/**
	 * Get completed items count
	 */
	public function getCompletedItems() {
		return $this
			->TrackedItems()
			->filter('Processed', 1)
			->count();
	}

	/**
	 * Returns the latest run, or otherwise creates a new one
	 *
	 * @return self
	 */
	public static function get_or_create() {
		// Check the current status
		$status = self::get_latest();
		if ($status && $status->Status == 'Running') {
			$status->updateStatus();
			return $status;
		}

		return self::create_status();
	}

	/*
	 * Create and prepare a new status
	 *
	 * @return self
	 */
	public static function create_status() {
		// If the script is to be started create a new status
		$status = self::create();
		$status->updateJobInfo('Creating new tracking object');

		$classes_to_check = Config::inst()->get('LinkChecker', 'classes_to_check');

		foreach ($classes_to_check as $class => $fieldsToCheck) {

            foreach ($fieldsToCheck as $field ) {

                $itemSet = DataList::create($class);

                foreach ($itemSet as $item) {
                    $trackItem = BrokenExternalItemTrack::create();

                    $trackItem->CheckClass = $class;
                    $trackItem->CheckField = $field;
                    $trackItem->ItemID = $item->ID;
                    $trackItem->StatusID = $status->ID;
                    $trackItem->write();
                }
            }
		}

		return $status;
	}

	public function updateJobInfo($message) {
		$this->JobInfo = $message;
		$this->write();
	}

	/**
	 * Self check status
	 */
	public function updateStatus() {
		if ($this->CompletedItems == $this->TotalItems) {
			$this->Status = 'Completed';
			$this->updateJobInfo('Setting to completed');
		}
	}
}
