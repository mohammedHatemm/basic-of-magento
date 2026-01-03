<?php

namespace Elshrif\Loyalty\Api;
/*
     * interface for api
     * */

use Elshrif\Loyalty\Api\Data\PointsInterface;
use Elshrif\Loyalty\Model\PointsRepository;

interface PointsRepositoryInterface
{

    /**
     * @param int $entityId
     * @return PointsInterface
     * @throws NoSuchEntityException
     *
     *
     */

    public function getById(int $entityId): PointsInterface;

    /**
     * @param  int $customerId
     * @return PointsInterface
     * @throws NoSuchEntityException
     *
     *
     */

    public  function getCustomerId(int $customerId): PointsInterface;

    /**
     *
     * @param  PointsInterface $points
     * @return PointsInterface
     * @throws CouldNotSaveException
     *
     *
     */


    public function save(PointsInterface $points): PointsInterface;


    /**
     * @param PointsInterface $points
     * @return PointsInterface
     * @throws CouldNotDeleteException
     */
    public function delete(PointsInterface $points): PointsInterface;


    /**
     * @param int $entityId
     * @return bool
     * @throws CouldNotDeleteException
     * @throws NoSuchEntityException
     */

    public function deleteById(int $entityId): bool;


    /**
     * @param int $customerId
     * @param int $points
     * @param string $reason
     * @return PointInterface
     * @throws CouldNotSaveException
     */
    public function addPoints(
        int $customerId,
        int $Points,
        string $reason = ''
    ): PointsInterface;

    /**
     * @param int $customreId
     * @param int $pointsToSpend
     * @param string $reason
     * @return PointsInterface
     * @throws CouldNotSaveException
     * @throws LocalizedException
     */

    public function spendPoints(
        int $customerId,
        int $pointsToSpend,
        string $reason
    ): PointsInterface;

    /**
     *  @param int $customerId
     * @return  PointsInterface
     *
     */
    public function getOrCreate(int $customerId ) : PointsInterface; 


}
