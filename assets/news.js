function GetNews(){
  let news;
  fetch("/course/get_news/technology").then(response => response.json()).then(data => {
    console.log(data)
  });

}
export {GetNews}