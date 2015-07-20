<?php

/**
 * Represents a track for a single item
 */
class BrokenExternalItemTrack extends DataObject {

	private static $db = array(
		'Processed' => 'Boolean'
	);

	private static $has_one = array(
		'Item' => 'DataSet',
		'Status' => 'BrokenExternalItemTrackStatus'
	);

	private static $has_many = array(
		'BrokenLinks' => 'BrokenExternalLink'
	);

	/**
	 * @return SiteTree
	 */
	public function Item() {
		return DataSet::get()
			->byID($this->ItemID);
	}
}
