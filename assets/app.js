import 'bootstrap/js/src/modal';
import 'bootstrap';
import './styles/app.css';
import {Modal} from 'bootstrap';

var registerModal = document.getElementById('registerModal')
var loginModal = document.getElementById('loginModal')
window.addEventListener('load', function () {
    if (registerModal) {
        new Modal(registerModal).show();
    } else if (loginModal) {
        new Modal(loginModal).show();
    }
})


document
    .querySelectorAll('.add_item_link')
    .forEach(btn => {
        btn.addEventListener("click", (e) => {
            const collectionHolder = document.querySelector('.' + e.currentTarget.dataset.collectionHolderClass);
            const item = document.createElement('li');
            item.className = 'list-group-item';
            item.innerHTML = collectionHolder
                .dataset
                .prototype
                .replace(
                    /__name__/g,
                    collectionHolder.dataset.index
                );

            collectionHolder.appendChild(item);

            collectionHolder.dataset.index++

            document.querySelectorAll('ul.translations li:last-child').forEach((li) => {
                addTagFormDeleteLink(li)
            })

        })

    });


const addTagFormDeleteLink = (li) => {
    const removeFormButton = document.createElement('button');
    removeFormButton.className = 'btn btn-danger mt-4';
    removeFormButton.innerText = '-';
    li.firstElementChild.append(removeFormButton);
    removeFormButton.addEventListener('click', (e) => {
        e.preventDefault();
        // remove the li for the tag form
        item.remove();
    });
}

let frontCard = document.querySelector('.front-card');
document.querySelectorAll(".add-repetition").forEach(btn => {
    btn.addEventListener('click', function(){
        const data = { 'courseId': btn.dataset.id };

        fetch('/course/flashcards', {
            method: 'POST', // or 'PUT'
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data),
        })
            .then(response => response.json())
            .then(data => {
                console.log('Success:', data);
            })
            .catch((error) => {
                console.error('Error:', error);
            });

        frontCard.innerText = 'test';
    })
})
