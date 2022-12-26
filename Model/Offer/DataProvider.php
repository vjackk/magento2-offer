<?php
/**
 * @author    Vjackk <vincentjacquemin34@gmail.com>
 * @copyright 2022 Vjackk
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Vjackk\Offer\Model\Offer;

use Magento\Framework\App\ObjectManager;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Ui\DataProvider\Modifier\PoolInterface;
use Magento\Ui\DataProvider\ModifierPoolDataProvider;
use Vjackk\Offer\Api\Data\OfferInterface;
use Vjackk\Offer\Api\OfferRepositoryInterface;
use Vjackk\Offer\Model\OfferFactory;
use Vjackk\Offer\Model\ResourceModel\Offer\CollectionFactory;

class DataProvider extends ModifierPoolDataProvider
{
    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var array
     */
    protected $loadedData;

    /**
     * @var Json
     */
    protected $json;

    /**
     * @var OfferRepositoryInterface
     */
    private $offerRepository;

    /**
     * @var RequestInterface
     */
    private $request;

    /**
     * @var OfferFactory
     */
    private $offerFactory;

    /**
     * @param $name
     * @param $primaryFieldName
     * @param $requestFieldName
     * @param CollectionFactory $offerCollectionFactory
     * @param DataPersistorInterface $dataPersistor
     * @param Json $json
     * @param array $meta
     * @param array $data
     * @param PoolInterface|null $pool
     * @param RequestInterface|null $request
     * @param OfferRepositoryInterface|null $offerRepository
     * @param OfferFactory|null $offerFactory
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        $name,
        $primaryFieldName,
        $requestFieldName,
        CollectionFactory $offerCollectionFactory,
        DataPersistorInterface $dataPersistor,
        Json $json,
        array $meta = [],
        array $data = [],
        PoolInterface $pool = null,
        ?RequestInterface $request = null,
        ?OfferRepositoryInterface $offerRepository = null,
        ?OfferFactory $offerFactory = null
    ) {
        parent::__construct($name, $primaryFieldName, $requestFieldName, $meta, $data, $pool);
        $this->collection = $offerCollectionFactory->create();
        $this->dataPersistor = $dataPersistor;
        $this->json = $json;
        $this->meta = $this->prepareMeta($this->meta);
        $this->request = $request ?? ObjectManager::getInstance()->get(RequestInterface::class);
        $this->offerRepository = $offerRepository ?? ObjectManager::getInstance()->get(OfferRepositoryInterface::class);
        $this->offerFactory = $offerFactory ?: ObjectManager::getInstance()->get(OfferFactory::class);
    }

    /**
     * Prepares Meta
     *
     * @param array $meta
     * @return array
     */
    public function prepareMeta(array $meta)
    {
        return $meta;
    }

    /**
     * Get data
     *
     * @return array
     */
    public function getData()
    {
        if (isset($this->loadedData)) {
            return $this->loadedData;
        }

        $offer = $this->getCurrentOffer();
        $offerData = $offer->getData();
        $offer->load($offer->getId());

        if (isset($offerData['image_data'])) {
            $offerData['image_data'] = $offer->getImageData();
        }

        $offer->setData($offerData);
        $this->loadedData[$offer->getId()] = $offerData;

        return $this->loadedData;
    }

    /**
     * Return current offer
     *
     * @return OfferInterface
     */
    private function getCurrentOffer(): OfferInterface
    {
        $offerId = $this->getOfferId();
        if ($offerId) {
            try {
                $offer = $this->offerRepository->getById($offerId);
            } catch (LocalizedException $exception) {
                $offer = $this->offerFactory->create();
            }

            return $offer;
        }

        $data = $this->dataPersistor->get('offer');
        if (empty($data)) {
            return $this->offerFactory->create();
        }
        $this->dataPersistor->clear('offer');

        return $this->offerFactory->create()->setData($data);
    }

    /**
     * Returns current offer id from request
     *
     * @return int
     */
    private function getOfferId(): int
    {
        return (int) $this->request->getParam($this->getRequestFieldName());
    }
}
