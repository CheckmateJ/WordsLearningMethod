/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)

// start the Stimulus application
import './bootstrap';
import 'bootstrap/js/src/modal'

var myInput = document.getElementById('openModal')
var myModal = new bootstrap.Modal(document.getElementById('exampleModal'))

if (myModal) {
    myModal.show();
    myInput.addEventListener('click', function (event) {
        event.preventDefault()
    })
}

