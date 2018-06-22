<?php
/**
 * Pmclain_Stripe extension
 * NOTICE OF LICENSE
 *
 * This source file is subject to the OSL 3.0 License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/osl-3.0.php
 *
 * @category  Pmclain
 * @package   Pmclain_Stripe
 * @copyright Copyright (c) 2017-2018
 * @license   Open Software License (OSL 3.0)
 */

namespace Magento\Sanalpos\Model\Adminhtml\Source;

use Magento\Framework\Option\ArrayInterface;

class ProvUserIdType implements ArrayInterface
{
    public function toOptionArray()
    {
        return [
            [
                'value' => 'PROVAUT',
                'label' => __('PROVAUT'),
            ],
            [
                'value' => 'PROVRFN',
                'label' => __('PROVRFN'),
            ],
            [
                'value' => 'PROVOOS',
                'label' => __('PROVOOS'),
            ]
        ];
    }
}
