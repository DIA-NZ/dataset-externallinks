<?php

/**
 * Represents a track for a single page
 */
class BrokenExternalPageTrack extends DataObject {

	private static $db = array(
		'Processed' => 'Boolean'
	);

	private static $has_one = array(
		'Page' => 'DataObject',
		'Status' => 'BrokenExternalPageTrackStatus'
	);

	private static $has_many = array(
		'BrokenLinks' => 'BrokenExternalLink'
	);

	/**
	 * @return SiteTree
	 */
	public function Page() {
		return DataSet::get()
			->byID($this->PageID);
	}
}
