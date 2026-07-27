@if(session('login_greeting_name'))
<script>
(function () {
    var userName = @json(session('login_greeting_name'));

    function buildGreeting(name) {
        var hour = new Date().getHours();

        if (hour >= 5 && hour < 12) {
            return 'Good Morning ' + name + ', Have a Nice day.';
        }
        if (hour >= 12 && hour < 17) {
            return 'Good afternoon ' + name + ', Have a Nice day.';
        }
        if (hour >= 17 && hour < 21) {
            return 'Good evening ' + name + ', Have a Nice day.';
        }

        return 'Good night ' + name;
    }

    function pickVoice() {
        if (!window.speechSynthesis) {
            return null;
        }

        var voices = window.speechSynthesis.getVoices();
        if (!voices.length) {
            return null;
        }

        var preferred = voices.find(function (voice) {
            return voice.lang && voice.lang.toLowerCase().indexOf('en') === 0;
        });

        return preferred || voices[0];
    }

    function speakGreeting() {
        if (!window.speechSynthesis) {
            return;
        }

        var message = buildGreeting(userName);
        window.speechSynthesis.cancel();

        var utterance = new SpeechSynthesisUtterance(message);
        utterance.rate = 0.95;
        utterance.pitch = 1;
        utterance.volume = 1;

        var voice = pickVoice();
        if (voice) {
            utterance.voice = voice;
            utterance.lang = voice.lang;
        } else {
            utterance.lang = 'en-US';
        }

        window.speechSynthesis.speak(utterance);
    }

    function whenPageReady(run) {
        if (document.body.classList.contains('panel-page-ready')) {
            run();
            return;
        }

        var observer = new MutationObserver(function () {
            if (document.body.classList.contains('panel-page-ready')) {
                observer.disconnect();
                run();
            }
        });

        observer.observe(document.body, { attributes: true, attributeFilter: ['class'] });
    }

    function init() {
        whenPageReady(function () {
            window.setTimeout(function () {
                if (window.speechSynthesis.getVoices().length === 0) {
                    window.speechSynthesis.addEventListener('voiceschanged', function handleVoices() {
                        window.speechSynthesis.removeEventListener('voiceschanged', handleVoices);
                        speakGreeting();
                    }, { once: true });
                    window.speechSynthesis.getVoices();
                    window.setTimeout(speakGreeting, 250);
                } else {
                    speakGreeting();
                }
            }, 350);
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>
@endif
