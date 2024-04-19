// модальное окно с формой для входа
const modal = document.querySelector('.modal');
const open_auth_button = document.querySelector('.login-button');
const login_form = document.querySelector('#login_form');
const login_button = login_form.querySelector('button');

// форма ссылок
const url_form = document.querySelector('#url_form');
const submit_button = url_form.querySelector('button');

/**
 * Логика отображения модального окна
 * @param evt
 */
const handle_modal = (evt) => {
    evt.preventDefault();
    modal.classList.remove('hidden-section');
    let close_button = document.querySelector('.close-button');
    let main = document.querySelector('main');
    main.style.filter = 'blur(10px)';

    close_button.addEventListener('click', () => {
        modal.classList.add('hidden-section');
        main.style.filter = 'none';
    })
}

/**
 * Обработка формы авторизации
 * @param evt
 */
const login = async (evt) => {
    evt.preventDefault();

    let form_data = new FormData(login_form, login_button);

    // отправка запроса с данными для входа
    let response = await fetch('/scenarios/auth.php', {
        body: form_data,
        method: 'post'
    });

    if (response.ok) {
        // обновление страницы при успешном логине
        window.location.reload();
    } else {
        // отображение ошибки входа
        let error_label = login_form.querySelector('.input-password')
        error_label.value = '';
        error_label.placeholder = (await response.json()).error;
    }
}

/**
 * Обработчик формы для укорачивания ссылки
 * @param evt
 */
const handle_short_url = async (evt) => {
    evt.preventDefault();

    const result_field = document.querySelector('#short_url');
    result_field.classList.remove('input-error');
    result_field.value = '';

    let form_data = new FormData(url_form, submit_button);

    // отправка запроса со ссылкой
    let response = await fetch('/scenarios/shorten.php', {
        body: form_data,
        method: 'post'
    });

    if (response.ok) {
        // получение и отображение ответа
        let result = await response.json();
        result = result.shortUrl;

        result_field.value = result;
        result_field.removeAttribute('disabled'); // поле становится активным

        // копирование короткой ссылки в буфер обмена
        const copy_button = document.querySelector('.clipboard-button');
        copy_button.addEventListener('click', (evt) => {
            result_field.select();
            result_field.setSelectionRange(0, 99);

            // navigator.clipboard.writeText(result_field.value); // работает только с HTTPS
            document.execCommand('copy');
            copy_button.classList.add('ok');
            result_field.classList.add('success');
        });

        // добавление новой строчки в таблицу ссылок пользователя
        const table = document.querySelector('.result_table tbody');

        if (table) {
            let new_row = document.createElement('tr');
            let full_url_cell = document.createElement('td');

            let short_url_cell = document.createElement('td');
            let url_link = document.createElement('a');
            url_link.textContent = result_field.value;
            url_link.href = result_field.value;
            short_url_cell.appendChild(url_link);

            let view_count_cell = document.createElement('td');

            full_url_cell.textContent = document.querySelector('#full_url').value;
            view_count_cell.textContent = '0';

            new_row.append(short_url_cell, full_url_cell, view_count_cell);
            table.appendChild(new_row);
        }
    } else {
        // показ ошибок
        result_field.value = (await response.json()).error;
        result_field.classList.add('input-error');
    }
}

// Навешивание обработчиков
open_auth_button?.addEventListener('click', handle_modal);
url_form.addEventListener('submit', handle_short_url)
login_form.addEventListener('submit', login);
