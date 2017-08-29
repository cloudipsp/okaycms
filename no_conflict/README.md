# Модуль для OkayCMS

---

### Для установки необходимо: 
1. Разместить папки payment и design в корень сайта (если у вас установлен сторонний шаблон скопировать файл payments_form.tpl в папку html вашего шаблона)

!Примечание, если у вас были установлены сторонние методы оплаты рекомендуется вставить данный код в конец Вашего файла payments_form.tpl заменив ```{/if}```
```sh
{elseif $payment_module == "Fondy"}
    {* Способ оплаты Fondy *}
    <div class="row">
        <form class="col-lg-7" id="fondy_to_checkout" method="POST" action="https://api.fondy.eu/api/checkout/redirect/">
		<input type="hidden" name="order_id" value="{$order_id}">
		<input type="hidden" name="merchant_id" value="{$merchant_id}">
		<input type="hidden" name="order_desc" value="{$order_desc}">
		<input type="hidden" name="amount" value="{$amount}">
		<input type="hidden" name="currency" value="{$currency}">
		<input type="hidden" name="server_callback_url" value="{$server_callback_url}">
		<input type="hidden" name="response_url" value="{$response_url}">
		<input type="hidden" name="lang" value="{$lang}">
		<input type="hidden" name="sender_email" value="{$sender_email}">
		<input type="hidden" name="preauth" value="{$preauth}">
		<input type="hidden" name="signature" value="{$signature}">
		<input type="submit" class="button" value="{$btn_text}">
		</form>
    </div>
{/if}
```
2. Зайти в панель управления.
3. Перейти в раздел "Настройки cайта -> Способы оплаты".
4. Нажать "Добавить способ оплаты" .
5. Ввести имя (Fondy), из выпавшего списка выбрать "Fondy", ввести ID мерчанта и Секретный ключ и остальные данные и сохранить.
6. После настройки установить методы доставки и активировать способ Fondy.

---

![Скриншот][1]

[1]: https://raw.githubusercontent.com/cloudipsp/okaycms/master/Screenshot_1.png