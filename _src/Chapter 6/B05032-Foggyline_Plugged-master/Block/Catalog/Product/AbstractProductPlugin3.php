<?php

namespace Foggyline\Plugged\Block\Catalog\Product;

class AbstractProductPlugin3
{
    public function beforeGetAddToCartUrl(
        $subject,
        $product, $additional = []
    )
    {
        var_dump('Plugin3 - beforeGetAddToCartUrl');
    }

    public function afterGetAddToCartUrl($subject)
    {
        var_dump(get_class($subject));
        var_dump('Plugin3 - afterGetAddToCartUrl');
    }

    public function aroundGetAddToCartUrl(
        $subject,
        \Closure $proceed,
        $product,
        $additional = []
    )
    {
        var_dump('Plugin3 - aroundGetAddToCartUrl');
        return $proceed($product, $additional);
    }
}
