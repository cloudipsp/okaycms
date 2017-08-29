{* Страница заказа *}

{$meta_title = "Ваш заказ №`$invoice.transaction`" scope=parent}

{if $invoice.status == 'approved'}
	<H1>Заказ успешно оплачен.</H1>
	<p>Сумма: {$invoice.amount}</p>
	<p>Ваш заказ №:{$invoice.transaction}</p>
	<br>
	<img src="https://fondy.eu/wp-content/uploads/sites/17/fondy-logo.svg" class="logo" alt="fondy-logo" itemprop="logo">
{else}
	<H1>Ошибка оплаты.</H1>
	<p>Код ошибки: {$invoice.error_code}</p>
	<br>
<p>Описание ошибки :{$invoice.error_message}</p>
{/if}


