<?php
declare(strict_types=1);



class Points extends AbstractModel implements  PointsInterface
{
    public function __construct()
    {
        $this->_init(PointsResouce::class);
    }
}

