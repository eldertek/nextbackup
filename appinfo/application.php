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

use OCP\AppFramework\App;
use OCA\NextBackup\Controller\AdminController;
use OCA\NextBackup\Service\BackupService;
use OCA\NextBackup\Service\ConfigService;
use OCP\IDBConnection;
use OCP\ILogger;

class Application extends App
{
    public function __construct(array $urlParams = [])
    {
        parent::__construct('nextbackup', $urlParams);

        $container = $this->getContainer();
        $container->registerService('AdminController', function($c) {
            return new AdminController(
                $c->query('AppName'),
                $c->query('Request'),
                $c->query('BackupService')
            );
        });

        $container->registerService(BackupService::class, function($c) {
            return new BackupService(
                $c->query('AppName'),
                $c->query(IDBConnection::class),
                $c->query(ConfigService::class),
                $c->query(ILogger::class),
                $c->query('UserId')
            );
        });
        $container->registerService('BackupService', function($c) {
            return $c->query(BackupService::class);
        });
    }

    public function registerSettings() {
        // Register settings scripts
        if (method_exists('\OCP\App', 'registerAdmin')) {
            \OCP\App::registerAdmin('nextbackup', 'admin');
        }
    }

}
