<?php

use App\Services\CartServices;
use App\Services\OrderServices;

/*
 * Characterization tests: they pin down what the code does TODAY so the
 * Laravel 13 port can be checked against it. They are not a statement that
 * the behaviour is correct.
 */

function orderServices()
{
    return new OrderServices(new CartServices());
}

it('rounds the payable amount up to the next 5 cents', function ($input, $expected) {
    expect((float) orderServices()->roundDecimal($input))->toEqualWithDelta($expected, 0.00001);
})->with([
    'x.x1 -> x.x5' => [10.01, 10.05],
    'x.x4 -> x.x5' => [12.34, 12.35],
    'x.x5 unchanged' => [10.05, 10.05],
    'x.x6 -> next 10c' => [10.06, 10.10],
    'x.x7 -> next 10c' => [12.37, 12.40],
    'x.x9 -> next 10c' => [10.99, 11.00],
    'one decimal unchanged' => [12.3, 12.3],
    'whole number unchanged' => [10, 10],
]);

it('looks only at the second decimal and drops the rest', function () {
    // 10.123: second decimal 2 -> +3c on the truncated 10.12
    expect((float) orderServices()->roundDecimal(10.123))->toEqualWithDelta(10.15, 0.00001);
    // 10.129 rounds the same way: the third decimal is ignored
    expect((float) orderServices()->roundDecimal(10.129))->toEqualWithDelta(10.15, 0.00001);
    // 10.999: second decimal 9 -> +1c on 10.99
    expect((float) orderServices()->roundDecimal(10.999))->toEqualWithDelta(11.00, 0.00001);
});

it('leaves float noise untouched when the second decimal is 0', function () {
    // "0.30000000000000004": second decimal is "0", so nothing is rounded or truncated
    expect(orderServices()->roundDecimal(0.1 + 0.2))->toBe(0.1 + 0.2);
});

it('formats order numbers as Ymd plus the id padded to 4 digits', function ($id, $expected) {
    expect(orderServices()->manipulateOrderNumber($id, '2026-03-04 10:15:00'))->toBe($expected);
})->with([
    [5, '202603040005'],
    [42, '202603040042'],
    [123, '202603040123'],
    [1234, '202603041234'],
    [12345, '2026030412345'], // 5+ digits: no padding
]);
