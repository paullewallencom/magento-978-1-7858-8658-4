<?php

namespace Foggyline\DailyDeal\Model\Product\Type;

class DailyDeal extends \Magento\Catalog\Model\Product\Type\AbstractType
{
    const TYPE_DAILY_DEAL = 'foggylinedailydeal';

    /**
     * Delete data specific for this product type
     *
     * @param \Magento\Catalog\Model\Product $product
     * @return void
     */
    public function deleteTypeSpecificData(\Magento\Catalog\Model\Product $product)
    {
        // TODO: Implement deleteTypeSpecificData() method.
    }
}
