(function (window) {

    var ready = function (fn) {
        if (document.readyState != 'loading'){
            fn();
        } else {
            document.addEventListener('DOMContentLoaded', fn);
        }
    };

    var highlight = function () {
        var res = window.location.hash.match(/(msg[1-9]\d*)/);
        if (res) {
            var msgs = document.querySelectorAll('div.msg');
            for (var i = 0; i < msgs.length; i++) {
                if (msgs[i].id === res[0])
                    msgs[i].classList.add('highLighted');
                else if (msgs[i].classList.contains('highLighted'))
                    msgs[i].classList.remove('highLighted');
            }
        }
    };

    ready(function () {

        document.querySelector('body').classList.add('js');

        // Forum recommendations show/hide
        var recBlock = document.getElementById('trigger');
        if (recBlock) {
            recBlock.addEventListener('click', function (e) {
                e.preventDefault();
                var box = document.getElementById('box');
                if (box.style.display === 'block') {
                    box.style.display = 'none';
                } else {
                    box.style.display = 'block';
                }
            });
        }

        // Highlight
        window.addEventListener('hashchange', highlight);
        highlight();            // Trigger on load

        // Filter block
        var filterLinks = document.querySelectorAll('a.filter-link');
        for (var i = 0; i < filterLinks.length; i++) {
            filterLinks[i].addEventListener('click', function (e) {
                e.preventDefault();
                var block = document.querySelector(this.dataset.fblock);
                if (this.classList.contains('opened')) {
                    this.classList.remove('opened');
                    block.style.display = 'none';
                } else {
                    this.classList.add('opened');
                    block.style.display = 'block';
                }
            });
        }

        // Toggle filtered message
        var filtered = document.querySelectorAll('a.toggle-hidden');
        for (var i = 0; i < filtered.length; i++) {
            filtered[i].addEventListener('click', function (e) {
                e.preventDefault();
                var content = document.querySelector(this.dataset.hidden);
                if (content.classList.contains('msg-hidden')) {
                    content.classList.add('msg-show');
                    content.classList.remove('msg-hidden');
                } else {
                    content.classList.add('msg-hidden');
                }
            });
        }

    });

})(window);
