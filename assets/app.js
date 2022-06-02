import 'bootstrap/js/src/modal';
import './bootstrap';
import {Modal} from 'bootstrap';
import './scripts'

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
        li.remove();
    });
}

let card = document.querySelector('.course-card');
let flashcard, courseId, front, previous, wordId;
document.querySelectorAll(".add-repetition").forEach(btn => {
    btn.addEventListener('click', function (e) {
        courseId = btn.dataset.courseId
        card = document.querySelector('.course-card');
        let currentRepetition = 0;
        let lastRepetition = parseInt(card.dataset.repetition);
        wordId = card.dataset.wordId;
        if (btn.innerText === "Don't know") {
            currentRepetition = 1;
        } else if (btn.innerText === "Almost") {
            currentRepetition = 2;
        } else if (lastRepetition === 1 || lastRepetition === 0 || lastRepetition === 2) {
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
    let newCourse = window.location.pathname.indexOf('presentation') > -1;
    const data = {
        'courseId': id,
        'repetition': repetition,
        'id': wordId,
        lastRepetition: lastRepetition,
        newCourse: newCourse,
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
                card.dataset.wordId = flashcard.id
                card.dataset.repetition = flashcard.repetition;
                document.querySelector('.back-side').innerText = flashcard.backSide;
                document.querySelector('.back-side').style.display = 'none';
                document.querySelectorAll('.add-repetition').forEach(btn => {
                    btn.style.display = 'none';
                })
                card.innerText = flashcard.frontSide;
                previous = card.innerText
            }
        })
        .catch((error) => {
            console.error('Error:', error);
        });
}

let id;

let check = document.querySelector('.check');
let newWords = document.querySelector('.start-new-words')
let repetition = document.querySelector('.start-repetition');
if (check) {
    check.addEventListener('click', function () {
            let backSide = document.querySelector('.back-side').innerText.replace(/[^a-z -]/gi, '');
            let answer = document.querySelector('.answer-input')
            let frontSide = answer.value.replace(/[^a-z]/gi, '');
            document.querySelectorAll('.add-repetition').forEach(value =>{
                value.classList.remove('active');
                value.classList.remove('pulse');
            })
        if(frontSide.localeCompare(backSide) === 0 ){
            document.querySelector('.good-answer').classList += ' active pulse';
        }else if(frontSide.length < (backSide.length -1)){
            document.querySelector('.wrong-answer').classList += ' active pulse';
        }else{
            let mistakes = 0
            for(const character in frontSide){
                if(frontSide[character] !== backSide[character]){
                    mistakes++;
                }
                if(mistakes > 1){
                    document.querySelector('.wrong-answer').classList += ' active pulse';
                    break;
                }else if((parseInt(character) === frontSide.length -1) && mistakes < 2){
                    document.querySelector('.try-again').classList += ' active pulse';
                    break;
                }

            }
        }

        document.querySelector('.back-side').style.display = 'block';
        document.querySelectorAll('.add-repetition').forEach(btn => {
            btn.style.display = 'inline-flex';
        })
        answer.value = '';
    })
}

if (newWords) {
    newWords.addEventListener('click', function () {
        window.location.href = `/course/${id}/presentation`;
    })
}

if (repetition) {
    if (repetition.dataset.countRepetition) {
        repetition.addEventListener('click', function () {
            window.location.href = `/course/${id}/repetition`;
        })
    }

}

document.querySelectorAll('.course-type').forEach(course => {
    course.addEventListener('click', function () {
        id = course.dataset.id;
    })
})
