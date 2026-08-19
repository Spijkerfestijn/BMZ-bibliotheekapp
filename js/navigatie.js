(function () {
    var SLEUTEL = 'boekenResultatenLijst';

    window.kaftFout = function (img, modus) {
        if (modus === 'verwijder') {
            img.remove();
            return;
        }
        var span = document.createElement('span');
        span.className = 'thumb thumb-leeg';
        span.setAttribute('aria-hidden', 'true');
        img.replaceWith(span);
    };

    window.initLijstNavigatie = function () {
        var links = Array.prototype.slice.call(document.querySelectorAll('.boekenlijst > li > a'));
        if (links.length === 0) {
            return;
        }

        function isInvoerveld(el) {
            return !!el && (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA' || el.tagName === 'SELECT');
        }

        document.addEventListener('keydown', function (e) {
            if (e.key !== 'ArrowDown' && e.key !== 'ArrowUp') {
                return;
            }
            if (isInvoerveld(document.activeElement)) {
                return;
            }

            e.preventDefault();

            var huidigeIndex = links.indexOf(document.activeElement);
            var volgendeIndex;
            if (e.key === 'ArrowDown') {
                volgendeIndex = huidigeIndex === -1 ? 0 : Math.min(huidigeIndex + 1, links.length - 1);
            } else {
                volgendeIndex = huidigeIndex === -1 ? links.length - 1 : Math.max(huidigeIndex - 1, 0);
            }
            links[volgendeIndex].focus();
        });
    };

    window.bewaarResultatenLijst = function (urls) {
        try {
            sessionStorage.setItem(SLEUTEL, JSON.stringify(urls));
        } catch (e) {
            // sessionStorage niet beschikbaar (bijv. privénavigatie) - swipe-navigatie werkt dan simpelweg niet
        }
    };

    window.initSwipeNavigatie = function () {
        var ruw = null;
        try {
            ruw = sessionStorage.getItem(SLEUTEL);
        } catch (e) {
            return;
        }
        if (!ruw) {
            return;
        }

        var lijst;
        try {
            lijst = JSON.parse(ruw);
        } catch (e) {
            return;
        }
        if (!Array.isArray(lijst) || lijst.length < 2) {
            return;
        }

        var huidigeUrl = window.location.pathname + window.location.search;
        var index = lijst.indexOf(huidigeUrl);
        if (index === -1) {
            return;
        }

        var vorige = index > 0 ? lijst[index - 1] : null;
        var volgende = index < lijst.length - 1 ? lijst[index + 1] : null;

        var balk = document.createElement('div');
        balk.className = 'swipe-navigatie';

        var vorigeEl = document.createElement('a');
        vorigeEl.className = 'swipe-knop';
        vorigeEl.setAttribute('aria-label', 'Vorig boek');
        vorigeEl.innerHTML = '&larr;';
        if (vorige) {
            vorigeEl.href = vorige;
        } else {
            vorigeEl.classList.add('swipe-knop-uit');
        }

        var teller = document.createElement('span');
        teller.className = 'swipe-teller';
        teller.textContent = (index + 1) + ' / ' + lijst.length;

        var volgendeEl = document.createElement('a');
        volgendeEl.className = 'swipe-knop';
        volgendeEl.setAttribute('aria-label', 'Volgend boek');
        volgendeEl.innerHTML = '&rarr;';
        if (volgende) {
            volgendeEl.href = volgende;
        } else {
            volgendeEl.classList.add('swipe-knop-uit');
        }

        balk.appendChild(vorigeEl);
        balk.appendChild(teller);
        balk.appendChild(volgendeEl);

        var main = document.querySelector('main');
        if (main) {
            main.insertBefore(balk, main.firstChild);
        }

        var startX = null;
        var startY = null;

        document.addEventListener('touchstart', function (e) {
            startX = e.touches[0].clientX;
            startY = e.touches[0].clientY;
        }, { passive: true });

        document.addEventListener('touchend', function (e) {
            if (startX === null) {
                return;
            }
            var dx = e.changedTouches[0].clientX - startX;
            var dy = e.changedTouches[0].clientY - startY;
            startX = null;

            if (Math.abs(dx) < 60 || Math.abs(dx) < Math.abs(dy) * 1.5) {
                return;
            }
            if (dx < 0 && volgende) {
                window.location.href = volgende;
            } else if (dx > 0 && vorige) {
                window.location.href = vorige;
            }
        }, { passive: true });
    };
})();
