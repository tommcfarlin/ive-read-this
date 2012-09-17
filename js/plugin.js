(function ($) {
	$(function () {
		
		// If the "I've Read This Container" is on this page, let's setup the event handler
		if(1 === $('#ive-read-this-container').length) {
			
			// We use the change attribute so that the event handler fires
			// whenever the checkbox or its associated label is clicked.
			$('input[name="ive-read-this"]').change(function (evt) {
				
				// We can retrieve the ID of this post from the <article>'s ID. This will be required
				// so that we can mark that the user has read this particular post and we can hide it.
				var sArticleId, iPostId;
				
				// Get the article ID and split it - the second index is always the post ID in twentyeleven
				sArticleId = $("article").attr('id');
				iPostId = parseInt(sArticleId.split('-')[1]);

				// Initial the request to mark this this particular post as read
				$.post(ajaxurl, {
				
					action:		'mark_as_read',
					post_id:	iPostId
					
				}, function (response) {
					console.log(response);
					// If the server returns '1', then we can mark this post as read, so we'll hide the checkbox
					// container. Next time the user browses the index, this post won't appear
					if (1 === parseInt(response)) {
					
						$('#ive-read-this-container').slideUp('fast');
					
					// Otherwise, let's alert the user that there was a problem. In a larger environment, we'd
					// want to handle this more gracefully.
					} else {
					
						alert("There was an error marking this post as read. Please try again.");
						
					} // end if/else
					
				});
				
			});
			
		} // end if
		
	});
}(jQuery));