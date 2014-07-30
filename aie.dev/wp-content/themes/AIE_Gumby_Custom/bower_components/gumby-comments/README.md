Gumby Comments
==============

This extension provides a purely client-side solution to conditional loading. Refactored into a Gumby UI module from the awesome [ResponsiveComments](http://responsivecomments.com/). For more detailed documentation please check out the [Gumby docs](http://gumbyframework.com).

Installation
------------

A bower package is available to install this module into your project. We recommend using this method to install Gumby and any extra UI modules, however you can alternatively move the individuals files into your project.

	$ bower install gumby-comments

Include gumby.comments.js in the same fashion as your other UI modules, after gumby.js and before gumby.init.js. In production you should minify JavaScript files into a single optimized gumby.min.js file, ensuring the order (gumby.js, UI modules, gumby.init.js) is retained.

	<!--
	Include gumby.js followed by UI modules.
	Or concatenate and minify into a single file-->
	<script src="/bower_components/gumby/js/libs/gumby.js"></script>
	<script src="/bower_components/gumby/js/libs/ui/gumby.skiplink.js"></script>
	<script src="/bower_components/gumby/js/libs/ui/gumby.toggleswitch.js"></script>
	<script src="/bower_components/gumby-comments/gumby.comments.js"></script>
	<script src="/bower_components/gumby/js/libs/gumby.init.js"></script>

	<!-- In production minifiy and combine the above files into gumby.min.js -->
	<script src="js/gumby.min.js"></script>
	<script src="js/plugins.js"></script>
	<script src="js/main.js"></script>

Usage
-----
### Media Queries

To get started using Gumby Comments, add a `[gumby-comment-media]` attribute containing a valid media query to any element containing children that you wish to conditionally load. The Gumby Comments concept will only work in a progressivelty enhanced experience so make sure you use minimum width media queries.

```html
<div gumby-comment-media="(min-width: 769px)">...</div>
```

The `[gumby-comment-media]` containers should contain one commented-out segment of markup as well as any other HTML they require.

```html
<div gumby-comment-media="(min-width: 769px)">
	<!-- <div><p>Any content can go in here</p></div> -->
</div>
```

### Feature Detection

Gumby Comments also supports feature detection using [Modernizr](http://modernizr.com/). Make sure you include a Modernizr build with all the tests you need before the Gumby JS. A Modernizr test, or a comma separated list of multiple Modernizr tests, can then be specified in `[gumby-comment-supports]`.

```html
<div gumby-comment-supports="svg">
	<!-- <div><p>Any content can go in here, including images</p></div> -->
</div>
```

The `[gumby-comment-supports]` containers should also contain one commented out segment of markup as well as any other HTML they require.

### DOM Insertion

The commented out markup inside the Gumby Comments container will be inserted into the DOM when the specified media query or feature detection passes. Any valid [insertAdjacentHTML](https://developer.mozilla.org/en-US/docs/Web/API/Element.insertAdjacentHTML) insertion type can be specified in `[data-responsive-comment-insert]`, valid options are `beforebegin`, `afterbegin`, `beforeend`, `afterend` with a default of `beforeend`.

```html
<div gumby-comment-insert="afterbegin"
     gumby-comment-media="(min-width: 769px)">
	<!-- <div><p>Any content can go in here, including images</p></div> -->
</div>
```

Check out the [ResonsiveComments website](http://responsivecomments.com/) for a detailed look at how the module works.


**MIT Open Source License**

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit
persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the
Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
