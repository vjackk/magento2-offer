<?php
/**
 * @author    Vjackk <vincentjacquemin34@gmail.com>
 * @copyright 2022 Vjackk
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Vjackk\Offer\Model\ResourceModel\Offer\Relation\Category;

use Magento\Framework\EntityManager\Operation\ExtensionInterface;
use Magento\Framework\Exception\LocalizedException;
use Vjackk\Offer\Model\ResourceModel\Offer;

class ReadHandler implements ExtensionInterface
{
    /**
     * @var Offer
     */
    protected $resourceOffer;

    /**
     * @param Offer $resourceOffer
     */
    public function __construct(
        Offer $resourceOffer
    ) {
        $this->resourceOffer = $resourceOffer;
    }

    /**
     * @param object $entity
     * @param array $arguments
     * @return object
     * @throws LocalizedException
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function execute($entity, $arguments = [])
    {
        if ($entity->getId()) {
            $categories = $this->resourceOffer->lookupCategoryIds((int)$entity->getId());
            $entity->setData('category_id', $categories);
            $entity->setData('categories', $categories);
        }
        return $entity;
    }
}
