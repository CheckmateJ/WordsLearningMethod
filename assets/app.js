import { createPopper } from '@popperjs/core';
import 'bootstrap'
import {Modal} from 'bootstrap';
import {GetNews} from "./news";

var registerModal = document.getElementById('registerModal')
var loginModal = document.getElementById('loginModal')
window.addEventListener('load', function () {
    if (registerModal) {
        new Modal(registerModal).show();
    } else if (loginModal) {
        new Modal(loginModal).show();
    }
})


let translations = document.querySelector('.translations-list')
if (translations) {
    translations.querySelectorAll('tbody tr td:last-child').forEach(td => {
        addTagFormDeleteLink(td)
    })
}

GetNews()
document
    .querySelectorAll('.add_item_link')
    .forEach(btn => {
        btn.addEventListener("click", (e) => {
            const collectionHolder = document.querySelector('.' + e.currentTarget.dataset.collectionHolderClass);
            const tr = document.createElement('tr');
            const tdFront = document.createElement('td');
            const tdBack = document.createElement('td');
            const tdAction = document.createElement('td');
            const inputFront = document.createElement('textarea');
            const inputBack = document.createElement('textarea');
            const id = document.createElement('td')
            inputFront.setAttribute('id', `course_translations_${collectionHolder.childElementCount}_frontSide`);
            inputFront.setAttribute('required', true);
            inputFront.setAttribute('class', `form-control`);
            inputFront.setAttribute('name', `course[translations][${collectionHolder.childElementCount}][frontSide]`);
            inputBack.setAttribute('id', `course_translations_${collectionHolder.childElementCount}_frontSide`);
            inputBack.setAttribute('required', true);
            inputBack.setAttribute('class', `form-control`);
            inputBack.setAttribute('name', `course[translations][${collectionHolder.childElementCount}][backSide]`);
            id.setAttribute('class', 'card-id')
            id.innerText = parseInt(document.querySelector('tr:last-of-type .card-id').innerText) + 1;
            tdFront.append(inputFront);
            tdBack.append(inputBack);
            tr.append(id);
            tr.append(tdFront);
            tr.append(tdBack);
            tr.append(tdAction);

            collectionHolder.appendChild(tr);

            collectionHolder.dataset.index++
            document.querySelector('tbody tr:last-of-type textarea').focus();
            document.querySelectorAll('tbody.translations tr:last-child td:last-child').forEach((td) => {
                addTagFormDeleteLink(td)
            })

        })

    });

function addTagFormDeleteLink(td) {
    const removeFormButton = document.createElement('button');
    removeFormButton.className = 'btn btn-danger align-self-center';
    removeFormButton.innerText = '-';
    td.append(removeFormButton);
    removeFormButton.addEventListener('click', (e) => {
        e.preventDefault();
        // remove the tr for the tag form
        td.parentElement.remove();
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
        let answer = document.querySelector('.answer-input').value = ''
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
            if (data.message) {
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
        //     let backSide = document.querySelector('.back-side').innerText.replace(/[^a-z -]/gi, '');
        //     let answer = document.querySelector('.answer-input')
        //     let frontSide = answer.value.replace(/[^a-z]/gi, '');
        //     document.querySelectorAll('.add-repetition').forEach(value =>{
        //         value.classList.remove('active');
        //         value.classList.remove('pulse');
        //     })
        // if(frontSide.localeCompare(backSide) === 0 ){
        //     document.querySelector('.good-answer').classList += ' active pulse';
        // }else if(frontSide.length < (backSide.length -1)){
        //     document.querySelector('.wrong-answer').classList += ' active pulse';
        // }else{
        //     let mistakes = 0
        //     for(const character in frontSide){
        //         if(frontSide[character] !== backSide[character]){
        //             mistakes++;
        //         }
        //         if(mistakes > 1){
        //             document.querySelector('.wrong-answer').classList += ' active pulse';
        //             break;
        //         }else if((parseInt(character) === frontSide.length -1) && mistakes < 2){
        //             document.querySelector('.try-again').classList += ' active pulse';
        //             break;
        //         }
        //
        //     }
        // }

        document.querySelector('.back-side').style.display = 'block';
        document.querySelectorAll('.add-repetition').forEach(btn => {
            btn.style.display = 'inline-flex';
        })
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

if(document.querySelector('.progress-bar')){
    document.querySelectorAll('.progress-bar').forEach(bar => {
        bar.style.width = bar.dataset.progress * 100 + '%';
        console.log(bar.dataset.progress * 100)
    })
}

document.querySelectorAll('.news-category').forEach(news => {
    news.addEventListener('click', function(){
        let language = document.querySelector('.news-language')
        let newsLanguage = language.options[language.selectedIndex].dataset.language;
       GetNews(news.innerText, newsLanguage)
    })
})

let sidebar = document.querySelector('.grammar-sidebar');
sidebar.addEventListener('click', function(e){
    sidebar.style.width = '100%';
})