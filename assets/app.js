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

var openRegisterModal = document.getElementById('openRegisterModal')
var openLoginModal = document.getElementById('openLoginModal')
var registerModal = document.getElementById('registerModal')
var loginModal = document.getElementById('loginModal')
if (registerModal) {
    new bootstrap.Modal(registerModal).show();
    openRegisterModal.addEventListener('click', function (event) {
        event.preventDefault()
    })
}else if(loginModal){
    new bootstrap.Modal(loginModal).show();
    openLoginModal.addEventListener('click', function (event) {
        event.preventDefault()
    })
}

