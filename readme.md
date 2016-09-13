# WP MathJax Advanced

This plugin enables mathjax (http://www.mathjax.org) functionality for
WordPress (http://www.wordpress.org).

# Description

Mathjax enables rendering of embedded latex or mathml in HTML pages.
This plugin adds this functionality to wordpress. The mathjax javascript is
inject on-demand only to those pages which require it. This ensures that
mathjax is not loaded for all pages, which will otherwise slow loading down.

The MathJax javascript can be delivered from your own server, or you can
utilise the [MathJax Content Distribution Network (CDN)]
(http://www.mathjax.org/docs/latest/start.html#mathjax-cdn), which is the preferred
mechanism as it offers increased speed and stability over hosting the Javascript
and configuring the library yourself. Use of the CDN is governed by these
[Terms of Service](http://www.mathjax.org/download/mathjax-cdn-terms-of-service/).

This plugin uses two short codes below instead of "latex", "nomathjax", $$x^2&& and \(x^2\).

+ [mj-i]E=mc^2[/mj-i] short code renders formula as inline element.  
+ [mj-b]E=mc^2[/mj-b] short code renders formula as block element.

Base plugin "MathJax-LaTeX" is developed on
[Github](https://github.com/phillord/mathjax-latex).

# Copyright

GPLv3

Base plugin "MathJax-LaTeX" is copyright Phillip Lord, Newcastle University and is licensed
under GPLv2.