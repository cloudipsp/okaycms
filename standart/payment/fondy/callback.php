<?php

chdir ('../../');
require_once('api/Okay.php');
require_once('payment/fondy/fondy.cls.php');
require_once(dirname(__FILE__).'/FondyView.php');
$fonView = new FondyView();
$Okay = new Okay();
$err = '';
////////////////////////////////////////////////
// Выберем заказ из базы
////////////////////////////////////////////////
	if (empty($_POST)){
		$callack = json_decode(file_get_contents("php://input"));
		if(empty($callack)){
			die('go away!');
		}
		$_POST = array();
		foreach($callack as $key=>$val){
			$_POST[$key] =  $val ;
		}
		if(!$_POST['order_id']){
			die('go away!');
		}
	}
	
    list($order_id,) = explode(fondycsl::ORDER_SEPARATOR, $_POST['order_id']);
    $order = $Okay->orders->get_order(intval($order_id));
    $payment_method = $Okay->payment->get_payment_method($order->payment_method_id);
    $payment_currency = $Okay->money->get_currency(intval($payment_method->currency_id));
    $settings = $Okay->payment->get_payment_settings($payment_method->id);

    $options = array(
        'merchant' => $settings['fondy_merchantid'],
        'secretkey' => $settings['fondy_secret']
    );
    $paymentInfo = fondycsl::isPaymentValid($options, $_POST);

    if (!$order->paid) {
		
        if ($_POST['amount'] / 100 >= round($Okay->money->convert($order->total_price, $payment_method->currency_id, false), 2)) {
            if ($paymentInfo === true) {
                if ($_POST['order_status'] == fondycsl::ORDER_APPROVED) {

                    // Установим статус оплачен

                    $Okay->orders->update_order(intval($order->id), array('paid' => 1));

                    // Отправим уведомление на email
                    $Okay->notify->email_order_user(intval($order->id));
                    $Okay->notify->email_order_admin(intval($order->id));

                    // Спишем товары
                    $Okay->orders->close(intval($order->id));


                    $invoice['status'] = $_POST['order_status'];
                    $invoice['transaction'] = $_POST['order_id'];
                    $invoice['system'] = 'fondy';
                    $invoice['amount'] = $_POST['amount'] / 100 . " " . $_POST['actual_currency'];
					
                    $fonView->design->assign('invoice', $invoice);

                    print $fonView->fetch();

                } else {
                    $Okay->orders->update_order(intval($order->id), array('paid' => 0));
               
                    $invoice['status'] = $_POST['order_status'];
                    $invoice['error_message'] = $_POST['response_description'];
                    $invoice['error_code'] = $_POST['response_code'];
                    $fonView->design->assign('invoice', $invoice);

                    print $fonView->fetch();

                }
            } else
                $err = $paymentInfo;
        } else
            $err = "Amount check failed";
    }else {
		$invoice['transaction'] = $_POST['order_id'];
        $invoice['system'] = 'fondy';
        $invoice['amount'] = $_POST['amount'] / 100 . " " . $_POST['actual_currency'];
	}
    $invoice['error_code'] = 'unknown code';
    $invoice['status'] = $_POST['order_status'];
    $invoice['error_message'] = $err;
    $fonView->design->assign('invoice', $invoice);
    print $fonView->fetch();