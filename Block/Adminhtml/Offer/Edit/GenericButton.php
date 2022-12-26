<?php
/**
 * @author    Vjackk <vincentjacquemin34@gmail.com>
 * @copyright 2022 Vjackk
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Vjackk\Offer\Block\Adminhtml\Offer\Edit;

use Magento\Backend\Block\Widget\Context;
use Vjackk\Offer\Api\OfferRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;

class GenericButton
{
    /**
     * @var Context
     */
    protected $context;

    /**
     * @var OfferRepositoryInterface
     */
    protected $offerRepository;

    /**
     * @param Context $context
     * @param OfferRepositoryInterface $offerRepository
     */
    public function __construct(
        Context $context,
        OfferRepositoryInterface $offerRepository
    ) {
        $this->context = $context;
        $this->offerRepository = $offerRepository;
    }

    /**
     * Return offer ID
     *
     * @return int|null
     */
    public function getOfferId()
    {
        try {
            return $this->offerRepository->getById(
                $this->context->getRequest()->getParam('offer_id')
            )->getId();
        } catch (NoSuchEntityException $e) {
        }
        return null;
    }

    /**
     * Generate url by route and parameters
     *
     * @param   string $route
     * @param   array $params
     * @return  string
     */
    public function getUrl($route = '', $params = [])
    {
        return $this->context->getUrlBuilder()->getUrl($route, $params);
    }
}
