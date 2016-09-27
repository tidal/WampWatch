<?php


namespace Tidal\WampWatch\Model;

use Tidal\WampWatch\Model\Property\Scalar\HasUriTrait;
use Tidal\WampWatch\Model\Contract;

class Topic implements Contract\TopicInterface
{
    use HasUriTrait;

    public function __construct($uri)
    {
        $this->setUri($uri);
    }

}