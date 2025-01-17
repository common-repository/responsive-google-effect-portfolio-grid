/*
* debouncedresize: special jQuery event that happens once after a window resize
*
* latest version and complete README available on Github:
* https://github.com/louisremi/jquery-smartresize/blob/master/jquery.debouncedresize.js
*
* Copyright 2011 @louis_remi
* Licensed under the MIT license.
*/
var jQueryevent = jQuery.event,
jQueryspecial,
resizeTimeout;

jQueryspecial = jQueryevent.special.debouncedresize = {
	setup: function() {
		jQuery( this ).on( "resize", jQueryspecial.handler );
	},
	teardown: function() {
		jQuery( this ).off( "resize", jQueryspecial.handler );
	},
	handler: function( event, execAsap ) {
		// Save the context
		var context = this,
			args = arguments,
			dispatch = function() {
				// set correct event type
				event.type = "debouncedresize";
				jQueryevent.dispatch.apply( context, args );
			};

		if ( resizeTimeout ) {
			clearTimeout( resizeTimeout );
		}

		execAsap ?
			dispatch() :
			resizeTimeout = setTimeout( dispatch, jQueryspecial.threshold );
	},
	threshold: 250
};

// ======================= imagesLoaded Plugin ===============================
// https://github.com/desandro/imagesloaded

// jQuery('#my-container').imagesLoaded(myFunction)
// execute a callback when all images have loaded.
// needed because .load() doesn't work on cached images

// callback function gets image collection as argument
//  this is the container

// original: MIT license. Paul Irish. 2010.
// contributors: Oren Solomianik, David DeSandro, Yiannis Chatzikonstantinou

// blank image data-uri bypasses webkit log warning (thx doug jones)
var BLANK = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';

jQuery.fn.imagesLoaded = function( callback ) {
	var jQuerythis = this,
		deferred = jQuery.isFunction(jQuery.Deferred) ? jQuery.Deferred() : 0,
		hasNotify = jQuery.isFunction(deferred.notify),
		jQueryimages = jQuerythis.find('img').add( jQuerythis.filter('img') ),
		loaded = [],
		proper = [],
		broken = [];

	// Register deferred callbacks
	if (jQuery.isPlainObject(callback)) {
		jQuery.each(callback, function (key, value) {
			if (key === 'callback') {
				callback = value;
			} else if (deferred) {
				deferred[key](value);
			}
		});
	}

	function doneLoading() {
		var jQueryproper = jQuery(proper),
			jQuerybroken = jQuery(broken);

		if ( deferred ) {
			if ( broken.length ) {
				deferred.reject( jQueryimages, jQueryproper, jQuerybroken );
			} else {
				deferred.resolve( jQueryimages );
			}
		}

		if ( jQuery.isFunction( callback ) ) {
			callback.call( jQuerythis, jQueryimages, jQueryproper, jQuerybroken );
		}
	}

	function imgLoaded( img, isBroken ) {
		// don't proceed if BLANK image, or image is already loaded
		if ( img.src === BLANK || jQuery.inArray( img, loaded ) !== -1 ) {
			return;
		}

		// store element in loaded images array
		loaded.push( img );

		// keep track of broken and properly loaded images
		if ( isBroken ) {
			broken.push( img );
		} else {
			proper.push( img );
		}

		// cache image and its state for future calls
		jQuery.data( img, 'imagesLoaded', { isBroken: isBroken, src: img.src } );

		// trigger deferred progress method if present
		if ( hasNotify ) {
			deferred.notifyWith( jQuery(img), [ isBroken, jQueryimages, jQuery(proper), jQuery(broken) ] );
		}

		// call doneLoading and clean listeners if all images are loaded
		if ( jQueryimages.length === loaded.length ){
			setTimeout( doneLoading );
			jQueryimages.unbind( '.imagesLoaded' );
		}
	}

	// if no images, trigger immediately
	if ( !jQueryimages.length ) {
		doneLoading();
	} else {
		jQueryimages.bind( 'load.imagesLoaded error.imagesLoaded', function( event ){
			// trigger imgLoaded
			imgLoaded( event.target, event.type === 'error' );
		}).each( function( i, el ) {
			var src = el.src;

			// find out if this image has been already checked for status
			// if it was, and src has not changed, call imgLoaded on it
			var cached = jQuery.data( el, 'imagesLoaded' );
			if ( cached && cached.src === src ) {
				imgLoaded( el, cached.isBroken );
				return;
			}

			// if complete is true and browser supports natural sizes, try
			// to check for image status manually
			if ( el.complete && el.naturalWidth !== undefined ) {
				imgLoaded( el, el.naturalWidth === 0 || el.naturalHeight === 0 );
				return;
			}

			// cached images don't fire load sometimes, so we reset src, but only when
			// dealing with IE, or image is complete (loaded) and failed manual check
			// webkit hack from http://groups.google.com/group/jquery-dev/browse_thread/thread/eee6ab7b2da50e1f
			if ( el.readyState || el.complete ) {
				el.src = BLANK;
				el.src = src;
			}
		});
	}

	return deferred ? deferred.promise( jQuerythis ) : jQuerythis;
};

