<?php
/**
 * @author    Vjackk <vincentjacquemin34@gmail.com>
 * @copyright 2022 Vjackk
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Vjackk\Offer\Controller\Adminhtml\Offer;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\Stdlib\DateTime\Filter\Date;
use Magento\Framework\View\Model\Layout\Update\ValidatorFactory;
use Vjackk\Offer\Api\Data\OfferInterface;
use Vjackk\Offer\Model\Offer\FileUploader\FileProcessor;

class PostDataProcessor
{
    /**
     * @var Date
     */
    protected $dateFilter;

    /**
     * @var ValidatorFactory
     */
    protected $validatorFactory;

    /**
     * @var ManagerInterface
     */
    protected $messageManager;

    /**
     * @var Json
     */
    protected $json;

    /**
     * @var FileProcessor
     */
    protected $fileProcessor;

    /**
     * @param Date $dateFilter
     * @param ManagerInterface $messageManager
     * @param ValidatorFactory $validatorFactory
     * @param Json $json
     * @param FileProcessor $fileProcessor
     */
    public function __construct(
        Date $dateFilter,
        ManagerInterface $messageManager,
        ValidatorFactory $validatorFactory,
        Json $json,
        FileProcessor $fileProcessor
    ) {
        $this->dateFilter = $dateFilter;
        $this->messageManager = $messageManager;
        $this->validatorFactory = $validatorFactory;
        $this->json = $json;
        $this->fileProcessor = $fileProcessor;
    }

    /**
     * Filtering posted data. Converting localized data if needed
     *
     * @param array $data
     * @return array
     */
    public function filter($data)
    {
        $filterRules = [];

        foreach ([OfferInterface::START_DATE, OfferInterface::END_DATE] as $dateField) {
            if (!empty($data[$dateField])) {
                $filterRules[$dateField] = $this->dateFilter;
            }
        }

        // Native phpcs Magento error (@see \Magento\Cms\Controller\Adminhtml\Page\PostDataProcessor)
        // @codingStandardsIgnoreLine
        return (new \Zend_Filter_Input($filterRules, [], $data))->getUnescaped();
    }

    /**
     * @param array $data
     * @return array
     */
    public function convertToJson($data)
    {
        if (!empty($data[OfferInterface::IMAGE_DATA])) {
            $data[OfferInterface::IMAGE_DATA] = $this->json->serialize($data[OfferInterface::IMAGE_DATA]);
        } else {
            $data[OfferInterface::IMAGE_DATA] = null;
        }

        return $data;
    }

    /**
     * @param array $imageData
     * @return void
     * @throws LocalizedException
     */
    public function uploadFile($imageData)
    {
        $imageData = reset($imageData);
        if (isset($imageData['name'])) {
            $this->fileProcessor->moveTemporaryFile($imageData['name']);
        }
    }

    /**
     * Check if required fields is not empty
     *
     * @param array $data
     * @return bool
     */
    public function validateRequireEntry($data)
    {
        $requiredFields = [
            'label' => __('Offer Label')
        ];
        $errorNo = true;
        foreach ($data as $field => $value) {
            if (in_array($field, array_keys($requiredFields)) && $value == '') {
                $errorNo = false;
                $this->messageManager->addErrorMessage(
                    __('To apply changes you should fill in hidden required "%1" field', $requiredFields[$field])
                );
            }
        }
        return $errorNo;
    }
}
