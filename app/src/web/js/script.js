// auto focus input
function focusInput(event) {
    let el;
    let hashID = event.target.getAttribute('HREF');
    if (hashID == '#' || hashID == '') {
        el = event.target.nextElementSibling.querySelector('input');
    } else {
        let focusInput = hashID + ' input';
        let focusTextArea = hashID + ' textarea';
        el_focusInput = document.querySelector(focusInput);
        el_focusTextArea = document.querySelector(focusTextArea);
        el = el_focusInput ? el_focusInput : el_focusTextArea;
    }
    let timer = setTimeout(function addFocus(el) {
        el.focus()
    }, 500, el)
}

function noClose(event){
    event.stopPropagation();
}

(function() {
    'use strict';
    window.addEventListener('load', function() {
        var forms = document.getElementsByClassName('needs-validation');
        var validation = Array.prototype.filter.call(forms, function(form) {
            form.addEventListener('submit', function(event) {
                if (form.checkValidity() === false) {
                    event.preventDefault();
                    event.stopPropagation();
                }
                form.classList.add('was-validated');
            }, false);
        });
    }, false);
})();

// menu links
// document.addEventListener('DOMContentLoaded', linksHandler);

// function linksHandler() {
//     const arrLinks = document.querySelectorAll('#menuContent .dropdown-item');
//     arrLinks.forEach(element => {
//         element.addEventListener('click', (event) => {
//             event.preventDefault();
//             const url = event.target.getAttribute('href');
//             event.target.ontransitionend = function () {}
//         })
//     });
// }

$('nav a.dropdown-item[href="#"]').on('click', function () {
    $('#not-avilable-modal').modal('show');
    return false;
});
