<?php

declare(strict_types=1);

/*
 * CoreShop
 *
 * This source file is available under two different licenses:
 *  - GNU General Public License version 3 (GPLv3)
 *  - CoreShop Commercial License (CCL)
 * Full copyright and license information is available in
 * LICENSE.md which is distributed with this source code.
 *
 * @copyright  Copyright (c) CoreShop GmbH (https://www.coreshop.org)
 * @license    https://www.coreshop.org/license     GPLv3 and CCL
 *
 */

namespace CoreShop\Component\Order\Model;

use CoreShop\Component\Currency\Model\Money;
use CoreShop\Component\Resource\Model\ResourceInterface;
use CoreShop\Component\Taxation\Model\TaxRuleGroupInterface;

interface PurchasableInterface extends ResourceInterface
{
    public function getName($language = null): ?string;

    public function getWholesaleBuyingPrice(): ?Money;

    public function getTaxRule(): ?TaxRuleGroupInterface;
}
