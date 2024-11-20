/**
* function for js file from URL into head adding and loading and then executing
*/
export function loadScript({ url, callback = null, async_bool = false }) {
    // Добавляем тег сценария в head – как и предлагалось выше
    var head = document.getElementsByTagName('head')[0];
    var script = document.createElement('script');
    script.type = 'text/javascript';
    script.src = url;
    script.async = async_bool;
    // Затем связываем событие и функцию обратного вызова.
    // Для поддержки большинства обозревателей используется несколько событий.
    script.onreadystatechange = callback;
    script.onload = callback;

    // Начинаем загрузку
    head.appendChild(script);
}