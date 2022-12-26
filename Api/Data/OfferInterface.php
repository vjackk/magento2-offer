<?php
/**
 * @author    Vjackk <vincentjacquemin34@gmail.com>
 * @copyright 2022 Vjackk
 * @license   https://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 */
declare(strict_types=1);

namespace Vjackk\Offer\Api\Data;

interface OfferInterface
{
    /**#@+
     * Constants defined for keys of array, makes typos less likely
     */
    const OFFER_ID = 'offer_id';

    const LABEL = 'label';

    const IMAGE_DATA = 'image_data';

    const IMAGE_ALT = 'image_alt';

    const REDIRECT_LINK = 'redirect_link';

    const START_DATE = 'start_date';

    const END_DATE = 'end_date';

    const CREATED_AT = 'created_at';

    const UPDATED_AT = 'updated_at';

    /**
     * @return int
     */
    public function getId();

    /**
     * @param int $id
     * @return void
     */
    public function setId($id);

    /**
     * @return string
     */
    public function getLabel();

    /**
     * @param string $label
     * @return void
     */
    public function setLabel($label);

    /**
     * @return string
     */
    public function getImageData();

    /**
     * @param array $imageData
     * @return void
     */
    public function setImageData($imageData);

    /**
     * @return string
     */
    public function getImageAlt();

    /**
     * @param string $imageAlt
     * @return void
     */
    public function setImageAlt($imageAlt);

    /**
     * @return string
     */
    public function getRedirectLink();

    /**
     * @param string $redirectLink
     * @return void
     */
    public function setRedirectLink($redirectLink);

    /**
     * @return string
     */
    public function getStartDate();

    /**
     * @param string $startDate
     * @return void
     */
    public function setStartDate($startDate);

    /**
     * @return string
     */
    public function getEndDate();

    /**
     * @param string $endDate
     * @return void
     */
    public function setEndDate($endDate);

    /**
     * @return string
     */
    public function getCreatedAt();

    /**
     * @param string $createdAt
     * @return void
     */
    public function setCreatedAt($createdAt);

    /**
     * @return string
     */
    public function getUpdatedAt();

    /**
     * @param string $updatedAt
     * @return void
     */
    public function setUpdatedAt($updatedAt);
}
