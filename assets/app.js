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
let flashcard, keys, courseId, front, previous, repetitionId, wordId;
let i = 1;
document.querySelectorAll(".add-repetition").forEach(btn => {
    btn.addEventListener('click', function (e) {
        courseId = btn.dataset.courseId
        let card = document.querySelector('.course-card');
        let currentRepetition = 0;
        let lastRepetition = parseInt(card.dataset.repetition);
        wordId = card.dataset.wordId;
        if (btn.innerText === "Don't know") {
            currentRepetition = 1;
        } else if (btn.innerText === "Almost") {
            currentRepetition = 2;
        } else if (lastRepetition === 1 || lastRepetition === 0) {
            currentRepetition = 3;
        } else if (lastRepetition === 3) {
            currentRepetition = 5;
        } else if (lastRepetition === 5) {
            currentRepetition = 7
        } else if (lastRepetition >= 7) {
            currentRepetition = lastRepetition * 2;
        }
        getWordsFromCourse(btn, courseId, false, currentRepetition, wordId, lastRepetition);
    })
})

document.querySelectorAll('.course-card').forEach(card => {
    card.addEventListener('click', function () {
        courseId = card.dataset.courseId;
        let fromCard = true;
        getWordsFromCourse(card, courseId, fromCard);
    })
})

function getWordsFromCourse(element, id, fromCard, repetition = null, wordId = null, lastRepetition) {
    console.log(id, repetition, wordId)
    let newCourse = window.location.pathname.indexOf('presentation') > -1;
    console.log(newCourse)
    const data = {
        'courseId': id,
        'repetition': repetition,
        'id': wordId,
        lastRepetition: lastRepetition,
        newCourse: newCourse
    };
    fetch('/course/flashcards', {
        method: 'POST', // or 'PUT'
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
    })
        .then(response => response.json())
        .then(data => {
            console.log(JSON.parse(data))
            if(data.message){
                alert(data.message);
                window.location.href = `/course`;
            }
            flashcard = JSON.parse(data);
            console.log(flashcard)
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
                console.log(card)
                card.dataset.wordId = flashcard.id
                card.dataset.repetition = flashcard.repetition;
                document.querySelector('.back-side').innerText = flashcard.backSide;
                document.querySelector('.back-side').style.display = 'none';
                card.innerText = flashcard.frontSide;
                previous = card.innerText
            }
        })
        .catch((error) => {
            console.error('Error:', error);
        });
}

let id;

document.querySelectorAll('.course-type').forEach(course => {
    course.addEventListener('click', function () {
        id = course.dataset.id;
    })
})
// document.querySelector('.start-new-words').addEventListener('click', function () {
//     window.location.href = `/course/${id}/presentation`;
// })
// document.querySelector('.start-repetition').addEventListener('click', function () {
//     window.location.href = `/course/${id}/repetition`;
// })
document.querySelector('.check').addEventListener('click', function () {
    console.log(document.querySelector('.back-side'))
    document.querySelector('.back-side').style.display = 'block';
    document.querySelectorAll('.add-repetition').forEach(btn => {
        btn.style.display = 'inline-flex';
    })
})


// fetching all words to learning
// changing cureently way to displaying to loop each word and then displaying words