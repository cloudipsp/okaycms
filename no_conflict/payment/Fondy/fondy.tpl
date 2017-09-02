{* Страница заказа *}

{$meta_title = "Ваш заказ №`$invoice.transaction`" scope=parent}

{if $invoice.status != 'declined' or $invoice.status != 'expired'}
<div style="background:white;padding:10px;">
	<div class="h3">
		<span>Ваш заказ успешно оплачен.</span>
	</div>
	<table style="max-width:290px" class="order_details">
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
					<span>Ваш номер заказа:</span>
				</td>
				<td>
					<span> {$invoice.transaction}</span>
				</td>
			</tr>			
		</tbody>
	</table>		
	
	<img style="margin-bottom:20px;margin-left: 50px;margin-top: 30px;width: 150px;" src="/payment/Fondy/2015x1000_color.png" class="logo" alt="fondy-logo" itemprop="logo">
</div>	
{else}
	<div class="h3">
		<span>Оплата отклонена системой.</pant>
	</div>
{/if}


