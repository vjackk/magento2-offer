<?php
/**
 * @author    Vjackk <vincentjacquemin34@gmail.com>
 * @copyright 2022 Vjackk
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Vjackk\Offer\Model\Offer\FileUploader;

use Exception;
use Magento\Framework\App\Config\Value;
use Magento\Framework\Exception\FileSystemException;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Filesystem;
use Magento\MediaStorage\Model\File\UploaderFactory;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\Filesystem\Directory\WriteInterface;
use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Theme\Model\Design\Backend\File;
use Magento\Theme\Model\Design\BackendModelFactory;
use Vjackk\Offer\Model\Offer\MetadataProvider;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class FileProcessor
{
    /**
     * @var UploaderFactory
     */
    protected $uploaderFactory;

    /**
     * @var BackendModelFactory
     */
    protected $backendModelFactory;

    /**
     * @var MetadataProvider
     */
    protected $metadataProvider;

    /**
     * @var WriteInterface
     */
    protected $mediaDirectory;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * @var string
     */
    public const FILE_DIR = 'offer/image';

    /**
     * @var string
     */
    public const TMP_DIR = 'tmp';

    /**
     * @param UploaderFactory $uploaderFactory
     * @param BackendModelFactory $backendModelFactory
     * @param MetadataProvider $metadataProvider
     * @param Filesystem $filesystem
     * @param StoreManagerInterface $storeManager
     * @throws FileSystemException
     */
    public function __construct(
        UploaderFactory $uploaderFactory,
        BackendModelFactory $backendModelFactory,
        MetadataProvider $metadataProvider,
        Filesystem $filesystem,
        StoreManagerInterface $storeManager
    ) {
        $this->uploaderFactory = $uploaderFactory;
        $this->backendModelFactory = $backendModelFactory;
        $this->metadataProvider = $metadataProvider;
        $this->storeManager = $storeManager;
        $this->mediaDirectory = $filesystem->getDirectoryWrite(DirectoryList::MEDIA);
    }

    /**
     * Save file to temp media directory
     *
     * @param  string $fileId
     * @return array
     */
    public function saveToTmp($fileId)
    {
        try {
            $result = $this->save($fileId, $this->getAbsoluteTmpMediaPath());
            $result['url'] = $this->getTmpMediaUrl($result['file']);
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }
        return $result;
    }

    /**
     * Retrieve temp media url
     *
     * @param string $file
     * @return string
     * @throws NoSuchEntityException
     */
    protected function getTmpMediaUrl($file)
    {
        return $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA)
            . self::TMP_DIR . DIRECTORY_SEPARATOR . self::FILE_DIR . DIRECTORY_SEPARATOR . $this->prepareFile($file);
    }

    /**
     * Retrieve temp media url
     *
     * @param string $file
     * @return string
     */
    protected function getTmpMediaRelativeUrl($file)
    {
        return self::TMP_DIR . DIRECTORY_SEPARATOR . self::FILE_DIR . DIRECTORY_SEPARATOR . $this->prepareFile($file);
    }

    /**
     * Retrieve temp media url
     *
     * @param string $file
     * @return string
     */
    protected function getMediaRelativeUrl($file)
    {
        return self::FILE_DIR . DIRECTORY_SEPARATOR . $this->prepareFile($file);
    }

    /**
     * Prepare file
     *
     * @param string $file
     * @return string
     */
    protected function prepareFile($file)
    {
        return $file !== null ? ltrim(str_replace('\\', '/', $file), '/') : '';
    }

    /**
     * Retrieve absolute temp media path
     *
     * @return string
     */
    protected function getAbsoluteTmpMediaPath()
    {
        return $this->mediaDirectory->getAbsolutePath(self::TMP_DIR . DIRECTORY_SEPARATOR . self::FILE_DIR);
    }

    /**
     * Save image
     *
     * @param string $fileId
     * @param string $destination
     * @return array
     * @throws LocalizedException
     */
    protected function save($fileId, $destination)
    {
        /** @var File $backendModel */
        $backendModel = $this->getBackendModel($fileId);
        $uploader = $this->uploaderFactory->create(['fileId' => $fileId]);
        $uploader->setAllowRenameFiles(true);
        $uploader->setFilesDispersion(false);
        $uploader->setAllowedExtensions($backendModel->getAllowedExtensions());
        $uploader->addValidateCallback('size', $backendModel, 'validateMaxSize');

        $result = $uploader->save($destination);
        unset($result['path']);

        return $result;
    }

    /**
     * Retrieve backend model by field code
     *
     * @param string $code
     * @return File|Value
     * @throws LocalizedException
     */
    protected function getBackendModel($code)
    {
        $metadata = $this->metadataProvider->get();
        if (!(isset($metadata[$code]) && isset($metadata[$code]['backend_model']))) {
            throw new LocalizedException(__('The backend model isn\'t specified for "%1".', $code));
        }
        return $this->backendModelFactory->createByPath($metadata[$code]['path']);
    }

    /**
     * Move file from temporary directory into base directory
     *
     * @param string $fileName
     * @return string
     * @throws LocalizedException
     */
    public function moveTemporaryFile($fileName)
    {
        if (!$this->isFileTemporary($fileName)) {
            return $fileName;
        }

        $fileName = $this->prepareFile($fileName);

        $tmpFile = $this->getTmpMediaRelativeUrl($fileName);
        $destination = $this->getMediaRelativeUrl($fileName);

        try {
            $this->mediaDirectory->renameFile(
                $tmpFile,
                $destination
            );
        } catch (Exception $e) {
            throw new LocalizedException(
                __('Something went wrong while saving the file.'),
                $e
            );
        }

        return $destination;
    }

    /**
     * Verify if given file temporary.
     *
     * @param string $fileName
     * @return bool
     */
    private function isFileTemporary(string $fileName): bool
    {
        $tmpFile = $this->getTmpMediaRelativeUrl($fileName);
        $destinationFile = $this->getMediaRelativeUrl($fileName);

        return $this->mediaDirectory->isExist($tmpFile) && !$this->mediaDirectory->isExist($destinationFile);
    }
}
