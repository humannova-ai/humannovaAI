<?php
// Chat partial (body-only) based on userai/user/chat.html
?>
<div id="chatButton" style="position:fixed;bottom:25px;right:25px;width:60px;height:60px;background:#00DCB9;color:white;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:26px;cursor:pointer;z-index:9999;">💬</div>
<div id="chatWindow" style="position:fixed;bottom:95px;right:25px;width:350px;height:500px;background:white;border-radius:12px;display:none;flex-direction:column;z-index:10000;overflow:hidden;opacity:0;transform:scale(0.8);transition:opacity .25s,transform .25s;">
    <div id="chatHeader" style="background:#00A9A5;color:white;padding:12px;display:flex;justify-content:space-between;align-items:center;">Chatbot <button id="closeChat" style="background:none;border:none;color:white;font-size:22px;">×</button></div>
    <iframe id="chatIframe" style="flex:1;width:100%;border:none"></iframe>
</div>
<script>
const chatButton = document.getElementById('chatButton');
const chatWindow = document.getElementById('chatWindow');
const chatIframe = document.getElementById('chatIframe');
const closeChat = document.getElementById('closeChat');
// Build a robust URL that works when the app is hosted under a subpath (e.g. /our)
let chatbotUrl;
( () => {
    try {
        const pathname = window.location.pathname || '';
        const idx = pathname.indexOf('/blog');
        const base = idx !== -1 ? pathname.substring(0, idx) : '';
        const siteBase = (window.SITE_BASE || '');
        const candidates = [];

        // If server provided SITE_BASE, try it first
        if (siteBase !== '') {
            candidates.push(window.location.origin + siteBase + '/blog/public/chat.html');
            candidates.push(window.location.origin + siteBase + '/public/chat.html');
        }

        // Common absolute candidates
        candidates.push(window.location.origin + base + '/blog/public/chat.html');
        candidates.push(window.location.origin + '/blog/public/chat.html');
        candidates.push(window.location.origin + base + '/public/chat.html');
        candidates.push(window.location.origin + '/public/chat.html');

        // Add a candidate using the first path segment (handles setups like /our/blog/...)
        const parts = pathname.split('/').filter(Boolean);
        if (parts.length > 0) {
            const root = '/' + parts[0];
            // Avoid duplicating if root already contains 'blog'
            if (!root.includes('blog')) {
                candidates.push(window.location.origin + root + '/blog/public/chat.html');
                candidates.push(window.location.origin + root + '/public/chat.html');
            }
        }

        // Relative candidates (try relative to current URL)
        const relBase = (window.location.pathname || '').replace(/\/[^/]*$/, '');
        candidates.push(relBase + '/public/chat.html');
        candidates.push(relBase + '/blog/public/chat.html');
        candidates.push('./public/chat.html');
        candidates.push('public/chat.html');
        candidates.push('../public/chat.html');

        // Last-resort hardcoded fallbacks
        candidates.push('/blog/public/chat.html');
        candidates.push('/public/chat.html');

        // probe candidates and pick first that responds OK
        async function findFirstWorkingUrl(list) {
            console.debug('Chat probe candidates:', list);
            for (const u of list) {
                try {
                    const resp = await fetch(u, { method: 'GET', cache: 'no-store' });
                    if (resp && resp.ok) {
                        console.debug('Chat probe ok:', u);
                        return u;
                    }
                    console.debug('Chat probe bad status', u, resp && resp.status);
                } catch (e) {
                    console.debug('Chat probe error', u, e && e.message);
                    // try next
                }
            }
            return list[list.length - 1];
        }

        window.__chatbotUrlPromise = findFirstWorkingUrl(candidates);
    } catch (e) {
        window.__chatbotUrlPromise = Promise.resolve('/blog/public/chat.html');
    }
})();
let iframeLoaded = false;
chatButton.addEventListener('click', async ()=>{
    chatWindow.style.display = 'flex';
    if (!iframeLoaded) {
        try {
            const url = await (window.__chatbotUrlPromise || Promise.resolve('/blog/public/chat.html'));
            chatIframe.src = url;
            iframeLoaded = true;
        } catch (e) {
            console.error('Chat iframe failed to load URL probe', e);
            chatIframe.src = '/blog/public/chat.html';
            iframeLoaded = true;
        }
    }
    setTimeout(()=> chatWindow.classList.add('open'),10);
});
closeChat.addEventListener('click', ()=>{ chatWindow.classList.remove('open'); setTimeout(()=> chatWindow.style.display='none',250); });
</script>