var Grid = (function() {

		// list of items
	var jQuerygrid = jQuery( '.og-grid' ),
		// the items
		jQueryitems = jQuerygrid.children( 'li' ),
				// current expanded item's index
		current = -1,
		// position (top) of the expanded item
		// used to know if the preview will expand in a different row
		previewPos = -1,
		// extra amount of pixels to scroll the window
		scrollExtra = 0,
		// extra margin when expanded (between preview overlay and the next items)
		marginExpanded = 10,
		jQuerywindow = jQuery( window ), winsize,
		jQuerybody = jQuery( 'html, body' ),
		// transitionend events
		transEndEventNames = {
			'WebkitTransition' : 'webkitTransitionEnd',
			'MozTransition' : 'transitionend',
			'OTransition' : 'oTransitionEnd',
			'msTransition' : 'MSTransitionEnd',
			'transition' : 'transitionend'
		},
		transEndEventName = transEndEventNames[ Modernizr.prefixed( 'transition' ) ],
		// support for csstransitions
		support = Modernizr.csstransitions,
		// default settings
		settings = {
			minHeight : 500,
			speed : 350,
			easing : 'ease'
		};

	function init( config ) {
		
		// the settings..
		settings = jQuery.extend( true, {}, settings, config );

		// preload all images
		jQuerygrid.imagesLoaded( function() {

			// save item´s size and offset
			saveItemInfo( true );
			// get window´s size
			getWinSize();
			// initialize some events
			initEvents();

		} );

	}

	// add more items to the grid.
	// the new items need to appended to the grid.
	// after that call Grid.addItems(theItems);
	function addItems( jQuerynewitems ) {

		jQueryitems = jQueryitems.add( jQuerynewitems );

		jQuerynewitems.each( function() {
			var jQueryitem = jQuery( this );
			jQueryitem.data( {
				offsetTop : jQueryitem.offset().top,
				height : jQueryitem.height()
			} );
		} );

		initItemsEvents( jQuerynewitems );

	}

	// saves the item´s offset top and height (if saveheight is true)
	function saveItemInfo( saveheight ) {
		jQueryitems.each( function() {
			var jQueryitem = jQuery( this );
			jQueryitem.data( 'offsetTop', jQueryitem.offset().top );
			if( saveheight ) {
				jQueryitem.data( 'height', jQueryitem.height() );
			}
		} );
	}

	function initEvents() {
		
		// when clicking an item, show the preview with the item´s info and large image.
		// close the item if already expanded.
		// also close if clicking on the item´s cross
		initItemsEvents( jQueryitems );
		
		// on window resize get the window´s size again
		// reset some values..
		jQuerywindow.on( 'debouncedresize', function() {
			
			scrollExtra = 0;
			previewPos = -1;
			// save item´s offset
			saveItemInfo();
			getWinSize();
			var preview = jQuery.data( this, 'preview' );
			if( typeof preview != 'undefined' ) {
				hidePreview();
			}

		} );

	}

	function initItemsEvents( jQueryitems ) {
	

		jQueryitems.on( 'click', 'span.og-close', function() {
			hidePreview();
			return false;
		} ).children( 'a' ).on( 'click', function(e) {

			var temp_id = jQuery(this).attr('id');
			var jQueryitem = jQuery( this ).parent();
			// check if item already opened
			current === jQueryitem.index() ? hidePreview() : showPreview( jQueryitem );
			
			var link_text = jQuery('#rgpg-hidden-'+temp_id).val();
			
			jQuery( this ).next('.og-expander').find('.visit').html(link_text);

			return false;

		} );
	}

	function getWinSize() {
		winsize = { width : jQuerywindow.width(), height : jQuerywindow.height() };
	}

	function showPreview( jQueryitem ) {

		var preview = jQuery.data( this, 'preview' ),
			// item´s offset top
			position = jQueryitem.data( 'offsetTop' );

		scrollExtra = 0;

		// if a preview exists and previewPos is different (different row) from item´s top then close it
		if( typeof preview != 'undefined' ) {

			// not in the same row
			if( previewPos !== position ) {
				// if position > previewPos then we need to take te current preview´s height in consideration when scrolling the window
				if( position > previewPos ) {
					scrollExtra = preview.height;
				}
				hidePreview();
			}
			// same row
			else {
				preview.update( jQueryitem );
				return false;
			}
			
		}

		// update previewPos
		previewPos = position;
		// initialize new preview for the clicked item
		preview = jQuery.data( this, 'preview', new Preview( jQueryitem ) );
		// expand preview overlay
		preview.open();

	}

	function hidePreview() {
		current = -1;
		var preview = jQuery.data( this, 'preview' );
		preview.close();
		jQuery.removeData( this, 'preview' );
	}

	// the preview obj / overlay
	function Preview( jQueryitem ) {
		this.jQueryitem = jQueryitem;
		this.expandedIdx = this.jQueryitem.index();
		this.create();
		this.update();
	}

	Preview.prototype = {
		create : function() {
			// create Preview structure:
			this.jQuerytitle = jQuery( '<h3></h3>' );
			this.jQuerydescription = jQuery( '<p></p>' );
			this.jQueryhref = jQuery( '<a href="#" class="visit">Visit website</a>' );
			this.jQuerydetails = jQuery( '<div class="og-details"></div>' ).append( this.jQuerytitle, this.jQuerydescription, this.jQueryhref );
			this.jQueryloading = jQuery( '<div class="og-loading"></div>' );
			this.jQueryfullimage = jQuery( '<div class="og-fullimg"></div>' ).append( this.jQueryloading );
			this.jQueryclosePreview = jQuery( '<span class="og-close"></span>' );
			this.jQuerypreviewInner = jQuery( '<div class="og-expander-inner clearfix"></div>' ).append( this.jQueryclosePreview, this.jQueryfullimage, this.jQuerydetails );
			this.jQuerypreviewEl = jQuery( '<div class="og-expander"></div>' ).append( this.jQuerypreviewInner );
			// append preview element to the item
			this.jQueryitem.append( this.getEl() );
			// set the transitions for the preview and the item
			if( support ) {
				this.setTransition();
			}
		},
		update : function( jQueryitem ) {

			if( jQueryitem ) {
				this.jQueryitem = jQueryitem;
			}
			
			// if already expanded remove class "og-expanded" from current item and add it to new item
			if( current !== -1 ) {
				var jQuerycurrentItem = jQueryitems.eq( current );
				jQuerycurrentItem.removeClass( 'og-expanded' );
				this.jQueryitem.addClass( 'og-expanded' );
				// position the preview correctly
				this.positionPreview();
			}

			// update current value
			current = this.jQueryitem.index();

			// update preview´s content
			var jQueryitemEl = this.jQueryitem.children( 'a' ),
				eldata = {
					href : jQueryitemEl.attr( 'href' ),
					largesrc : jQueryitemEl.data( 'largesrc' ),
					title : jQueryitemEl.data( 'title' ),
					description : jQueryitemEl.data( 'description' )
				};

			this.jQuerytitle.html( eldata.title );
			this.jQuerydescription.html( eldata.description );
			this.jQueryhref.attr( 'href', eldata.href );

			var self = this;
			
			// remove the current image in the preview
			if( typeof self.jQuerylargeImg != 'undefined' ) {
				self.jQuerylargeImg.remove();
			}

			// preload large image and add it to the preview
			// for smaller screens we don´t display the large image (the media query will hide the fullimage wrapper)
			if( self.jQueryfullimage.is( ':visible' ) ) {
				this.jQueryloading.show();
				jQuery( '<img/>' ).load( function() {
					var jQueryimg = jQuery( this );
					if( jQueryimg.attr( 'src' ) === self.jQueryitem.children('a').data( 'largesrc' ) ) {
						self.jQueryloading.hide();
						self.jQueryfullimage.find( 'img' ).remove();
						self.jQuerylargeImg = jQueryimg.fadeIn( 350 );
						self.jQueryfullimage.append( self.jQuerylargeImg );
					}
				} ).attr( 'src', eldata.largesrc );	
			}

		},
		open : function() {

			setTimeout( jQuery.proxy( function() {	
				// set the height for the preview and the item
				this.setHeights();
				// scroll to position the preview in the right place
				this.positionPreview();
			}, this ), 25 );

		},
		close : function() {

			var self = this,
				onEndFn = function() {
					if( support ) {
						jQuery( this ).off( transEndEventName );
					}
					self.jQueryitem.removeClass( 'og-expanded' );
					self.jQuerypreviewEl.remove();
				};

			setTimeout( jQuery.proxy( function() {

				if( typeof this.jQuerylargeImg !== 'undefined' ) {
					this.jQuerylargeImg.fadeOut( 'fast' );
				}
				this.jQuerypreviewEl.css( 'height', 0 );
				// the current expanded item (might be different from this.jQueryitem)
				var jQueryexpandedItem = jQueryitems.eq( this.expandedIdx );
				jQueryexpandedItem.css( 'height', jQueryexpandedItem.data( 'height' ) ).on( transEndEventName, onEndFn );

				if( !support ) {
					onEndFn.call();
				}

			}, this ), 25 );
			
			return false;

		},
		calcHeight : function() {

			var heightPreview = winsize.height - this.jQueryitem.data( 'height' ) - marginExpanded,
				itemHeight = winsize.height;

			if( heightPreview < settings.minHeight ) {
				heightPreview = settings.minHeight;
				itemHeight = settings.minHeight + this.jQueryitem.data( 'height' ) + marginExpanded;
			}

			this.height = heightPreview;
			this.itemHeight = itemHeight;

		},
		setHeights : function() {

			var self = this,
				onEndFn = function() {
					if( support ) {
						self.jQueryitem.off( transEndEventName );
					}
					self.jQueryitem.addClass( 'og-expanded' );
				};

			this.calcHeight();
			this.jQuerypreviewEl.css( 'height', this.height );
			this.jQueryitem.css( 'height', this.itemHeight ).on( transEndEventName, onEndFn );

			if( !support ) {
				onEndFn.call();
			}

		},
		positionPreview : function() {

			// scroll page
			// case 1 : preview height + item height fits in window´s height
			// case 2 : preview height + item height does not fit in window´s height and preview height is smaller than window´s height
			// case 3 : preview height + item height does not fit in window´s height and preview height is bigger than window´s height
			var position = this.jQueryitem.data( 'offsetTop' ),
				previewOffsetT = this.jQuerypreviewEl.offset().top - scrollExtra,
				scrollVal = this.height + this.jQueryitem.data( 'height' ) + marginExpanded <= winsize.height ? position : this.height < winsize.height ? previewOffsetT - ( winsize.height - this.height ) : previewOffsetT;
			
			jQuerybody.animate( { scrollTop : scrollVal }, settings.speed );

		},
		setTransition  : function() {
			this.jQuerypreviewEl.css( 'transition', 'height ' + settings.speed + 'ms ' + settings.easing );
			this.jQueryitem.css( 'transition', 'height ' + settings.speed + 'ms ' + settings.easing );
		},
		getEl : function() {
			return this.jQuerypreviewEl;
		}
	}

	return { 
		init : init,
		addItems : addItems
	};

})();

jQuery(function() {
	Grid.init();
});