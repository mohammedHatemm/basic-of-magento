<?php
namespace Elshrif\HelloWorld;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Elshrif\HelloWorld\Api\Data\EntityInterface;
use Elshrif\HelloWorld\Model\ResourceModel\Entity AS ResourceModel;


class Entity extends AbstractModel implements EntityInterface , IdentityInterface
{

    protected function _construct()
    {

    }

}

