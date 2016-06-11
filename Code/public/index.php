<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8" />

    <title>Docker</title>

    <meta name="description" content="A framework for easily creating beautiful presentations using HTML" />
    <meta name="author" content="Hakim El Hattab" />

    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent" />

    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no, minimal-ui" />

    <link rel="stylesheet" href="/libs/reveal.js-3.2.0/css/reveal.css" />
    <link rel="stylesheet" href="/libs/reveal.js-3.2.0/css/theme/black.css" id="theme" />

    <!-- Code syntax highlighting -->
    <link rel="stylesheet" href="/libs/reveal.js-3.2.0/lib/css/zenburn.css" />

    <!-- Printing and PDF exports -->
    <script>
            var link = document.createElement( 'link' );
            link.rel = 'stylesheet';
            link.type = 'text/css';
            link.href = window.location.search.match( /print-pdf/gi ) ? '/libs/reveal.js-3.2.0/css/print/pdf.css' : '/libs/reveal.js-3.2.0/css/print/paper.css';
            document.getElementsByTagName( 'head' )[0].appendChild( link );
    </script>
</head>

<body>
    <div class="reveal">
        <!-- Any section element inside of this container is displayed as a slide -->
        <div class="slides">
            <div class="reveal">
                <div class="slides">
                    <section data-markdown="/slides/docker/docker.md" data-separator="---"></section>
                </div>
            </div>
        </div>
    </div>

    <script src="/libs/reveal.js-3.2.0/lib/js/head.min.js"></script>
    <script src="/libs/reveal.js-3.2.0/js/reveal.js"></script>
    <script>
            // Full list of configuration options available at:
            // https://github.com/hakimel/reveal.js#configuration
            Reveal.initialize({
                controls: true,
                progress: true,
                history: true,
                center: true,

                transition: 'slide', // none/fade/slide/convex/concave/zoom

                // Optional reveal.js plugins
                dependencies: [
                    { src: '/libs/reveal.js-3.2.0/lib/js/classList.js', condition: function() { return !document.body.classList; } },
                    { src: '/libs/reveal.js-3.2.0/plugin/markdown/marked.js', condition: function() { return !!document.querySelector( '[data-markdown]' ); } },
                    { src: '/libs/reveal.js-3.2.0/plugin/markdown/markdown.js', condition: function() { return !!document.querySelector( '[data-markdown]' ); } },
                    { src: '/libs/reveal.js-3.2.0/plugin/highlight/highlight.js', async: true, callback: function() { hljs.initHighlightingOnLoad(); } },
                    { src: '/libs/reveal.js-3.2.0/plugin/zoom-js/zoom.js', async: true },
                    { src: '/libs/reveal.js-3.2.0/plugin/notes/notes.js', async: true }
                ]
            });

    </script>

</body>
</html>
