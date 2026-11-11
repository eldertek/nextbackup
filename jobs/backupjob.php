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

namespace OCA\NextBackup\Jobs;

use OCP\BackgroundJob\TimedJob;
use OCP\AppFramework\Utility\ITimeFactory;
use OCP\App\IAppManager;
use OCA\NextBackup\AppInfo\Application;
use OCA\NextBackup\Service\BackupService;

class BackupJob extends TimedJob
{
    public function __construct(?ITimeFactory $time = null)
    {
        if ($time !== null) {
            parent::__construct($time);
        } else {
            try {
                parent::__construct(\OCP\Server::get(ITimeFactory::class));
            } catch (\Throwable $e) {
                // fallback
            }
        }
        $this->setInterval( 600 ); // check every 10 minutes if we need a new backup
    }

    /**
     * @param mixed $arguments
     * @throws \OCP\AppFramework\QueryException
     */
    protected function run( $arguments ): void
    {
        try {
            if ( !\OCP\Server::get( IAppManager::class )->isInstalled( 'nextbackup' ) )
            {
                return;
            }
        } catch ( \Throwable $e ) {
            return;
        }

        $app = new Application();
        $container = $app->getContainer();

        /** @var BackupService $backupService */
        $backupService = $container->query('OCA\NextBackup\Service\BackupService');

        // check if we need a new backup
        if ( $backupService->needNewBackup() )
        {
            // create new backup
            $backupService->createDBBackup();

            // expiring old backups
            $backupService->expireOldBackups();
        }
    }
}
