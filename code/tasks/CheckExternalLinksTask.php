<?php

class CheckExternalLinksTask extends BuildTask {

	private static $dependencies = array(
		'LinkChecker' => '%$LinkChecker'
	);

	/**
	 * @var bool
	 */
	protected $silent = false;

	/**
	 * @var LinkChecker
	 */
	protected $linkChecker;

	protected $title = 'Checking broken External links in the SiteTree';

	protected $description = 'A task that records external broken links in the SiteTree';

	protected $enabled = true;

	/**
	 * Log a message
	 *
	 * @param string $message
	 */
	protected function log($message) {
		if(!$this->silent) Debug::message($message);
	}

	public function run($request) {
		$this->runLinksCheck();
	}
	/**
	 * Turn on or off message output
	 *
	 * @param bool $silent
	 */
	public function setSilent($silent) {
		$this->silent = $silent;
	}

	/**
	 * @param LinkChecker $linkChecker
	 */
	public function setLinkChecker(LinkChecker $linkChecker) {
		$this->linkChecker = $linkChecker;
	}

	/**
	 * @return LinkChecker
	 */
	public function getLinkChecker() {
		return $this->linkChecker;
	}

	/**
	 * Check the status of a single link on a item
	 *
	 * @param BrokenExternalItemTrack $itemTrack
	 * @param DOMNode $link
	 */
	protected function checkItemLink(BrokenExternalItemTrack $itemTrack, DOMNode $link, Int $rawURL) {

			// Check link
			$httpCode = $this->linkChecker->checkLink($link);
			if($httpCode === null) return; // Null link means uncheckable, such as an internal link

			// If this code is broken then mark as such
			if($foundBroken = $this->isCodeBroken($httpCode)) {
				// Create broken record
				$brokenLink = new BrokenExternalLink();
				$brokenLink->Link = $link;
                $brokenLink->ClassChecked = $itemTrack->CheckClass;
                $brokenLink->FieldChecked = $itemTrack->CheckField;
				$brokenLink->HTTPCode = $httpCode;
				$brokenLink->TrackID = $itemTrack->ID;
				$brokenLink->StatusID = $itemTrack->StatusID; // Slight denormalisation here for performance reasons
				$brokenLink->write();
			}

			return;

	}

	/**
	 * Determine if the given HTTP code is "broken"
	 *
	 * @param int $httpCode
	 * @return bool True if this is a broken code
	 */
	protected function isCodeBroken($httpCode) {
		// Null represents no request attempted
		if($httpCode === null) return false;

		// do we have any whitelisted codes
		$ignoreCodes = Config::inst()->get('CheckExternalLinks', 'IgnoreCodes');
		if(is_array($ignoreCodes) && in_array($httpCode, $ignoreCodes)) return false;

		// Check if code is outside valid range
		return $httpCode < 200 || $httpCode > 302;
	}

	/**
	 * Runs the links checker and returns the track used
	 *
	 * @param int $limit Limit to number of items to run, or null to run all
	 * @return BrokenExternalItemTrackStatus
	 */
	public function runLinksCheck($limit = null) {
        // Check the current status
		$status = BrokenExternalItemTrackStatus::get_or_create();

		// Calculate items to run
		$itemTracks = $status->getIncompleteTracks();
		if($limit) $itemTracks = $itemTracks->limit($limit);

		// Check each item
		foreach ($itemTracks as $itemTrack) {

			// Flag as complete
			$itemTrack->Processed = 1;
			$itemTrack->write();

			$item = $itemTrack->Item();

            // Check if it has SiteTree as a parent
            $classAncestry = ClassInfo::ancestry($item->ClassName, $tablesOnly = false);

            if (in_array($item->ClassName, $classAncestry)) {
                $className = 'SiteTree';
            } else {
                $className = $itemTrack->CheckClass;
            }
            $this->log("Checking {$item->Title} - ClassName: {$className}");

            // Define the field to be checked
			$checkField = $itemTrack->CheckField;

            $links = LinkParser::find_links($item->$checkField);

            foreach($links as $link) {
                $this->checkItemLink($itemTrack, $link, 0);
            }

			// Once all links have been created for this item update HasBrokenLinks
			$count = $itemTrack->BrokenLinks()->count();
			//$this->log("Found {$count} broken links");
			/*if($count) {
				// Bypass the ORM as syncLinkTracking does not allow you to update HasBrokenLink to true
				DB::query(sprintf(
					'UPDATE "SiteTree" SET "HasBrokenLink" = 1 WHERE "ID" = \'%d\'',
					intval($itemTrack->ID)
				));
			}*/
		}

		$status->updateJobInfo('Updating completed items');
		$status->updateStatus();
		return $status;
	}

	private function updateCompletedItems($trackID = 0) {
		$noItems = BrokenExternalItemTrack::get()
			->filter(array(
				'TrackID' => $trackID,
				'Processed' => 1
			))
			->count();
		$track = BrokenExternalItemTrackStatus::get_latest();
		$track->CompletedItems = $noItems;
		$track->write();
		return $noItems;
	}

	private function updateJobInfo($message) {
		$track = BrokenExternalItemTrackStatus::get_latest();
		if($track) {
			$track->JobInfo = $message;
			$track->write();
		}
	}
}
