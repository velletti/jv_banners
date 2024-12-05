<?php
namespace JVelletti\JvBanners\Domain\Repository;

use TYPO3\CMS\Extbase\Persistence\QueryInterface;

/***
 *
 * This file is part of the "Banner Guthaben" Extension for TYPO3 CMS.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 *  (c) 2019 Joerg Velletti <typo3@velletti.de>
 *
 ***/

/**
 * The repository for Connectors
 */
class BannerRepository extends \DERHANSEN\SfBanners\Domain\Repository\BannerRepository
{
    protected $defaultOrderings = ['endtime' => QueryInterface::ORDER_DESCENDING];
    }
