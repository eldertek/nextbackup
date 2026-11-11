<?php
/**
 * ownCloud - nextbackup
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Patrizio Bekerle <patrizio@bekerle.com>
 * @copyright Patrizio Bekerle 2015
 */

namespace OCA\NextBackup\AppInfo;

use OCA\NextBackup\Jobs\BackupJob;
use OCP\BackgroundJob\IJobList;

$app = new Application();
$app->registerSettings();

try {
    $jobList = \OCP\Server::get(IJobList::class);
    if (!$jobList->has(BackupJob::class, null)) {
        $jobList->add(BackupJob::class);
    }
} catch (\Throwable $e) {
    // Registered via info.xml
}
