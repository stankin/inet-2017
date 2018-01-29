var questions = [
  {
    "id": 1,
    "heading": "Столица Индии:",
    "options": [
      {
        "name": "Мапуту",
        "true": false
      },
      {
        "name": "Нью-Дели",
        "true": true
      },
      {
        "name": "Мумбай",
        "true": false
      },
      {
        "name": "Париж",
        "true": false
      }
    ]
  },
  {
    "id": 2,
    "heading": "Флаг Великобритании:",
    "options": [
      {
        "name": "<img src='img/2.jpg'>",
        "true": false
      },
      {
        "name": "<img src='img/3.jpg'>",
        "true": false
      },
      {
        "name": "<img src='img/4.jpg'>",
        "true": true
      },
      {
        "name": "<img src='img/5.jpg'>",
        "true": false
      }
    ]
  },
  {
    "id": 3,
    "heading": "Денежная единица Казахстана:",
    "options": [
      {
        "name": "Тенге",
        "true": true
      },
      {
        "name": "Рубль",
        "true": false
      },
      {
        "name": "Лев",
        "true": false
      },
      {
        "name": "Гривна",
        "true": false
      }
    ]
  },
  {
    "id": 4,
    "heading": "Первое место по населению занимает:",
    "options": [
      {
        "name": "Индия",
        "true": false
      },
      {
        "name": "США",
        "true": false
      },
      {
        "name": "Китай",
        "true": true
      },
      {
        "name": "Россия",
        "true": false
      }
    ]
  },
  {
    "id": 5,
    "heading": "Антананариву является столицей:",
    "options": [
      {
        "name": "Турции",
        "true": false
      },
      {
        "name": "Мадагаскара",
        "true": true
      },
      {
        "name": "Эфиопии",
        "true": false
      },
      {
        "name": "Индонезии",
        "true": false
      }
    ]
  },
  {
    "id": 6,
    "heading": "Второе место по площади занимает:",
    "options": [
      {
        "name": "Россия",
        "true": false
      },
      {
        "name": "Бразилия",
        "true": false
      },
      {
        "name": "Австралия",
        "true": false
      },
      {
        "name": "Канада",
        "true": true
      }
    ]
  },
  {
    "id": 7,
    "heading": "Флаг <img src='img/1.jpg'> принадлежит:",
    "options": [
      {
        "name": "Финляндии",
        "true": false
      },
      {
        "name": "Швеции",
        "true": true
      },
      {
        "name": "Исландии",
        "true": false
      },
      {
        "name": "Грузии",
        "true": false
      }
    ]
  },
  {
    "id": 8,
    "heading": "Официальный язык Бельгии:",
    "options": [
      {
        "name": "Французский",
        "true": false
      },
      {
        "name": "Нидерландский",
        "true": false
      },
      {
        "name": "Немецкий",
        "true": false
      },
      {
        "name": "Все вышеперечисленные",
        "true": true
      }
    ]
  },
  {
    "id": 9,
    "heading": "Форма правления в России:",
    "options": [
      {
        "name": "Президентская",
        "true": true
      },
      {
        "name": "Парламентская",
        "true": false
      },
      {
        "name": "Монархия",
        "true": false
      },
      {
        "name": "Смешанная",
        "true": false
      }
    ]
  },
  {
    "id": 10,
    "heading": "Форинт является денежной единицей:",
    "options": [
      {
        "name": "Польши",
        "true": false
      },
      {
        "name": "Монголии",
        "true": false
      },
      {
        "name": "Венгрии",
        "true": true
      },
      {
        "name": "Боливии",
        "true": false
      }
    ]
  }
];

function nextQues(element) {
  var $next = $(element).parent().next();
  $(element).parent().hide(300);
  $next.length ? $next.show(300) : showResult();
}

function checkAnswer() {
  var result = {
        "trueId": [],
        "falseId": []
      };
  $('#form-questions input[type="radio"]:checked').each(function(index) {
    var optionNumber = $(this).attr("id").split("option-")[1].split('-')[0] - 1;
    var thisId = $(this).attr("id").split("option-")[1].split('-')[1] - 1;
    var thisOption = questions[thisId].options[optionNumber].true;

    thisOption ? result.trueId.push(thisId+1) : result.falseId.push(thisId+1)
  });

  return result
}

function showResult() {
  var answers = checkAnswer();
  var trueId = answers.trueId;
  var falseId = answers.falseId;
  var trueLen = trueId.length;
  var falseLen = falseId.length;

  $("#form-questions").append('<p>Количество верных ответов: ' + trueLen + '; <br>Количество неверных ответов: ' + falseLen + ';</p>');

  var rating;

  var persent = 100 * trueLen / questions.length;

  if (persent < 40) {
    rating = "Неудовлетворительно"
  } if (persent >= 40 && trueLen < 70) {
    rating = "Удовлетворительно"
  } if (persent >= 70 && trueLen < 100) {
    rating = "Хорошо"
  } if ( persent == 100) {
    rating = "Отлично"
  }

  console.log(persent);
  $("#form-questions").append("<p>Ваша оценка: " + rating + "</p>")

  var i = 0;
  $("#form-questions").append("<ul></ul>")
  for (i; i< trueId.length; i++) {
    var ques = questions[trueId[i]-1].heading
    $("#form-questions ul").append('<li class="true">' + ques + '</li>');
  }
  var i = 0;
  for (i; i< falseId.length; i++) {
    var ques = questions[falseId[i]-1].heading
    $("#form-questions ul").append('<li class="false">' + ques + '</li>');
  }
  $("#form-questions").append('<button onClick="location.reload()">Начать заново</button>');
}

var $formQuestion = $("#form-questions");

for (var i = 0; i < questions.length; i++) {
  var question = questions[i];
  var options = question.options;
  var display = ( i + 1 == 1 ) ? "block" : "none";
  $formQuestion.append('<div style="display:' +  display + '" class="question" id="question-' + question.id + '"><h4 class="question-heading">' + question.heading + '</h4><div class="question-body"></div><button onClick="nextQues(this)" disabled="disabled">Ответить</button></div>');
};

$("#form-questions .question").each(function(index) {
  var questionOptions = questions[index].options;
  for (var i = 0; i < questionOptions.length; i++) {
    $(this).find('.question-body').append('<div><input type="radio" id="option-' + (i * 1 + 1) + '-' + +(index + 1) + '" name="option-' + +(index + 1) + '" value="' + questionOptions[i].name + '"><label for="option-' + (i * 1 + 1) + '-' + +(index + 1) + '">' + questionOptions[i].name + '</label></div>');
  }
});

$("input[type='radio']").on('change', function() {
  console.log($(this).attr("id").split("option-")[1].split('-')[0])
  $(this).parent().parent().parent().find('button').attr('disabled', false);
});



// Modal

function showModal(el) {
  var link = el.getAttribute('href');
  $(link).addClass('modal-opened');
  $(link).append('<div class="modal-close" onClick="closeModal(this)">X</div>')
}

function closeModal(el) {
  el.parentElement.classList.remove('modal-opened');
}
