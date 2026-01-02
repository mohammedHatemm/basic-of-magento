<?php

namespace Elshrif\Loyalty\Model;
use Elshrif\Loyalty\Model\PointsFactory;
use Elshrif\Loyalty\Model\ResourceModel\Points as PointsResource;
use Elshrif\Loyalty\Model\ResourceModel\Points\CollectionFactory;
use Psr\Log\LoggerInterface;


class PointsRepository
{
    public function __construct(
        private PointsFactory $pointsFactory,
        private PointsResource $pointsResource,
        private CollectionFactory $collectionFactory,
        private LoggerInterface $logger
    )
    {}

    /*
     * GEt points by Customer
     * */

    public function getByCustomerId(int $customerId): Points
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('customer_id', $customerId);
        /** @var Points $points */

        $points = $collection->getFirstItem();
        if (!$points ->getEntityId()) {
            $points= $this->pointsFactory->create();
            $points->setCustomerId($customerId);
            $points->setPointsBalance(0);
            $points->setTotalSpent(0);
            $points->setTotalEarned(0);

        }
        return $points;


    }

    /*
     * save Points
     * */


    public function save(Points $points): Points
    {

        try{
            $this->pointsResource->save($points);
        }
        catch (\Exception $exception){
            $this->logger->error( 'Error saving points'.$exception->getMessage());
            throw $exception;
        }
        return $points;

    }

    /*
     * add points to Customer
     * */

    public function addPoint(int $customerId , int $pointsToAdd , string $reason=''): Points
    {
        $points = $this->getByCustomerId($customerId);
        $newBalancePoint=$points->getPointsBalance() + $pointsToAdd;
        $newTotalEarnedPoints = $points->getTotalEarnings() + $pointsToAdd;
        $points->setPointsBalance($newBalancePoint);
        $points->setTotalEarned($newTotalEarnedPoints);
        $this->pointsResource->save($points);

        $this->logger->info(sprintf('Add %d Points to customer %d . Reason: %s',
            $pointsToAdd,
            $customerId,
            $reason
            )
        );


        return $points;


    }

    /*
     * spent points
     * */
    public function spendPoints(int $customerId , int $pointsToSpend , string $reason= ''): Points
    {
        $points = $this->getByCustomerId($customerId);
        if($points->getPointsBalance() < $pointsToSpend){
            throw new \Exception('Points balance is not enough to spend');
        }

        $newBalance = $points->getPointsBalance() - $pointsToSpend;
        $newTotalSpend = $points->getTotalSpent() + $pointsToSpend;

        $points->setPointsBalance($newBalance);
        $points->setTotalSpent($newTotalSpend);
        $this->pointsResource->save($points);
        $this->logger->info(sprintf('Add %d Points to customer %d . Reason: %s',
        $pointsToSpend,
        $customerId,
        $reason
        ));
        return $points;

    }

}
