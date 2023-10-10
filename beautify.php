<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Online CSS/HTML/JavaScript beautifier</title>
    <meta name="keywords" content="save web, get template, save template, get html, save html, save img, get css, bizweb, haravan, templatemonster, chili, w3layouts"/>
    <meta name="description" content="web2zip allows you to <u>download a page of website</u> and compress all file to a ZIP file"/>
    <link rel="alternate" hreflang="vi" href="http://web2zip.com/" />
    <link rel="stylesheet" href="assets/css/bootstrap.css">
    <link rel="stylesheet" href="assets/css/bootstrap-theme.css">
    <link rel="stylesheet" href="assets/css/font-awesome.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/codemirror.css">
    <script src="assets/js/jquery-2.0.3.min.js"></script>
    <script src="assets/js/bootstrap.min.js"></script>
    <script src="assets/js/codemirror/codemirror.js"></script>
    <script src="assets/js/codemirror/javascript.js"></script>
    <script src="assets/js/beautify.js"></script>
    <script src="assets/js/beautify-css.js"></script>
    <script src="assets/js/beautify-html.js"></script>
    <script src="assets/js/unpackers/javascriptobfuscator_unpacker.js"></script>
    <script src="assets/js/unpackers/urlencode_unpacker.js"></script>
    <script src="assets/js/unpackers/p_a_c_k_e_r_unpacker.js"></script>
    <script src="assets/js/unpackers/myobfuscate_unpacker.js"></script>
    <script>
        (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
                (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
            m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
        })(window,document,'script','https://www.google-analytics.com/analytics.js','ga');

        ga('create', 'UA-35827811-21', 'auto');
        ga('send', 'pageview');

    </script>
    <script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
    <script>
        (adsbygoogle = window.adsbygoogle || []).push({
            google_ad_client: "ca-pub-1657160077838995",
            enable_page_level_ads: true
        });
    </script>
    <script type="text/javascript">
        var the = {
            use_codemirror: (!window.location.href.match(/without-codemirror/)),
            beautify_in_progress: false,
            editor: null
        };

        function unpacker_filter(source) {
            var trailing_comments = '',
                comment = '',
                unpacked = '',
                found = false;

            // cut trailing comments
            do {
                found = false;
                if (/^\s*\/\*/.test(source)) {
                    found = true;
                    comment = source.substr(0, source.indexOf('*/') + 2);
                    source = source.substr(comment.length).replace(/^\s+/, '');
                    trailing_comments += comment + "\n";
                } else if (/^\s*\/\//.test(source)) {
                    found = true;
                    comment = source.match(/^\s*\/\/.*/)[0];
                    source = source.substr(comment.length).replace(/^\s+/, '');
                    trailing_comments += comment + "\n";
                }
            } while (found);

            var unpackers = [P_A_C_K_E_R, Urlencoded, /*JavascriptObfuscator,*/ MyObfuscate];
            for (var i = 0; i < unpackers.length; i++) {
                if (unpackers[i].detect(source)) {
                    unpacked = unpackers[i].unpack(source);
                    if (unpacked != source) {
                        source = unpacker_filter(unpacked);
                    }
                }
            }

            return trailing_comments + source;
        }

        function beautify() {
            if (the.beautify_in_progress) return;

            the.beautify_in_progress = true;

            var source = the.editor ? the.editor.getValue() : $('#source').val(),
                output,
                opts = {};

            opts.indent_size = 4;
            opts.indent_char = opts.indent_size == 1 ? '\t' : ' ';
            opts.max_preserve_newlines = 5;
            opts.preserve_newlines = opts.max_preserve_newlines !== "-1";
            opts.keep_array_indentation = false;
            opts.break_chained_methods = false;
            opts.indent_scripts = 'normal';
            opts.brace_style = 'collapse';
            opts.space_before_conditional = true;
            opts.unescape_strings = false;
            opts.jslint_happy = false;
            opts.end_with_newline = false;
            opts.wrap_line_length = 0;
            opts.indent_inner_html = false;
            opts.comma_first = false;
            opts.e4x = false;

            if (looks_like_html(source)) {
                output = html_beautify(source, opts);
            } else {
                if ($('#detect-packers').prop('checked')) {
                    source = unpacker_filter(source);
                }
                output = js_beautify(source, opts);
            }
            if (the.editor) {
                the.editor.setValue(output);
            } else {
                $('#source').val(output);
            }

            the.beautify_in_progress = false;
        }

        function looks_like_html(source) {
            var trimmed = source.replace(/^[ \t\n\r]+/, '');
            var comment_mark = '<' + '!-' + '-';
            return (trimmed && (trimmed.substring(0, 1) === '<' && trimmed.substring(0, 4) !== comment_mark));
        }
    </script>
</head>
<body>
<div style="line-height: 0;">
    <textarea id="source" rows="30" cols="160"></textarea>
    <div style="text-align: center; margin-top: 10px;">
        <button class="btn btn-danger submit"><strong>Beautify</strong>  <em>(ctrl-enter)</em></button>
        <button class="btn btn-success selectall"><strong>Select</strong>  <em>(ctrl-a)</em></button>
    </div>
</div>
<div id="testresults"></div>
<script type="text/javascript">
    $(function () {
        var default_text =
            "// This is just a sample script. Paste your real code (javascript, CSS or HTML) here.\n\nif ('this_is'==/an_example/){of_beautifier();}else{var a=b?(c%d):e[f];}";
        var textArea = $('#source')[0];

        if (the.use_codemirror && typeof CodeMirror !== 'undefined') {
            the.editor = CodeMirror.fromTextArea(textArea, {
                theme: 'default',
                lineNumbers: true
            });
            the.editor.focus();

            the.editor.setValue(default_text);
            $('.CodeMirror').click(function () {
                if (the.editor.getValue() == default_text) {
                    the.editor.setValue('');
                }
            });
        } else {
            $('#source').val(default_text).bind('click focus', function () {
                if ($(this).val() == default_text) {
                    $(this).val('');
                }
            }).bind('blur', function () {
                if (!$(this).val()) {
                    $(this).val(default_text);
                }
            });
        }


        $(window).bind('keydown', function (e) {
            if (e.ctrlKey && e.keyCode == 13) {
                beautify();
            }
            if (e.ctrlKey && e.keyCode == 17) {
                the.editor.execCommand('selectAll');
            }
        });

        $('.submit').click(beautify);
        $('.selectall').click(function () {
            the.editor.execCommand('selectAll');
        });
    });
</script>
</body>
</html>