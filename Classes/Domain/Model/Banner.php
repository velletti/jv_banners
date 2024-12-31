<?php
namespace JVelletti\JvBanners\Domain\Model;

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
 * Connect a JVE Events Event to a SF Banner Object
 */
class Banner extends \DERHANSEN\SfBanners\Domain\Model\Banner
{
    /** @var integer */
    protected $starttime ;

    /** @var integer */
    protected $endtime ;

    /** @var integer */
    protected $feUser ;

    /** @var integer */
    protected $organizer ;

    /** @var string */
    protected string $link ;

    /**
     * @return int
     */
    public function getEndtime(): int
    {
        return $this->endtime;
    }

    /**
     * @param int $endtime
     */
    public function setEndtime($endtime): void
    {
        $this->endtime = $endtime;
    }

    /**
     * @return int
     */
    public function getStarttime(): int
    {
        return $this->starttime;
    }

    /**
     * @param int $starttime
     */
    public function setStarttime($starttime): void
    {
        $this->starttime = $starttime;
    }

    /**
     * @param int $lng
     */
    public function setLanguage($lng): void
    {
        $this->_languageUid = $lng;
    }

    /**
     * @param int $uid
     */
    public function setversionedUid($uid): void
    {
        $this->_versionedUid = $uid;
    }

    public function getLink(): string
    {
        return $this->link;
    }

    public function setLink(string $link): void
    {
        $this->link = $link;
    }

    public function getFeUser(): int
    {
        return $this->feUser;
    }

    public function setFeUser(int $feUser): void
    {
        $this->feUser = $feUser;
    }

    public function getOrganizer(): int
    {
        return $this->organizer;
    }
    public function setOrganizer(int $organizer): void
    {
        $this->organizer = $organizer;
    }




}
