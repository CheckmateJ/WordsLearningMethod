function GetNews(category = 'Technology', language = 'en'){

    const data = { newsCategory: category, languageNews: language  };
    fetch('/course/get_news', {
        method: 'POST', // or 'PUT'
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
    })
        .then(response => response.json())
        .then(data => {

            let result = data.results
            for(let i=0; i< 5; i++){
                let news = document.querySelector(`.news-${i}`)
                news.querySelector('.publish-date').innerText = result[i].pubDate;
                news.querySelector('.news-title').innerText = result[i].title;
                news.querySelector('.news-description').innerText = result[i].description;
                news.querySelector('.news-read-more').setAttribute('href', result[i].link);

                if(result[i].image_url != null){
                    news.nextElementSibling.style.setProperty('display', 'flex', 'important');
                    news.nextElementSibling.querySelector('img').setAttribute('src', result[i].image_url);
                }else{
                    news.style.setProperty('width', '100%', 'important');
                    news.style.setProperty('overflow', 'hidden', 'important');
                }
            }
        })
        .catch((error) => {
            console.error('Error:', error);
        });
}
export {GetNews}