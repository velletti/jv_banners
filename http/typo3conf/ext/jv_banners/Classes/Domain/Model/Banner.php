<?php
namespace JVE\JvBanners\Domain\Model;

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

    /**
     * @return int
     */
    public function getEndtime()
    {
        return $this->endtime;
    }

    /**
     * @param int $endtime
     */
    public function setEndtime($endtime)
    {
        $this->endtime = $endtime;
    }

    /**
     * @return int
     */
    public function getStarttime()
    {
        return $this->starttime;
    }

    /**
     * @param int $starttime
     */
    public function setStarttime($starttime)
    {
        $this->starttime = $starttime;
    }

    /**
     * @param int $lng
     */
    public function setLanguage($lng)
    {
        $this->_languageUid = $lng;
    }

    /**
     * @param int $uid
     */
    public function setversionedUid($uid)
    {
        $this->_versionedUid = $uid;
    }
}
