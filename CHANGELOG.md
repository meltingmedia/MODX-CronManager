# Changelog


## [unreleased]

* Moved packaging to [GPM](https://github.com/theboxer/Git-Package-Management)


## [v1.1.6-dev2] - 2014-04-05

* Fixed issue were jobs were not run due to getIterator instead of getCollection
* Prevent modMenu upgrade (in case menu have been moved out of "components" parent)

## v1.1.6-dev

* Code cleanup
* Setting next run before each job is run


## [v1.1.5-beta2] - 2013-03-26

* Fixes


## v1.1.5-beta

* Fixed issue when newly added jobs were executed event if not activated
* Brand new UI
* Class based controllers + ExtJS cleanup
* Meta data for mysql + map files update
* Optimised query to count logs
* Ability to run a "job" from the grid
* Prevent timeouts when there is a lot of logs


## v1.1.2-beta

* Ability to filter snippets per category (combo box)
* Added the number of logs of each job
* Added snippet description in the grid


## v1.1.1-beta

* Fixed pagination when purging logs


## [v1.1.0-pl] - 2012-04-29

* Fixed some UI stuff
* Batch purge logs without error
* Filter logs containing errors
* Added an error field to logs


## v1.0.1-rc1

* Enable viewing longer logs messages in a window
* Prevent having negative values in numberFields
* Grid dates now supports manager_*_format system settings
* Cron job next run editable via the grid
* Some addition to the logs grid
* Made the UI 2.2 compatible


## v1.0

* Initial commit


[unreleased]: https://github.com/meltingmedia/MODX-CronManager/compare/v1.1.6-dev2...HEAD
[v1.1.6-dev2]: https://github.com/meltingmedia/MODX-CronManager/compare/v1.1.5-beta2...v1.1.6-dev2
[v1.1.5-beta2]: https://github.com/meltingmedia/MODX-CronManager/compare/v1.1.0-pl...v1.1.5-beta2
[v1.1.0-pl]: https://github.com/meltingmedia/MODX-CronManager/commits/v1.1.0-pl
