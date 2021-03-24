$(document).ready(function () {
    new Swiper('.swiper-container', {
        // Вывод стрелок навигации
        navigation: {
            nextEl: '.swiper-button-next',
            prevEl: '.swiper-button-prev',
        },
        pagination: {
            el: '.swiper-pagination',
        },
        // Вывод скроллбара
        scrollbar: {
            el: '.swiper-scrollbar',
            draggable: true
        },
    })
})
