<?php

namespace App\Enums\PaymentEnums;

enum PaymentMethod: string
{
    case BANK_TRANSFER = 'bank_transfer';
    case QRIS = 'qris';
    case E_WALLET = 'e_wallet';
}