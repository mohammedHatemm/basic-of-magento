<?php

declare(strict_types=1);

namespace Elshrif\Loyalty\Model;

use Elshrif\Loyalty\Api\PointsRepositoryInterface;
use Elshrif\Loyalty\Api\Data\PointsInterface;
use Elshrif\Loyalty\Model\ResourceModel\Points as PointsResource;
use Elshrif\Loyalty\Model\ResourceModel\Points\CollectionFactory;
use Magento\Framework\Exception\CouldNotDeleteException;
use Magento\Framework\Exception\CouldNotSaveException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\LocalizedException;
use Psr\Log\LoggerInterface;

class PointsRepository implements PointsRepositoryInterface
{
    public function __construct(
        private PointsFactory $pointsFactory,
        private PointsResource $pointsResource,
        private CollectionFactory $collectionFactory,
        private LoggerInterface $logger
    ) {}

    /**
     * {@inheritdoc}
     */
    public function getById(int $entityId): PointsInterface
    {
        $points = $this->pointsFactory->create();
        $this->pointsResource->load($points, $entityId);

        if (!$points->getEntityId()) {
            throw new NoSuchEntityException(
                __('Points record with ID "%1" does not exist.', $entityId)
            );
        }
        return $points;
    }

    /**
     * {@inheritdoc}
     */
    public function getCustomerId(int $customerId): PointsInterface
    {
        $collection = $this->collectionFactory->create();
        $collection->addFieldToFilter('customer_id', $customerId);

        /** @var PointsInterface $points */
        $points = $collection->getFirstItem();

        if (!$points->getEntityId()) {
            throw new NoSuchEntityException(
                __('Points record for customer "%1" does not exist.', $customerId)
            );
        }
        return $points;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrCreate(int $customerId): PointsInterface
    {
        try {
            return $this->getCustomerId($customerId);
        } catch (NoSuchEntityException $e) {
            $points = $this->pointsFactory->create();
            $points->setCustomerId($customerId)
                ->setPointsBalance(0)
                ->setTotalEarned(0)
                ->setTotalSpent(0);
            return $points;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function save(PointsInterface $points): PointsInterface
    {
        try {
            $this->pointsResource->save($points);
        } catch (\Exception $e) {
            $this->logger->error('Error saving points: ' . $e->getMessage());
            throw new CouldNotSaveException(
                __('Could not save points: %1', $e->getMessage())
            );
        }
        return $points;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(PointsInterface $points): PointsInterface
    {
        try {
            $this->pointsResource->delete($points);
        } catch (\Exception $e) {
            throw new CouldNotDeleteException(
                __('Could not delete: %1', $e->getMessage())
            );
        }
        return $points;
    }

    /**
     * {@inheritdoc}
     */
    public function deleteById(int $entityId): bool
    {
        $points = $this->getById($entityId);
        $this->delete($points);
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function addPoints(int $customerId, int $points, string $reason = ''): PointsInterface
    {
        $pointsRecord = $this->getOrCreate($customerId);

        $newBalance = $pointsRecord->getPointsBalance() + $points;
        $newTotalEarned = $pointsRecord->getTotalEarned() + $points;

        $pointsRecord->setPointsBalance($newBalance)
            ->setTotalEarned($newTotalEarned);

        $this->save($pointsRecord);

        $this->logger->info(sprintf(
            'Added %d points to customer %d. Reason: %s. New balance: %d',
            $points,
            $customerId,
            $reason,
            $newBalance
        ));

        return $pointsRecord;
    }

    /**
     * {@inheritdoc}
     */
    public function spendPoints(int $customerId, int $pointsToSpend, string $reason = ''): PointsInterface
    {
        $pointsRecord = $this->getOrCreate($customerId);

        if ($pointsRecord->getPointsBalance() < $pointsToSpend) {
            throw new LocalizedException(__(
                'Insufficient points. Balance: %1, Required: %2',
                $pointsRecord->getPointsBalance(),
                $pointsToSpend
            ));
        }

        $newBalance = $pointsRecord->getPointsBalance() - $pointsToSpend;
        $newTotalSpent = $pointsRecord->getTotalSpent() + $pointsToSpend;

        $pointsRecord->setPointsBalance($newBalance)
            ->setTotalSpent($newTotalSpent);

        $this->save($pointsRecord);

        $this->logger->info(sprintf(
            'Spent %d points from customer %d. Reason: %s. New balance: %d',
            $pointsToSpend,
            $customerId,
            $reason,
            $newBalance
        ));

        return $pointsRecord;
    }
}
