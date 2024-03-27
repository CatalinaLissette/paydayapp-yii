<?php

namespace app\services;

use app\models\PaymentInfo;

class PaymentInfoService
{

    public function disableOtherPaymentsMethodByUserId(int $userId)
    {
        PaymentInfo::disableOther($userId);
    }
}