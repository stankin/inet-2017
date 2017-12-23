$(function () {

	// глобальные переменные

		// 	массив содержит объекты с информациоей о блюдах
	var products = [],

		// пример:
		// filters = {
		// 		"cuisine" = "грузинская",
		//		"type" = "суп"
		//	}
		filters = {};


	//	Event handlers for frontend navigation

	//	чекбоксы

	var checkboxes = $('.all-products');

	checkboxes.on('click', 'input[type=checkbox]', function () {

		var that = $(this),
			specName = that.attr('name');

		// когда чекбокс нажат, надо записать в объект;
		if(that.is(":checked")) {

			// если фильр с такой спецификацией не создавался, то создаем
			if(!(filters[specName] && filters[specName].length)){
				filters[specName] = [];
			}

			//	положить значение в фильтр
			filters[specName].push(that.val());

			// поменять юрл хэш
			createQueryHash(filters);

		}

		// если чекбокс не выбран, то надо очистить фильтр
		if(!that.is(":checked")) {

			if(filters[specName] && filters[specName].length && (filters[specName].indexOf(that.val()) != -1)){

				// находим значение чекбокса в соответствующем массиве внутри объекта фильтров
				var index = filters[specName].indexOf(that.val());
				filters[specName].splice(index, 1);

				//Если это было последнее оставшееся значение для этой спецификации, удалить весь массив.
				if(!filters[specName].length){
					delete filters[specName];
				}

			}

			// поменять юрл хэш
			createQueryHash(filters);
		}
	});

	// когда "очистить фильтры" нажаты поменять хэш на '#' (вернуться на начальную страницу)
	$('.filters button').click(function (e) {
		e.preventDefault();
		window.location.hash = '#';
        var checkboxes = $('input:checkbox');
        if (checkboxes.is(':checked')) {
            $('input:checkbox').prop('checked', false);
        }
        
	});
    
	$('#reindex').click(function (e) {
		e.preventDefault();
		window.location.hash = '#';
        var checkboxes = $('input:checkbox');
        if (checkboxes.is(':checked')) {
            $('input:checkbox').prop('checked', false);
        }
        $("html, body").animate({ scrollTop: 0 }, "slow");
        
	});

	// кнопка открытия каждого рецепта

	var singleProductPage = $('.single-product');

	singleProductPage.on('click', function (e) {
		if (singleProductPage.hasClass('visible')) {

			var clicked = $(e.target);

			// если нажать "закрыть" или просто на фон, то вернуться назад
			if (clicked.hasClass('cltypee') || clicked.hasClass('overlay')) {
				// поменять юрл на прошлый с фильтром
				renderNullPage(1,products);
                createQueryHash(filters);
			}

		}

	});


	// Используются при загрузки страницы:

	// получить данные о рецептах из products.json
	$.getJSON( "products.json", function( data ) {

		// написать данные в глобальную переменную
		products = data;

		// создать HTML для всех рецептов
		generateAllProductsHTML(products);

		// меняем тригер на hashchange для запуска
		$(window).trigger('hashchange');
	});


	// Обработчик событий с вызовом функции рендерингана каждом hashchange
	// функция ренедринга будет показывать соответствующее содержание страницы
	$(window).on('hashchange', function(){
		render(decodeURI(window.location.hash));
	});


	// навигация

	function render(url) {

		// получить слово из юрл
		var temp = url.split('/')[0];

		// скрыть текущую страницу
		$('.main-content .page').removeClass('visible');


		var	map = {

			// домашняя страница
			'': function() {

				// очистить фильтры, хэши и отобразить всё
				filters = {};
				checkboxes.prop('checked',false);

				renderProductsPage(products);
			},

			// страница отдельного рецепта
			'#product': function() {

				
                // Получить индекс того, какой рецепт мы хотим показать и вызываем соответствующую функцию
                var index = url.split('#product/')[1].trim();

                $('html, body').animate({scrollTop: 0},500);
				renderSingleProductPage(index, products);
			},

			// страница с фильтрацией
			'#filter': function() {

				// взять строку после ключевого слова '#filter/', вызвать функцию фильтрации
				url = url.split('#filter/')[1].trim();

				// разбор фильтра объектов 
				try {
					filters = JSON.parse(url);
				}
				// если не подходит json, то на дом страницу
				catch(err) {
					window.location.hash = '#';
					return;
				}

				renderFilterResults(filters, products);
			}

		};

		// выполнять нужные функции в зависимости от юрл (хранится в temp)
		if(map[temp]){
			map[temp]();
		}
		// если ключевое слово не найдено, то вывести ошибку
		else {
			renderErrorPage();
		}

	}


	// эта функция грузится только один раз во время програзки страницы
	// заполняем список рецептов по шаблону
	// получается один параметр - данные взятые из products.json
	function generateAllProductsHTML(data){

		var list = $('.all-products .products-list');

		var theTemplateScript = $("#products-template").html();
		//составить шаблон
		var theTemplate = Handlebars.compile (theTemplateScript);
		list.append (theTemplate(data));


		// каждый рецепт имеет индекс
		// нажимая на изменить юрл хэш, открывается окно для этого рецепта
		list.find('li').on('click', function (e) {
			e.preventDefault();

			var productIndex = $(this).data('index');

			window.location.hash = 'product/' + productIndex;
		})
	}

	// эта функция получает объект, содержащий весь рецепт, который мы хотим показать
	function renderProductsPage(data){

		var page = $('.all-products'),
			allProducts = $('.all-products .products-list > li');

		// скрыть все рецепты
		allProducts.addClass('hidden');

		// итерация по всем рецептам
		// если их идентификатор находится где-то в объекте данных, удаляем скрытый класс, чтобы выявить их
		allProducts.each(function () {

			var that = $(this);

			data.forEach(function (item) {
				if(that.data('index') == item.id){
					that.removeClass('hidden');
				}
			});
		});

		// показать саму страницу
		// (функция рендеринга скрывает все страницы, так что надо показать ту, которую хотим).
		page.addClass('visible');

	}


	// открываем страницу для одного рецепта
	// его параметрами являются индекс хэша и объект продуктов
	function renderSingleProductPage(index, data){

		var page = $('.single-product');
		var	container = $('.preview-large');
        var	container0 = $('.preview-large0');
        var container1 = $('.preview-large1');
        var container2 = $('.preview-large2');
        var container3 = $('.preview-large3');
        var container4 = $('.preview-large4');
        var container5 = $('.preview-large5');
        var container6 = $('.preview-large6');

		// найти нужный рецепт с помощью перебора данных объекта и поиска индекса
		if(data.length){
			data.forEach(function (item) {
				if(item.id == index){
					// заполняем страницу данными
					container.find('h3').text(item.name);
					container.find('img').attr('src', item.image.large);
                    container.find('p').text(item.description);
                    container0.find('p').text(item.description0);
                    container1.find('img').attr('src', item.image.large1);
					container1.find('p').text(item.description1);
                    container2.find('img').attr('src', item.image.large2);
					container2.find('p').text(item.description2);
                    container3.find('img').attr('src', item.image.large3);
					container3.find('p').text(item.description3);
                    container4.find('img').attr('src', item.image.large4);
					container4.find('p').text(item.description4);
                    container5.find('img').attr('src', item.image.large5);
					container5.find('p').text(item.description5);
                    container6.find('img').attr('src', item.image.large6);
				}
			});
		}

		// показать страницу
		page.addClass('visible');

	}
	function renderNullPage(index, data){

		var page = $('.single-product');
        var	container = $('.preview-large');
        var	container0 = $('.preview-large0');
        var container1 = $('.preview-large1');
        var container2 = $('.preview-large2');
        var container3 = $('.preview-large3');
        var container4 = $('.preview-large4');
        var container5 = $('.preview-large5');
        var container6 = $('.preview-large6');

		// найти нужный рецепт с помощью перебора данных объекта и поиска индекса
		if(data.length){
			data.forEach(function (item) {
				if(item.id == index){
					// заполняем страницу данными
					container.find('h3').text();
                    container.find('img').attr('src', item.name);
                    container.find('p').text(item.name);
                    container0.find('p').text(item.name);
                    container1.find('img').attr('src', item.name);
					container1.find('p').text(item.name);
                    container2.find('img').attr('src', item.name);
					container2.find('p').text(item.name);
                    container3.find('img').attr('src', item.name);
					container3.find('p').text(item.name);
                    container4.find('img').attr('src', item.name);
					container4.find('p').text(item.name);
                    container5.find('img').attr('src', item.name);
					container5.find('p').text(item.name);
                    container6.find('img').attr('src', item.name);
				}
			});
		}
        
		page.addClass('visible');

	}
	// Find and render the filtered data results. Arguments are:
	// filters - our global variable - the object with arrays about what we are searching for.
	// products - an object with the full products list (from product.json).
	function renderFilterResults(filters, products){

			// This array contains all the ptypesible filter criteria.
		var criteria = ['cuisine','ingredients','type','sphere'],
			results = [],
			isFiltered = false;

		// Uncheck all the checkboxes.
		// We will be checking them again one by one.
		checkboxes.prop('checked', false);


		criteria.forEach(function (c) {

			// Check if each of the ptypesible filter criteria is actually in the filters object.
			if(filters[c] && filters[c].length){


				// After we've filtered the products once, we want to keep filtering them.
				// That's why we make the object we search in (products) to equal the one with the results.
				// Then the results array is cleared, so it can be filled with the newly filtered data.
				if(isFiltered){
					products = results;
					results = [];
				}


				// In these nested 'for loops' we will iterate over the filters and the products
				// and check if they contain the same values (the ones we are filtering by).

				// Iterate over the entries inside filters.criteria (remember each criteria contains an array).
				filters[c].forEach(function (filter) {

					// Iterate over the products.
					products.forEach(function (item){

						// If the product has the same specification value as the one in the filter
						// push it inside the results array and mark the isFiltered flag true.

						if(typeof item.specs[c] == 'number'){
							if(item.specs[c] == filter){
								results.push(item);
								isFiltered = true;
							}
						}

						if(typeof item.specs[c] == 'string'){
							if(item.specs[c].toLowerCase().indexOf(filter) != -1){
								results.push(item);
								isFiltered = true;
							}
						}

					});

					// Here we can make the checkboxes representing the filters true,
					// keeping the app up to date.
					if(c && filter){
						$('input[name='+c+'][value='+filter+']').prop('checked',true);
					}
				});
			}

		});

		// Call the renderProductsPage.
		// As it's argument give the object with filtered products.
		renderProductsPage(results);
	}


	// Shows the error page.
	function renderErrorPage(){
		var page = $('.error');
		page.addClass('visible');
	}

	// Get the filters object, turn it into a string and write it into the hash.
	function createQueryHash(filters){
		// Here we check if filters isn't empty.
		if(!$.isEmptyObject(filters)){
			// Stringify the object via JSON.stringify and write it after the '#filter' keyword.
		    
            window.location.hash = '#filter/' + JSON.stringify(filters);
            
		}
		else{
			// If it's empty change the hash to '#' (the homepage).
        	
        	window.location.hash = '#';
            
		}

	}


//Checkboxes
var expanded = false;

function showCheckboxes() {
  var checkboxes = document.getElementById("checkboxes");
  if (!expanded) {
    checkboxes.style.display = "block";
    expanded = true;
  } else {
    checkboxes.style.display = "none";
    expanded = false;
  }
}


});