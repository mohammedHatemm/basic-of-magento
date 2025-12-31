<?php
namespace Elshrif\HelloWorld;

use Magento\Framework\DataObject\IdentityInterface;
use Magento\Framework\Model\AbstractModel;
use Elshrif\HelloWorld\Api\Data\EntityInterface;
use Elshrif\HelloWorld\Model\ResourceModel\Entity AS ResourceModel;


class Entity extends AbstractModel implements EntityInterface, IdentityInterface
{

    public const CACHE_TAG = 'hello_world_entity';
    protected $_chacheTag = self::CACHE_TAG;
    protected  $_eventPrefix = 'hello_world_entity';
    protected $_eventObject = 'entity';


    protected function _construct()
    {
        $this->_init(ResourceModel::class);

    }

    public function getIdentities()
    {
        // TODO: Implement getIdentities() method.
        return [self::CACHE_TAG . '_' . $this->getId()];
    }

    public function getEntityId():?int
    {
        return $this->getData(self::ENTITY_ID) ? (int) $this->getData(self::ENTITY_ID) : null;
    }

    public function setEntityId(int $entityId):EntityInterface
    {
        return $this->setData(self::ENTITY_ID , $entityId);
    }

    public function getName():?string
    {
        return $this->getData(self::NAME);
    }

}

