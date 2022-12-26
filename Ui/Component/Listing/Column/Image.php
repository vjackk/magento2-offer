<?php
/**
 * @author    Vjackk <vincentjacquemin34@gmail.com>
 * @copyright 2022 Vjackk
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Vjackk\Offer\Ui\Component\Listing\Column;

use Magento\Framework\DataObject;
use Magento\Framework\UrlInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Ui\Component\Listing\Columns\Column;
use Vjackk\Offer\Api\Data\OfferInterface;
use Vjackk\Offer\Helper\Image as ImageHelper;

class Image extends Column
{
    const NAME = 'image';

    const ALT_FIELD = 'offer';

    /**
     * @var UrlInterface
     */
    protected $urlBuilder;

    /**
     * @var ImageHelper
     */
    protected $imageHelper;

    /**
     * @param ContextInterface $context
     * @param UiComponentFactory $uiComponentFactory
     * @param UrlInterface $urlBuilder
     * @param ImageHelper $imageHelper
     * @param array $components
     * @param array $data
     */
    public function __construct(
        ContextInterface $context,
        UiComponentFactory $uiComponentFactory,
        UrlInterface $urlBuilder,
        ImageHelper $imageHelper,
        array $components = [],
        array $data = []
    ) {
        parent::__construct($context, $uiComponentFactory, $components, $data);
        $this->urlBuilder = $urlBuilder;
        $this->imageHelper = $imageHelper;
    }

    /**
     * Prepare Data Source
     *
     * @param array $dataSource
     * @return array
     */
    public function prepareDataSource(array $dataSource)
    {
        if (isset($dataSource['data']['items'])) {
            $fieldName = $this->getData('name');
            foreach ($dataSource['data']['items'] as & $item) {
                /** @var OfferInterface $offer */
                $offer = new DataObject($item);
                if ($offer->getData('image_data')) {
                    $item[$fieldName . '_src'] = $this->imageHelper->getUrl($offer);
                    $item[$fieldName . '_alt'] = $this->getAlt($item);
                    $item[$fieldName . '_link'] = $this->urlBuilder->getUrl(
                        'offer/offer/edit',
                        ['offer_id' => $offer->getOfferId()]
                    );
                    $item[$fieldName . '_orig_src'] = $this->imageHelper->getUrl($offer);
                }
            }
        }

        return $dataSource;
    }

    /**
     * Get Alt
     *
     * @param array $row
     *
     * @return null|string
     */
    protected function getAlt($row)
    {
        $altField = self::ALT_FIELD;
        return $row[$altField] ?? null;
    }
}
