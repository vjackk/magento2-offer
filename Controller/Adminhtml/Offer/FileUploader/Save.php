<?php
/**
 * @author    Vjackk <vincentjacquemin34@gmail.com>
 * @copyright 2022 Vjackk
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Vjackk\Offer\Controller\Adminhtml\Offer\FileUploader;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\App\Request\Http;
use Magento\Framework\Controller\ResultFactory;
use Vjackk\Offer\Model\Offer\FileUploader\FileProcessor;

class Save extends Action
{
    /**
     * @var FileProcessor
     */
    protected $fileProcessor;

    /**
     * @var Http
     */
    protected $httpRequest;

    /**
     * Authorization level
     */
    const ADMIN_RESOURCE = 'Vjackk_Offer::save';

    /**
     * @param Context $context
     * @param FileProcessor $fileProcessor
     * @param Http $httpRequest
     */
    public function __construct(
        Context $context,
        FileProcessor $fileProcessor,
        Http $httpRequest
    ) {
        parent::__construct($context);
        $this->fileProcessor = $fileProcessor;
        $this->httpRequest = $httpRequest;
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        $result = $this->fileProcessor->saveToTmp(key($this->httpRequest->getFiles()->toArray()));
        return $this->resultFactory->create(ResultFactory::TYPE_JSON)->setData($result);
    }

    public function _isAllowed()
    {
        return true;
    }
}
