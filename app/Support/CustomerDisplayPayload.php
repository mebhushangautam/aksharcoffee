<?php

namespace App\Support;

class CustomerDisplayPayload
{
    /**
     * Ensure empty carts never keep stale tax/charge rows on the customer display.
     *
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    public static function normalize(array $data): array
    {
        $items = $data['items'] ?? [];
        if (! is_array($items)) {
            $items = [];
        }

        $items = array_values(array_filter($items, static function ($item) {
            return is_array($item) && $item !== [];
        }));

        $subTotal = (float) ($data['sub_total'] ?? 0);
        $total = (float) ($data['total'] ?? 0);

        if (count($items) === 0 || ($subTotal <= 0 && $total <= 0)) {
            $data['items'] = [];
            $data['sub_total'] = 0;
            $data['discount'] = 0;
            $data['total'] = 0;
            $data['taxes'] = [];
            $data['extra_charges'] = [];
            $data['tip'] = 0;
            $data['delivery_fee'] = 0;
        }

        return $data;
    }
}
