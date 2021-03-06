;(function ()
{
	"use strict";

	// native lazyload
	var isNative = 'loading' in HTMLImageElement.prototype;
	BX(function ()
	{
		if (isNative)
		{
			var images = document.querySelectorAll('img[data-lazy-img]');
			images.forEach(function (img)
			{
				img.src = img.dataset.src;
				if (img.dataset.srcset !== undefined)
				{
					img.srcset = img.dataset.srcset;
				}
				img.removeAttribute('data-lazy-img');
				img.removeAttribute('data-src');
				img.removeAttribute('data-srcset');
			});
		}
	});

	var observerOptions = {
		rootMargin: (document.documentElement.clientHeight) / 2 + 'px'
	};
	var observer = new IntersectionObserver(onIntersection, observerOptions);

	BX.addCustomEvent("BX.Landing.Block:init", function (event)
	{
		observer.observe(event.block);
	});


	/**
	 * @param {IntersectionObserverEntry[]} entries
	 */
	function onIntersection(entries)
	{
		entries.forEach(function (entry)
		{
			// todo: why animation first? need set lazy src before anim
			if (entry.isIntersecting)
			{
				// load <img>
				if (!isNative)
				{
					var observableImages = [].slice.call(entry.target.querySelectorAll('[data-lazy-img]'));
					observableImages.forEach(function (img)
					{
						var origSrc = BX.data(img, 'src');
						var origSrcset = BX.data(img, 'srcset');
						BX.create("img", {
							attrs: {
								src: origSrc,
								srcset: origSrcset ? origSrcset : ''
							},
							events: {
								load: function ()
								{
									BX.adjust(img, {
										attrs: {
											src: origSrc,
											srcset: origSrcset ? origSrcset : '',
											'data-lazy-src': '',
											'data-src': '',
											'data-srcset': '',
										}
									});
									BX.remove(this);
									BX.onCustomEvent("BX.Landing.Lazyload:loadImage", [{target: entry.target, src: origSrc}]);
								}
							}
						});
					});
				}

				// bg
				var observableBg = [].slice.call(entry.target.querySelectorAll('[data-lazy-bg]'));
				observableBg.forEach(function (bg)
				{
					var origStyle = BX.data(bg, 'style');
					var origSrc = BX.data(bg, 'src');
					var origSrc2x = BX.data(bg, 'src2x');
					if (origSrc2x)
					{
						var origSrcset = origSrc2x + ' 2x';
					}

					BX.create("img", {
						attrs: {
							src: origSrc,
							srcset: origSrcset ? origSrcset : ''
						},
						events: {
							load: function ()
							{
								BX.adjust(bg, {
									attrs: {
										'style': origStyle,
										'data-style': '',
										'data-src': '',
										'data-src2x': '',
									}
								});
								BX.remove(this);
								BX.onCustomEvent("BX.Landing.Lazyload:loadImage", [{target: entry.target, src: origSrc}]);
							}
						}
					});
				});

				observer.unobserve(entry.target);
			}

		});

		// todo: show all after time
	}
})();