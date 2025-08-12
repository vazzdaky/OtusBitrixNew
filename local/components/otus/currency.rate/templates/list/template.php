<?php if ($arResult['CURRENCY_RATE']): ?>
	<p>Текущий курс выбранной валюты: <?php echo round($arResult['CURRENCY_RATE'], 2); ?></p>
	<a href="javascript:history.back()">Назад</a>
<?php else: ?>
	<form method="get">
		<select name="CURRENCY_ID" onchange="this.form.submit()">
			<?php foreach ($arResult['CURRENCIES'] as $currencyId => $currencyName): ?>
				<option value="<?php echo htmlspecialcharsbx($currencyId); ?>"><?php echo htmlspecialcharsbx($currencyName); ?></option>
			<?php endforeach; ?>
		</select>
	</form>
<?php endif; ?>