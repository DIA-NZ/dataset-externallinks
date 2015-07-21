<?php

/**
 * Represents a track for a single item
 */
class BrokenExternalItemTrack extends DataObject {

	private static $db = array(
		'Processed' => 'Boolean',
		'CheckClass' => 'Varchar(50)',
		'ItemID' => 'Int',
		'CheckField' => 'Varchar(50)'
	);

	private static $has_one = array(
		'Status' => 'BrokenExternalItemTrackStatus'
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
	public function Item() {
        
		return DataList::create($this->CheckClass)
			->byID($this->ItemID);
	}
}
