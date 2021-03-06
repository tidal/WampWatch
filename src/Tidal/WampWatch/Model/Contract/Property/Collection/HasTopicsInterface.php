<?php

/*
 * This file is part of the Tidal/WampWatch package.
 *   (c) 2016 Timo Michna <timomichna/yahoo.de>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 *
 */

namespace Tidal\WampWatch\Model\Contract\Property\Collection;

use Tidal\WampWatch\Model\Contract;

interface HasTopicsInterface
{
    public function addTopic(Contract\TopicInterface $topic);

    public function hasTopic($uri);

    /**
     * @param string $uri
     *
     * @return Contract\TopicInterface
     */
    public function getTopic($uri);

    /**
     * @return \Generator
     */
    public function listTopics();
}
