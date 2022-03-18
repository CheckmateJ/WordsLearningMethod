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
let words, keys, courseId, front, previous, repetitionId, translationId;
let i = 1;
document.querySelectorAll(".add-repetition").forEach(btn => {
    btn.addEventListener('click', function (e) {
        courseId = btn.dataset.courseId
        let repetition;
        translationId = btn.previousElementSibling.dataset.translationId
        if (btn.dataset.repetition === '0' && (btn.innerText === "Don't know" || btn.innerText === "Almost")) {
            repetition = 1;
        } else if (btn.dataset.repetition === '0') {
            repetition = 3;
        }
        getWordsFromCourse(btn, courseId, false, repetition, translationId);
    })
})

document.querySelectorAll('.course-card').forEach(card => {
    card.addEventListener('click', function () {
        courseId = card.dataset.courseId;
        let fromCard = true;
        getWordsFromCourse(card, courseId, fromCard);
    })
})

function getWordsFromCourse(element, id, fromCard, repetition = null, translationId = null) {
    const data = {'courseId': id, 'repetition': repetition, 'id': translationId};
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
            words = data[0];
            repetitionId = data[1];
            if (!front) {
                // previous = card.innerText;
                // card.innerText = words[previous];
                // card.dataset.translationId =
                // front = true;
                // card.dataset.translationId = repetitionId[card.innerText];
            } else {
                card.innerText = previous;
                front = false;
            }
            if (!fromCard) {
                keys = Object.keys(words);
                console.log(keys)
                card.dataset.translationId = repetitionId[keys[i]];
                card.innerText = keys[i];
                previous = card.innerText
            }
        })
        .catch((error) => {
            console.error('Error:', error);
        });
}

/*
 1. Refactoring
 2. Check names
 3. Add field in entity => repetitionDate
 4. Based on  button repetition add repetition to date
 5. When i fetching data from db check fieldRepetitionDate if This field is empty fetch this word


    Repetitions:
    1. Fix the tabs with courses
    2. For each course add button repetition
    3. Check in corntroller if repetitionDate in that course  equals = today if yes count this word and add to new array ?

 */
