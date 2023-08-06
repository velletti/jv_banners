<?php
namespace JVE\JvBanners\Domain\Model;

use TYPO3\CMS\Extbase\DomainObject\AbstractEntity;
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
class Connector extends AbstractEntity
{
    /**
     * eventname
     *
     * @var string
     */
    protected $eventname = '';

    /**
     * Returns the eventname
     *
     * @return string $eventname
     */
    public function getEventname()
    {
        return $this->eventname;
    }

    /**
     * Sets the eventname
     *
     * @param string $eventname
     * @return void
     */
    public function setEventname($eventname)
    {
        $this->eventname = $eventname;
    }
}
