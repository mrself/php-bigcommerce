<?php declare(strict_types=1);

namespace Mrself\Bigcommerce\Dev;

use Bigcommerce\Api\Resources\Category;
use Bigcommerce\Api\Resources\Product;

trait ResourceTrait
{
    protected $bcCategoryIndex = 0;

    protected $bcProductIndex = 0;

    protected $bcHookIndex = 0;

    protected function getBcCategory(array $source = [])
    {
        $this->bcCategoryIndex++;
        $source = array_merge([
            'id' => $this->bcCategoryIndex,
            'name' => 'name' . $this->bcCategoryIndex,
            'url' => 'url' . $this->bcCategoryIndex,
            'parent_id' => null
        ], $source);
        return new Category((object) $source);
    }

    protected function getBcProduct(array $source = [])
    {
        $this->bcProductIndex++;
        $source = array_merge([
            'id' => $this->bcProductIndex,
            'name' => 'name' . $this->bcProductIndex,
            'price' => $this->bcProductIndex . '.00',
            'retail_price' => ($this->bcProductIndex + 1) . '.00',
            'sale_price' => ($this->bcProductIndex + 2) . '.00',
            'sku' => 'sku' . $this->bcProductIndex,
            'custom_url' => 'url' . $this->bcProductIndex,
            'description' => 'desc' . $this->bcProductIndex,
            'is_visible' => false,
            'search_keywords' => 'keywords' . $this->bcProductIndex,
            'warranty' => 'warranty' . $this->bcProductIndex,
            'date_created' => 'Fri, 21 Sep 2012 02:31:01 +0000',
            'primary_image' => (object) [
                "standard_url" => 'url' . $this->bcProductIndex,
            ],
            'categories' => []
        ], $source);
        $this->formatBcProductCategories($source);
        return new Product((object) $source);
    }

    protected function formatBcProductCategories(array &$source)
    {
        $source['categories'] = array_map(function ($category) {
            if (is_object($category)) {
                return $category->id;
            }
            return $category;
        }, $source['categories']);
    }

    protected function getBcHook(array $source = [])
    {
        $this->bcHookIndex++;
        $source = array_merge([
            'id' => $this->bcHookIndex,
            'scope' => 'scope' . $this->bcHookIndex,
            'store_hash' => 'store_hash' . $this->bcHookIndex,
            'destination' => 'url' . $this->bcHookIndex
        ], $source);
        return (object) $source;
    }
}