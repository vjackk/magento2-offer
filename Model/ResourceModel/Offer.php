<?php
/**
 * @author    Vjackk <vincentjacquemin34@gmail.com>
 * @copyright 2022 Vjackk
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Vjackk\Offer\Model\ResourceModel;

use Magento\Framework\DB\Select;
use Magento\Framework\EntityManager\EntityManager;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime;
use Vjackk\Offer\Api\Data\OfferInterface;

class Offer extends AbstractDb
{
    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var DateTime
     */
    protected $dateTime;

    /**
     * @param Context $context
     * @param MetadataPool $metadataPool
     * @param EntityManager $entityManager
     * @param DateTime $dateTime
     * @param string $connectionName
     */
    public function __construct(
        Context $context,
        MetadataPool $metadataPool,
        EntityManager $entityManager,
        DateTime $dateTime,
        $connectionName = null
    ) {
        parent::__construct($context, $connectionName);
        $this->metadataPool = $metadataPool;
        $this->entityManager = $entityManager;
        $this->dateTime = $dateTime;
    }

    /**
     * Initialize table with PK name
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init('vjackk_offer', 'offer_id');
    }

    /**
     * @inheritDoc
     */
    public function getConnection()
    {
        return $this->metadataPool->getMetadata(OfferInterface::class)->getEntityConnection();
    }

    /**
     * @inheritDoc
     */
    public function save(AbstractModel $object)
    {
        $this->entityManager->save($object);
        return $this;
    }

    /**
     * Get offer identifier
     *
     * @param AbstractModel $object
     * @param string $value
     * @param string|null $field
     * @return bool|int|string
     * @throws LocalizedException
     * @throws \Exception
     */
    private function getOfferId(AbstractModel $object, $value, $field = null)
    {
        $entityMetadata = $this->metadataPool->getMetadata(OfferInterface::class);

        if (!$field || (!is_numeric($value) && $field === null)) {
            $field = $entityMetadata->getIdentifierField();;
        }

        $offerId = $value;
        if ($field != $entityMetadata->getIdentifierField() || $object->getCategoryId()) {
            $select = $this->_getLoadSelect($field, $value, $object);
            $select->reset(Select::COLUMNS)
                ->columns($this->getMainTable() . '.' . $entityMetadata->getIdentifierField())
                ->limit(1);
            $result = $this->getConnection()->fetchCol($select);
            $offerId = count($result) ? $result[0] : false;
        }
        return $offerId;
    }

    /**
     * Retrieve select object for load object data
     *
     * @param string $field
     * @param mixed $value
     * @param Offer|AbstractModel $object
     * @return Select
     */
    protected function _getLoadSelect($field, $value, $object)
    {
        $entityMetadata = $this->metadataPool->getMetadata(OfferInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = parent::_getLoadSelect($field, $value, $object);

        if ($object->getCategoryId()) {
            $categoryIds = [
                (int)$object->getCategoryId(),
            ];
            $select->join(
                ['vjackk_offer_category' => $this->getTable('vjackk_offer_category')],
                $this->getMainTable() . '.' . $linkField . ' = vjackk_offer_category.' . $linkField,
                []
            )
                ->where('vjackk_offer_category.category_id IN (?)', $categoryIds)
                ->order('vjackk_offer_category.category_id DESC');
        }

        return $select;
    }

    /**
     * Load an object
     *
     * @param Offer|AbstractModel $object
     * @param mixed $value
     * @param string $field field to load by (defaults to model id)
     * @return $this
     */
    public function load(AbstractModel $object, $value, $field = null)
    {
        $offerId = $this->getOfferId($object, $value, $field);
        if ($offerId) {
            $this->entityManager->load($object, $offerId);
        }
        return $this;
    }

    /**
     * Get category ids to which specified item is assigned
     *
     * @param int $offerId
     * @return array
     * @throws LocalizedException
     */
    public function lookupCategoryIds($offerId)
    {
        $connection = $this->getConnection();

        $entityMetadata = $this->metadataPool->getMetadata(OfferInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $select = $connection->select()
            ->from(['voc' => $this->getTable('vjackk_offer_category')], 'category_id')
            ->join(
                ['vo' => $this->getMainTable()],
                'voc.offer_id = vo.' . $linkField,
                []
            )
            ->where('vo.' . $entityMetadata->getIdentifierField() . ' = :offer_id');

        return $connection->fetchCol($select, ['offer_id' => (int)$offerId]);
    }

    /**
     * Process page data before saving
     *
     * @param AbstractModel $object
     * @return $this
     * @throws LocalizedException
     */
    protected function _beforeSave(AbstractModel $object)
    {
        if ($object->hasDataChanges()) {
            $object->setUpdatedAt(null);
        }

        /*
         * For two attributes which represent timestamp data in DB
         * we should make converting such as:
         * If they are empty we need to convert them into DB
         * type NULL so in DB they will be empty and not some default value
         */
        foreach ([OfferInterface::START_DATE, OfferInterface::END_DATE] as $field) {
            $value = !$object->getData($field) ? null : $this->dateTime->formatDate($object->getData($field));
            $object->setData($field, $value);
        }

        return parent::_beforeSave($object);
    }
}
