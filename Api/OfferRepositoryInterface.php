<?php
/**
 * @author    Vjackk <vincentjacquemin34@gmail.com>
 * @copyright 2022 Vjackk
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Vjackk\Offer\Api;

use Exception;
use Magento\Framework\Api\SearchCriteriaInterface;
use Magento\Framework\Exception\AlreadyExistsException;
use Magento\Framework\Exception\NoSuchEntityException;
use Vjackk\Offer\Api\Data\OfferInterface;
use Vjackk\Offer\Api\Data\OfferSearchResultInterface;

interface OfferRepositoryInterface
{
    /**
     * @param int $id
     * @return OfferInterface
     * @throws NoSuchEntityException
     */
    public function getById($id);

    /**
     * @param SearchCriteriaInterface $searchCriteria
     * @return OfferSearchResultInterface
     */
    public function getList(SearchCriteriaInterface $searchCriteria);

    /**
     * @param OfferInterface $offer
     * @return OfferInterface
     * @throws AlreadyExistsException
     */
    public function save(OfferInterface $offer);

    /**
     * @param OfferInterface $offer
     * @return void
     * @throws Exception
     */
    public function delete(OfferInterface $offer);

    /**
     * @param int $id
     * @return void
     * @throws Exception
     */
    public function deleteById($id);
}
