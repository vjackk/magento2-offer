<?php
/**
 * @author    Vjackk <vincentjacquemin34@gmail.com>
 * @copyright 2022 Vjackk
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Vjackk\Offer\Model;

use Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\DataObject;
use Magento\Framework\EntityManager\HydratorInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Vjackk\Offer\Api\OfferRepositoryInterface;
use Vjackk\Offer\Api\Data\OfferInterface;
use Vjackk\Offer\Api\Data\OfferSearchResultInterface;
use Vjackk\Offer\Api\Data\OfferSearchResultInterfaceFactory;
use Vjackk\Offer\Model\ResourceModel\Offer as ResourceOffer;
use Vjackk\Offer\Model\ResourceModel\Offer\Collection as OfferCollection;
use Vjackk\Offer\Model\ResourceModel\Offer\CollectionFactory as OfferCollectionFactory;

class OfferRepository implements OfferRepositoryInterface
{
    /**
     * @var OfferFactory
     */
    protected $offerFactory;

    /**
     * @var OfferCollectionFactory
     */
    protected $offerCollectionFactory;

    /**
     * @var OfferSearchResultInterfaceFactory
     */
    protected $searchResultFactory;

    /**
     * @var ResourceOffer
     */
    protected $resource;

    /**
     * @var CollectionProcessorInterface
     */
    protected $collectionProcessor;

    /**
     * @var HydratorInterface
     */
    private $hydrator;

    /**
     * @param OfferFactory $offerFactory
     * @param OfferCollectionFactory $offerCollectionFactory
     * @param OfferSearchResultInterfaceFactory $offerSearchResultInterfaceFactory
     * @param ResourceOffer $resource
     * @param CollectionProcessorInterface $collectionProcessor
     * @param HydratorInterface $hydrator
     */
    public function __construct(
        OfferFactory $offerFactory,
        OfferCollectionFactory $offerCollectionFactory,
        OfferSearchResultInterfaceFactory $offerSearchResultInterfaceFactory,
        ResourceOffer $resource,
        CollectionProcessorInterface $collectionProcessor = null,
        HydratorInterface $hydrator = null
    ) {
        $this->offerFactory = $offerFactory;
        $this->offerCollectionFactory = $offerCollectionFactory;
        $this->searchResultFactory = $offerSearchResultInterfaceFactory;
        $this->resource = $resource;
        $this->collectionProcessor = $collectionProcessor;
        $this->hydrator = $hydrator;
    }

    /**
     * @inheritDoc
     */
    public function getById($id)
    {
        /** @var Offer $offer */
        $offer = $this->offerFactory->create();
        $this->resource->load($offer, $id);
        if (!$offer->getId()) {
            throw new NoSuchEntityException(__('Unable to find offer with ID "%1"', $id));
        }
        return $offer;
    }

    /**
     * @inheritDoc
     */
    public function getList(SearchCriteriaInterface $searchCriteria)
    {
        /** @var OfferCollection|DataObject $collection */
        $collection = $this->offerCollectionFactory->create();

        $this->collectionProcessor->process($searchCriteria, $collection);

        /** @var OfferSearchResultInterface $searchResults */
        $searchResults = $this->searchResultFactory->create();
        $searchResults->setSearchCriteria($searchCriteria);
        $searchResults->setItems($collection->getItems());

        return $searchResults;
    }

    /**
     * @inheritDoc
     */
    public function save(OfferInterface $offer)
    {
        $offerId = $offer->getId();
        if ($offerId && !($offer instanceof Offer && $offer->getOrigData())) {
            $offer = $this->hydrator->hydrate($this->getById($offerId), $this->hydrator->extract($offer));
        }

        $this->resource->beforeSave($offer);
        $this->resource->save($offer);
        $this->resource->afterSave($offer);

        return $offer;
    }

    /**
     * @inheritDoc
     */
    public function delete(OfferInterface $offer)
    {
        $this->resource->delete($offer);
    }

    /**
     * @inheritDoc
     */
    public function deleteById($id)
    {
        $offer = $this->getById($id);
        $this->resource->delete($offer);
    }
}
