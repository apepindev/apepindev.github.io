<div id="comments" class="mt-8"></div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    function getGiscusTheme() {
        const theme = localStorage.getItem('dark-mode') === 'true' ? 'dark' : 'light';
        return theme === 'dark' ? 'noborder_dark' : 'noborder_light';
    }

    function setGiscusTheme() {
        function sendMessage(message) {
            const iframe = document.querySelector('iframe.giscus-frame');
            if (!iframe) return;
            iframe.contentWindow.postMessage({giscus: message}, 'https://giscus.app');
        }

        sendMessage({
            setConfig: {
                theme: getGiscusTheme(),
            },
        });
    }

    const giscusAttributes = {
        "src": "https://giscus.app/client.js",
        "data-repo": "apepindev/apepindev.github.io",
        "data-repo-id": "R_kgDOMPuhdA",
        "data-category": "Post comments",
        "data-category-id": "DIC_kwDOMPuhdM4CgeLA",
        "data-mapping": "og:title",
        "data-strict": "1",
        "data-reactions-enabled": "1",
        "data-emit-metadata": "0",
        "data-input-position": "top",
        "data-theme": getGiscusTheme(),
        "data-lang": "en",
        "crossorigin": "anonymous",
        // "data-loading": "lazy",
        "async": "",
    };

    // Dynamically create script tag
    const giscusScript = document.createElement("script");
    Object.entries(giscusAttributes).forEach(([key, value]) => giscusScript.setAttribute(key, value));

    // Append the script tag to the <article> element
    document.querySelector('#comments').appendChild(giscusScript);

    // Update giscus theme when theme switcher is clicked
    document.addEventListener('light-switched', setGiscusTheme);
});
</script>
@endpush