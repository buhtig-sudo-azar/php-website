# Команды очистки

## Поиск вредоносного кода
$ grep -r "eval(base64_decode" public_html/
public_html/index.php: eval(base64_decode("..."));

## Удаление вредоносной строки
$ sed -i '/eval(base64_decode/d' public_html/index.php

## Проверка
$ echo "Вирус удалён!"