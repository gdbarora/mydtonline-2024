jQuery( document ).ready( function( $ ) {
	
var Prevent_Content_Copy = Prevent_Content_Copy || {};

Prevent_Content_Copy.init = function() {
	$( 'body' ).on( 'contextmenu', function( e ) {
		e.preventDefault();
		return false;
	});

	$( 'body' ).bind( 'cut copy paste', function( e ) {
		e.preventDefault();
		return false;
	});
};

Prevent_Content_Copy.init();

} );