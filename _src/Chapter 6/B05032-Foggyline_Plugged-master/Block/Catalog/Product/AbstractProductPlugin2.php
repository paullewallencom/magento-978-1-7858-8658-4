<?php

namespace Foggyline\Plugged\Block\Catalog\Product;

class AbstractProductPlugin2
{
    public function beforeGetAddToCartUrl(
        $subject,
        $product, $additional = []
    )
    {
        var_dump('Plugin2 - beforeGetAddToCartUrl');
    }

    public function afterGetAddToCartUrl($subject)
    {
        var_dump(get_class($subject));
        var_dump('Plugin2 - afterGetAddToCartUrl');
    }

    public function aroundGetAddToCartUrl(
        $subject,
        \Closure $proceed,
        $product,
        $additional = []
    )
    {
        var_dump(get_class($subject));
        var_dump('Plugin2 - aroundGetAddToCartUrl');
        return $proceed($product, $additional);
    }
}
