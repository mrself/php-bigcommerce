<?php declare(strict_types=1);

namespace App\BigCommerce\Entity;

trait EntityTrait
{
    /**
     * @ORM\Column(name="regular_shop_id", type="integer")
     * @var integer
     * Shop provider id. The id which is used by shop provider system.
     * For example, if a provider is BigCommerce, an id is BigCommerce id
     */
    protected $shopId;

    /**
     * Is an entity synchronised with a provider shop
     * @ORM\Column(type="boolean", options={"default": false})
     * @var bool
     */
    protected $isSynced = false;

    /**
     * @return int
     */
    public function getShopId(): int
    {
        return $this->shopId;
    }

    /**
     * @param int $shopId
     */
    public function setShopId(int $shopId): void
    {
        $this->shopId = $shopId;
    }

    /**
     * @return bool
     */
    public function getIsSynced(): bool
    {
        return $this->isSynced;
    }

    /**
     * @param bool $isSynced
     */
    public function setIsSynced(bool $isSynced): void
    {
        $this->isSynced = $isSynced;
    }
}