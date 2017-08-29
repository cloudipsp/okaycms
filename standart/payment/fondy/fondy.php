<?php

require_once('api/Okay.php');
require_once('payment/fondy/fondy.cls.php');

class fondy extends Okay
{	
	public function checkout_form($order_id, $button_text = null)
	{
		if(empty($button_text))
			$button_text = 'Перейти к оплате';
		
		$order = $this->orders->get_order((int)$order_id);
		$payment_method = $this->payment->get_payment_method($order->payment_method_id);
		$payment_currency = $this->money->get_currency(intval($payment_method->currency_id));
		$settings = $this->payment->get_payment_settings($payment_method->id);
		
		$price = round($this->money->convert($order->total_price, $payment_method->currency_id, false), 2);
		
		
		// описание заказа
		// order description
		$desc = 'Заказ номер: '.$order->id;
		
		// Способ оплаты
		$paymode = $settings['fondy_paymode'];

		$success_url = $this->config->root_url.'/order/'.$order->url;
		$result_url = $this->config->root_url.'/payment/fondy/callback.php';

		$currency = $payment_currency->code;
		if ($currency == 'RUR')
			$currency = 'RUB';
        if ($settings['lang']=='') {
            $settings['lang'] ='ru';
        }
		$res = array(
			'order_id' => $order_id . fondycsl::ORDER_SEPARATOR . time(),
			'merchant_id' => $settings['fondy_merchantid'],
			'order_desc' => $desc,
			'amount' => $price * 100,
			'currency' => $currency,
			'server_callback_url' => $result_url,
			'response_url' => $result_url,
			'lang' =>  $settings['lang'],
			'sender_email' => $order->email
			);
		if ($paymode == 'Y'){
			$res['preauth'] = 'Y';
		}
		$res['signature'] = fondycsl::getSignature($res, $settings[fondy_secret]);
		
		if ($settings['mode'] == 'popup' or $settings['mode'] == 'onpage'){
			$url = $this->get_checkout($res);
			$button = '<script src="https://api.fondy.eu/static_common/v1/checkout/ipsp.js"></script>
                    <script src="https://rawgit.com/dimsemenov/Magnific-Popup/master/dist/jquery.magnific-popup.js"></script>
                    <link href="https://rawgit.com/dimsemenov/Magnific-Popup/master/dist/magnific-popup.css" type="text/css" rel="stylesheet" media="screen">
			<div id="checkout">
			<div id="checkout_wrapper">
			</div>
			</div>';
			if ($settings['mode'] == 'popup'){
				$button .='<script>
				function callmag(){
				$.magnificPopup.open({
				showCloseBtn:false,
						items: {
							src: $("#checkout_wrapper"),
							type: "inline"
						}
					});
				}
				$(document).ready(function() {
				 $.magnificPopup.open({
				 showCloseBtn:false,
						items: {
							src: $("#checkout_wrapper"),
							type: "inline"
						}
					});
					})
				</script>';
				}

				 $button .='
					 <style>
						#checkout_wrapper a{
							font-size: 20px;
							top: 30px;
							padding: 20px;
							position: relative;
						}
						#checkout_wrapper {
							text-align: left;
							position: relative;
							background: #FFF;
							max-height: 700px;
							padding-left: 15px;
							padding-right: 15px;
							padding-bottom: 30px;
							width: auto;
							max-width: 2000px;
							margin: 9px auto;
						}
					</style>
				 
					<script>
						function checkoutInit(url, val) {
							$ipsp("checkout").scope(function() {
								this.setCheckoutWrapper("#checkout_wrapper");
								this.addCallback(__DEFAULTCALLBACK__);
								this.action("show", function(data) {
								   $("#checkout_loader").remove();
									$("#checkout").show();
								});
								this.action("hide", function(data) {
									$("#checkout").hide();
								});
								if(val){
								this.width(val);
								this.action("resize", function(data) {
								$("#checkout_wrapper").width(val).height(data.height);
									});
								}else{
								 this.action("resize", function(data) {
								$("#checkout_wrapper").width(480).height(data.height);
									});
								}
								this.loadUrl(url);
							});
							};
							checkoutInit("' . $url . '");
						</script>';
				if ($settings['mode'] == 'popup'){
					$button .='<input type="button" onclick="callmag();" class="checkout_button" value="'.$button_text.'">';
				}
			}else{
				$button = '<form id="fondy_to_checkout" method="POST" action="https://api.fondy.eu/api/checkout/redirect/">';
				foreach($res as $key => $value){
					$button .= '<input type="hidden" name="'.$key.'" value="'.$value.'">';
				}
				$button .= '<input type="submit" class="checkout_button" value="'.$button_text.'"></form>';
				$button .= ' <script>document.getElementById("fondy_to_checkout").submit();</script>';
			}		
			echo $button;
	}
	protected function get_checkout($args){
		if(is_callable('curl_init')){
		$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, 'https://api.fondy.eu/api/checkout/url/');
			curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
			curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-type: application/json'));
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array('request'=>$args)));
			
			$result = json_decode(curl_exec($ch));
			$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
				
			if ( $httpCode != 200 ){
				echo "Return code is {$httpCode} \n"
					.curl_error($ch);
					exit;
			} 
			if ($result->response->response_status == 'failure'){
				echo $result->response->error_message;
				exit;
			}
			$url = $result->response->checkout_url;
			return $url;
		}else{
			echo "Curl not found!";
			die;
		}			
	}
}