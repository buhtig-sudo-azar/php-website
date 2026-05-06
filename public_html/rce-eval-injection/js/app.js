document.addEventListener('DOMContentLoaded', function() {
    const showInfectedBtn = document.getElementById('show-infected');
    const showCleanBtn = document.getElementById('show-clean');
    const sitePreview = document.getElementById('site-preview');
    const codeDisplay = document.getElementById('code-display');
    const codeButtons = document.querySelectorAll('.code-button');

    // Загрузка кода при клике на кнопку
    codeButtons.forEach(button => {
        button.addEventListener('click', function() {
            // Убираем активность со всех кнопок
            codeButtons.forEach(btn => btn.classList.remove('active'));
            // Добавляем активность на текущую кнопку
            this.classList.add('active');
            
            const file = this.dataset.file;
            fetch(`code-samples/${file}.txt`)
                .then(response => response.text())
                .then(text => {
                    codeDisplay.textContent = text;
                    if (Prism) {
                        Prism.highlightElement(codeDisplay);
                    }
                })
                .catch(err => console.error('Ошибка загрузки файла:', err));
        });
    });

    // Переключение предпросмотра
    showInfectedBtn.addEventListener('click', function() {
        sitePreview.src = 'pages/infected/home.html';
    });

    showCleanBtn.addEventListener('click', function() {
        sitePreview.src = 'pages/clean/home.html';
    });

    // Загружаем первый файл по умолчанию
    const firstCodeButton = document.querySelector('.code-button');
    if (firstCodeButton) {
        firstCodeButton.click();
    }
});