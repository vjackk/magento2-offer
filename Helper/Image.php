<?php
/**
 * @author    Vjackk <vincentjacquemin34@gmail.com>
 * @copyright 2022 Vjackk
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Vjackk\Offer\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Vjackk\Offer\Api\Data\OfferInterface;

class Image extends AbstractHelper implements ArgumentInterface
{
    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var Json
     */
    protected $json;

    /**
     * @param Context $context
     * @param UrlInterface $urlBuilder
     * @param Json $json
     */
    public function __construct(
        Context $context,
        UrlInterface $urlBuilder,
        Json $json
    ) {
        $this->urlBuilder = $urlBuilder;
        $this->json = $json;
        parent::__construct($context);
    }

    /**
     * @param OfferInterface $offer
     * @return string
     */
    public function getUrl($offer)
    {
        $path = '';

        $imageData = $offer->getImageData();
        if ($imageData) {
            if (!is_array($imageData)) {
                $imageData = $this->json->unserialize($imageData);
            }
            $imageData = reset($imageData);
            if (is_array($imageData) && isset($imageData['name'])) {
                $path = $imageData['url'];
            }
        }

        return $path;
    }
}
