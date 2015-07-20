<?php

if(!class_exists('AbstractQueuedJob')) return;

/**
 * A Job for running a external link check for published items
 *
 */
class CheckExternalLinksJob extends AbstractQueuedJob implements QueuedJob {

	public function getTitle() {
		return _t('CheckExternalLiksJob.TITLE', 'Checking for external broken links');
	}

	public function getJobType() {
		return QueuedJob::QUEUED;
	}

	public function getSignature() {
		return md5(get_class($this));
	}

	/**
	 * Check an individual item
	 */
	public function process() {
		$task = CheckExternalLinksTask::create();
		$track = $task->runLinksCheck(1);
		$this->currentStep = $track->CompletedItems;
		$this->totalSteps = $track->TotalItems;
		$this->isComplete = $track->Status === 'Completed';
	}

}
