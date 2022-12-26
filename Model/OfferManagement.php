<?php
/**
 * @author    Vjackk <vincentjacquemin34@gmail.com>
 * @copyright 2022 Vjackk
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
namespace Vjackk\Offer\Model;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Vjackk\Offer\Api\Data\OfferInterface;
use Vjackk\Offer\Api\OfferManagementInterface;
use Vjackk\Offer\Api\OfferRepositoryInterface;

class OfferManagement implements OfferManagementInterface
{
    /**
     * @var OfferRepositoryInterface
     */
    protected OfferRepositoryInterface $offerRepository;

    /**
     * @var FilterBuilder
     */
    protected FilterBuilder $filterBuilder;

    /**
     * @var SearchCriteriaBuilder
     */
    protected SearchCriteriaBuilder $searchCriteriaBuilder;

    /**
     * @param OfferRepositoryInterface $offerRepository
     * @param FilterBuilder $filterBuilder
     * @param SearchCriteriaBuilder $searchCriteriaBuilder
     */
    public function __construct(
        OfferRepositoryInterface $offerRepository,
        FilterBuilder $filterBuilder,
        SearchCriteriaBuilder $searchCriteriaBuilder
    ) {
        $this->offerRepository = $offerRepository;
        $this->filterBuilder = $filterBuilder;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
    }

    /**
     * @inheritDoc
     */
    public function getListByCategoryId($categoryId)
    {
        $now = (new \DateTime())->format('Y-m-d H:i:s');

        $this->searchCriteriaBuilder->addFilter('category_id', $categoryId);

        $filters = [];
        $filters[] = $this->filterBuilder
            ->setField(OfferInterface::START_DATE)
            ->setValue($now)
            ->setConditionType('lteq')
            ->create();
        $filters[] = $this->filterBuilder
            ->setField(OfferInterface::START_DATE)
            ->setValue(true)
            ->setConditionType('null')
            ->create();
        $this->searchCriteriaBuilder->addFilters($filters);

        $filters = [];
        $filters[] = $this->filterBuilder
            ->setField(OfferInterface::END_DATE)
            ->setValue($now)
            ->setConditionType('gteq')
            ->create();
        $filters[] = $this->filterBuilder
            ->setField(OfferInterface::END_DATE)
            ->setValue(true)
            ->setConditionType('null')
            ->create();
        $this->searchCriteriaBuilder->addFilters($filters);

        return $this->offerRepository->getList($this->searchCriteriaBuilder->create())->getItems();
    }
}
