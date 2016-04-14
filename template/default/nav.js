(function() {
    var link;

    if (!(document.body.addEventListener && document.querySelector)) {
        return;
    }
    document.body.addEventListener('keydown', function(e) {
        switch (e.which) {
            case 37: // left
                e.preventDefault();
                link = document.querySelector('.comic .nav [rel="prev"]');
                if (link) {
                    link.click();
                }
                break;
            case 39: // right
                e.preventDefault();
                link = document.querySelector('.comic .nav [rel="next"]');
                if (link) {
                    link.click();
                }
                break;
        }
    });

})();
