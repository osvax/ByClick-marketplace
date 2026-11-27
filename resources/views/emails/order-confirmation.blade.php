@php
    $scale = (int) config('marketplace.currency_scale', 100);
    $currency = config('marketplace.currency', 'Руб');
    $money = fn (int $amount): string => number_format($amount / $scale, 2, ',', ' ').' '.$currency;
@endphp

<!doctype html>
<html lang="ru">
    <head>
        <meta charset="utf-8" />
        <title>Заказ {{ $order->number }} оформлен</title>
    </head>
    <body style="margin:0;padding:24px;background:#f3f4f8;font-family:Arial,Helvetica,sans-serif;color:#1d2130;">
        <div style="max-width:620px;margin:0 auto;background:#ffffff;border-radius:14px;padding:28px;">
            <h1 style="margin:0 0 8px;font-size:22px;">Спасибо за заказ!</h1>
            <p style="margin:0 0 16px;font-size:14px;line-height:1.55;">
                Заказ <strong>{{ $order->number }}</strong> принят.
                Мы отправим его по выбранному способу доставки:
                <strong>{{ $order->delivery_method->label() }}</strong>
                @if ($order->delivery_city)
                    , {{ $order->delivery_city }}
                @endif
                @if ($order->address)
                    , {{ $order->address }}
                @endif
            </p>

            <table style="width:100%;border-collapse:collapse;font-size:13px;">
                @foreach ($order->items as $item)
                    <tr>
                        <td style="padding:8px 0;border-bottom:1px solid #e8e9ef;">
                            {{ $item->product_name }} × {{ $item->quantity }}
                        </td>
                        <td style="padding:8px 0;border-bottom:1px solid #e8e9ef;text-align:right;white-space:nowrap;">
                            {{ $money($item->total) }}
                        </td>
                    </tr>
                @endforeach
            </table>

            <p style="margin:16px 0 0;font-size:14px;">
                Товары: <strong>{{ $money($order->subtotal) }}</strong><br />
                Доставка: <strong>{{ $order->delivery_price > 0 ? $money($order->delivery_price) : 'бесплатно' }}</strong><br />
                Итого: <strong>{{ $money($order->total) }}</strong>
            </p>

            <p style="margin:20px 0 0;font-size:12px;color:#6f7588;">
                Это демонстрационный заказ ByClick: оплата не списывалась.
                Статус заказа можно посмотреть в личном кабинете.
            </p>
        </div>
    </body>
</html>
