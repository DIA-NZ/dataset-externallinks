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

    // override
    public static function create() {
        $args = func_get_args();

        // Class to create should be the calling class if not Object,
        // otherwise the first parameter
        $class = get_called_class();
        if($class == 'Object') $class = array_shift($args);

        $class = self::getCustomClass($class);

        return Injector::inst()->createWithArgs($class, $args);
    }

	/**
	 * @return SiteTree
	 */
	public function Page() {
		return DataSet::get()
			->byID($this->PageID);
	}
}
