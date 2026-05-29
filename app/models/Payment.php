<?php
namespace App\Models;

use App\Core\Model;

class Payment extends Model
{
    protected string $table = 'payments';

    public function record(int $orderId, string $provider, float $amount, string $txnRef, string $status, ?array $raw = null): int
    {
        return $this->insert([
            'order_id'     => $orderId,
            'provider'     => $provider,
            'txn_ref'      => $txnRef,
            'amount'       => $amount,
            'status'       => $status,
            'raw_response' => $raw ? json_encode($raw, JSON_UNESCAPED_UNICODE) : null,
        ]);
    }
}
