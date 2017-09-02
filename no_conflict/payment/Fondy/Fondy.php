<?php

require_once('api/Okay.php');
require_once('payment/Fondy/fondy.cls.php');

class Fondy extends Okay
{
    public function checkout_form($order_id, $button_text = null)
    {
        if (empty($button_text))
            $button_text = 'Перейти к оплате';

        $order = $this->orders->get_order((int)$order_id);
        $payment_method = $this->payment->get_payment_method($order->payment_method_id);
        $payment_currency = $this->money->get_currency(intval($payment_method->currency_id));
        $settings = $this->payment->get_payment_settings($payment_method->id);

        $price = round($this->money->convert($order->total_price, $payment_method->currency_id, false), 2);


        // описание заказа
        // order description
        $desc = 'Заказ номер: ' . $order->id;

       
        $result_url = $this->config->root_url . '/payment/Fondy/callback.php';

        $currency = $payment_currency->code;
        if ($currency == 'RUR')
            $currency = 'RUB';
        if ($settings['lang'] == '') {
            $settings['lang'] = 'ru';
        }
        $res = array(
            'order_id' => $order_id . fondycsl::ORDER_SEPARATOR . time(),
            'merchant_id' => $settings['fondy_merchantid'],
            'order_desc' => $desc,
            'amount' => $price * 100,
            'currency' => $currency,
            'server_callback_url' => $result_url,
            'response_url' => $result_url,
            'language' => $settings['lang'],
            'sender_email' => $order->email
        );
        if ($paymode == 'Y') {
            $res['preauth'] = 'Y';
        } else {
            $res['preauth'] = 'N';
        }
        $res['signature'] = fondycsl::getSignature($res, $settings['fondy_secret']);
        $res['btn_text'] = $button_text;
        return $res;
    }
}