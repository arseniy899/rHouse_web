<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<script src="//<?=REMOTE_ROOT?>/js/jquery.min.js" lazyload></script>
<link href="//<?=REMOTE_ROOT?>/css/fonts/SourceSansPro.css" rel="stylesheet" lazyload> 
<link href="//<?=REMOTE_ROOT?>/css/fonts/OpenSans.css" rel="stylesheet" lazyload> 

<link rel="stylesheet" href="//<?=REMOTE_ROOT?>/css/bootstrap.min.css"/ lazyload>
 <link rel="stylesheet" href="//<?=REMOTE_ROOT?>/css/ion.rangeSlider.min.css"/>
<link href="//netdna.bootstrapcdn.com/bootstrap/3.0.0/css/bootstrap-glyphicons.css" rel="stylesheet" lazyload>

<link rel="stylesheet" href="//<?=REMOTE_ROOT?>/css/icofont/icofont.min.css" lazyload>
<link rel="stylesheet" href="//<?=REMOTE_ROOT?>/css/lobibox.css"/ lazyload>
<link rel="stylesheet" href="//<?=REMOTE_ROOT?>/css/main.css"/ lazyload>
<link rel="shortcut icon" href="//<?=REMOTE_ROOT?>/favicon.ico" type="image/x-icon"/>
<script>
var isUserAuthorized = <?=(int)(IS_USER_AUTHED||(isset($CONFIG["userPolicy"]) && !$CONFIG["userPolicy"]))?>;
var REMOTE_URL = '//<?=REMOTE_ROOT?>';
var webVersion = '<?=$CONFIG["version"]?>';
var API_URL = '//<?=REMOTE_ROOT?>/api/';
var ADMIN_URL = '//<?=REMOTE_ROOT?>/admin/api/';
</script>


<script src="//<?=REMOTE_ROOT?>/js/lolibox/messageboxes.js" lazyload></script>
<script src="//<?=REMOTE_ROOT?>/js/lolibox/notifications.js" lazyload></script>
<script src="//<?=REMOTE_ROOT?>/js/toasts.js"></script>
<link 	href="//<?=REMOTE_ROOT?>/css/colorPick.min.css"	lazyload	rel="stylesheet" type="text/css">
<script src="//<?=REMOTE_ROOT?>/js/colorPick.min.js" 	lazyload		type="text/javascript"></script>
<link 	href="//<?=REMOTE_ROOT?>/css/image-picker.css"	lazyload		rel="stylesheet" type="text/css">
<script src="//<?=REMOTE_ROOT?>/js/image-picker.min.js"	lazyload	type="text/javascript"></script>
<link 	href="//<?=REMOTE_ROOT?>/css/forms.css?version=3" 	lazyload	rel="stylesheet" type="text/css">
<script src="//<?=REMOTE_ROOT?>/js/main.js?version=6" 		lazyload	type="text/javascript"></script>
<script src="//<?=REMOTE_ROOT?>/js/forms_ajax.js?version=2" lazyload	type="text/javascript"></script>
<meta name="viewport" content="width=device-width,height=device-height,initial-scale=1.0"/>
<meta name="theme-color" content="#ff6600">