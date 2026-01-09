<?php

declare(strict_types=1);

namespace Elshrif\Loyalty\Api\Data;
use Magento\Framework\Exception\LocalizedException;

interface PointsInterface
{

  public const ENTITY_ID = 'entity_id';
  public const CUSTOMER_ID = 'customer_id';
  public const POINTS_BALANCE = 'points_balance';
  public const TOTAL_EARNED = 'total_earned';
  public const TOTAL_SPENT = 'total_spent';
  public const UPDATED_AT = 'updated_at';

  /** @return int|null*/


  public function getEntityId(): int;

  /** @return int*/

  public function getCustomerId(): int;

  /** @return int*/


  public function getPointsBalance(): ?int;

  /** @return int*/

  public function getTotalEarned(): ?int;

  /** @return int*/

  public function getTotalSpent(): ?int;


  /**
   *@return int|null
   *
   */


  public function getUpdatedAt(): string;

  /**
   * Set Customer ID
   *
   * @param int $customerId
   * @return PointsInterface - Fluent return
   */


  public function setCustomerId(int $customerId): PointsInterface;


  /**
   *
   * set Points Balance
   * @param int $balance
   * @return PointsInterface
   *
   */

  public function setPointsBalance(int $balance): PointsInterface;

  /**
   *
   * set Total Earned
   *
   * @param int $total
   * @return PointsInterface
   *
   */
  public function setTotalEarned(int $total): PointsInterface;

  /**
   * set Total Spend
   * @param int $total
   * @return PointsInterface
   */
  public function setTotalSpent(int $total): PointsInterface;
}
