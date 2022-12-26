<?php
/**
 * @author    Vjackk <vincentjacquemin34@gmail.com>
 * @copyright 2022 Vjackk
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Vjackk\Offer\Controller\Adminhtml\Offer;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\Json;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\Controller\ResultInterface;
use Magento\Framework\Exception\LocalizedException;
use Vjackk\Offer\Api\OfferRepositoryInterface as OfferRepository;
use Vjackk\Offer\Api\Data\OfferInterface;
use Vjackk\Offer\Model\Offer;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class InlineEdit extends Action implements HttpPostActionInterface
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
     * @var OfferRepository
     */
    protected $offerRepository;

    /**
     * @var JsonFactory
     */
    protected $jsonFactory;

    /**
     * @param Context $context
     * @param PostDataProcessor $dataProcessor
     * @param OfferRepository $offerRepository
     * @param JsonFactory $jsonFactory
     */
    public function __construct(
        Context $context,
        PostDataProcessor $dataProcessor,
        OfferRepository $offerRepository,
        JsonFactory $jsonFactory
    ) {
        parent::__construct($context);
        $this->dataProcessor = $dataProcessor;
        $this->offerRepository = $offerRepository;
        $this->jsonFactory = $jsonFactory;
    }

    /**
     * Process the request
     *
     * @return ResultInterface
     * @throws LocalizedException
     */
    public function execute()
    {
        /** @var Json $resultJson */
        $resultJson = $this->jsonFactory->create();
        $error = false;
        $messages = [];

        $postItems = $this->getRequest()->getParam('items', []);

        if (!($this->getRequest()->getParam('isAjax') && count($postItems))) {
            return $resultJson->setData(
                [
                    'messages' => [__('Please correct the data sent.')],
                    'error' => true,
                ]
            );
        }

        foreach (array_keys($postItems) as $offerId) {
            /** @var Offer $offer */
            $offer = $this->offerRepository->getById($offerId);
            try {
                $extendedOfferData = $offer->getData();
                $offerData = $this->filterPost($postItems[$offerId]);
                $this->validatePost($offerData, $offer, $error, $messages);
                $this->setOfferData($offer, $extendedOfferData, $offerData);
                $this->offerRepository->save($offer);
            } catch (\RuntimeException $e) {
                $messages[] = $this->getErrorWithOfferId($offer, $e->getMessage());
                $error = true;
            } catch (\Exception $e) {
                $messages[] = $this->getErrorWithOfferId(
                    $offer,
                    __('Something went wrong while saving the offer.')
                );
                $error = true;
            }
        }

        return $resultJson->setData(
            [
                'messages' => $messages,
                'error' => $error
            ]
        );
    }

    /**
     * Filtering posted data.
     *
     * @param array $postData
     * @return array
     */
    protected function filterPost($postData = [])
    {
        $newOfferData = $this->dataProcessor->filter($postData);

        if (
            !empty($newOfferData['start_date'])
            && date("Y-m-d", strtotime($postData['start_date']))
            === date("Y-m-d", strtotime($postData['start_date']))
        ) {
            $newOfferData['start_date'] = date("Y-m-d", strtotime($postData['start_date']));
        }
        if (
            !empty($newOfferData['end_date'])
            && date("Y-m-d", strtotime($postData['end_date']))
            === date("Y-m-d", strtotime($postData['end_date']))
        ) {
            $newOfferData['end_date'] = date("Y-m-d", strtotime($postData['end_date']));
        }

        return $newOfferData;
    }

    /**
     * Validate post data
     *
     * @param array $offerData
     * @param Offer $offer
     * @param bool $error
     * @param array $messages
     * @return void
     */
    protected function validatePost(array $offerData, Offer $offer, &$error, array &$messages)
    {
        if (!$this->dataProcessor->validateRequireEntry($offerData)) {
            $error = true;
            foreach ($this->messageManager->getMessages(true)->getItems() as $error) {
                $messages[] = $this->getErrorWithOfferId($offer, $error->getText());
            }
        }
    }

    /**
     * Add offer title to error message
     *
     * @param OfferInterface $offer
     * @param string $errorText
     * @return string
     */
    protected function getErrorWithOfferId(OfferInterface $offer, $errorText)
    {
        return '[Offer ID: ' . $offer->getId() . '] ' . $errorText;
    }

    /**
     * Set offer data
     *
     * @param Offer $offer
     * @param array $extendedOfferData
     * @param array $offerData
     * @return $this
     */
    public function setOfferData(Offer $offer, array $extendedOfferData, array $offerData)
    {
        $offer->setData(array_merge($offer->getData(), $extendedOfferData, $offerData));
        return $this;
    }
}
