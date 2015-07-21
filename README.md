# External links

## Introduction

### Core module

The external links module is a task and ModelAdmin to track and to report on broken external links. It is maintained 
by Kirk Mayo at Silverstripe.

## Usage

Enter your configuration into the main config.yml file following the below pattern

LinkChecker:
  classes_to_check:
    - DataSet
Dataset:
  link_check_field: 'Description'

### This fork

This is a fork which swaps the use of SiteTree for that of DataSet, enabling Data.govt.nz to check external links within
 the content of the thousands of datasets. Implementing this module means the external broken link checker will no 
longer report on broken links within pages. A future state of development would involve allowing configuration to 
determine which Class/Field combinations are checked.
 
### Parent fork

This parent of this fork removes a core piece of the module's functionality. With this fork installed, the module will no 
longer write the `ss-broken` css class to broken URLs. The reason for this change is that saving the modified record
to the database changes the publication date of versioned content, pushing it back to the top of RSS feeds and Recently
Edited Pages.

The new `config.yml` file contains a boolean value that can be used to turn the "add class" functionality on and off.

Apart from the *Maintainer contact* in the next section, the remainder of this README is the original document by Kirk
Mayo.

## Maintainer Contact

	* Julian Thomson: julian (dot) thomson (at) dia (dot) govt (dot) nz

## Requirements

	* SilverStripe 3.1 +

## Features

* Add external links to broken links reports
* Add a task to track external broken links

## Installation

 1. If you have composer you can use `composer require silverstripe/externallinks:*`. Otherwise, 
    download the module from GitHub and extract to the 'externallinks' folder. Place this directory
    in your sites root directory. This is the one with framework and cms in it.
 2. Run in your browser - `/dev/build` to rebuild the database.
 3. Run the following task *http://path.to.silverstripe/dev/tasks/CheckExternalLinks* to check for
    broken external links

## Report ##

A new report is added called 'External Broken links report'. When viewing this report, a user may press
the "Create new report" button which will trigger an ajax request to initiate a report run.

In this initial ajax request this module will do one of two things, depending on which modules are included:

* If the queuedjobs module is installed, a new queued job will be initiated. The queuedjobs module will then
  manage the progress of the task.
* If the queuedjobs module is absent, then the controller will fallback to running a buildtask in the background.
  This is less robust, as a failure or error during this process will abort the run.

In either case, the background task will loop over every page in the system, inspecting all external urls and
checking the status code returned by requesting each one. If a URL returns a response code that is considered
"broken" (defined as < 200 or > 302) then the `ss-broken` css class will be assigned to that url, and 
a line item will be added to the report. If a previously broken link has been corrected or fixed, then
this class is removed.

In the actual report generated the user can click on any broken link item to either view the link in their browser,
or edit the containing page in the CMS.

While a report is running the current status of this report will be displayed on the report details page, along
with the status. The user may leave this page and return to it later to view the ongoing status of this report.

Any subsequent report may not be generated until a prior report has completed.

## Dev task ##

Run the following task *http://path.to.silverstripe/dev/tasks/CheckExternalLinks* to check your site for external
broken links.

## Queued job ##

If you have the queuedjobs module installed you can set the task to be run every so often
Add the following yml config to config.yml in mysite/_config have the the task run once every day (86400 seconds)

    CheckExternalLinks:
      Delay: 86400

## Whitelisting codes ##

If you want to ignore or whitelist certain http codes this can be setup via IgnoreCodes in the config.yml
file in mysite/_config

    CheckExternalLinks:
      Delay: 60
      IgnoreCodes:
        - 401
        - 403
        - 501
