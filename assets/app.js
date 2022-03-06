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

let card = document.querySelector('.course-card');
let words, keys, courseId, front, previous;
let i = 1;
document.querySelectorAll(".add-repetition").forEach(btn => {
    btn.addEventListener('click', function (e) {
        courseId = btn.dataset.id
        let repetition, slideId;
        slideId = btn.dataset.slideid;
        console.log(btn.dataset.repetition == '0', btn.innerText == "Don't know")
        if (btn.dataset.repetition == '0' && (btn.innerText == "Don't know" || btn.innerText == "Almost")) {
            repetition = 1;
        }else if(btn.dataset.repetition == '0'){
            repetition = 3;
        }
        console.log(slideId)
        getWordsFromCourse(btn, courseId, false ,repetition, slideId);
    })
})
document.querySelectorAll('.course-card').forEach(card => {
    card.addEventListener('click', function () {
        courseId = card.dataset.id;
        let fromCard = true;
        getWordsFromCourse(card, courseId, fromCard);
    })
})

function getWordsFromCourse(element, id, fromCard , repetition = null, slideId = null) {
    const data = {'courseId': id, 'repetition': repetition, 'id': slideId};
    fetch('/course/flashcards', {
        method: 'POST', // or 'PUT'
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
    })
        .then(response => response.json())
        .then(data => {
            words = [];
            keys = [];
            words = data;
            if (!front) {
                previous = card.innerText;
                card.innerText = words[previous];
                front = true;
            } else {
                card.innerText = previous;
                front = false;
            }
            if (!fromCard) {
                keys = Object.keys(words);
                card.innerText = keys[i];
                previous = card.innerText
                i++;
            }
        })
        .catch((error) => {
            console.error('Error:', error);
        });
}
