<?php
/**
 * @author    Vjackk <vincentjacquemin34@gmail.com>
 * @copyright 2022 Vjackk
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Vjackk\Offer\Controller\Adminhtml\Offer;

use Magento\Backend\App\Action;
use Magento\Backend\Model\View\Result\Redirect;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\App\Cache\Frontend\Pool;
use Magento\Framework\App\Cache\Type\Block;
use Magento\Framework\App\Cache\TypeListInterface;
use Magento\Framework\App\Request\DataPersistorInterface;
use Magento\Framework\App\ResponseInterface;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Magento\PageCache\Model\Cache\Type;
use Vjackk\Offer\Api\Data\OfferInterface;
use Vjackk\Offer\Api\OfferRepositoryInterface;
use Vjackk\Offer\Model\Offer;
use Vjackk\Offer\Model\OfferFactory;
use Zend_Log_Exception;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Save extends Action implements HttpPostActionInterface
{
    /**
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Vjackk_Offer::save';

    /**
     * @var PostDataProcessor
     */
    protected $dataProcessor;

    /**
     * @var DataPersistorInterface
     */
    protected $dataPersistor;

    /**
     * @var OfferFactory
     */
    protected $offerFactory;

    /**
     * @var OfferRepositoryInterface
     */
    protected $offerRepository;

    /**
     * @var TypeListInterface
     */
    protected $cacheTypeList;

    /**
     * @var Pool
     */
    protected $cacheFrontendPool;

    /**
     * @param Action\Context $context
     * @param PostDataProcessor $dataProcessor
     * @param DataPersistorInterface $dataPersistor
     * @param OfferFactory $offerFactory
     * @param OfferRepositoryInterface $offerRepository
     * @param TypeListInterface $cacheTypeList
     * @param Pool $cacheFrontendPool
     */
    public function __construct(
        Action\Context $context,
        PostDataProcessor $dataProcessor,
        DataPersistorInterface $dataPersistor,
        OfferFactory $offerFactory,
        OfferRepositoryInterface $offerRepository,
        TypeListInterface $cacheTypeList,
        Pool $cacheFrontendPool
    ) {
        $this->dataProcessor = $dataProcessor;
        $this->dataPersistor = $dataPersistor;
        $this->offerFactory = $offerFactory;
        $this->offerRepository = $offerRepository;
        $this->cacheTypeList = $cacheTypeList;
        $this->cacheFrontendPool = $cacheFrontendPool;
        parent::__construct($context);
    }

    /**
     * @return Redirect|ResponseInterface|ResultInterface
     * @throws Zend_Log_Exception
     */
    public function execute()
    {
        $data = $this->getRequest()->getPostValue();
        /** @var Redirect $resultRedirect */
        $resultRedirect = $this->resultRedirectFactory->create();

        if ($data) {
            $imageData = $data[OfferInterface::IMAGE_DATA] ?? [];

            $data = $this->dataProcessor->filter($data);
            $data = $this->dataProcessor->convertToJson($data);

            /** @var Offer $model */
            $model = $this->offerFactory->create();

            $id = $this->getRequest()->getParam('offer_id');
            if ($id) {
                try {
                    $model = $this->offerRepository->getById($id);
                } catch (LocalizedException $e) {
                    $this->messageManager->addErrorMessage(__('This offer no longer exists.'));
                    return $resultRedirect->setPath('*/*/');
                }
            }

            $model->setData($data);

            try {
                $this->_eventManager->dispatch(
                    'offer_prepare_save',
                    ['offer' => $model, 'request' => $this->getRequest()]
                );

                $this->offerRepository->save($model);
                if (is_array($imageData)) {
                    $this->dataProcessor->uploadFile($imageData);
                }
                $this->messageManager->addSuccessMessage(__('You saved the offer.'));
                $this->invalidCache();
                return $this->processResultRedirect($model, $resultRedirect, $data);
            } catch (LocalizedException $e) {
                $this->messageManager->addExceptionMessage($e->getPrevious() ?: $e);
            } catch (\Throwable $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while saving the offer.'));
            }

            $this->dataPersistor->set('offer', $data);
            return $resultRedirect->setPath('*/*/edit', ['offer_id' => $this->getRequest()->getParam('offer_id')]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    /**
     * Process result redirect
     *
     * @param OfferInterface $model
     * @param Redirect $resultRedirect
     * @param array $data
     * @return Redirect
     * @throws LocalizedException
     */
    private function processResultRedirect($model, $resultRedirect, $data)
    {
        if ($this->getRequest()->getParam('back', false) === 'duplicate') {
            $newOffer = $this->offerFactory->create(['data' => $data]);
            $newOffer->setId(null);
            $this->offerRepository->save($newOffer);
            $this->messageManager->addSuccessMessage(__('You duplicated the offer.'));
            return $resultRedirect->setPath(
                '*/*/edit',
                [
                    'offer_id' => $newOffer->getId(),
                    '_current' => true,
                ]
            );
        }
        $this->dataPersistor->clear('offer');
        if ($this->getRequest()->getParam('back')) {
            return $resultRedirect->setPath('*/*/edit', ['offer_id' => $model->getId(), '_current' => true]);
        }
        return $resultRedirect->setPath('*/*/');
    }

    private function invalidCache()
    {
        $types = array(
            Block::TYPE_IDENTIFIER,
            Type::TYPE_IDENTIFIER
        );

        foreach ($types as $type) {
            $this->cacheTypeList->invalidate($type);
        }
    }
}
