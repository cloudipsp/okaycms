{* Страница заказа *}

{$meta_title = "Ваш заказ №`$invoice.transaction`" scope=parent}

{if $invoice.status == 'approved'}
<div style="background:white;padding:10px;">
	<div class="h3">
		<span>Заказ успешно оплачен.</span>
	</div>
	<table style="max-width: 330px" class="order_details">
        <tbody>
			<tr>
                <td>
					<span>Сумма:</span>
				</td>
				<td>
					<span> {$invoice.amount}</span>
				</td>
			</tr>
			<tr>
                <td>
					<span>Ваш заказ №:</span>
				</td>
				<td>
					<span> {$invoice.transaction}</span>
				</td>
			</tr>			
		</tbody>
	</table>		
	
	<img style="margin-bottom:20px" src="https://fondy.eu/wp-content/uploads/sites/17/fondy-logo.svg" class="logo" alt="fondy-logo" itemprop="logo">
</div>	
{else}
	<h1>Ошибка оплаты.</h1>
	<p>Код ошибки: {$invoice.error_code}</p>
	<br>
<p>Описание ошибки :{$invoice.error_message}</p>
{/if}


