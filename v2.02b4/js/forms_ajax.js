/* attach a submit handler to the form */
	(function blink()
	{
		//$('div#loadingAlert').fadeOut(800).fadeIn(1000, blink);
	})();
	$.urlParam = function (name)
	{
		var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
		if (results == null)
		{
			return null;
		}
		else
		{
			return results[1] || 0;
		}
	}
	$(function()
	{
		var $form;
		$(".form").submit(function (event)
		{
			console.log('Sending form'); 
			/* stop form from submitting normally */
			event.preventDefault();
			
			var xhttp;
			xhttp = new XMLHttpRequest();
			$form = $(this); var url = $form.attr('action');
			ShowLoading();
			xhttp.onreadystatechange = function() 
			{
				if (this.readyState == 4)
				{
					DismissLoading();
					if(this.status == 200) 
					{
						var ajaxRes = JSON.parse(this.responseText).responce;
						Toast.showAjaxRes(ajaxRes);
						if(ajaxRes.error == 0 && !$form.hasClass( "no-reload" ))
						{
							if ($.urlParam('red') != null && $.urlParam('red').length > 0)
								window.location.replace($.urlParam('red'));
							else if (window.location.pathname.search("login").length>0)
								window.location.replace("/account.php");
							else
								window.location.replace(window.location.href);
						}
					}
				}
			};
			xhttp.open("POST", url, true);
			xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
			//xhttp.send(new URLSearchParams(new FormData(this)).toString());
			xhttp.send($form.serialize());
			
		});
	});
	function formSuccess(data)
	{
		//alert(obj);
		var obj = jQuery.parseJSON(data);
		$("div#loadingAlert").css('visibility', 'hidden');
		
		//alert(obj.responce.error);
		if (typeof obj == 'object')
		{
			if (obj.responce.error == 0)
			{
				Toast.showAjaxRes(0);
				
				if(!$form.hasClass( "no-reload" ))
				{
					if ($.urlParam('red') != null && $.urlParam('red').length > 0)
						window.location.replace($.urlParam('red'));
					else if (window.location.pathname.search("login").length>0)
						window.location.replace("/account.php");
					else
						window.location.replace(window.location.href);
				}
			}
			else
			{
				Toast.showAjaxRes(obj.responce.error,obj.responce.desc);
				
			}
		}
		else
		{
			Toast.showAjaxRes(1,'Ошибка получения данных');
			
		}
		//$("#result").empty().append(content);

	}
/*$(document).ready(function()
{
	// Set trigger and container variables
	var trigger = $('a'),container = $('html');
		
	// Fire on click
	trigger.on('click', function(){
		target = $(this).attr('href');	   
		$.ajax({url:target+'?rel=tab',success: function(data){
			$("html").html($(data).find("html").html());
		}});
		// Load target page into container
		//container.load(target);
		//if(pageurl!=window.location)
		  //  window.history.pushState({path:target},'',target); 
		// Stop normal link behavior
		/*$(window).bind('popstate', function() 
		{
			$.ajax({url:target+'?rel=tab',success: function(data)
			{
				$('body').html(data);
			}});
		});*
		return false;
	});
});*/
