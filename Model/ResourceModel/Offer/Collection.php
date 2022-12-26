<?php
/**
 * @author    Vjackk <vincentjacquemin34@gmail.com>
 * @copyright 2022 Vjackk
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Vjackk\Offer\Model\ResourceModel\Offer;

use Exception;
use Magento\Catalog\Model\Category;
use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Psr\Log\LoggerInterface;
use Vjackk\Offer\Api\Data\OfferInterface;

class Collection extends AbstractCollection
{
    /**
     * @var string
     */
    protected $_idFieldName = 'offer_id';

    /**
     * Event prefix
     *
     * @var string
     */
    protected $_eventPrefix = 'offer_offer_collection';

    /**
     * Event object
     *
     * @var string
     */
    protected $_eventObject = 'offer_offer_collection';

    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @param EntityFactoryInterface $entityFactory
     * @param LoggerInterface $logger
     * @param FetchStrategyInterface $fetchStrategy
     * @param ManagerInterface $eventManager
     * @param MetadataPool $metadataPool
     * @param AdapterInterface|null $connection
     * @param AbstractDb|null $resource
     */
    public function __construct(
        EntityFactoryInterface $entityFactory,
        LoggerInterface $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface $eventManager,
        MetadataPool $metadataPool,
        AdapterInterface $connection = null,
        AbstractDb $resource = null
    ) {
        $this->metadataPool = $metadataPool;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    /**
     * Resource initialization
     *
     * @return void
     */
    protected function _construct()
    {
        $this->_init(\Vjackk\Offer\Model\Offer::class, \Vjackk\Offer\Model\ResourceModel\Offer::class);
        $this->_map['fields']['category_id'] = 'category_table.category_id';
        $this->_map['fields']['offer_id'] = 'main_table.offer_id';
    }

    /**
     * Add field filter to collection
     *
     * @param array|string $field
     * @param string|int|array|null $condition
     * @return $this
     */
    public function addFieldToFilter($field, $condition = null)
    {
        if ($field === 'category_id') {
            return $this->addCategoryFilter($condition);
        }

        return parent::addFieldToFilter($field, $condition);
    }

    /**
     * Add filter by category
     *
     * @param int|array|Category $category
     * @return $this
     */
    public function addCategoryFilter($category)
    {
        if ($category instanceof Category) {
            $category = [$category->getId()];
        }

        if (!is_array($category)) {
            $category = [$category];
        }

        $this->addFilter('category_id', ['in' => $category], 'public');

        return $this;
    }

    /**
     * Join category relation table if there is category filter
     *
     * @return void
     * @throws Exception
     */
    protected function _renderFiltersBefore()
    {
        $entityMetadata = $this->metadataPool->getMetadata(OfferInterface::class);
        $this->joinCategoryRelationTable('vjackk_offer_category', $entityMetadata->getLinkField());
    }

    /**
     * Join category relation table if there is category filter
     *
     * @param string $tableName
     * @param string|null $linkField
     * @return void
     */
    protected function joinCategoryRelationTable($tableName, $linkField)
    {
        if ($this->getFilter('category_id')) {
            $this->getSelect()->join(
                ['category_table' => $this->getTable($tableName)],
                'main_table.' . $linkField . ' = category_table.offer_id',
                []
            )->group(
                'main_table.' . $linkField
            );
        }

        parent::_renderFiltersBefore();
    }
}
