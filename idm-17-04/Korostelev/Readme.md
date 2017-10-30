# Проект Анжела

Распознавание голоса реализовано с помощью нового экспериментального JavaScript API — **webkitSpeechRecognition**. API под названием **speechSynthesis** позволяет озвучивать человеческим голосом любой текст.

Поддержка есть только Chrome и Opera

![group](https://github.com/stankin/inet-2017/blob/master/idm-17-04/Korostelev/SpeechRecognition.JPG)

Чтобы проверить поддерживает ли ваш браузер данный API, откройте консоль разработчика и введите:
```bash
('webkitSpeechRecognition' in window)
```
Если в ответ будет **true**, то браузер поддерживает API.

Для работы необходимо установить серверное программное обеспечение, [OpenServer](https://ospanel.io/download/)

Пример озвучивания текста:
```javascript
speechSynthesis.speak(
  new SpeechSynthesisUtterance('Здравствуйте, я помошник Анжела, нажмите микрофон и скажите что вам нужно')
);
```
Проект Анжела распознаёт речь и делает запрос в поисковую систему google.com с открытием окна по вашему запросу

[API SpeechSynthesis](https://developer.mozilla.org/en-US/docs/Web/API/SpeechSynthesis)

[Статья]()

[Проект Speech API](https://github.com/stankin/inet-2017/tree/master/idm-17-04/Korostelev/Speech%20API)
