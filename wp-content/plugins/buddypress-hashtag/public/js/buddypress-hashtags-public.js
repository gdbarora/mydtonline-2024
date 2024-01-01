(function( $ ) {
	'use strict';

	/**
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 */

	 $(function() {
	 	// $('#whats-new').keyup( function(e){
	 	// 	if (e.which !== 0) {
	 	// 		// if( String.fromCharCode(e.which) == '3' ){

	 	// 		// }
	 	// 		if ( '#ritu'.match(/(?:^|\s)(?:#)([a-zA-Z\d]+)/) ) {
	 	// 			alert('yes');
	 	// 			//$(this).css( 'background-color', 'yellow' );
	 	// 		}
	 	// 	}
	 	// } );
	 	//alert( bpht_ajax_object.minlen );
	 	//alert( bpht_ajax_object.maxlen );

	 	// function bpht_match_hashtags() {
	 	// 	if ( e.which.match(/(?:^|\s)(?:#)([a-zA-Z\d]+)/) ) {
			//     $(this).css( 'background-color' : 'red' );
			// }
	 	// }
	 });
	document.addEventListener("DOMContentLoaded", () => {
		const addHashTag = (e, tag) => {
			let value = e.detail.data.value;
			if(''!=value){
				value = value.replace('#', '');
				jQuery.post(bpht_ajax_object.ajax_url, { nonce: bpht_ajax_object.ajax_nonce, tag: value, action:'hashtag_add' }, function(responce){
					console.log(responce);
				});
			}
		}

		const removeHashTag = (e, tag) => {
			let value = e.detail.data.value;
			if(''!=value){
				value = value.replace('#', '');
				jQuery.post(bpht_ajax_object.ajax_url, { nonce: bpht_ajax_object.ajax_nonce, tag: value, action:'remove_hashtag' }, function(responce){
					if (!responce.success){
						tag.addTags(value, true);
					}
				});
			}
		}

		var input = document.getElementById('bp-hashtag'),
			tagify = new Tagify(input, {
				delimiters: ",| ",  
				backspace: "edit",
				callbacks: {
					add: e => { addHashTag(e, tagify) },
					remove: e => { removeHashTag(e, tagify) }  
				},
			});
	});
	

	

})( jQuery );