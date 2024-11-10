<?php

declare(strict_types=1);

/*
 * This file is part of the "news" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 */

namespace JVelletti\JvBanners\Updates;

use TYPO3\CMS\Core\Database\Connection;
use TYPO3\CMS\Core\Database\ConnectionPool;
use TYPO3\CMS\Core\Database\Query\Restriction\DeletedRestriction;
use TYPO3\CMS\Core\Information\Typo3Version;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Install\Updates\DatabaseUpdatedPrerequisite;

use TYPO3\CMS\Install\Attribute\UpgradeWizard;
use TYPO3\CMS\Install\Updates\UpgradeWizardInterface;

#[UpgradeWizard('bannerUpdateWizard')]
class BannerUpdateWizard implements UpgradeWizardInterface
{

    public function getTitle(): string
    {
        return 'EXT:JvBanner: Update Link to JvEvent ';
    }

    public function getDescription(): string
    {
        return 'This update wizard updates link field in sysfile reference from old jvevents_events to jv_events_event single view plugin';
    }
    public function getPrerequisites(): array
    {
        return [
        ];
    }

    public function updateNecessary(): bool
    {
        return $this->checkIfWizardIsRequired();
    }

    public function executeUpdate(): bool
    {
        return $this->performMigration();
    }

    public function checkIfWizardIsRequired(): bool
    {
        return true ;
    }

    public function performMigration(): bool
    {
        $records = $this->getMigrationRecords();
        $i = 0;
        foreach ($records as $record) {
            $i++;
            $this->updateRow($record);
        }
        echo "Number of changed Links in Banner Records : " . $i ;
        return true;
    }

    protected function getMigrationRecords(): array
    {
        $connectionPool = GeneralUtility::makeInstance(ConnectionPool::class);
        $queryBuilder = $connectionPool->getQueryBuilderForTable('sys_file_reference');
        $queryBuilder->getRestrictions()->removeAll()->add(GeneralUtility::makeInstance(DeletedRestriction::class));

        return $queryBuilder
            ->select('uid', 'link')
            ->from('sys_file_reference')
            ->where(
                $queryBuilder->expr()->like(
                    'link',
                    $queryBuilder->createNamedParameter('%'
                        . $queryBuilder->escapeLikeWildcards('tx_jvevents_events%5Baction%5D=show&tx_jvevents_events%5Bevent') . '%')
                )
            )
            ->executeQuery()
            ->fetchAllAssociative();
    }

    protected function updateRow(array $row): void
    {
        $newLink = str_replace(
             "tx_jvevents_events%5Baction%5D=show&tx_jvevents_events%5Bevent",
             "tx_jvevents_event%5Baction%5D=show&tx_jvevents_event%5Bevent",
            $row['link']
        ) ;

        $queryBuilder = GeneralUtility::makeInstance(ConnectionPool::class)->getQueryBuilderForTable('sys_file_reference');
        $queryBuilder->update('sys_file_reference')
            ->set('link', $newLink)
            ->where(
                $queryBuilder->expr()->eq(
                    'uid',
                    $queryBuilder->createNamedParameter($row['uid'], Connection::PARAM_INT)
                )
            )
            ->executeStatement();
    }
}
