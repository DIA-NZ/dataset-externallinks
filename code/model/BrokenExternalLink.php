<?php

/**
 * Represents a single link checked for a single run that is broken
 *
 * @method BrokenExternalItemTrack Track()
 * @method BrokenExternalItemTrackStatus Status()
 */
class BrokenExternalLink extends DataObject {

	private static $db = array(
		'Link' => 'Varchar(2083)', // 2083 is the maximum length of a URL in Internet Explorer.
		'HTTPCode' =>'Int',
        'ClassChecked' => 'Varchar(50)',
        'FieldChecked' => 'Varchar(50)'
	);

	private static $has_one = array(
		'Track' => 'BrokenExternalItemTrack',
		'Status' => 'BrokenExternalItemTrackStatus'
	);

	private static $summary_fields = array(
		'Created' => 'Checked',
		'Link' => 'External Link',
		'HTTPCodeDescription' => 'HTTP Error Code',
		'Item.Title' => 'Item link is on'
	);

	private static $searchable_fields = array(
		'HTTPCode' => array('title' => 'HTTP Code')
	);

	/**
	 * @return SiteTree
	 */
	public function Item() {
		return $this->Track()->Item();
	}

	public function canEdit($member = false) {
		return false;
	}

	public function canView($member = false) {
		$member = $member ? $member : Member::currentUser();
		$codes = array('content-authors', 'administrators');
		return Permission::checkMember($member, $codes);
	}

	/**
	 * Retrieve a human readable description of a response code
	 *
	 * @return string
	 */
	public function getHTTPCodeDescription() {
		$code = $this->HTTPCode;
		if(empty($code)) {
			// Assume that $code = 0 means there was no response
			$description = _t('BrokenExternalLink.NOTAVAILABLE', 'Server Not Available');
		} elseif(
			($descriptions = Config::inst()->get('SS_HTTPResponse', 'status_codes'))
			&& isset($descriptions[$code])
		) {
			$description = $descriptions[$code];
		} else {
			$description = _t('BrokenExternalLink.UNKNOWNRESPONSE', 'Unknown Response Code');
		}
		return sprintf("%d (%s)", $code, $description);
	}
}


