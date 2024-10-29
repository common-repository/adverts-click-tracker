(function( $ ) {
	
		$( "#accordion" ).accordion({
			collapsible: true
		});
		


		$("#menu-posts-webd_adverts .wp-submenu > li:nth-child(5)").addClass('proVersion proSpan');
		$(".proVersion").click(function(e){
			e.preventDefault();
			$("#AdvertsClickTrackerModal").slideDown();
		});

		$("#AdvertsClickTrackerModal .close").click(function(e){
			e.preventDefault();
			$("#AdvertsClickTrackerModal").fadeOut();
		});		

		var modal = document.getElementById('AdvertsClickTrackerModal');

		// When the user clicks anywhere outside of the modal, close it
		window.onclick = function(event) {
			if (event.target == modal) {
				modal.style.display = "none";
			}
		}
		
		$(".AdvertsClickTrackerToggler").click(function(e){
			e.preventDefault();
			$("#AdvertsClickTrackerForm").hide();
			$(".AdvertsClickTrackerVideo").slideDown();
		});
		
		
})( jQuery )	