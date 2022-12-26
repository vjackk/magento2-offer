<?php
/**
 * @author    Vjackk <vincentjacquemin34@gmail.com>
 * @copyright 2022 Vjackk
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Vjackk\Offer\Model;

use Magento\Framework\Data\Collection\AbstractDb;
use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\Context;
use Magento\Framework\Model\ResourceModel\AbstractResource;
use Magento\Framework\Registry;
use Magento\Framework\Serialize\Serializer\Json;
use Vjackk\Offer\Api\Data\OfferInterface;

/**
 * @method Offer setCategoryId(int $storeId)
 * @method int getCategoryId()
 */
class Offer extends AbstractModel implements OfferInterface
{
    /**
     * @var Json
     */
    protected $json;

    /**
     * @param Context $context
     * @param Registry $registry
     * @param AbstractResource|null $resource
     * @param AbstractDb|null $resourceCollection
     * @param Json $json
     * @param array $data
     */
    public function __construct(
        Context $context,
        Registry $registry,
        Json $json,
        AbstractResource $resource = null,
        AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->json = $json;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    protected function _construct()
    {
        $this->_init(ResourceModel\Offer::class);
    }

    /**
     * @inheritDoc
     */
    public function getId()
    {
        return parent::getData(self::OFFER_ID);
    }

    /**
     * @inheritDoc
     */
    public function setId($id)
    {
        $this->setData(self::OFFER_ID, $id);
    }

    /**
     * @inheritDoc
     */
    public function getLabel()
    {
        return $this->getData(self::LABEL);
    }

    /**
     * @inheritDoc
     */
    public function setLabel($label)
    {
        $this->setData(self::LABEL, $label);
    }

    /**
     * @inheritDoc
     */
    public function getImageData()
    {
        if ($this->getData(self::IMAGE_DATA) && !is_array($this->getData(self::IMAGE_DATA))) {
            return $this->json->unserialize($this->getData(self::IMAGE_DATA));
        }

        return $this->getData(self::IMAGE_DATA);
    }

    /**
     * @inheritDoc
     */
    public function setImageData($imageData)
    {
        $this->setData(self::IMAGE_DATA, $imageData);
    }

    /**
     * @inheritDoc
     */
    public function getImageAlt()
    {
        return $this->getData(self::IMAGE_ALT);
    }

    /**
     * @inheritDoc
     */
    public function setImageAlt($imageAlt)
    {
        $this->setData(self::IMAGE_ALT, $imageAlt);
    }

    /**
     * @inheritDoc
     */
    public function getRedirectLink()
    {
        return $this->getData(self::REDIRECT_LINK);
    }

    /**
     * @inheritDoc
     */
    public function setRedirectLink($redirectLink)
    {
        $this->setData(self::REDIRECT_LINK, $redirectLink);
    }

    /**
     * @inheritDoc
     */
    public function getCategories()
    {
        return $this->hasData('categories') ? $this->getData('categories') : (array)$this->getData('category_id');
    }

    /**
     * @inheritDoc
     */
    public function getStartDate()
    {
        $this->getData(self::START_DATE);
    }

    /**
     * @inheritDoc
     */
    public function setStartDate($startDate)
    {
        $this->setData(self::START_DATE, $startDate);
    }

    /**
     * @inheritDoc
     */
    public function getEndDate()
    {
        $this->getData(self::START_DATE);
    }

    /**
     * @inheritDoc
     */
    public function setEndDate($endDate)
    {
        $this->setData(self::END_DATE, $endDate);
    }

    /**
     * @inheritDoc
     */
    public function getCreatedAt()
    {
        $this->getData(self::CREATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setCreatedAt($createdAt)
    {
        $this->setData(self::CREATED_AT, $createdAt);
    }

    /**
     * @inheritDoc
     */
    public function getUpdatedAt()
    {
        $this->getData(self::UPDATED_AT);
    }

    /**
     * @inheritDoc
     */
    public function setUpdatedAt($updatedAt)
    {
        $this->setData(self::UPDATED_AT, $updatedAt);
    }
}
