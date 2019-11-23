/***************** MENU ******************/

jQuery(function() {
	jQuery(".navbar-toggle").click(function() {
    	jQuery("#gnavi").slideToggle(200);
    	jQuery(".icon-bar").toggleClass("closeup");
    	return false;
  	});
});

/***************** Drawer MENU 閉じる ******************/

jQuery(document).ready(function () {
	jQuery(".navbar-nav li a").click(function(event) {
		jQuery(".navbar-collapse").removeClass('show');
  	});
});
