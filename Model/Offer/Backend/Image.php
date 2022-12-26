<?php
/**
 * @author    Vjackk <vincentjacquemin34@gmail.com>
 * @copyright 2022 Vjackk
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Vjackk\Offer\Model\Offer\Backend;

class Image extends \Magento\Theme\Model\Design\Backend\Image
{
    /**
     * The tail part of directory path for uploading
     */
    const UPLOAD_DIR = 'offer/image';

    /**
     * @var int
     */
    protected $maxFileSize = 2048;

    /**
     * @return string
     */
    protected function _getUploadDir()
    {
        return $this->_mediaDirectory->getRelativePath($this->_appendScopeInfo(static::UPLOAD_DIR));
    }

    /**
     * @return bool
     */
    protected function _addWhetherScopeInfo()
    {
        return true;
    }

    /**
     * @return string[]
     */
    public function getAllowedExtensions()
    {
        return ['jpg', 'jpeg', 'gif', 'png'];
    }
}
