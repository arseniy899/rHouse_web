class Toast
{
	static showAjaxRes(code, text)
	{
		if(code instanceof Object)
			Toast.showAjaxResObj(code);
		else
		{
			if(code == 0)
				Lobibox.notify('success', {
					size: 'mini',
					delay: 3000,
					onClick: function()
					{
						var str = JSON.stringify(result.data, null, 2);
						Lobibox.alert("info", {msg: str});
					},  
					msg: 'Операция прошла успешно!'
					});
			else
				Lobibox.notify('error', {
					size: 'mini',
					delay: 7000, 
					onClick: function()
					{
						Lobibox.alert("error", {msg: 
`Ошибка: #${result.error}<br />
Название: ${result.desc}`
						});
					},
					msg: `ОШИБКА: <br />#${code}: ${text}`
					});
		}
	}
	static showAjaxResObj(result)
	{
		if(result.error == 0)
			Lobibox.notify('success', {
				size: 'mini',
				delay: 3000,
				onClick: function()
				{
					var str = JSON.stringify(result.data, null, 2);
					Lobibox.alert("info", {msg: str});
				}, 
				msg: 'Операция прошла успешно!'
				});
		else if(result.message != undefined)
			Lobibox.notify('error', {
				size: 'mini',
				closeOnClick : false,
				onClick: function()
				{
					Lobibox.alert("error", {msg: 
`Ошибка: #${result.error}<br />
Название: ${result.desc}<br />
Подробнее:<br />
${result.message}`
					});
				},
				delay: 10000, 
				msg: `ОШИБКА: <br />#${result.error}: ${result.desc}`
				});
		else
			Lobibox.notify('error', {
				size: 'mini',
				delay: 7000, 
				onClick: function()
				{
					Lobibox.alert("error", {msg: 
`Ошибка: #${result.error}<br />
Название: ${result.desc}`
					});
				},
				msg: `ОШИБКА: <br />#${result.error}: ${result.desc}`
				});
			
	}
	static showToast(text, obj)
	{
		//if(obj.)
	}
	
	static showHTML(html)
	{
		var toast_track = document.getElementById("toast-track");
		toast_track.innerHTML = html;
	}
}

function closeMessage(el) {
	el.addClass('is-hidden');
}
