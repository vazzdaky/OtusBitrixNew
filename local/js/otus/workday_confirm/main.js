BX.namespace('Otus.Workday_Confirm'); // пространство имен
console.log('[Otus.Workday_Confirm] Namespace initialized'); 

// обработчик события открытия окна
BX.addCustomEvent('onTimeManWindowOpen', function(e) {

    // получаем атрибут в переменную
    const modify = BXTIMEMAN.WND.LAYOUT;

    // проверяем установлен атрибут
	if (modify.hasAttribute('data-has-custom-handler')) {
		return;
	}
    // устанавливаем атрибут
	modify.setAttribute('data-has-custom-handler', 'Y');

    // подписываемся на событие нажатия
	BX.Event.bind(modify, 'click',	function (e) {

			// пропускаем обработку события  
			if (e.detail?.isManual) {
				return;
			}
            // пропускем обработку других дочерных событий
			if (!e.target.matches('button.ui-btn.ui-btn-success.ui-btn-icon-start')) {
				return;
			}
            
            // получаем в переменную элемент найденный по классу button.ui-btn.ui-btn-success.ui-btn-icon-start отменяем обработчики  
			const button = modify.querySelector('button.ui-btn.ui-btn-success.ui-btn-icon-start');
			if (!button) {
				return;
			}
			
			BX.Event.unbindAll(button);
			BX.Otus.Workday_Confirm.ConfirmWorkday();
		},
		{ 
			capture: true 
		}
	);

});

// обработчик события закрытия окна
BX.addCustomEvent('onPopupAfterClose', function(popup) {

    // проверяем уникальность
	if (popup.uniquePopupId !== 'workday-confirm') {
		return;
	}
    // получаем в переменную элемент найденный по классу button.ui-btn.ui-btn-success.ui-btn-icon-start
	const button = BXTIMEMAN.WND.LAYOUT.querySelector('button.ui-btn.ui-btn-success.ui-btn-icon-start');

    // создаем новое событие на кнопке
	const customClick = new CustomEvent('click', {
		detail: { isManual: true },
		bubbles: false,
	});
	button.dispatchEvent(customClick);
});

// определяем функцию всплывающего окна и содержимое
BX.Otus.Workday_Confirm.ConfirmWorkday = function() {

	const popupWorkDay = BX.PopupWindowManager.create("workday-confirm", null, {
		compatibleMode: true,
		content: 'Вы точно хотите начать рабочий день?',
		width: 400, // ширина окна
		height: 200, // высота окна
		zIndex: 100, 
		offsetTop: 0,
		offsetLeft: 0,
		closeIcon: {
			opacity: 1
		},
		titleBar: 'Подтверждение',
		closeByEsc: true, // возможность закрывать окно по esc
		darkMode: true, // светлая или темная тема окна
		autoHide: true, // закрытие окна при клике не в окне
		draggable: true, // возможность двигать окно
		resizable: true, // возможность изменять размер окна
		min_height: 100, // минимальная высота окна
		min_width: 100, // минимальная ширина окна
		lightShadow: true, // тень у окна
		angle: false, // уголок у окна
		overlay: {
			backgroundColor: 'black',
			opacity: 500
		},
		buttons: [
			new BX.PopupWindowButton({
				text: 'Подтвердить', // текст кнопки
				id: 'save-btn', // идентификатор
				className: 'ui-btn ui-btn-success', // дополнительные классы
				events: {
					click: function () {
						const button = BXTIMEMAN.WND.LAYOUT.querySelector('button.ui-btn.ui-btn-success.ui-btn-icon-start');
						BX.Event.bind(button, 'click', BX.proxy(BX.CTimeManWindow.prototype.MainButtonClick, BXTIMEMAN.WND));
						popupWorkDay.close();
					}
				}
			}),
			new BX.PopupWindowButton({
				text: 'Отмена', // текст кнопки
				id: 'copy-btn', // идентификатор
				className: 'ui-btn ui-btn-primary', // дополнительные классы
				events: {
					click: function () {
						popupWorkDay.close();
					}
				}
			})
		],
	});

	popupWorkDay.show();
}