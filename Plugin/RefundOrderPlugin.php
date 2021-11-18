<?php

namespace PayYourWay\Pyw\Plugin;

use Magento\Sales\Api\Data\CreditmemoCommentCreationInterface;
use Magento\Sales\Api\Data\CreditmemoCreationArgumentsInterface;
use Magento\Sales\Api\Data\CreditmemoItemCreationInterface;
use Magento\Sales\Api\RefundOrderInterface;

class RefundOrderPlugin
{
    /**
     * @param RefundOrderInterface $subject
     * @param int $result
     * @param int $orderId
     * @param CreditmemoItemCreationInterface[] $items
     * @param bool|null $notify
     * @param bool|null $appendComment
     * @param CreditmemoCommentCreationInterface|null $comment
     * @param CreditmemoCreationArgumentsInterface|null $arguments
     * @return int
     */
    public function afterExecute(
        RefundOrderInterface $subject,
        int $result,
        $orderId,
        array $items = [],
        $notify = false,
        $appendComment = false,
        CreditmemoCommentCreationInterface $comment = null,
        CreditmemoCreationArgumentsInterface $arguments = null
    ): int {
        // TODO: Implement plugin method.

        return $result;
    }
}
