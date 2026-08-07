<?php

if (! function_exists('formatRupiah')) {
    function formatRupiah(float|int|string|null $amount): string
    {
        return 'Rp '.number_format((float) $amount, 0, ',', '.');
    }
}
