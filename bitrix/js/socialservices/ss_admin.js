;(function()
{
	BX.NetworkPopup = function()
	{
		this.popup = null;
		this.dontShow = false;
	};

	BX.NetworkPopup.prototype.show = function()
	{
		if(!this.popup)
		{
			this.popup = new BX.CDialog({
				'title': BX.message('SS_NETWORK_POPUP_TITLE'),
				'content': this.getContent(),
				'width': 550,
				'height': 220,
				'resizable': false,
				'buttons': [
					{
						title: BX.message('SS_NETWORK_POPUP_CONNECT'),
						id: 'save',
						name: 'save',
						className: 'adm-btn-save',
						action: BX.proxy(this.connect, this)
					},
					BX.CDialog.btnClose
				]
			});

			BX.addCustomEvent(this.popup, 'onBeforeWindowClose', BX.proxy(this.popupClose, this));
		}

		this.popup.Show();
		BX.userOptions.save('socialservices', 'networkPopup', 'showcount', +BX.message('SS_NETWORK_POPUP_COUNT')+1);
	};

	BX.NetworkPopup.prototype.connect = function()
	{
		BX.util.popup(BX.message('SS_NETWORK_URL'), 700, 500);
	};

	BX.NetworkPopup.prototype.clickCheckbox = function()
	{
		this.dontShow = BX.proxy_context.checked;
	};

	BX.NetworkPopup.prototype.popupClose = function()
	{
		if(this.dontShow)
		{
			BX.userOptions.save('socialservices', 'networkPopup', 'dontshow', 'Y');
		}
		else
		{
			BX.ajax.get("/bitrix/tools/oauth/socserv.ajax.php?action=networkclosepopup&sessid=" + BX.bitrix_sessid());
		}
	};

	BX.NetworkPopup.prototype.getContent = function()
	{
		var s = BX.create('DIV');

		s.appendChild(BX.create('DIV', {
			props: {
				className: 'ss-network-connect-text'
			},
			html: BX.message('SS_NETWORK_POPUP_TEXT')
		}));

		if(BX.message('SS_NETWORK_POPUP_COUNT') > 2)
		{
			var checkboxId = "ss_dontshow_" + Math.random();

			s.appendChild(BX.create('DIV', {
				props: {
					className: 'ss-network-dontshow'
				},
				children: [
					BX.create('INPUT', {
						props: {
							type: 'checkbox',
							className: 'ss-network-dontshow-checkbox',
							id: checkboxId
						},
						events: {
							'click': BX.proxy(this.clickCheckbox, this)
						}
					}),
					BX.create('LABEL', {
						props: {
							htmlFor: checkboxId
						},
						text: ' ' + BX.message('SS_NETWORK_POPUP_DONTSHOW')
					})
				]
			}));
		}

		return s;
	};

	BX.ready(BX.defer(function(){
		if(BX.message('SS_NETWORK_DISPLAY') == "Y")
		{
			(new BX.NetworkPopup()).show();
		}
	}));
})();