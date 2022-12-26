<?php
/**
 * @author    Vjackk <vincentjacquemin34@gmail.com>
 * @copyright 2022 Vjackk
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Vjackk\Offer\Block\Offer;

use Magento\Catalog\Api\Data\CategoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Registry;
use Magento\Framework\View\Element\Template;
use Vjackk\Offer\Api\Data\OfferInterface;
use Vjackk\Offer\Api\OfferManagementInterface;
use Vjackk\Offer\Helper\Image as ImageHelper;

class OfferList extends Template
{
    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * @var OfferManagementInterface
     */
    protected $offerManagement;

    /**
     * @var Registry
     */
    protected $registry;

    /**
     * @var ImageHelper
     */
    protected $imageHelper;

    /**
     * @param Template\Context $context
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     * @param OfferManagementInterface $offerManagement
     * @param Registry $registry
     * @param ImageHelper $imageHelper
     * @param array $data
     */
    public function __construct(
        Template\Context $context,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        OfferManagementInterface $offerManagement,
        Registry $registry,
        ImageHelper $imageHelper,
        array $data = []
    ) {
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->offerManagement = $offerManagement;
        $this->registry = $registry;
        $this->imageHelper = $imageHelper;
        parent::__construct($context, $data);
    }

    /**
     * @return \Vjackk\Offer\Api\Data\OfferInterface[]
     */
    public function getOfferList()
    {
        /** @var CategoryInterface $currentCategory */
        $currentCategory = $this->registry->registry('current_category');

        return $this->offerManagement->getListByCategoryId($currentCategory->getId());
    }

    /**
     * @param OfferInterface $offer
     * @return string
     */
    public function getImageUrl($offer)
    {
        return $this->imageHelper->getUrl($offer);
    }
}
