<?php
declare(strict_types=1);


namespace Elshri\Loyalty\Model;
use Elshrif\Loyalty\Model\Points as PointsModel;
use Elshrif\Loyalty\Model\ResourceModel\Points as PointsResource;





use Psr\Log\LoggerInterface;

class PointsRepository
{
    public function __construct
    (
        private PointFactory $pointFactory,
        private PointResouce  $pointResouce,
        private CollecionFactory $collecionFactory,
        private LoggerInterface $logger
    )

    {}





}
