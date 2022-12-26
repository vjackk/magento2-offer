<?php
/**
 * @author    Vjackk <vincentjacquemin34@gmail.com>
 * @copyright 2022 Vjackk
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Vjackk\Offer\Model\ResourceModel\Offer\Relation\Category;

use Exception;
use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\EntityManager\MetadataPool;
use Vjackk\Offer\Api\Data\OfferInterface;
use Vjackk\Offer\Model\ResourceModel\Offer;

class SaveHandler implements ExtensionInterface
{
    /**
     * @var MetadataPool
     */
    protected $metadataPool;

    /**
     * @var Offer
     */
    protected $resourceOffer;

    /**
     * @param MetadataPool $metadataPool
     * @param Offer $resourceOffer
     */
    public function __construct(
        MetadataPool $metadataPool,
        Offer $resourceOffer
    ) {
        $this->metadataPool = $metadataPool;
        $this->resourceOffer = $resourceOffer;
    }

    /**
     * @param object $entity
     * @param array $arguments
     * @return object
     * @throws Exception
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        $entityMetadata = $this->metadataPool->getMetadata(OfferInterface::class);
        $linkField = $entityMetadata->getLinkField();

        $connection = $entityMetadata->getEntityConnection();

        $oldCategories = $this->resourceOffer->lookupCategoryIds((int)$entity->getId());
        $newCategories = (array)$entity->getCategories();

        $table = $this->resourceOffer->getTable('vjackk_offer_category');

        $delete = array_diff($oldCategories, $newCategories);
        if ($delete) {
            $where = [
                'offer_id' . ' = ?' => (int)$entity->getData($linkField),
                'category_id IN (?)' => $delete,
            ];
            $connection->delete($table, $where);
        }

        $insert = array_diff($newCategories, $oldCategories);
        if ($insert) {
            foreach ($insert as $categoryId) {
                $data[] = [
                    'offer_id' => (int)$entity->getData($linkField),
                    'category_id' => (int)$categoryId,
                ];
            }
            $connection->insertMultiple($table, $data);
        }

        return $entity;
    }
}
