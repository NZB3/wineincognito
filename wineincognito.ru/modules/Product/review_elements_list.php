<?php
$list = array(
    'base_elements'=>array(
            array(
                    'caption'=>langTranslate('product','review elements','Wine type','Wine type'),//Вид вина
                    'name'=>'wine-type',
                    'hide_empty_from_merge'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Wine type - Still','Still'),'segment-base-class'=>'wine-type-still'),//Тихое
                            array('value'=>2,'label'=>langTranslate('product','review elements','Wine type - Sparkling','Sparkling'),'segment-base-class'=>'wine-type-sparkling'),//Игристое
                            array('value'=>3,'label'=>langTranslate('product','review elements','Wine type - Fortified','Fortified'),'segment-base-class'=>'wine-type-fortified'),//Крепленое
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Color spectrum','Color spectrum'),//Цветовая гамма
                    'name'=>'color-spectrum',
                    'class'=>'color-spectrum',
                    'hide_empty_from_merge'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Color spectrum - White','White'),'segment-base-class'=>'wine-color-white'),//Белое
                            array('value'=>2,'label'=>langTranslate('product','review elements','Color spectrum - Rose','Rose'),'segment-base-class'=>'wine-color-pink'),//Розовое
                            array('value'=>3,'label'=>langTranslate('product','review elements','Color spectrum - Red','Red'),'segment-base-class'=>'wine-color-red'),//Красное
                        )
                ),
        ),
    'color_spectrum_subcolor_data'=>array(
            array(
                    'color'=>1,
                    'subcolor'=>1,
                    'depth'=>null,
                    'title'=>langTranslate('product','review elements','Color spectrum - Lemon green','Lemon green'),//Лимонный с зеленым отблеском
                    'example'=>'#ede7b5'
                ),
            array(
                    'color'=>1,
                    'subcolor'=>1,
                    'depth'=>1,
                    'title'=>langTranslate('product','review elements','Color spectrum - Pale lemon green','Pale lemon green'),//Легкий лимонный с зеленым отблеском
                    'example'=>'#edf0db'
                ),
            array(
                    'color'=>1,
                    'subcolor'=>1,
                    'depth'=>2,
                    'title'=>langTranslate('product','review elements','Color spectrum - Lemon green','Lemon green'),//Лимонный с зеленым отблеском
                    'example'=>'#ede7b5'
                ),
            array(
                    'color'=>1,
                    'subcolor'=>1,
                    'depth'=>3,
                    'title'=>langTranslate('product','review elements','Color spectrum - Deep lemon green','Deep lemon green'),//Интенсивный лимонный с зеленым отблеском
                    'example'=>'#e1dc98'
                ),
            array(
                    'color'=>1,
                    'subcolor'=>2,
                    'depth'=>null,
                    'title'=>langTranslate('product','review elements','Color spectrum - Lemon','Lemon'),//Чистый лимонный
                    'example'=>'#eae29a'
                ),
            array(
                    'color'=>1,
                    'subcolor'=>2,
                    'depth'=>1,
                    'title'=>langTranslate('product','review elements','Color spectrum - Pale lemon','Pale lemon'),//Легкий лимонный
                    'example'=>'#f4f2b5'
                ),
            array(
                    'color'=>1,
                    'subcolor'=>2,
                    'depth'=>2,
                    'title'=>langTranslate('product','review elements','Color spectrum - Lemon','Lemon'),//Чистый лимонный
                    'example'=>'#eae29a'
                ),
            array(
                    'color'=>1,
                    'subcolor'=>2,
                    'depth'=>3,
                    'title'=>langTranslate('product','review elements','Color spectrum - Deep lemon','Deep lemon'),//Интенсивный лимонный
                    'example'=>'#f2e37c'
                ),
            array(
                    'color'=>1,
                    'subcolor'=>3,
                    'depth'=>null,
                    'title'=>langTranslate('product','review elements','Color spectrum - Gold','Gold'),//Золотистый
                    'example'=>'#ead07b'
                ),
            array(
                    'color'=>1,
                    'subcolor'=>3,
                    'depth'=>1,
                    'title'=>langTranslate('product','review elements','Color spectrum - Pale gold','Pale gold'),//Легкий золотистый
                    'example'=>'#eedf95'
                ),
            array(
                    'color'=>1,
                    'subcolor'=>3,
                    'depth'=>2,
                    'title'=>langTranslate('product','review elements','Color spectrum - Gold','Gold'),//Золотистый
                    'example'=>'#ead07b'
                ),
            array(
                    'color'=>1,
                    'subcolor'=>3,
                    'depth'=>3,
                    'title'=>langTranslate('product','review elements','Color spectrum - Deep gold','Deep gold'),//Интенсивный золотистый
                    'example'=>'#efc362'
                ),
            array(
                    'color'=>1,
                    'subcolor'=>4,
                    'depth'=>null,
                    'title'=>langTranslate('product','review elements','Color spectrum - Amber','Amber'),//Янтарный
                    'example'=>'#c57d29'
                ),
            array(
                    'color'=>1,
                    'subcolor'=>4,
                    'depth'=>1,
                    'title'=>langTranslate('product','review elements','Color spectrum - Pale amber','Pale amber'),//Легкий янтарный
                    'example'=>'#f6aa38'
                ),
            array(
                    'color'=>1,
                    'subcolor'=>4,
                    'depth'=>2,
                    'title'=>langTranslate('product','review elements','Color spectrum - Amber','Amber'),//Янтарный
                    'example'=>'#c57d29'
                ),
            array(
                    'color'=>1,
                    'subcolor'=>4,
                    'depth'=>3,
                    'title'=>langTranslate('product','review elements','Color spectrum - Deep amber','Deep amber'),//Интенсивный янтарный
                    'example'=>'#954417'
                ),
            array(
                    'color'=>1,
                    'subcolor'=>5,
                    'depth'=>null,
                    'title'=>langTranslate('product','review elements','Color spectrum - Brown','Brown'),//Коричневый
                    'example'=>'#ab5e28'
                ),
            array(
                    'color'=>1,
                    'subcolor'=>5,
                    'depth'=>1,
                    'title'=>langTranslate('product','review elements','Color spectrum - Pale brown','Pale brown'),//Легкий коричневый
                    'example'=>'#e3ad40'
                ),
            array(
                    'color'=>1,
                    'subcolor'=>5,
                    'depth'=>2,
                    'title'=>langTranslate('product','review elements','Color spectrum - Brown','Brown'),//Коричневый
                    'example'=>'#ab5e28'
                ),
            array(
                    'color'=>1,
                    'subcolor'=>5,
                    'depth'=>3,
                    'title'=>langTranslate('product','review elements','Color spectrum - Deep brown','Deep brown'),//Интенсивный коричневый
                    'example'=>'#894c28'
                ),
            array(
                    'color'=>1,
                    'subcolor'=>null,
                    'depth'=>1,
                    'title'=>langTranslate('product','review elements','Color spectrum - Pale','Pale'),//Легкий
                    'example'=>null
                ),
            array(
                    'color'=>1,
                    'subcolor'=>null,
                    'depth'=>2,
                    'title'=>langTranslate('product','review elements','Color spectrum - Medium','Medium'),//Средний
                    'example'=>null
                ),
            array(
                    'color'=>1,
                    'subcolor'=>null,
                    'depth'=>3,
                    'title'=>langTranslate('product','review elements','Color spectrum - Deep','Deep'),//Интенсивный
                    'example'=>null
                ),
    //2
            array(
                    'color'=>2,
                    'subcolor'=>1,
                    'depth'=>null,
                    'title'=>langTranslate('product','review elements','Color spectrum - Lilac','Lilac'),//Сиреневый
                    'example'=>'#ef757c'
                ),
            array(
                    'color'=>2,
                    'subcolor'=>1,
                    'depth'=>1,
                    'title'=>langTranslate('product','review elements','Color spectrum - Pale lilac','Pale lilac'),//Легкий сиреневый
                    'example'=>'#f5c3c4'
                ),
            array(
                    'color'=>2,
                    'subcolor'=>1,
                    'depth'=>2,
                    'title'=>langTranslate('product','review elements','Color spectrum - Lilac','Lilac'),//Сиреневый
                    'example'=>'#ef757c'
                ),
            array(
                    'color'=>2,
                    'subcolor'=>1,
                    'depth'=>3,
                    'title'=>langTranslate('product','review elements','Color spectrum - Deep lilac','Deep lilac'),//Интенсивный сиреневый
                    'example'=>'#ec4b5c'
                ),
            array(
                    'color'=>2,
                    'subcolor'=>2,
                    'depth'=>null,
                    'title'=>langTranslate('product','review elements','Color spectrum - Pink (salmon)','Pink (salmon)'),//Розовый (лососевый)
                    'example'=>'#f39079'
                ),
            array(
                    'color'=>2,
                    'subcolor'=>2,
                    'depth'=>1,
                    'title'=>langTranslate('product','review elements','Color spectrum - Pale pink (salmon)','Pale pink (salmon)'),//Легкий розовый (лососевый)
                    'example'=>'#f0b0a1'
                ),
            array(
                    'color'=>2,
                    'subcolor'=>2,
                    'depth'=>2,
                    'title'=>langTranslate('product','review elements','Color spectrum - Pink (salmon)','Pink (salmon)'),//Розовый (лососевый)
                    'example'=>'#f39079'
                ),
            array(
                    'color'=>2,
                    'subcolor'=>2,
                    'depth'=>3,
                    'title'=>langTranslate('product','review elements','Color spectrum - Deep pink (salmon)','Deep pink (salmon)'),//Интенсивный розовый (лососевый)
                    'example'=>'#f0856b'
                ),
            array(
                    'color'=>2,
                    'subcolor'=>3,
                    'depth'=>null,
                    'title'=>langTranslate('product','review elements','Color spectrum - Copper','Copper'),//Медный
                    'example'=>'#ef9356'
                ),
            array(
                    'color'=>2,
                    'subcolor'=>3,
                    'depth'=>1,
                    'title'=>langTranslate('product','review elements','Color spectrum - Pale copper','Pale copper'),//Легкий медный
                    'example'=>'#e3ad7e'
                ),
            array(
                    'color'=>2,
                    'subcolor'=>3,
                    'depth'=>2,
                    'title'=>langTranslate('product','review elements','Color spectrum - Copper','Copper'),//Медный
                    'example'=>'#ef9356'
                ),
            array(
                    'color'=>2,
                    'subcolor'=>3,
                    'depth'=>3,
                    'title'=>langTranslate('product','review elements','Color spectrum - Deep copper','Deep copper'),//Интенсивный медный
                    'example'=>'#de5a2c'
                ),
            array(
                    'color'=>2,
                    'subcolor'=>null,
                    'depth'=>1,
                    'title'=>langTranslate('product','review elements','Color spectrum - Pale','Pale'),//Легкий
                    'example'=>null
                ),
            array(
                    'color'=>2,
                    'subcolor'=>null,
                    'depth'=>2,
                    'title'=>langTranslate('product','review elements','Color spectrum - Medium','Medium'),//Средний
                    'example'=>null
                ),
            array(
                    'color'=>2,
                    'subcolor'=>null,
                    'depth'=>3,
                    'title'=>langTranslate('product','review elements','Color spectrum - Deep','Deep'),//Интенсивный
                    'example'=>null
                ),
    //3
            array(
                    'color'=>3,
                    'subcolor'=>1,
                    'depth'=>null,
                    'title'=>langTranslate('product','review elements','Color spectrum - Purple','Purple'),//Пурпурный
                    'example'=>'#641937'
                ),
            array(
                    'color'=>3,
                    'subcolor'=>1,
                    'depth'=>1,
                    'title'=>langTranslate('product','review elements','Color spectrum - Pale purple','Pale purple'),//Легкий пурпурный
                    'example'=>'#8d1b3f'
                ),
            array(
                    'color'=>3,
                    'subcolor'=>1,
                    'depth'=>2,
                    'title'=>langTranslate('product','review elements','Color spectrum - Purple','Purple'),//Пурпурный
                    'example'=>'#641937'
                ),
            array(
                    'color'=>3,
                    'subcolor'=>1,
                    'depth'=>3,
                    'title'=>langTranslate('product','review elements','Color spectrum - Deep purple','Deep purple'),//Интенсивный пурпурный
                    'example'=>'#550d25'
                ),
            array(
                    'color'=>3,
                    'subcolor'=>2,
                    'depth'=>null,
                    'title'=>langTranslate('product','review elements','Color spectrum - Ruby','Ruby'),//Рубиновый
                    'example'=>'#88182e'
                ),
            array(
                    'color'=>3,
                    'subcolor'=>2,
                    'depth'=>1,
                    'title'=>langTranslate('product','review elements','Color spectrum - Pale ruby','Pale ruby'),//Легкий рубиновый
                    'example'=>'#971a38'
                ),
            array(
                    'color'=>3,
                    'subcolor'=>2,
                    'depth'=>2,
                    'title'=>langTranslate('product','review elements','Color spectrum - Ruby','Ruby'),//Рубиновый
                    'example'=>'#88182e'
                ),
            array(
                    'color'=>3,
                    'subcolor'=>2,
                    'depth'=>3,
                    'title'=>langTranslate('product','review elements','Color spectrum - Deep ruby','Deep ruby'),//Интенсивный рубиновый
                    'example'=>'#741d2e'
                ),
            array(
                    'color'=>3,
                    'subcolor'=>3,
                    'depth'=>null,
                    'title'=>langTranslate('product','review elements','Color spectrum - Ruby with light red','Ruby with light red'),//Рубиновый с легкой рыжиной
                    'example'=>'#8c191d'
                ),
            array(
                    'color'=>3,
                    'subcolor'=>3,
                    'depth'=>1,
                    'title'=>langTranslate('product','review elements','Color spectrum - Pale ruby with light red','Pale ruby with light red'),//Легкий рубиновый с легкой рыжиной
                    'example'=>'#ac2023'
                ),
            array(
                    'color'=>3,
                    'subcolor'=>3,
                    'depth'=>2,
                    'title'=>langTranslate('product','review elements','Color spectrum - Ruby with light red','Ruby with light red'),//Рубиновый с легкой рыжиной
                    'example'=>'#8c191d'
                ),
            array(
                    'color'=>3,
                    'subcolor'=>3,
                    'depth'=>3,
                    'title'=>langTranslate('product','review elements','Color spectrum - Deep ruby with light red','Deep ruby with light red'),//Интенсивный рубиновый с легкой рыжиной
                    'example'=>'#571110'
                ),
            array(
                    'color'=>3,
                    'subcolor'=>4,
                    'depth'=>null,
                    'title'=>langTranslate('product','review elements','Color spectrum - Tawny','Tawny'),//Кирпичный
                    'example'=>'#722817'
                ),
            array(
                    'color'=>3,
                    'subcolor'=>4,
                    'depth'=>1,
                    'title'=>langTranslate('product','review elements','Color spectrum - Pale tawny','Pale tawny'),//Легкий кирпичный
                    'example'=>'#993221'
                ),
            array(
                    'color'=>3,
                    'subcolor'=>4,
                    'depth'=>2,
                    'title'=>langTranslate('product','review elements','Color spectrum - Tawny','Tawny'),//Кирпичный
                    'example'=>'#722817'
                ),
            array(
                    'color'=>3,
                    'subcolor'=>4,
                    'depth'=>3,
                    'title'=>langTranslate('product','review elements','Color spectrum - Deep tawny','Deep tawny'),//Интенсивный кирпичный
                    'example'=>'#471812'
                ),
            array(
                    'color'=>3,
                    'subcolor'=>5,
                    'depth'=>null,
                    'title'=>langTranslate('product','review elements','Color spectrum - Brown','Brown'),//Коричневый
                    'example'=>'#ab5e28'
                ),
            array(
                    'color'=>3,
                    'subcolor'=>5,
                    'depth'=>1,
                    'title'=>langTranslate('product','review elements','Color spectrum - Pale brown','Pale brown'),//Легкий коричневый
                    'example'=>'#e3ad40'
                ),
            array(
                    'color'=>3,
                    'subcolor'=>5,
                    'depth'=>2,
                    'title'=>langTranslate('product','review elements','Color spectrum - Brown','Brown'),//Коричневый
                    'example'=>'#ab5e28'
                ),
            array(
                    'color'=>3,
                    'subcolor'=>5,
                    'depth'=>3,
                    'title'=>langTranslate('product','review elements','Color spectrum - Deep brown','Deep brown'),//Интенсивный коричневый
                    'example'=>'#894c28'
                ),
            array(
                    'color'=>3,
                    'subcolor'=>null,
                    'depth'=>1,
                    'title'=>langTranslate('product','review elements','Color spectrum - Pale','Pale'),//Легкий
                    'example'=>null
                ),
            array(
                    'color'=>3,
                    'subcolor'=>null,
                    'depth'=>2,
                    'title'=>langTranslate('product','review elements','Color spectrum - Medium','Medium'),//Средний
                    'example'=>null
                ),
            array(
                    'color'=>3,
                    'subcolor'=>null,
                    'depth'=>3,
                    'title'=>langTranslate('product','review elements','Color spectrum - Deep','Deep'),//Интенсивный
                    'example'=>null
                ),
        ),
    'external_observation_elements'=>array(
            array(
                    'caption'=>langTranslate('product','review elements','Observation','Observation'),//Внешнее наблюдение
                    'name'=>'external-observation',
                    'list'=>'cols',
                    'optional'=>true,
                    'multichoice'=>true,
                    'hide_empty_from_merge'=>true,
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Observation - Hazy','Hazy')/*Мутное*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Observation - Deposit','Deposit')/*Осадок*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Observation - Petillance','Petillance')/*Остаточная игристость*/,'segment-class'=>'wine-type-still wine-type-fortified'),
                        )
                ),
        ),
    'sparkling_rating_elements'=>array(
            array(
                    'caption'=>langTranslate('product','review elements','Perlage - Bubble size','Bubble size'),//Размер пузырьков
                    'name'=>'sparkling-rating-bubblesize',
                    'segment-class'=>'wine-type-sparkling',
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Perlage - Bubble size - Small','Small')/*Мелкие*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Perlage - Bubble size - Average','Average')/*Средние*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Perlage - Bubble size - Big','Big')/*Крупные*/),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Perlage - Quantity','Quantity'),//Количество
                    'name'=>'sparkling-rating-quantity',
                    'segment-class'=>'wine-type-sparkling',
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Perlage - Quantity - Few','Few')/*Небольшое*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Perlage - Quantity - Average','Average')/*Среднее*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Perlage - Quantity - A lot','A lot')/*Большое*/),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Perlage - Duration','Duration'),//Длительность перляжа
                    'name'=>'sparkling-rating-continuance',
                    'segment-class'=>'wine-type-sparkling',
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Perlage - Duration - Short','Short')/*Малая*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Perlage - Duration - Average','Average')/*Средняя*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Perlage - Duration - Long','Long')/*Большая*/),
                        )
                ),
        ),
    // 'consistency_analysis_elements'=>array(
    //         array(
    //                 'name'=>'consistency-analysis',
    //                 'list'=>'cols',
    //                 'values'=>array(
    //                         array('value'=>1,'label'=>'Водянистая/жидкая'),
    //                         array('value'=>2,'label'=>'Легкая'),
    //                         array('value'=>3,'label'=>'Средняя'),
    //                         array('value'=>4,'label'=>'Плотная'),
    //                         array('value'=>5,'label'=>'Вязкая/маслянистая'),
    //                     )
    //             ),
    //     ),
    'faultcheck_elements'=>array(
            array(
                    'name'=>'faultcheck',
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Faultcheck - Faulty - Clean','Clean')/*Чистое*/,'default'=>true),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Faultcheck - Faulty - Faulty','Faulty')/*Дефект*/),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Faultcheck - Cork','Cork'),//Пробка
                    'subelement-of'=>'faultcheck',
                    'name'=>'faultcheck-cork',
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Faultcheck - None','None')/*Нет*/,'default'=>true),array('value'=>1,'label'=>langTranslate('product','review elements','Faultcheck - Hint','Hint')/*Намек*/),array('value'=>2,'label'=>langTranslate('product','review elements','Faultcheck - Present','Present')/*Присутствует*/),array('value'=>3,'label'=>langTranslate('product','review elements','Faultcheck - Cork - Obvious','Obvious')/*Выражена*/),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Faultcheck - Oxidation','Oxidation'),//Окисление
                    'subelement-of'=>'faultcheck',
                    'name'=>'faultcheck-oxidation',
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Faultcheck - None','None')/*Нет*/,'default'=>true),array('value'=>1,'label'=>langTranslate('product','review elements','Faultcheck - Hint','Hint')/*Намек*/),array('value'=>2,'label'=>langTranslate('product','review elements','Faultcheck - Present','Present')/*Присутствует*/),array('value'=>3,'label'=>langTranslate('product','review elements','Faultcheck - Oxidation - Obvious','Obvious')/*Выражено*/),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Faultcheck - Reduction','Reduction'),//Редукция
                    'subelement-of'=>'faultcheck',
                    'name'=>'faultcheck-reduction',
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Faultcheck - None','None')/*Нет*/,'default'=>true),array('value'=>1,'label'=>langTranslate('product','review elements','Faultcheck - Hint','Hint')/*Намек*/),array('value'=>2,'label'=>langTranslate('product','review elements','Faultcheck - Present','Present')/*Присутствует*/),array('value'=>3,'label'=>langTranslate('product','review elements','Faultcheck - Reduction - Obvious','Obvious')/*Выражена*/),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Faultcheck - Brett','Brett'),//Бретт
                    'subelement-of'=>'faultcheck',
                    'name'=>'faultcheck-brettanomyces',
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Faultcheck - None','None')/*Нет*/,'default'=>true),array('value'=>1,'label'=>langTranslate('product','review elements','Faultcheck - Hint','Hint')/*Намек*/),array('value'=>2,'label'=>langTranslate('product','review elements','Faultcheck - Present','Present')/*Присутствует*/),array('value'=>3,'label'=>langTranslate('product','review elements','Faultcheck - Brett - Obvious','Obvious')/*Выражен*/),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','MLF problems','MLF problems'),//Проблемы с малолактикой
                    'subelement-of'=>'faultcheck',
                    'name'=>'faultcheck-malolactik-fermentation',
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Faultcheck - None','None')/*Нет*/,'default'=>true),array('value'=>1,'label'=>langTranslate('product','review elements','Faultcheck - Hint','Hint')/*Намек*/),array('value'=>2,'label'=>langTranslate('product','review elements','Faultcheck - Present','Present')/*Присутствует*/),array('value'=>3,'label'=>langTranslate('product','review elements','Faultcheck - MLF problems - Obvious','Obvious')/*Выражены*/),
                        )
                ),
        ),
    'overall_aroma_elements'=>array(
            array(
                    'caption'=>langTranslate('product','review elements','Intensity','Intensity'),//Интенсивность
                    'name'=>'overall-aroma-intensity',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Intensity - Light','Light')/*Приглушенный*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Intensity - Medium (-)','Medium (-)')/*Ниже среднего*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Intensity - Medium','Medium')/*Средний*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Intensity - Medium (+)','Medium (+)')/*Выше среднего*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Intensity - Pronounced','Pronounced')/*Яркий*/),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Complexity','Complexity'),//Сложность
                    'name'=>'overall-aroma-complexity',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Complexity - Simple','Simple')/*Одномерный*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Complexity - Medium (-)','Medium (-)')/*Нельзя назвать простым*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Complexity - Medium','Medium')/*Средней сложности*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Complexity - Medium (+)','Medium (+)')/*Достаточно сложный, но мог бы быть сложнее*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Complexity - Very Complex','Very Complex')/*Многогранный*/),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Fruit maturity','Fruit maturity'),//Степень спелости фруктовой составляющей
                    'name'=>'overall-aroma-ripeness',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Fruit maturity - Unripe fruits','Unripe fruits')/*Незрелые*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Fruit maturity - Not ripe enough','Not ripe enough')/*Недостаточно спелые*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Fruit maturity - Ripe fruits','Ripe fruits')/*Спелые фрукты*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Fruit maturity - Overripe fruits','Overripe fruits')/*Переспелые фрукты*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Fruit maturity - Jammy fruits','Jammy fruits')/*Джем*/),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Development','Development'),//Развитие
                    'name'=>'overall-aroma-development',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Development - Just primary aromas','Just primary aromas')/*Молодой*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Development - Hints of tertiary','Hints of tertiary')/*Намеки на третичные ароматы*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Development - Primary to dominate tertiary','Primary to dominate tertiary')/*Третичные ароматы отчетливо слышны, но сортовые доминируют*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Development - Balance of primary and tertiary','Balance of primary and tertiary')/*Поровну сортовых и третичных ароматов*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Development - Tertiary dominate','Tertiary dominate')/*Третичные ароматы доминируют*/),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Outstanding aroma','Outstanding aroma'),//Выдающийся аромат
                    'hint'=>langTranslate('product','review elements','Personal emotional response','Personal emotional response'),//Личный эмоциональный отклик
                    'name'=>'overall-aroma-exceptional',
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Outstanding aroma - Simple','Simple')/*Самый обычный*/),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Outstanding aroma - Interesting','Interesting')/*Есть интересные ноты*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Outstanding aroma - Notable','Notable')/*Не оставил равнодушным*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Outstanding aroma - Appealing','Appealing')/*Нравится*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Outstanding aroma - Outstanding','Outstanding')/*Очень нравится*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Outstanding aroma - Delightful','Delightful')/*Вызывает восторг*/),
                            
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Oak','Oak'),//Дуб (наличие интеграции)
                    'name'=>'overall-aroma-oak',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Oak - None','None')/*Нет*/),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Oak - Hints','Hints')/*Легкие намеки на дубовую бочку*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Oak - Well integrated oak, fruit dominate','Well integrated oak, fruit dominate')/*Дуб слышен, хорошо интегрирован, фруктовые ароматы доминируют*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Oak - Obvious oak, balanced by fruit','Obvious oak, balanced by fruit')/*Поровну дубовых и сортовых ароматов*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Oak - Oak dominates','Oak dominates')/*Дуб заглушает сортовые характеристики*/),
                        )
                ),
        ),
    'primary_aroma_elements'=>array(
            array(
                    'caption'=>langTranslate('product','review elements','Floral','Floral'),//Цветочные
                    'name'=>'primary-aroma-flowery',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Aroma - None','None')/*Нет*/,'default'=>true),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Aroma - Hints','Hints')/*Тонкий штрих*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Aroma - Medium (-)','Medium (-)')/*Немного*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Aroma - Medium','Medium')/*Средне*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Aroma - Medium (+)','Medium (+)')/*Заметно*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Aroma - Pronounced','Pronounced')/*Ярко*/),
                        )
                ),
            array(
                    'subelement-of'=>'primary-aroma-flowery',
                    'name'=>'primary-aroma-flowery-type',
					'aroma'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'multichoice'=>true,
                    'optional'=>true,
                    'hide_empty_from_merge'=>true,
                    'order_by_total_count_in_merge'=>true,
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Floral - Rose','Rose')/*Роза*/,'image'=>BASE_URL.'/modules/Product/img/review/flowery/rose.png'),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Floral - Violet','Violet')/*Фиалка*/,'image'=>BASE_URL.'/modules/Product/img/review/flowery/violet.png'),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Floral - Iris','Iris')/*Ирисы*/,'image'=>BASE_URL.'/modules/Product/img/review/flowery/iris.png'),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Floral - Peony','Peony')/*Пионы*/,'image'=>BASE_URL.'/modules/Product/img/review/flowery/pion.png'),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Floral - Acacia','Acacia')/*Акация*/,'image'=>BASE_URL.'/modules/Product/img/review/flowery/akacia.png'),
                            array('value'=>6,'label'=>langTranslate('product','review elements','Floral - Orange-blossom','Orange-blossom')/*Флердоранж*/,'image'=>BASE_URL.'/modules/Product/img/review/flowery/orange-blossom.png'),
                            array('value'=>7,'label'=>langTranslate('product','review elements','Floral - Elderflower','Elderflower')/*Бузина*/,'image'=>BASE_URL.'/modules/Product/img/review/flowery/elderflower.png'),
                            array('value'=>8,'label'=>langTranslate('product','review elements','Floral - Wild Flowers','Wild Flowers')/*Полевые цветы*/,'image'=>BASE_URL.'/modules/Product/img/review/flowery/wild-flowers.png'),
                            array('value'=>9,'label'=>langTranslate('product','review elements','Floral - Honeysuckle','Honeysuckle')/*Цветы медоносы*/,'image'=>BASE_URL.'/modules/Product/img/review/flowery/honeysuckle.png'),
                            array('value'=>10,'label'=>langTranslate('product','review elements','Floral - Chamomile','Chamomile')/*Ромашка*/,'image'=>BASE_URL.'/modules/Product/img/review/flowery/chamomile.png'),
                            array('value'=>11,'label'=>langTranslate('product','review elements','Floral - Linden','Linden')/*Липа*/,'image'=>BASE_URL.'/modules/Product/img/review/flowery/linden.png'),
                            array('value'=>12,'label'=>langTranslate('product','review elements','Floral - Dried flowers','Dried flowers')/*Сухие цветы*/,'image'=>BASE_URL.'/modules/Product/img/review/flowery/dried-flowers.png'),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Herbaceous','Herbaceous'),//Зелень, овощи
                    'name'=>'primary-aroma-greenery',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Aroma - None','None')/*Нет*/,'default'=>true),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Aroma - Hints','Hints')/*Тонкий штрих*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Aroma - Medium (-)','Medium (-)')/*Немного*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Aroma - Medium','Medium')/*Средне*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Aroma - Medium (+)','Medium (+)')/*Заметно*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Aroma - Pronounced','Pronounced')/*Ярко*/),
                        )
                ),
            array(
                    'subelement-of'=>'primary-aroma-greenery',
                    'name'=>'primary-aroma-greenery-type',
					'aroma'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'multichoice'=>true,
                    'optional'=>true,
                    'hide_empty_from_merge'=>true,
                    'order_by_total_count_in_merge'=>true,
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Herbaceous - Grass','Grass')/*Стебли травы*/,'image'=>BASE_URL.'/modules/Product/img/review/greenery/grass.png'),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Herbaceous - Tomato leaf','Tomato leaf')/*Томатный лист*/,'image'=>BASE_URL.'/modules/Product/img/review/greenery/tomato-leaf.png'),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Herbaceous - Blackcurrant leaf','Blackcurrant leaf')/*Лист черной смородины*/,'image'=>BASE_URL.'/modules/Product/img/review/greenery/blackcurrant-leaf.png'),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Herbaceous - Asparagus','Asparagus')/*Спаржа*/,'image'=>BASE_URL.'/modules/Product/img/review/greenery/asparagus.png'),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Herbaceous - Green bell pepper','Green bell pepper')/*Зеленый болгарский перец*/,'image'=>BASE_URL.'/modules/Product/img/review/greenery/green-bell-pepper.png'),
                            array('value'=>6,'label'=>langTranslate('product','review elements','Herbaceous - Green pea','Green pea')/*Зеленый горошек*/,'image'=>BASE_URL.'/modules/Product/img/review/greenery/green-pea.png'),
                            array('value'=>7,'label'=>langTranslate('product','review elements','Herbaceous - Zucchini','Zucchini')/*Кабачок*/,'image'=>BASE_URL.'/modules/Product/img/review/greenery/zucchini.png'),
                            array('value'=>8,'label'=>langTranslate('product','review elements','Herbaceous - Potato','Potato')/*Картофель*/,'image'=>BASE_URL.'/modules/Product/img/review/greenery/potato.png'),
                            array('value'=>9,'label'=>langTranslate('product','review elements','Herbaceous - Beetroot','Beetroot')/*Свекла*/,'image'=>BASE_URL.'/modules/Product/img/review/greenery/beetroot.png'),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Aromatic herbs','Aromatic herbs'),//Ароматические травы
                    'name'=>'primary-aroma-herbs',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Aroma - None','None')/*Нет*/,'default'=>true),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Aroma - Hints','Hints')/*Тонкий штрих*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Aroma - Medium (-)','Medium (-)')/*Немного*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Aroma - Medium','Medium')/*Средне*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Aroma - Medium (+)','Medium (+)')/*Заметно*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Aroma - Pronounced','Pronounced')/*Ярко*/),
                        )
                ),
            array(
                    'subelement-of'=>'primary-aroma-herbs',
                    'name'=>'primary-aroma-herbs-type',
					'aroma'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'multichoice'=>true,
                    'optional'=>true,
                    'hide_empty_from_merge'=>true,
                    'order_by_total_count_in_merge'=>true,
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Aromatic herbs - Eucalyptus','Eucalyptus')/*Эвкалипт*/,'image'=>BASE_URL.'/modules/Product/img/review/herbs/eucalyptus.png'),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Aromatic herbs - Mint','Mint')/*Мята*/,'image'=>BASE_URL.'/modules/Product/img/review/herbs/mint.png'),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Aromatic herbs - Lavender','Lavender')/*Лаванда*/,'image'=>BASE_URL.'/modules/Product/img/review/herbs/lavender.png'),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Aromatic herbs - Medicinal','Medicinal')/*Аптечные травы*/,'image'=>BASE_URL.'/modules/Product/img/review/herbs/medicinal.png'),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Aromatic herbs - Field Grasses','Field Grasses')/*Полевые травы*/,'image'=>BASE_URL.'/modules/Product/img/review/herbs/field-grasses.png'),
                            array('value'=>6,'label'=>langTranslate('product','review elements','Aromatic herbs - Fennel','Fennel')/*Фенхель*/,'image'=>BASE_URL.'/modules/Product/img/review/herbs/fennel.png'),
                            array('value'=>7,'label'=>langTranslate('product','review elements','Aromatic herbs - Dill','Dill')/*Укроп*/,'image'=>BASE_URL.'/modules/Product/img/review/herbs/dill.png'),
                            array('value'=>8,'label'=>langTranslate('product','review elements','Aromatic herbs - Thyme','Thyme')/*Тимьян*/,'image'=>BASE_URL.'/modules/Product/img/review/herbs/thyme.png'),
                            array('value'=>9,'label'=>langTranslate('product','review elements','Aromatic herbs - Basil','Basil')/*Базилик*/,'image'=>BASE_URL.'/modules/Product/img/review/herbs/basil.png'),
                            array('value'=>10,'label'=>langTranslate('product','review elements','Aromatic herbs - Rosemary','Rosemary')/*Розмарин*/,'image'=>BASE_URL.'/modules/Product/img/review/herbs/rosemary.png'),
                            array('value'=>11,'label'=>langTranslate('product','review elements','Aromatic herbs - Pine','Pine')/*Хвоя*/,'image'=>BASE_URL.'/modules/Product/img/review/herbs/pine.png'),
                            array('value'=>12,'label'=>langTranslate('product','review elements','Aromatic herbs - Herbs for Pickling','Herbs for Pickling')/*Травы для засолки*/,'image'=>BASE_URL.'/modules/Product/img/review/herbs/herbs-for-pickling.png'),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Citrus fruits','Citrus fruits'),//Цитрусы
                    'name'=>'primary-aroma-citrus',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Aroma - None','None')/*Нет*/,'default'=>true),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Aroma - Hints','Hints')/*Тонкий штрих*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Aroma - Medium (-)','Medium (-)')/*Немного*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Aroma - Medium','Medium')/*Средне*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Aroma - Medium (+)','Medium (+)')/*Заметно*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Aroma - Pronounced','Pronounced')/*Ярко*/),
                        )
                ),
            array(
                    'subelement-of'=>'primary-aroma-citrus',
                    'name'=>'primary-aroma-citrus-type',
					'aroma'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'multichoice'=>true,
                    'optional'=>true,
                    'hide_empty_from_merge'=>true,
                    'order_by_total_count_in_merge'=>true,
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Citrus fruits - Lime','Lime')/*Лайм*/,'image'=>BASE_URL.'/modules/Product/img/review/citrus/lime.png'),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Citrus fruits - Lemon','Lemon')/*Лимон*/,'image'=>BASE_URL.'/modules/Product/img/review/citrus/lemon.png'),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Citrus fruits - Grapefruit','Grapefruit')/*Грейпфрут*/,'image'=>BASE_URL.'/modules/Product/img/review/citrus/grapefruit.png'),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Citrus fruits - Orange peel','Orange peel')/*Цедра апельсина*/,'image'=>BASE_URL.'/modules/Product/img/review/citrus/orange-peel.png'),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Citrus fruits - Lemon peel','Lemon peel')/*Цедра лимона*/,'image'=>BASE_URL.'/modules/Product/img/review/citrus/lemon-peel.png'),
                            array('value'=>6,'label'=>langTranslate('product','review elements','Citrus fruits - Pomelo','Pomelo')/*Помело*/,'image'=>BASE_URL.'/modules/Product/img/review/citrus/pomelo.png'),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','White/green fruits/berries','White/green fruits/berries'),//Белые/зеленые ягоды/фрукты
                    'name'=>'primary-aroma-white-fruit',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Aroma - None','None')/*Нет*/,'default'=>true),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Aroma - Hints','Hints')/*Тонкий штрих*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Aroma - Medium (-)','Medium (-)')/*Немного*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Aroma - Medium','Medium')/*Средне*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Aroma - Medium (+)','Medium (+)')/*Заметно*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Aroma - Pronounced','Pronounced')/*Ярко*/),
                        )
                ),
            array(
                    'subelement-of'=>'primary-aroma-white-fruit',
                    'name'=>'primary-aroma-white-fruit-type',
					'aroma'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'multichoice'=>true,
                    'optional'=>true,
                    'hide_empty_from_merge'=>true,
                    'order_by_total_count_in_merge'=>true,
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','White/green fruits/berries - White currant','White currant')/*Белая смородина*/,'image'=>BASE_URL.'/modules/Product/img/review/white-fruit/white-currant.png'),
                            array('value'=>2,'label'=>langTranslate('product','review elements','White/green fruits/berries - Gooseberry','Gooseberry')/*Крыжовник*/,'image'=>BASE_URL.'/modules/Product/img/review/white-fruit/gooseberry.png'),
                            array('value'=>3,'label'=>langTranslate('product','review elements','White/green fruits/berries - Grape','Grape')/*Столовый виноград*/,'image'=>BASE_URL.'/modules/Product/img/review/white-fruit/grape.png'),
                            array('value'=>4,'label'=>langTranslate('product','review elements','White/green fruits/berries - Apple','Apple')/*Яблоко*/,'image'=>BASE_URL.'/modules/Product/img/review/white-fruit/apple.png'),
                            array('value'=>5,'label'=>langTranslate('product','review elements','White/green fruits/berries - Pear','Pear')/*Груша*/,'image'=>BASE_URL.'/modules/Product/img/review/white-fruit/pear.png'),
                            array('value'=>6,'label'=>langTranslate('product','review elements','White/green fruits/berries - Quince','Quince')/*Айва*/,'image'=>BASE_URL.'/modules/Product/img/review/white-fruit/quince.png'),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Red fruits/berries','Red fruits/berries'),//Красные ягоды/фрукты
                    'name'=>'primary-aroma-red-fruit',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Aroma - None','None')/*Нет*/,'default'=>true),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Aroma - Hints','Hints')/*Тонкий штрих*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Aroma - Medium (-)','Medium (-)')/*Немного*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Aroma - Medium','Medium')/*Средне*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Aroma - Medium (+)','Medium (+)')/*Заметно*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Aroma - Pronounced','Pronounced')/*Ярко*/),
                        )
                ),
            array(
                    'subelement-of'=>'primary-aroma-red-fruit',
                    'name'=>'primary-aroma-red-fruit-type',
					'aroma'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'multichoice'=>true,
                    'optional'=>true,
                    'hide_empty_from_merge'=>true,
                    'order_by_total_count_in_merge'=>true,
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Red fruits/berries - Cranberry','Cranberry')/*Клюква*/,'image'=>BASE_URL.'/modules/Product/img/review/red-fruit/cranberry.png'),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Red fruits/berries - Lingonberry','Lingonberry')/*Брусника*/,'image'=>BASE_URL.'/modules/Product/img/review/red-fruit/lingonberry.png'),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Red fruits/berries - Redcurrant','Redcurrant')/*Красная смородина*/,'image'=>BASE_URL.'/modules/Product/img/review/red-fruit/redcurrant.png'),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Red fruits/berries - Raspberry','Raspberry')/*Малина*/,'image'=>BASE_URL.'/modules/Product/img/review/red-fruit/raspberry.png'),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Red fruits/berries - Strawberry','Strawberry')/*Клубника*/,'image'=>BASE_URL.'/modules/Product/img/review/red-fruit/strawberry.png'),
                            array('value'=>6,'label'=>langTranslate('product','review elements','Red fruits/berries - Red cherry','Red cherry')/*Красная вишня*/,'image'=>BASE_URL.'/modules/Product/img/review/red-fruit/red-cherry.png'),
                            array('value'=>7,'label'=>langTranslate('product','review elements','Red fruits/berries - Red plum','Red plum')/*Красная слива*/,'image'=>BASE_URL.'/modules/Product/img/review/red-fruit/red-plum.png'),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Black fruits/berries','Black fruits/berries'),//Черные ягоды/фрукты
                    'name'=>'primary-aroma-black-fruit',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Aroma - None','None')/*Нет*/,'default'=>true),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Aroma - Hints','Hints')/*Тонкий штрих*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Aroma - Medium (-)','Medium (-)')/*Немного*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Aroma - Medium','Medium')/*Средне*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Aroma - Medium (+)','Medium (+)')/*Заметно*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Aroma - Pronounced','Pronounced')/*Ярко*/),
                        )
                ),
            array(
                    'subelement-of'=>'primary-aroma-black-fruit',
                    'name'=>'primary-aroma-black-fruit-type',
					'aroma'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'multichoice'=>true,
                    'optional'=>true,
                    'hide_empty_from_merge'=>true,
                    'order_by_total_count_in_merge'=>true,
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Black fruits/berries - Blackcurrant','Blackcurrant')/*Черная смородина*/,'image'=>BASE_URL.'/modules/Product/img/review/black-fruit/blackcurrant.png'),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Black fruits/berries - Blueberry','Blueberry')/*Черника*/,'image'=>BASE_URL.'/modules/Product/img/review/black-fruit/blueberry.png'),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Black fruits/berries - Blackberry','Blackberry')/*Ежевика*/,'image'=>BASE_URL.'/modules/Product/img/review/black-fruit/blackberry.png'),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Black fruits/berries - Black cherry','Black cherry')/*Темная вишня*/,'image'=>BASE_URL.'/modules/Product/img/review/black-fruit/black-cherry.png'),
                            array('value'=>6,'label'=>langTranslate('product','review elements','Black fruits/berries - Black plum','Black plum')/*Черная слива*/,'image'=>BASE_URL.'/modules/Product/img/review/black-fruit/black-plum.png'),
                            array('value'=>7,'label'=>langTranslate('product','review elements','Black fruits/berries - Chokeberry','Chokeberry')/*Черноплодная рябина*/,'image'=>BASE_URL.'/modules/Product/img/review/black-fruit/chokeberry.png'),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Stone fruits','Stone fruits'),//Косточковые фрукты
                    'name'=>'primary-aroma-ossicle-fruit',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Aroma - None','None')/*Нет*/,'default'=>true),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Aroma - Hints','Hints')/*Тонкий штрих*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Aroma - Medium (-)','Medium (-)')/*Немного*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Aroma - Medium','Medium')/*Средне*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Aroma - Medium (+)','Medium (+)')/*Заметно*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Aroma - Pronounced','Pronounced')/*Ярко*/),
                        )
                ),
            array(
                    'subelement-of'=>'primary-aroma-ossicle-fruit',
                    'name'=>'primary-aroma-ossicle-fruit-type',
					'aroma'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'multichoice'=>true,
                    'optional'=>true,
                    'hide_empty_from_merge'=>true,
                    'order_by_total_count_in_merge'=>true,
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Stone fruits - Peach','Peach')/*Персик*/,'image'=>BASE_URL.'/modules/Product/img/review/ossicle-fruit/peach.png'),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Stone fruits - Apricot','Apricot')/*Абрикос*/,'image'=>BASE_URL.'/modules/Product/img/review/ossicle-fruit/apricot.png'),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Stone fruits - Nectarine','Nectarine')/*Нектарин*/,'image'=>BASE_URL.'/modules/Product/img/review/ossicle-fruit/nectarine.png'),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Stone fruits - Yellow plum','Yellow plum')/*Желтая слива*/,'image'=>BASE_URL.'/modules/Product/img/review/ossicle-fruit/yellow-plum.png'),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Tropical fruits','Tropical fruits'),//Тропические фрукты
                    'name'=>'primary-aroma-tropical-fruit',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Aroma - None','None')/*Нет*/,'default'=>true),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Aroma - Hints','Hints')/*Тонкий штрих*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Aroma - Medium (-)','Medium (-)')/*Немного*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Aroma - Medium','Medium')/*Средне*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Aroma - Medium (+)','Medium (+)')/*Заметно*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Aroma - Pronounced','Pronounced')/*Ярко*/),
                        )
                ),
            array(
                    'subelement-of'=>'primary-aroma-tropical-fruit',
                    'name'=>'primary-aroma-tropical-fruit-type',
					'aroma'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'multichoice'=>true,
                    'optional'=>true,
                    'hide_empty_from_merge'=>true,
                    'order_by_total_count_in_merge'=>true,
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Tropical fruits - Lychee','Lychee')/*Личи*/,'image'=>BASE_URL.'/modules/Product/img/review/tropical-fruit/lychee.png'),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Tropical fruits - Melon','Melon')/*Дыня*/,'image'=>BASE_URL.'/modules/Product/img/review/tropical-fruit/melon.png'),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Tropical fruits - Banana','Banana')/*Банан*/,'image'=>BASE_URL.'/modules/Product/img/review/tropical-fruit/banana.png'),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Tropical fruits - Pineapple','Pineapple')/*Ананас*/,'image'=>BASE_URL.'/modules/Product/img/review/tropical-fruit/pineapple.png'),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Tropical fruits - Mango','Mango')/*Манго*/,'image'=>BASE_URL.'/modules/Product/img/review/tropical-fruit/mango.png'),
                            array('value'=>6,'label'=>langTranslate('product','review elements','Tropical fruits - Passion fruit','Passion fruit')/*Маракуйя*/,'image'=>BASE_URL.'/modules/Product/img/review/tropical-fruit/passion-fruit.png'),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Mineral tones','Mineral tones'),//Минеральные тона
                    'name'=>'primary-aroma-mineral',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Aroma - None','None')/*Нет*/,'default'=>true),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Aroma - Hints','Hints')/*Тонкий штрих*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Aroma - Medium (-)','Medium (-)')/*Немного*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Aroma - Medium','Medium')/*Средне*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Aroma - Medium (+)','Medium (+)')/*Заметно*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Aroma - Pronounced','Pronounced')/*Ярко*/),
                        )
                ),
            array(
                    'subelement-of'=>'primary-aroma-mineral',
                    'name'=>'primary-aroma-mineral-type',
					'aroma'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'multichoice'=>true,
                    'optional'=>true,
                    'hide_empty_from_merge'=>true,
                    'order_by_total_count_in_merge'=>true,
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Mineral tones - Flint stone','Flint stone')/*Кремень*/,'image'=>BASE_URL.'/modules/Product/img/review/mineral/flint-stone.png'),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Mineral tones - Limestone, shells','Limestone, shells')/*Известняк, ракушки*/,'image'=>BASE_URL.'/modules/Product/img/review/mineral/limestone-shells.png'),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Mineral tones - Wet stones','Wet stones')/*Мокрые камни*/,'image'=>BASE_URL.'/modules/Product/img/review/mineral/wet-stones.png'),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Mineral tones - Steely','Steely')/*Металлический тон*/,'image'=>BASE_URL.'/modules/Product/img/review/mineral/steely.png'),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Mineral tones - Salty tone','Salty tone')/*Соленый тон*/,'image'=>BASE_URL.'/modules/Product/img/review/mineral/salty-tone.png'),
                        )
                ),
        ),
    'secondary_aroma_elements'=>array(
            array(
                    'caption'=>langTranslate('product','review elements','Yeast','Yeast'),//Дрожжевые
                    'name'=>'secondary-aroma-yeast',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Aroma - None','None')/*Нет*/,'default'=>true),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Aroma - Hints','Hints')/*Тонкий штрих*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Aroma - Medium (-)','Medium (-)')/*Немного*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Aroma - Medium','Medium')/*Средне*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Aroma - Medium (+)','Medium (+)')/*Заметно*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Aroma - Pronounced','Pronounced')/*Ярко*/),
                        )
                ),
            array(
                    'subelement-of'=>'secondary-aroma-yeast',
                    'name'=>'secondary-aroma-yeast-type',
					'aroma'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'multichoice'=>true,
                    'optional'=>true,
                    'hide_empty_from_merge'=>true,
                    'order_by_total_count_in_merge'=>true,
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Yeast - Bread','Bread')/*Хлеб*/,'image'=>BASE_URL.'/modules/Product/img/review/yeast/bread.png'),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Yeast - Pastry','Pastry')/*Сухари*/,'image'=>BASE_URL.'/modules/Product/img/review/yeast/pastry.png'),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Yeast - Bread dough','Bread dough')/*Тесто*/,'image'=>BASE_URL.'/modules/Product/img/review/yeast/bread-dough.png'),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Yeast - Yeast','Yeast')/*Дрожжевой тон*/,'image'=>BASE_URL.'/modules/Product/img/review/yeast/yeast.png'),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','MLF','MLF'),//Молочные
                    'name'=>'secondary-aroma-milk',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Aroma - None','None')/*Нет*/,'default'=>true),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Aroma - Hints','Hints')/*Тонкий штрих*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Aroma - Medium (-)','Medium (-)')/*Немного*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Aroma - Medium','Medium')/*Средне*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Aroma - Medium (+)','Medium (+)')/*Заметно*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Aroma - Pronounced','Pronounced')/*Ярко*/),
                        )
                ),
            array(
                    'subelement-of'=>'secondary-aroma-milk',
                    'name'=>'secondary-aroma-milk-type',
					'aroma'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'multichoice'=>true,
                    'optional'=>true,
                    'hide_empty_from_merge'=>true,
                    'order_by_total_count_in_merge'=>true,
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','MLF - Butter','Butter')/*Сливочное масло*/,'image'=>BASE_URL.'/modules/Product/img/review/milk/butter.png'),
                            array('value'=>2,'label'=>langTranslate('product','review elements','MLF - Toffee','Toffee')/*Сливочный ирис*/,'image'=>BASE_URL.'/modules/Product/img/review/milk/toffee.png'),
                            array('value'=>3,'label'=>langTranslate('product','review elements','MLF - Cream','Cream')/*Сливки*/,'image'=>BASE_URL.'/modules/Product/img/review/milk/cream.png'),
                            array('value'=>4,'label'=>langTranslate('product','review elements','MLF - Yoghurt','Yoghurt')/*Йогурт*/,'image'=>BASE_URL.'/modules/Product/img/review/milk/yoghurt.png'),
                            array('value'=>5,'label'=>langTranslate('product','review elements','MLF - Cheese','Cheese')/*Сыр*/,'image'=>BASE_URL.'/modules/Product/img/review/milk/cheese.png'),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Hot spices','Hot spices'),//Острые пряности
                    'name'=>'secondary-aroma-spice',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Aroma - None','None')/*Нет*/,'default'=>true),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Aroma - Hints','Hints')/*Тонкий штрих*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Aroma - Medium (-)','Medium (-)')/*Немного*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Aroma - Medium','Medium')/*Средне*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Aroma - Medium (+)','Medium (+)')/*Заметно*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Aroma - Pronounced','Pronounced')/*Ярко*/),
                        )
                ),
            array(
                    'subelement-of'=>'secondary-aroma-spice',
                    'name'=>'secondary-aroma-spice-type',
					'aroma'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'multichoice'=>true,
                    'optional'=>true,
                    'hide_empty_from_merge'=>true,
                    'order_by_total_count_in_merge'=>true,
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Hot spices - White pepper','White pepper')/*Белый перец*/,'image'=>BASE_URL.'/modules/Product/img/review/spice/white-pepper.png'),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Hot spices - Black pepper','Black pepper')/*Черный перец*/,'image'=>BASE_URL.'/modules/Product/img/review/spice/black-pepper.png'),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Hot spices - Ginger','Ginger')/*Имбирь*/,'image'=>BASE_URL.'/modules/Product/img/review/spice/ginger.png'),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Sweet spices','Sweet spices'),//Кондитерские пряности
                    'name'=>'secondary-aroma-confectionery',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Aroma - None','None')/*Нет*/,'default'=>true),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Aroma - Hints','Hints')/*Тонкий штрих*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Aroma - Medium (-)','Medium (-)')/*Немного*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Aroma - Medium','Medium')/*Средне*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Aroma - Medium (+)','Medium (+)')/*Заметно*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Aroma - Pronounced','Pronounced')/*Ярко*/),
                        )
                ),
            array(
                    'subelement-of'=>'secondary-aroma-confectionery',
                    'name'=>'secondary-aroma-confectionery-type',
					'aroma'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'multichoice'=>true,
                    'optional'=>true,
                    'hide_empty_from_merge'=>true,
                    'order_by_total_count_in_merge'=>true,
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Sweet spices - Vanilla','Vanilla')/*Ваниль*/,'image'=>BASE_URL.'/modules/Product/img/review/confectionery/vanilla.png'),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Sweet spices - Cinnamon','Cinnamon')/*Корица*/,'image'=>BASE_URL.'/modules/Product/img/review/confectionery/cinnamon.png'),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Sweet spices - Cloves','Cloves')/*Гвоздика*/,'image'=>BASE_URL.'/modules/Product/img/review/confectionery/cloves.png'),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Sweet spices - Nutmeg','Nutmeg')/*Мускатный орех*/,'image'=>BASE_URL.'/modules/Product/img/review/confectionery/nutmeg.png'),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Sweet spices - Coconut','Coconut')/*Кокос*/,'image'=>BASE_URL.'/modules/Product/img/review/confectionery/coconut.png'),
                            array('value'=>6,'label'=>langTranslate('product','review elements','Sweet spices - Licorice','Licorice')/*Лакрица*/,'image'=>BASE_URL.'/modules/Product/img/review/confectionery/licorice.png'),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Empyreumatic','Empyreumatic'),//Эмпиреоматические
                    'name'=>'secondary-aroma-empirical',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Aroma - None','None')/*Нет*/,'default'=>true),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Aroma - Hints','Hints')/*Тонкий штрих*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Aroma - Medium (-)','Medium (-)')/*Немного*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Aroma - Medium','Medium')/*Средне*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Aroma - Medium (+)','Medium (+)')/*Заметно*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Aroma - Pronounced','Pronounced')/*Ярко*/),
                        )
                ),
            array(
                    'subelement-of'=>'secondary-aroma-empirical',
                    'name'=>'secondary-aroma-empirical-type',
					'aroma'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'multichoice'=>true,
                    'optional'=>true,
                    'hide_empty_from_merge'=>true,
                    'order_by_total_count_in_merge'=>true,
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Empyreumatic - Toasts','Toasts')/*Тосты*/,'image'=>BASE_URL.'/modules/Product/img/review/empirical/toasts.png'),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Empyreumatic - Smoke','Smoke')/*Дым*/,'image'=>BASE_URL.'/modules/Product/img/review/empirical/smoke.png'),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Empyreumatic - Coffee','Coffee')/*Кофе*/,'image'=>BASE_URL.'/modules/Product/img/review/empirical/coffee.png'),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Empyreumatic - Chocolate','Chocolate')/*Шоколад*/,'image'=>BASE_URL.'/modules/Product/img/review/empirical/chocolate.png'),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Empyreumatic - Burnt sugar','Burnt sugar')/*Жженый сахар*/,'image'=>BASE_URL.'/modules/Product/img/review/empirical/burnt-sugar.png'),
                            array('value'=>6,'label'=>langTranslate('product','review elements','Empyreumatic - Grilled grass','Grilled grass')/*Травы на гриле*/,'image'=>BASE_URL.'/modules/Product/img/review/empirical/grilled-grass.png'),
                        )
                ),
        ),
    'tertiary_aroma_elements'=>array(
            array(
                    'caption'=>langTranslate('product','review elements','Dried fruits','Dried fruits'),//Сухофрукты
                    'name'=>'tertiary-aroma-dried-fruit',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Aroma - None','None')/*Нет*/,'default'=>true),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Aroma - Hints','Hints')/*Тонкий штрих*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Aroma - Medium (-)','Medium (-)')/*Немного*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Aroma - Medium','Medium')/*Средне*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Aroma - Medium (+)','Medium (+)')/*Заметно*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Aroma - Pronounced','Pronounced')/*Ярко*/),
                        )
                ),
            array(
                    'subelement-of'=>'tertiary-aroma-dried-fruit',
                    'name'=>'tertiary-aroma-dried-fruit-type',
					'aroma'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'multichoice'=>true,
                    'optional'=>true,
                    'hide_empty_from_merge'=>true,
                    'order_by_total_count_in_merge'=>true,
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Dried fruits - Dried apricots','Dried apricots')/*Курага*/,'image'=>BASE_URL.'/modules/Product/img/review/dried-fruit/dried-apricots.png'),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Dried fruits - Raisins','Raisins')/*Изюм*/,'image'=>BASE_URL.'/modules/Product/img/review/dried-fruit/raisins.png'),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Dried fruits - Prunes','Prunes')/*Чернослив*/,'image'=>BASE_URL.'/modules/Product/img/review/dried-fruit/prunes.png'),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Dried fruits - Figs','Figs')/*Инжир*/,'image'=>BASE_URL.'/modules/Product/img/review/dried-fruit/figs.png'),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Dried fruits - Dates','Dates')/*Финики*/,'image'=>BASE_URL.'/modules/Product/img/review/dried-fruit/dates.png'),
                            array('value'=>6,'label'=>langTranslate('product','review elements','Dried fruits - Dried apples','Dried apples')/*Сушеные яблоки*/,'image'=>BASE_URL.'/modules/Product/img/review/dried-fruit/dried-apples.png'),
                            array('value'=>7,'label'=>langTranslate('product','review elements','Dried fruits - Dried pears','Dried pears')/*Сушеные груши*/,'image'=>BASE_URL.'/modules/Product/img/review/dried-fruit/dried-pears.png'),
                            array('value'=>8,'label'=>langTranslate('product','review elements','Dried fruits - Dried peaches','Dried peaches')/*Сушеные персики*/,'image'=>BASE_URL.'/modules/Product/img/review/dried-fruit/dried-peaches.png'),
                            array('value'=>9,'label'=>langTranslate('product','review elements','Dried fruits - Dried bananas','Dried bananas')/*Сушеные бананы*/,'image'=>BASE_URL.'/modules/Product/img/review/dried-fruit/dried-bananas.png'),
                            array('value'=>10,'label'=>langTranslate('product','review elements','Dried fruits - Candied fruits','Candied fruits')/*Цукаты*/,'image'=>BASE_URL.'/modules/Product/img/review/dried-fruit/candied-fruits.png'),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Nuts','Nuts'),//Орехи
                    'name'=>'tertiary-aroma-nuts',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Aroma - None','None')/*Нет*/,'default'=>true),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Aroma - Hints','Hints')/*Тонкий штрих*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Aroma - Medium (-)','Medium (-)')/*Немного*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Aroma - Medium','Medium')/*Средне*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Aroma - Medium (+)','Medium (+)')/*Заметно*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Aroma - Pronounced','Pronounced')/*Ярко*/),
                        )
                ),
            array(
                    'subelement-of'=>'tertiary-aroma-nuts',
                    'name'=>'tertiary-aroma-nuts-type',
					'aroma'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'multichoice'=>true,
                    'optional'=>true,
                    'hide_empty_from_merge'=>true,
                    'order_by_total_count_in_merge'=>true,
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Nuts - Almond','Almond')/*Миндаль*/,'image'=>BASE_URL.'/modules/Product/img/review/nuts/almond.png'),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Nuts - Marzipan','Marzipan')/*Марципан*/,'image'=>BASE_URL.'/modules/Product/img/review/nuts/marzipan.png'),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Nuts - Walnut','Walnut')/*Грецкий орех*/,'image'=>BASE_URL.'/modules/Product/img/review/nuts/walnut.png'),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Nuts - Hazelnut','Hazelnut')/*Фундук*/,'image'=>BASE_URL.'/modules/Product/img/review/nuts/hazelnut.png'),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Confectionery tones','Confectionery tones'),//Кондитерские тона
                    'name'=>'tertiary-aroma-confection',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Aroma - None','None')/*Нет*/,'default'=>true),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Aroma - Hints','Hints')/*Тонкий штрих*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Aroma - Medium (-)','Medium (-)')/*Немного*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Aroma - Medium','Medium')/*Средне*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Aroma - Medium (+)','Medium (+)')/*Заметно*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Aroma - Pronounced','Pronounced')/*Ярко*/),
                        )
                ),
            array(
                    'subelement-of'=>'tertiary-aroma-confection',
                    'name'=>'tertiary-aroma-confection-type',
					'aroma'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'multichoice'=>true,
                    'optional'=>true,
                    'hide_empty_from_merge'=>true,
                    'order_by_total_count_in_merge'=>true,
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Confectionery tones - Honey','Honey')/*Мед*/,'image'=>BASE_URL.'/modules/Product/img/review/confection/honey.png'),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Confectionery tones - Marmalade','Marmalade')/*Мармелад*/,'image'=>BASE_URL.'/modules/Product/img/review/confection/marmalade.png'),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Confectionery tones - Caramel','Caramel')/*Карамель*/,'image'=>BASE_URL.'/modules/Product/img/review/confection/caramel.png'),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Confectionery tones - Confiture','Confiture')/*Конфитюр*/,'image'=>BASE_URL.'/modules/Product/img/review/confection/confiture.png'),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Confectionery tones - Brioche','Brioche')/*Бриошь*/,'image'=>BASE_URL.'/modules/Product/img/review/confection/brioche.png'),
                            array('value'=>6,'label'=>langTranslate('product','review elements','Confectionery tones - Biscuit','Biscuit')/*Бисквит*/,'image'=>BASE_URL.'/modules/Product/img/review/confection/biscuit.png'),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Other notes','Other notes'),//Тона выдержки
                    'name'=>'tertiary-aroma-aging',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Aroma - None','None')/*Нет*/,'default'=>true),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Aroma - Hints','Hints')/*Тонкий штрих*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Aroma - Medium (-)','Medium (-)')/*Немного*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Aroma - Medium','Medium')/*Средне*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Aroma - Medium (+)','Medium (+)')/*Заметно*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Aroma - Pronounced','Pronounced')/*Ярко*/),
                        )
                ),
            array(
                    'subelement-of'=>'tertiary-aroma-aging',
                    'name'=>'tertiary-aroma-aging-type',
					'aroma'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'multichoice'=>true,
                    'optional'=>true,
                    'hide_empty_from_merge'=>true,
                    'order_by_total_count_in_merge'=>true,
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Other notes - Petrol','Petrol')/*Нефтяной тон*/,'image'=>BASE_URL.'/modules/Product/img/review/aging/petrol.png'),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Other notes - Mushrooms','Mushrooms')/*Грибы*/,'image'=>BASE_URL.'/modules/Product/img/review/aging/mushrooms.png'),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Other notes - Wet Wool','Wet Wool')/*Шерсть*/,'image'=>BASE_URL.'/modules/Product/img/review/aging/wet-wool.png'),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Other notes - Hay','Hay')/*Сено*/,'image'=>BASE_URL.'/modules/Product/img/review/aging/hay.png'),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Other notes - Game','Game')/*Дичь*/,'image'=>BASE_URL.'/modules/Product/img/review/aging/game.png'),
                            array('value'=>6,'label'=>langTranslate('product','review elements','Other notes - Cured meat','Cured meat')/*Вяленое мясо*/,'image'=>BASE_URL.'/modules/Product/img/review/aging/cured-meat.png'),
                            array('value'=>7,'label'=>langTranslate('product','review elements','Other notes - Leather','Leather')/*Кожа*/,'image'=>BASE_URL.'/modules/Product/img/review/aging/leather.png'),
                            array('value'=>8,'label'=>langTranslate('product','review elements','Other notes - Olives','Olives')/*Оливки*/,'image'=>BASE_URL.'/modules/Product/img/review/aging/olives.png'),
                            array('value'=>9,'label'=>langTranslate('product','review elements','Other notes - Truffle','Truffle')/*Трюфель*/,'image'=>BASE_URL.'/modules/Product/img/review/aging/truffle.png'),
                            array('value'=>10,'label'=>langTranslate('product','review elements','Other notes - Soil','Soil')/*Земля*/,'image'=>BASE_URL.'/modules/Product/img/review/aging/soil.png'),
                            array('value'=>11,'label'=>langTranslate('product','review elements','Other notes - Wet leaves','Wet leaves')/*Мокрая листва*/,'image'=>BASE_URL.'/modules/Product/img/review/aging/wet-leaves.png'),
                            array('value'=>12,'label'=>langTranslate('product','review elements','Other notes - Forest floor','Forest floor')/*Подлесок*/,'image'=>BASE_URL.'/modules/Product/img/review/aging/forest-floor.png'),
                            array('value'=>13,'label'=>langTranslate('product','review elements','Other notes - Cedar','Cedar')/*Кедр*/,'image'=>BASE_URL.'/modules/Product/img/review/aging/cedar.png'),
                            array('value'=>14,'label'=>langTranslate('product','review elements','Other notes - Cigar box','Cigar box')/*Сигарная коробка*/,'image'=>BASE_URL.'/modules/Product/img/review/aging/cigar-box.png'),
                            array('value'=>15,'label'=>langTranslate('product','review elements','Other notes - Farmyard','Farmyard')/*Скотный двор*/,'image'=>BASE_URL.'/modules/Product/img/review/aging/farmyard.png'),


                        )
                ),
        ),
    'taste_elements'=>array(
            array(
                    'caption'=>langTranslate('product','review elements','Intensity','Intensity'),//Интенсивность
                    'name'=>'taste-intensity',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Intensity - Light','Light')/*Приглушенный*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Intensity - Medium (-)','Medium (-)')/*Ниже среднего*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Intensity - Medium','Medium')/*Средний*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Intensity - Medium (+)','Medium (+)')/*Выше среднего*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Intensity - Pronounced','Pronounced')/*Яркий*/),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Length','Length'),//Длина вкуса
                    'name'=>'taste-continuance',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Length - Short','Short')/*Короткий*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Length - Medium (-)','Medium (-)')/*Короче среднего*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Length - Medium','Medium')/*Средний*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Length - Medium (+)','Medium (+)')/*Выше среднего*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Length - Long','Long')/*Долгий*/),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Complexity','Complexity'),//Сложность
                    'name'=>'taste-complexity',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Complexity - Simple','Simple')/*Одномерный*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Complexity - Medium (-)','Medium (-)')/*Нельзя назвать простым*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Complexity - Medium','Medium')/*Средней сложности*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Complexity - Medium (+)','Medium (+)')/*Достаточно сложный, но мог бы быть сложнее*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Complexity - Very Complex','Very Complex')/*Многогранный*/),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Fruit maturity','Fruit maturity'),//Степень спелости фруктовой составляющей
                    'name'=>'taste-ripeness',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Fruit maturity - Unripe fruits','Unripe fruits')/*Незрелые*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Fruit maturity - Not ripe enough','Not ripe enough')/*Недостаточно спелые*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Fruit maturity - Ripe fruits','Ripe fruits')/*Спелые фрукты*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Fruit maturity - Overripe fruits','Overripe fruits')/*Переспелые фрукты*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Fruit maturity - Jammy fruits','Jammy fruits')/*Джем*/),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Development','Development'),//Развитие
                    'name'=>'taste-development',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Development - Just primary aromas','Just primary aromas')/*Молодой*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Development - Hints of tertiary','Hints of tertiary')/*Намеки на третичные ароматы*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Development - Primary to dominate tertiary','Primary to dominate tertiary')/*Третичные ароматы отчетливо слышны, но сортовые доминируют*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Development - Balance of primary and tertiary','Balance of primary and tertiary')/*Поровну сортовых и третичных ароматов*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Development - Tertiary dominate','Tertiary dominate')/*Третичные ароматы доминируют*/),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Outstanding aftertaste','Outstanding aftertaste'),//Выдающееся послевкусие
                    'hint'=>langTranslate('product','review elements','Personal emotional response','Personal emotional response'),//Личный эмоциональный отклик
                    'name'=>'taste-aftertaste',
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Outstanding aftertaste - Simple','Simple')/*Самое обычное*/),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Outstanding aftertaste - Interesting','Interesting')/*Есть интересные ноты*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Outstanding aftertaste - Notable','Notable')/*Не оставило равнодушным*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Outstanding aftertaste - Appealing','Appealing')/*Нравится*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Outstanding aftertaste - Outstanding','Outstanding')/*Очень нравится*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Outstanding aftertaste - Delightful','Delightful')/*Вызывает восторг*/),
                            
                        )
                ),
        ),
    'taste_structure_elements'=>array(
            array(
                    'caption'=>langTranslate('product','review elements','Sweetness','Sweetness'),//Сахар
                    'name'=>'taste-structure-sugar',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Sweetness - Dry','Dry')/*Сухое*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Sweetness - Off-dry','Off-dry')/*Офф-драй*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Sweetness - Medium-dry','Medium-dry')/*Полусухое*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Sweetness - Medium-sweet','Medium-sweet')/*Полусладкое*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Sweetness - Sweet','Sweet')/*Сладкое*/),
                            array('value'=>6,'label'=>langTranslate('product','review elements','Sweetness - Luscious','Luscious')/*Приторное*/),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Acidity','Acidity'),//Кислотность
                    'name'=>'taste-structure-acidity',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Acidity - Low','Low')/*Низкая*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Acidity - Medium(-)','Medium(-)')/*Ниже среднего*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Acidity - Medium','Medium')/*Средняя*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Acidity - Medium(+)','Medium(+)')/*Выше среднего*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Acidity - High','High')/*Высокая*/),
                            array('value'=>6,'label'=>langTranslate('product','review elements','Acidity - Extremely high','Extremely high')/*Чрезвычайно высокая*/),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Acidity balance','Acidity balance'),//Баланс кислотности
                    'class'=>'balance-score',
                    'name'=>'balance-acid',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Balance - Noticeably imbalanced','Noticeably imbalanced')/*Заметный дисбаланс*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Balance - Slightly imbalanced','Slightly imbalanced')/*Легкий дисбаланс*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Balance - Balanced','Balanced')/*Сбалансировано*/),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Tannin','Tannin'),//Танины
                    'name'=>'taste-structure-tannins',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Tannin - None','None')/*Нет*/),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Tannin - Low','Low')/*Мало*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Tannin - Medium(-)','Medium(-)')/*Ниже среднего*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Tannin - Medium','Medium')/*Среднее количество*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Tannin - Medium(+)','Medium(+)')/*Выше среднего*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Tannin - High','High')/*Много*/),
                            array('value'=>6,'label'=>langTranslate('product','review elements','Tannin - Extremely high','Extremely high')/*Чрезвычайно много*/),
                        )
                ),
            array(//white pink
                    'caption'=>langTranslate('product','review elements','Texture','Texture'),//Текстура
                    'name'=>'taste-structure-texture',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Texture - Drying','Drying')/*Сушащая*/,'segment-class'=>'wine-color-white wine-color-pink'),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Texture - Almost smooth','Almost smooth')/*Недостаточно округлая*/,'segment-class'=>'wine-color-white wine-color-pink'),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Texture - Smooth','Smooth')/*Округлая*/,'segment-class'=>'wine-color-white wine-color-pink'),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Texture - Soft','Soft')/*Мягкая*/,'segment-class'=>'wine-color-white wine-color-pink'),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Texture - Oily','Oily')/*Маслянистая*/,'segment-class'=>'wine-color-white wine-color-pink'),

                            array('value'=>1,'label'=>langTranslate('product','review elements','Texture - Tough','Tough')/*Грубая*/,'segment-class'=>'wine-color-red'),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Texture - Rough','Rough')/*Шероховатая*/,'segment-class'=>'wine-color-red'),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Texture - Mature/durable','Mature/durable')/*Зрелая/прочная*/,'segment-class'=>'wine-color-red'),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Texture - Velvet','Velvet')/*Бархатная*/,'segment-class'=>'wine-color-red'),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Texture - Silky','Silky')/*Шелковистая*/,'segment-class'=>'wine-color-red'),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Fruit balance','Fruit balance'),//Баланс фруктовости
                    'hint'=>langTranslate('product','review elements','Balance between fruit maturity and texture','Balance between fruit maturity and texture'),//Баланс между фруктовой составляющей и текстурой. Дисбаланс означает, что вину не хватает фруктовости. Например, холодный год в Медоке
                    'class'=>'balance-score',
                    'name'=>'balance-fruit',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Balance - Noticeably imbalanced','Noticeably imbalanced')/*Заметный дисбаланс*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Balance - Slightly imbalanced','Slightly imbalanced')/*Легкий дисбаланс*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Balance - Balanced','Balanced')/*Сбалансировано*/),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Body','Body'),//Вес
                    'name'=>'taste-structure-body',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Body - Low','Low')/*Легкое*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Body - Medium(-)','Medium(-)')/*Легче среднего*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Body - Medium','Medium')/*Среднее*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Body - Medium(+)','Medium(+)')/*Выше среднего*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Body - Full','Full')/*Полновесное*/),
                            array('value'=>6,'label'=>langTranslate('product','review elements','Body - Very heavy','Very heavy')/*Очень тяжелое*/),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Alcohol','Alcohol'),//Алкоголь
                    'name'=>'taste-structure-alcohol',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Alcohol - Low','Low')/*Низкий*/,'segment-class'=>'wine-type-still wine-type-sparkling'),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Alcohol - Medium(-)','Medium(-)')/*Ниже среднего*/,'segment-class'=>'wine-type-still wine-type-sparkling'),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Alcohol - Medium','Medium')/*Средний*/,'segment-class'=>'wine-type-still wine-type-sparkling'),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Alcohol - Medium(+)','Medium(+)')/*Выше среднего*/,'segment-class'=>'wine-type-still wine-type-sparkling'),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Alcohol - High','High')/*Высокий*/,'segment-class'=>'wine-type-still wine-type-sparkling'),
                            array('value'=>6,'label'=>langTranslate('product','review elements','Alcohol - Extremely high','Extremely high')/*Обжигающий*/,'segment-class'=>'wine-type-still wine-type-sparkling'),

                            array('value'=>1,'label'=>langTranslate('product','review elements','Alcohol - Low','Low')/*Низкий*/,'segment-class'=>'wine-type-fortified'),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Alcohol - Medium 15-19','Medium 15-19')/*Средний 15-19*/,'segment-class'=>'wine-type-fortified'),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Alcohol - High >19','High >19')/*Высокий >19*/,'segment-class'=>'wine-type-fortified'),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Alcohol balance','Alcohol balance'),//Баланс алкоголя
                    'class'=>'balance-score',
                    'name'=>'balance-alcohol',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Balance - Noticeably imbalanced','Noticeably imbalanced')/*Заметный дисбаланс*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Balance - Slightly imbalanced','Slightly imbalanced')/*Легкий дисбаланс*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Balance - Balanced','Balanced')/*Сбалансировано*/),
                        )
                ),
        ),
    'primary_flavor_elements'=>array(
            array(
                    'caption'=>langTranslate('product','review elements','Floral','Floral'),//Цветочные
                    'name'=>'primary-flavor-flowery',
                    'autofill_from'=>'primary-aroma-flowery',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Aroma - None','None')/*Нет*/,'default'=>true),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Aroma - Hints','Hints')/*Тонкий штрих*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Aroma - Medium (-)','Medium (-)')/*Немного*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Aroma - Medium','Medium')/*Средне*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Aroma - Medium (+)','Medium (+)')/*Заметно*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Aroma - Pronounced','Pronounced')/*Ярко*/),
                        )
                ),
            array(
                    'subelement-of'=>'primary-flavor-flowery',
                    'name'=>'primary-flavor-flowery-type',
					'aroma'=>true,
                    'autofill_from'=>'primary-aroma-flowery-type',
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'multichoice'=>true,
                    'optional'=>true,
                    'hide_empty_from_merge'=>true,
                    'order_by_total_count_in_merge'=>true,
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Floral - Rose','Rose')/*Роза*/,'image'=>BASE_URL.'/modules/Product/img/review/flowery/rose.png'),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Floral - Violet','Violet')/*Фиалка*/,'image'=>BASE_URL.'/modules/Product/img/review/flowery/violet.png'),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Floral - Iris','Iris')/*Ирисы*/,'image'=>BASE_URL.'/modules/Product/img/review/flowery/iris.png'),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Floral - Peony','Peony')/*Пионы*/,'image'=>BASE_URL.'/modules/Product/img/review/flowery/pion.png'),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Floral - Acacia','Acacia')/*Акация*/,'image'=>BASE_URL.'/modules/Product/img/review/flowery/akacia.png'),
                            array('value'=>6,'label'=>langTranslate('product','review elements','Floral - Orange-blossom','Orange-blossom')/*Флердоранж*/,'image'=>BASE_URL.'/modules/Product/img/review/flowery/orange-blossom.png'),
                            array('value'=>7,'label'=>langTranslate('product','review elements','Floral - Elderflower','Elderflower')/*Бузина*/,'image'=>BASE_URL.'/modules/Product/img/review/flowery/elderflower.png'),
                            array('value'=>8,'label'=>langTranslate('product','review elements','Floral - Wild Flowers','Wild Flowers')/*Полевые цветы*/,'image'=>BASE_URL.'/modules/Product/img/review/flowery/wild-flowers.png'),
                            array('value'=>9,'label'=>langTranslate('product','review elements','Floral - Honeysuckle','Honeysuckle')/*Цветы медоносы*/,'image'=>BASE_URL.'/modules/Product/img/review/flowery/honeysuckle.png'),
                            array('value'=>10,'label'=>langTranslate('product','review elements','Floral - Chamomile','Chamomile')/*Ромашка*/,'image'=>BASE_URL.'/modules/Product/img/review/flowery/chamomile.png'),
                            array('value'=>11,'label'=>langTranslate('product','review elements','Floral - Linden','Linden')/*Липа*/,'image'=>BASE_URL.'/modules/Product/img/review/flowery/linden.png'),
                            array('value'=>12,'label'=>langTranslate('product','review elements','Floral - Dried flowers','Dried flowers')/*Сухие цветы*/,'image'=>BASE_URL.'/modules/Product/img/review/flowery/dried-flowers.png'),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Herbaceous','Herbaceous'),//Зелень, овощи
                    'name'=>'primary-flavor-greenery',
                    'autofill_from'=>'primary-aroma-greenery',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Aroma - None','None')/*Нет*/,'default'=>true),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Aroma - Hints','Hints')/*Тонкий штрих*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Aroma - Medium (-)','Medium (-)')/*Немного*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Aroma - Medium','Medium')/*Средне*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Aroma - Medium (+)','Medium (+)')/*Заметно*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Aroma - Pronounced','Pronounced')/*Ярко*/),
                        )
                ),
            array(
                    'subelement-of'=>'primary-flavor-greenery',
                    'name'=>'primary-flavor-greenery-type',
					'aroma'=>true,
                    'autofill_from'=>'primary-aroma-greenery-type',
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'multichoice'=>true,
                    'optional'=>true,
                    'hide_empty_from_merge'=>true,
                    'order_by_total_count_in_merge'=>true,
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Herbaceous - Grass','Grass')/*Стебли травы*/,'image'=>BASE_URL.'/modules/Product/img/review/greenery/grass.png'),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Herbaceous - Tomato leaf','Tomato leaf')/*Томатный лист*/,'image'=>BASE_URL.'/modules/Product/img/review/greenery/tomato-leaf.png'),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Herbaceous - Blackcurrant leaf','Blackcurrant leaf')/*Лист черной смородины*/,'image'=>BASE_URL.'/modules/Product/img/review/greenery/blackcurrant-leaf.png'),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Herbaceous - Asparagus','Asparagus')/*Спаржа*/,'image'=>BASE_URL.'/modules/Product/img/review/greenery/asparagus.png'),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Herbaceous - Green bell pepper','Green bell pepper')/*Зеленый болгарский перец*/,'image'=>BASE_URL.'/modules/Product/img/review/greenery/green-bell-pepper.png'),
                            array('value'=>6,'label'=>langTranslate('product','review elements','Herbaceous - Green pea','Green pea')/*Зеленый горошек*/,'image'=>BASE_URL.'/modules/Product/img/review/greenery/green-pea.png'),
                            array('value'=>7,'label'=>langTranslate('product','review elements','Herbaceous - Zucchini','Zucchini')/*Кабачок*/,'image'=>BASE_URL.'/modules/Product/img/review/greenery/zucchini.png'),
                            array('value'=>8,'label'=>langTranslate('product','review elements','Herbaceous - Potato','Potato')/*Картофель*/,'image'=>BASE_URL.'/modules/Product/img/review/greenery/potato.png'),
                            array('value'=>9,'label'=>langTranslate('product','review elements','Herbaceous - Beetroot','Beetroot')/*Свекла*/,'image'=>BASE_URL.'/modules/Product/img/review/greenery/beetroot.png'),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Aromatic herbs','Aromatic herbs'),//Ароматические травы
                    'name'=>'primary-flavor-herbs',
                    'autofill_from'=>'primary-aroma-herbs',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Aroma - None','None')/*Нет*/,'default'=>true),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Aroma - Hints','Hints')/*Тонкий штрих*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Aroma - Medium (-)','Medium (-)')/*Немного*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Aroma - Medium','Medium')/*Средне*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Aroma - Medium (+)','Medium (+)')/*Заметно*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Aroma - Pronounced','Pronounced')/*Ярко*/),
                        )
                ),
            array(
                    'subelement-of'=>'primary-flavor-herbs',
                    'name'=>'primary-flavor-herbs-type',
					'aroma'=>true,
                    'autofill_from'=>'primary-aroma-herbs-type',
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'multichoice'=>true,
                    'optional'=>true,
                    'hide_empty_from_merge'=>true,
                    'order_by_total_count_in_merge'=>true,
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Aromatic herbs - Eucalyptus','Eucalyptus')/*Эвкалипт*/,'image'=>BASE_URL.'/modules/Product/img/review/herbs/eucalyptus.png'),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Aromatic herbs - Mint','Mint')/*Мята*/,'image'=>BASE_URL.'/modules/Product/img/review/herbs/mint.png'),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Aromatic herbs - Lavender','Lavender')/*Лаванда*/,'image'=>BASE_URL.'/modules/Product/img/review/herbs/lavender.png'),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Aromatic herbs - Medicinal','Medicinal')/*Аптечные травы*/,'image'=>BASE_URL.'/modules/Product/img/review/herbs/medicinal.png'),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Aromatic herbs - Field Grasses','Field Grasses')/*Полевые травы*/,'image'=>BASE_URL.'/modules/Product/img/review/herbs/field-grasses.png'),
                            array('value'=>6,'label'=>langTranslate('product','review elements','Aromatic herbs - Fennel','Fennel')/*Фенхель*/,'image'=>BASE_URL.'/modules/Product/img/review/herbs/fennel.png'),
                            array('value'=>7,'label'=>langTranslate('product','review elements','Aromatic herbs - Dill','Dill')/*Укроп*/,'image'=>BASE_URL.'/modules/Product/img/review/herbs/dill.png'),
                            array('value'=>8,'label'=>langTranslate('product','review elements','Aromatic herbs - Thyme','Thyme')/*Тимьян*/,'image'=>BASE_URL.'/modules/Product/img/review/herbs/thyme.png'),
                            array('value'=>9,'label'=>langTranslate('product','review elements','Aromatic herbs - Basil','Basil')/*Базилик*/,'image'=>BASE_URL.'/modules/Product/img/review/herbs/basil.png'),
                            array('value'=>10,'label'=>langTranslate('product','review elements','Aromatic herbs - Rosemary','Rosemary')/*Розмарин*/,'image'=>BASE_URL.'/modules/Product/img/review/herbs/rosemary.png'),
                            array('value'=>11,'label'=>langTranslate('product','review elements','Aromatic herbs - Pine','Pine')/*Хвоя*/,'image'=>BASE_URL.'/modules/Product/img/review/herbs/pine.png'),
                            array('value'=>12,'label'=>langTranslate('product','review elements','Aromatic herbs - Herbs for Pickling','Herbs for Pickling')/*Травы для засолки*/,'image'=>BASE_URL.'/modules/Product/img/review/herbs/herbs-for-pickling.png'),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Citrus fruits','Citrus fruits'),//Цитрусы
                    'name'=>'primary-flavor-citrus',
                    'autofill_from'=>'primary-aroma-citrus',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Aroma - None','None')/*Нет*/,'default'=>true),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Aroma - Hints','Hints')/*Тонкий штрих*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Aroma - Medium (-)','Medium (-)')/*Немного*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Aroma - Medium','Medium')/*Средне*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Aroma - Medium (+)','Medium (+)')/*Заметно*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Aroma - Pronounced','Pronounced')/*Ярко*/),
                        )
                ),
            array(
                    'subelement-of'=>'primary-flavor-citrus',
                    'name'=>'primary-flavor-citrus-type',
					'aroma'=>true,
                    'autofill_from'=>'primary-aroma-citrus-type',
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'multichoice'=>true,
                    'optional'=>true,
                    'hide_empty_from_merge'=>true,
                    'order_by_total_count_in_merge'=>true,
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Citrus fruits - Lime','Lime')/*Лайм*/,'image'=>BASE_URL.'/modules/Product/img/review/citrus/lime.png'),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Citrus fruits - Lemon','Lemon')/*Лимон*/,'image'=>BASE_URL.'/modules/Product/img/review/citrus/lemon.png'),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Citrus fruits - Grapefruit','Grapefruit')/*Грейпфрут*/,'image'=>BASE_URL.'/modules/Product/img/review/citrus/grapefruit.png'),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Citrus fruits - Orange peel','Orange peel')/*Цедра апельсина*/,'image'=>BASE_URL.'/modules/Product/img/review/citrus/orange-peel.png'),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Citrus fruits - Lemon peel','Lemon peel')/*Цедра лимона*/,'image'=>BASE_URL.'/modules/Product/img/review/citrus/lemon-peel.png'),
                            array('value'=>6,'label'=>langTranslate('product','review elements','Citrus fruits - Pomelo','Pomelo')/*Помело*/,'image'=>BASE_URL.'/modules/Product/img/review/citrus/pomelo.png'),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','White/green fruits/berries','White/green fruits/berries'),//Белые/зеленые ягоды/фрукты
                    'name'=>'primary-flavor-white-fruit',
                    'autofill_from'=>'primary-aroma-white-fruit',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Aroma - None','None')/*Нет*/,'default'=>true),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Aroma - Hints','Hints')/*Тонкий штрих*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Aroma - Medium (-)','Medium (-)')/*Немного*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Aroma - Medium','Medium')/*Средне*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Aroma - Medium (+)','Medium (+)')/*Заметно*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Aroma - Pronounced','Pronounced')/*Ярко*/),
                        )
                ),
            array(
                    'subelement-of'=>'primary-flavor-white-fruit',
                    'name'=>'primary-flavor-white-fruit-type',
					'aroma'=>true,
                    'autofill_from'=>'primary-aroma-white-fruit-type',
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'multichoice'=>true,
                    'optional'=>true,
                    'hide_empty_from_merge'=>true,
                    'order_by_total_count_in_merge'=>true,
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','White/green fruits/berries - White currant','White currant')/*Белая смородина*/,'image'=>BASE_URL.'/modules/Product/img/review/white-fruit/white-currant.png'),
                            array('value'=>2,'label'=>langTranslate('product','review elements','White/green fruits/berries - Gooseberry','Gooseberry')/*Крыжовник*/,'image'=>BASE_URL.'/modules/Product/img/review/white-fruit/gooseberry.png'),
                            array('value'=>3,'label'=>langTranslate('product','review elements','White/green fruits/berries - Grape','Grape')/*Столовый виноград*/,'image'=>BASE_URL.'/modules/Product/img/review/white-fruit/grape.png'),
                            array('value'=>4,'label'=>langTranslate('product','review elements','White/green fruits/berries - Apple','Apple')/*Яблоко*/,'image'=>BASE_URL.'/modules/Product/img/review/white-fruit/apple.png'),
                            array('value'=>5,'label'=>langTranslate('product','review elements','White/green fruits/berries - Pear','Pear')/*Груша*/,'image'=>BASE_URL.'/modules/Product/img/review/white-fruit/pear.png'),
                            array('value'=>6,'label'=>langTranslate('product','review elements','White/green fruits/berries - Quince','Quince')/*Айва*/,'image'=>BASE_URL.'/modules/Product/img/review/white-fruit/quince.png'),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Red fruits/berries','Red fruits/berries'),//Красные ягоды/фрукты
                    'name'=>'primary-flavor-red-fruit',
                    'autofill_from'=>'primary-aroma-red-fruit',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Aroma - None','None')/*Нет*/,'default'=>true),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Aroma - Hints','Hints')/*Тонкий штрих*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Aroma - Medium (-)','Medium (-)')/*Немного*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Aroma - Medium','Medium')/*Средне*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Aroma - Medium (+)','Medium (+)')/*Заметно*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Aroma - Pronounced','Pronounced')/*Ярко*/),
                        )
                ),
            array(
                    'subelement-of'=>'primary-flavor-red-fruit',
                    'name'=>'primary-flavor-red-fruit-type',
					'aroma'=>true,
                    'autofill_from'=>'primary-aroma-red-fruit-type',
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'multichoice'=>true,
                    'optional'=>true,
                    'hide_empty_from_merge'=>true,
                    'order_by_total_count_in_merge'=>true,
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Red fruits/berries - Cranberry','Cranberry')/*Клюква*/,'image'=>BASE_URL.'/modules/Product/img/review/red-fruit/cranberry.png'),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Red fruits/berries - Lingonberry','Lingonberry')/*Брусника*/,'image'=>BASE_URL.'/modules/Product/img/review/red-fruit/lingonberry.png'),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Red fruits/berries - Redcurrant','Redcurrant')/*Красная смородина*/,'image'=>BASE_URL.'/modules/Product/img/review/red-fruit/redcurrant.png'),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Red fruits/berries - Raspberry','Raspberry')/*Малина*/,'image'=>BASE_URL.'/modules/Product/img/review/red-fruit/raspberry.png'),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Red fruits/berries - Strawberry','Strawberry')/*Клубника*/,'image'=>BASE_URL.'/modules/Product/img/review/red-fruit/strawberry.png'),
                            array('value'=>6,'label'=>langTranslate('product','review elements','Red fruits/berries - Red cherry','Red cherry')/*Красная вишня*/,'image'=>BASE_URL.'/modules/Product/img/review/red-fruit/red-cherry.png'),
                            array('value'=>7,'label'=>langTranslate('product','review elements','Red fruits/berries - Red plum','Red plum')/*Красная слива*/,'image'=>BASE_URL.'/modules/Product/img/review/red-fruit/red-plum.png'),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Black fruits/berries','Black fruits/berries'),//Черные ягоды/фрукты
                    'name'=>'primary-flavor-black-fruit',
                    'autofill_from'=>'primary-aroma-black-fruit',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Aroma - None','None')/*Нет*/,'default'=>true),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Aroma - Hints','Hints')/*Тонкий штрих*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Aroma - Medium (-)','Medium (-)')/*Немного*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Aroma - Medium','Medium')/*Средне*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Aroma - Medium (+)','Medium (+)')/*Заметно*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Aroma - Pronounced','Pronounced')/*Ярко*/),
                        )
                ),
            array(
                    'subelement-of'=>'primary-flavor-black-fruit',
                    'name'=>'primary-flavor-black-fruit-type',
					'aroma'=>true,
                    'autofill_from'=>'primary-aroma-black-fruit-type',
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'multichoice'=>true,
                    'optional'=>true,
                    'hide_empty_from_merge'=>true,
                    'order_by_total_count_in_merge'=>true,
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Black fruits/berries - Blackcurrant','Blackcurrant')/*Черная смородина*/,'image'=>BASE_URL.'/modules/Product/img/review/black-fruit/blackcurrant.png'),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Black fruits/berries - Blueberry','Blueberry')/*Черника*/,'image'=>BASE_URL.'/modules/Product/img/review/black-fruit/blueberry.png'),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Black fruits/berries - Blackberry','Blackberry')/*Ежевика*/,'image'=>BASE_URL.'/modules/Product/img/review/black-fruit/blackberry.png'),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Black fruits/berries - Black cherry','Black cherry')/*Темная вишня*/,'image'=>BASE_URL.'/modules/Product/img/review/black-fruit/black-cherry.png'),
                            array('value'=>6,'label'=>langTranslate('product','review elements','Black fruits/berries - Black plum','Black plum')/*Черная слива*/,'image'=>BASE_URL.'/modules/Product/img/review/black-fruit/black-plum.png'),
                            array('value'=>7,'label'=>langTranslate('product','review elements','Black fruits/berries - Chokeberry','Chokeberry')/*Черноплодная рябина*/,'image'=>BASE_URL.'/modules/Product/img/review/black-fruit/chokeberry.png'),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Stone fruits','Stone fruits'),//Косточковые фрукты
                    'name'=>'primary-flavor-ossicle-fruit',
                    'autofill_from'=>'primary-aroma-ossicle-fruit',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Aroma - None','None')/*Нет*/,'default'=>true),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Aroma - Hints','Hints')/*Тонкий штрих*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Aroma - Medium (-)','Medium (-)')/*Немного*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Aroma - Medium','Medium')/*Средне*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Aroma - Medium (+)','Medium (+)')/*Заметно*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Aroma - Pronounced','Pronounced')/*Ярко*/),
                        )
                ),
            array(
                    'subelement-of'=>'primary-flavor-ossicle-fruit',
                    'name'=>'primary-flavor-ossicle-fruit-type',
					'aroma'=>true,
                    'autofill_from'=>'primary-aroma-ossicle-fruit-type',
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'multichoice'=>true,
                    'optional'=>true,
                    'hide_empty_from_merge'=>true,
                    'order_by_total_count_in_merge'=>true,
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Stone fruits - Peach','Peach')/*Персик*/,'image'=>BASE_URL.'/modules/Product/img/review/ossicle-fruit/peach.png'),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Stone fruits - Apricot','Apricot')/*Абрикос*/,'image'=>BASE_URL.'/modules/Product/img/review/ossicle-fruit/apricot.png'),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Stone fruits - Nectarine','Nectarine')/*Нектарин*/,'image'=>BASE_URL.'/modules/Product/img/review/ossicle-fruit/nectarine.png'),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Stone fruits - Yellow plum','Yellow plum')/*Желтая слива*/,'image'=>BASE_URL.'/modules/Product/img/review/ossicle-fruit/yellow-plum.png'),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Tropical fruits','Tropical fruits'),//Тропические фрукты
                    'name'=>'primary-flavor-tropical-fruit',
                    'autofill_from'=>'primary-aroma-tropical-fruit',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Aroma - None','None')/*Нет*/,'default'=>true),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Aroma - Hints','Hints')/*Тонкий штрих*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Aroma - Medium (-)','Medium (-)')/*Немного*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Aroma - Medium','Medium')/*Средне*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Aroma - Medium (+)','Medium (+)')/*Заметно*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Aroma - Pronounced','Pronounced')/*Ярко*/),
                        )
                ),
            array(
                    'subelement-of'=>'primary-flavor-tropical-fruit',
                    'name'=>'primary-flavor-tropical-fruit-type',
					'aroma'=>true,
                    'autofill_from'=>'primary-aroma-tropical-fruit-type',
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'multichoice'=>true,
                    'optional'=>true,
                    'hide_empty_from_merge'=>true,
                    'order_by_total_count_in_merge'=>true,
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Tropical fruits - Lychee','Lychee')/*Личи*/,'image'=>BASE_URL.'/modules/Product/img/review/tropical-fruit/lychee.png'),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Tropical fruits - Melon','Melon')/*Дыня*/,'image'=>BASE_URL.'/modules/Product/img/review/tropical-fruit/melon.png'),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Tropical fruits - Banana','Banana')/*Банан*/,'image'=>BASE_URL.'/modules/Product/img/review/tropical-fruit/banana.png'),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Tropical fruits - Pineapple','Pineapple')/*Ананас*/,'image'=>BASE_URL.'/modules/Product/img/review/tropical-fruit/pineapple.png'),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Tropical fruits - Mango','Mango')/*Манго*/,'image'=>BASE_URL.'/modules/Product/img/review/tropical-fruit/mango.png'),
                            array('value'=>6,'label'=>langTranslate('product','review elements','Tropical fruits - Passion fruit','Passion fruit')/*Маракуйя*/,'image'=>BASE_URL.'/modules/Product/img/review/tropical-fruit/passion-fruit.png'),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Mineral tones','Mineral tones'),//Минеральные тона
                    'name'=>'primary-flavor-mineral',
                    'autofill_from'=>'primary-aroma-mineral',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Aroma - None','None')/*Нет*/,'default'=>true),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Aroma - Hints','Hints')/*Тонкий штрих*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Aroma - Medium (-)','Medium (-)')/*Немного*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Aroma - Medium','Medium')/*Средне*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Aroma - Medium (+)','Medium (+)')/*Заметно*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Aroma - Pronounced','Pronounced')/*Ярко*/),
                        )
                ),
            array(
                    'subelement-of'=>'primary-flavor-mineral',
                    'name'=>'primary-flavor-mineral-type',
					'aroma'=>true,
                    'autofill_from'=>'primary-aroma-mineral-type',
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'multichoice'=>true,
                    'optional'=>true,
                    'hide_empty_from_merge'=>true,
                    'order_by_total_count_in_merge'=>true,
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Mineral tones - Flint stone','Flint stone')/*Кремень*/,'image'=>BASE_URL.'/modules/Product/img/review/mineral/flint-stone.png'),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Mineral tones - Limestone, shells','Limestone, shells')/*Известняк, ракушки*/,'image'=>BASE_URL.'/modules/Product/img/review/mineral/limestone-shells.png'),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Mineral tones - Wet stones','Wet stones')/*Мокрые камни*/,'image'=>BASE_URL.'/modules/Product/img/review/mineral/wet-stones.png'),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Mineral tones - Steely','Steely')/*Металлический тон*/,'image'=>BASE_URL.'/modules/Product/img/review/mineral/steely.png'),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Mineral tones - Salty tone','Salty tone')/*Соленый тон*/,'image'=>BASE_URL.'/modules/Product/img/review/mineral/salty-tone.png'),
                        )
                ),
        ),
    'secondary_flavor_elements'=>array(
            array(
                    'caption'=>langTranslate('product','review elements','Yeast','Yeast'),//Дрожжевые
                    'name'=>'secondary-flavor-yeast',
                    'autofill_from'=>'secondary-aroma-yeast',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Aroma - None','None')/*Нет*/,'default'=>true),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Aroma - Hints','Hints')/*Тонкий штрих*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Aroma - Medium (-)','Medium (-)')/*Немного*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Aroma - Medium','Medium')/*Средне*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Aroma - Medium (+)','Medium (+)')/*Заметно*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Aroma - Pronounced','Pronounced')/*Ярко*/),
                        )
                ),
            array(
                    'subelement-of'=>'secondary-flavor-yeast',
                    'name'=>'secondary-flavor-yeast-type',
					'aroma'=>true,
                    'autofill_from'=>'secondary-aroma-yeast-type',
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'multichoice'=>true,
                    'optional'=>true,
                    'hide_empty_from_merge'=>true,
                    'order_by_total_count_in_merge'=>true,
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Yeast - Bread','Bread')/*Хлеб*/,'image'=>BASE_URL.'/modules/Product/img/review/yeast/bread.png'),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Yeast - Pastry','Pastry')/*Сухари*/,'image'=>BASE_URL.'/modules/Product/img/review/yeast/pastry.png'),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Yeast - Bread dough','Bread dough')/*Тесто*/,'image'=>BASE_URL.'/modules/Product/img/review/yeast/bread-dough.png'),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Yeast - Yeast','Yeast')/*Дрожжевой тон*/,'image'=>BASE_URL.'/modules/Product/img/review/yeast/yeast.png'),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','MLF','MLF'),//Молочные
                    'name'=>'secondary-flavor-milk',
                    'autofill_from'=>'secondary-aroma-milk',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Aroma - None','None')/*Нет*/,'default'=>true),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Aroma - Hints','Hints')/*Тонкий штрих*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Aroma - Medium (-)','Medium (-)')/*Немного*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Aroma - Medium','Medium')/*Средне*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Aroma - Medium (+)','Medium (+)')/*Заметно*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Aroma - Pronounced','Pronounced')/*Ярко*/),
                        )
                ),
            array(
                    'subelement-of'=>'secondary-flavor-milk',
                    'name'=>'secondary-flavor-milk-type',
					'aroma'=>true,
                    'autofill_from'=>'secondary-aroma-milk-type',
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'multichoice'=>true,
                    'optional'=>true,
                    'hide_empty_from_merge'=>true,
                    'order_by_total_count_in_merge'=>true,
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','MLF - Butter','Butter')/*Сливочное масло*/,'image'=>BASE_URL.'/modules/Product/img/review/milk/butter.png'),
                            array('value'=>2,'label'=>langTranslate('product','review elements','MLF - Toffee','Toffee')/*Сливочный ирис*/,'image'=>BASE_URL.'/modules/Product/img/review/milk/toffee.png'),
                            array('value'=>3,'label'=>langTranslate('product','review elements','MLF - Cream','Cream')/*Сливки*/,'image'=>BASE_URL.'/modules/Product/img/review/milk/cream.png'),
                            array('value'=>4,'label'=>langTranslate('product','review elements','MLF - Yoghurt','Yoghurt')/*Йогурт*/,'image'=>BASE_URL.'/modules/Product/img/review/milk/yoghurt.png'),
                            array('value'=>5,'label'=>langTranslate('product','review elements','MLF - Cheese','Cheese')/*Сыр*/,'image'=>BASE_URL.'/modules/Product/img/review/milk/cheese.png'),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Hot spices','Hot spices'),//Острые пряности
                    'name'=>'secondary-flavor-spice',
                    'autofill_from'=>'secondary-aroma-spice',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Aroma - None','None')/*Нет*/,'default'=>true),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Aroma - Hints','Hints')/*Тонкий штрих*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Aroma - Medium (-)','Medium (-)')/*Немного*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Aroma - Medium','Medium')/*Средне*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Aroma - Medium (+)','Medium (+)')/*Заметно*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Aroma - Pronounced','Pronounced')/*Ярко*/),
                        )
                ),
            array(
                    'subelement-of'=>'secondary-flavor-spice',
                    'name'=>'secondary-flavor-spice-type',
					'aroma'=>true,
                    'autofill_from'=>'secondary-aroma-spice-type',
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'multichoice'=>true,
                    'optional'=>true,
                    'hide_empty_from_merge'=>true,
                    'order_by_total_count_in_merge'=>true,
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Hot spices - White pepper','White pepper')/*Белый перец*/,'image'=>BASE_URL.'/modules/Product/img/review/spice/white-pepper.png'),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Hot spices - Black pepper','Black pepper')/*Черный перец*/,'image'=>BASE_URL.'/modules/Product/img/review/spice/black-pepper.png'),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Hot spices - Ginger','Ginger')/*Имбирь*/,'image'=>BASE_URL.'/modules/Product/img/review/spice/ginger.png'),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Sweet spices','Sweet spices'),//Кондитерские пряности
                    'name'=>'secondary-flavor-confectionery',
                    'autofill_from'=>'secondary-aroma-confectionery',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Aroma - None','None')/*Нет*/,'default'=>true),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Aroma - Hints','Hints')/*Тонкий штрих*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Aroma - Medium (-)','Medium (-)')/*Немного*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Aroma - Medium','Medium')/*Средне*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Aroma - Medium (+)','Medium (+)')/*Заметно*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Aroma - Pronounced','Pronounced')/*Ярко*/),
                        )
                ),
            array(
                    'subelement-of'=>'secondary-flavor-confectionery',
                    'name'=>'secondary-flavor-confectionery-type',
					'aroma'=>true,
                    'autofill_from'=>'secondary-aroma-confectionery-type',
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'multichoice'=>true,
                    'optional'=>true,
                    'hide_empty_from_merge'=>true,
                    'order_by_total_count_in_merge'=>true,
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Sweet spices - Vanilla','Vanilla')/*Ваниль*/,'image'=>BASE_URL.'/modules/Product/img/review/confectionery/vanilla.png'),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Sweet spices - Cinnamon','Cinnamon')/*Корица*/,'image'=>BASE_URL.'/modules/Product/img/review/confectionery/cinnamon.png'),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Sweet spices - Cloves','Cloves')/*Гвоздика*/,'image'=>BASE_URL.'/modules/Product/img/review/confectionery/cloves.png'),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Sweet spices - Nutmeg','Nutmeg')/*Мускатный орех*/,'image'=>BASE_URL.'/modules/Product/img/review/confectionery/nutmeg.png'),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Sweet spices - Coconut','Coconut')/*Кокос*/,'image'=>BASE_URL.'/modules/Product/img/review/confectionery/coconut.png'),
                            array('value'=>6,'label'=>langTranslate('product','review elements','Sweet spices - Licorice','Licorice')/*Лакрица*/,'image'=>BASE_URL.'/modules/Product/img/review/confectionery/licorice.png'),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Empyreumatic','Empyreumatic'),//Эмпиреоматические
                    'name'=>'secondary-flavor-empirical',
                    'autofill_from'=>'secondary-aroma-empirical',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Aroma - None','None')/*Нет*/,'default'=>true),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Aroma - Hints','Hints')/*Тонкий штрих*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Aroma - Medium (-)','Medium (-)')/*Немного*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Aroma - Medium','Medium')/*Средне*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Aroma - Medium (+)','Medium (+)')/*Заметно*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Aroma - Pronounced','Pronounced')/*Ярко*/),
                        )
                ),
            array(
                    'subelement-of'=>'secondary-flavor-empirical',
                    'name'=>'secondary-flavor-empirical-type',
					'aroma'=>true,
                    'autofill_from'=>'secondary-aroma-empirical-type',
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'multichoice'=>true,
                    'optional'=>true,
                    'hide_empty_from_merge'=>true,
                    'order_by_total_count_in_merge'=>true,
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Empyreumatic - Toasts','Toasts')/*Тосты*/,'image'=>BASE_URL.'/modules/Product/img/review/empirical/toasts.png'),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Empyreumatic - Smoke','Smoke')/*Дым*/,'image'=>BASE_URL.'/modules/Product/img/review/empirical/smoke.png'),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Empyreumatic - Coffee','Coffee')/*Кофе*/,'image'=>BASE_URL.'/modules/Product/img/review/empirical/coffee.png'),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Empyreumatic - Chocolate','Chocolate')/*Шоколад*/,'image'=>BASE_URL.'/modules/Product/img/review/empirical/chocolate.png'),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Empyreumatic - Burnt sugar','Burnt sugar')/*Жженый сахар*/,'image'=>BASE_URL.'/modules/Product/img/review/empirical/burnt-sugar.png'),
                            array('value'=>6,'label'=>langTranslate('product','review elements','Empyreumatic - Grilled grass','Grilled grass')/*Травы на гриле*/,'image'=>BASE_URL.'/modules/Product/img/review/empirical/grilled-grass.png'),
                        )
                ),
        ),
    'tertiary_flavor_elements'=>array(
            array(
                    'caption'=>langTranslate('product','review elements','Dried fruits','Dried fruits'),//Сухофрукты
                    'name'=>'tertiary-flavor-dried-fruit',
                    'autofill_from'=>'tertiary-aroma-dried-fruit',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Aroma - None','None')/*Нет*/,'default'=>true),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Aroma - Hints','Hints')/*Тонкий штрих*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Aroma - Medium (-)','Medium (-)')/*Немного*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Aroma - Medium','Medium')/*Средне*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Aroma - Medium (+)','Medium (+)')/*Заметно*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Aroma - Pronounced','Pronounced')/*Ярко*/),
                        )
                ),
            array(
                    'subelement-of'=>'tertiary-flavor-dried-fruit',
                    'name'=>'tertiary-flavor-dried-fruit-type',
					'aroma'=>true,
                    'autofill_from'=>'tertiary-aroma-dried-fruit-type',
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'multichoice'=>true,
                    'optional'=>true,
                    'hide_empty_from_merge'=>true,
                    'order_by_total_count_in_merge'=>true,
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Dried fruits - Dried apricots','Dried apricots')/*Курага*/,'image'=>BASE_URL.'/modules/Product/img/review/dried-fruit/dried-apricots.png'),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Dried fruits - Raisins','Raisins')/*Изюм*/,'image'=>BASE_URL.'/modules/Product/img/review/dried-fruit/raisins.png'),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Dried fruits - Prunes','Prunes')/*Чернослив*/,'image'=>BASE_URL.'/modules/Product/img/review/dried-fruit/prunes.png'),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Dried fruits - Figs','Figs')/*Инжир*/,'image'=>BASE_URL.'/modules/Product/img/review/dried-fruit/figs.png'),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Dried fruits - Dates','Dates')/*Финики*/,'image'=>BASE_URL.'/modules/Product/img/review/dried-fruit/dates.png'),
                            array('value'=>6,'label'=>langTranslate('product','review elements','Dried fruits - Dried apples','Dried apples')/*Сушеные яблоки*/,'image'=>BASE_URL.'/modules/Product/img/review/dried-fruit/dried-apples.png'),
                            array('value'=>7,'label'=>langTranslate('product','review elements','Dried fruits - Dried pears','Dried pears')/*Сушеные груши*/,'image'=>BASE_URL.'/modules/Product/img/review/dried-fruit/dried-pears.png'),
                            array('value'=>8,'label'=>langTranslate('product','review elements','Dried fruits - Dried peaches','Dried peaches')/*Сушеные персики*/,'image'=>BASE_URL.'/modules/Product/img/review/dried-fruit/dried-peaches.png'),
                            array('value'=>9,'label'=>langTranslate('product','review elements','Dried fruits - Dried bananas','Dried bananas')/*Сушеные бананы*/,'image'=>BASE_URL.'/modules/Product/img/review/dried-fruit/dried-bananas.png'),
                            array('value'=>10,'label'=>langTranslate('product','review elements','Dried fruits - Candied fruits','Candied fruits')/*Цукаты*/,'image'=>BASE_URL.'/modules/Product/img/review/dried-fruit/candied-fruits.png'),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Nuts','Nuts'),//Орехи
                    'name'=>'tertiary-flavor-nuts',
                    'autofill_from'=>'tertiary-aroma-nuts',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Aroma - None','None')/*Нет*/,'default'=>true),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Aroma - Hints','Hints')/*Тонкий штрих*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Aroma - Medium (-)','Medium (-)')/*Немного*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Aroma - Medium','Medium')/*Средне*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Aroma - Medium (+)','Medium (+)')/*Заметно*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Aroma - Pronounced','Pronounced')/*Ярко*/),
                        )
                ),
            array(
                    'subelement-of'=>'tertiary-flavor-nuts',
                    'name'=>'tertiary-flavor-nuts-type',
					'aroma'=>true,
                    'autofill_from'=>'tertiary-aroma-nuts-type',
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'multichoice'=>true,
                    'optional'=>true,
                    'hide_empty_from_merge'=>true,
                    'order_by_total_count_in_merge'=>true,
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Nuts - Almond','Almond')/*Миндаль*/,'image'=>BASE_URL.'/modules/Product/img/review/nuts/almond.png'),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Nuts - Marzipan','Marzipan')/*Марципан*/,'image'=>BASE_URL.'/modules/Product/img/review/nuts/marzipan.png'),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Nuts - Walnut','Walnut')/*Грецкий орех*/,'image'=>BASE_URL.'/modules/Product/img/review/nuts/walnut.png'),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Nuts - Hazelnut','Hazelnut')/*Фундук*/,'image'=>BASE_URL.'/modules/Product/img/review/nuts/hazelnut.png'),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Confectionery tones','Confectionery tones'),//Кондитерские тона
                    'name'=>'tertiary-flavor-confection',
                    'autofill_from'=>'tertiary-aroma-confection',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Aroma - None','None')/*Нет*/,'default'=>true),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Aroma - Hints','Hints')/*Тонкий штрих*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Aroma - Medium (-)','Medium (-)')/*Немного*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Aroma - Medium','Medium')/*Средне*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Aroma - Medium (+)','Medium (+)')/*Заметно*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Aroma - Pronounced','Pronounced')/*Ярко*/),
                        )
                ),
            array(
                    'subelement-of'=>'tertiary-flavor-confection',
                    'name'=>'tertiary-flavor-confection-type',
					'aroma'=>true,
                    'autofill_from'=>'tertiary-aroma-confection-type',
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'multichoice'=>true,
                    'optional'=>true,
                    'hide_empty_from_merge'=>true,
                    'order_by_total_count_in_merge'=>true,
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Confectionery tones - Honey','Honey')/*Мед*/,'image'=>BASE_URL.'/modules/Product/img/review/confection/honey.png'),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Confectionery tones - Marmalade','Marmalade')/*Мармелад*/,'image'=>BASE_URL.'/modules/Product/img/review/confection/marmalade.png'),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Confectionery tones - Caramel','Caramel')/*Карамель*/,'image'=>BASE_URL.'/modules/Product/img/review/confection/caramel.png'),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Confectionery tones - Confiture','Confiture')/*Конфитюр*/,'image'=>BASE_URL.'/modules/Product/img/review/confection/confiture.png'),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Confectionery tones - Brioche','Brioche')/*Бриошь*/,'image'=>BASE_URL.'/modules/Product/img/review/confection/brioche.png'),
                            array('value'=>6,'label'=>langTranslate('product','review elements','Confectionery tones - Biscuit','Biscuit')/*Бисквит*/,'image'=>BASE_URL.'/modules/Product/img/review/confection/biscuit.png'),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Other notes','Other notes'),//Тона выдержки
                    'name'=>'tertiary-flavor-aging',
                    'autofill_from'=>'tertiary-aroma-aging',
                    'automatic-evaluation'=>true,
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Aroma - None','None')/*Нет*/,'default'=>true),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Aroma - Hints','Hints')/*Тонкий штрих*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Aroma - Medium (-)','Medium (-)')/*Немного*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Aroma - Medium','Medium')/*Средне*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Aroma - Medium (+)','Medium (+)')/*Заметно*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Aroma - Pronounced','Pronounced')/*Ярко*/),
                        )
                ),
            array(
                    'subelement-of'=>'tertiary-flavor-aging',
                    'name'=>'tertiary-flavor-aging-type',
					'aroma'=>true,
                    'autofill_from'=>'tertiary-aroma-aging-type',
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'multichoice'=>true,
                    'optional'=>true,
                    'hide_empty_from_merge'=>true,
                    'order_by_total_count_in_merge'=>true,
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Other notes - Petrol','Petrol')/*Нефтяной тон*/,'image'=>BASE_URL.'/modules/Product/img/review/aging/petrol.png'),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Other notes - Mushrooms','Mushrooms')/*Грибы*/,'image'=>BASE_URL.'/modules/Product/img/review/aging/mushrooms.png'),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Other notes - Wet Wool','Wet Wool')/*Шерсть*/,'image'=>BASE_URL.'/modules/Product/img/review/aging/wet-wool.png'),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Other notes - Hay','Hay')/*Сено*/,'image'=>BASE_URL.'/modules/Product/img/review/aging/hay.png'),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Other notes - Game','Game')/*Дичь*/,'image'=>BASE_URL.'/modules/Product/img/review/aging/game.png'),
                            array('value'=>6,'label'=>langTranslate('product','review elements','Other notes - Cured meat','Cured meat')/*Вяленое мясо*/,'image'=>BASE_URL.'/modules/Product/img/review/aging/cured-meat.png'),
                            array('value'=>7,'label'=>langTranslate('product','review elements','Other notes - Leather','Leather')/*Кожа*/,'image'=>BASE_URL.'/modules/Product/img/review/aging/leather.png'),
                            array('value'=>8,'label'=>langTranslate('product','review elements','Other notes - Olives','Olives')/*Оливки*/,'image'=>BASE_URL.'/modules/Product/img/review/aging/olives.png'),
                            array('value'=>9,'label'=>langTranslate('product','review elements','Other notes - Truffle','Truffle')/*Трюфель*/,'image'=>BASE_URL.'/modules/Product/img/review/aging/truffle.png'),
                            array('value'=>10,'label'=>langTranslate('product','review elements','Other notes - Soil','Soil')/*Земля*/,'image'=>BASE_URL.'/modules/Product/img/review/aging/soil.png'),
                            array('value'=>11,'label'=>langTranslate('product','review elements','Other notes - Wet leaves','Wet leaves')/*Мокрая листва*/,'image'=>BASE_URL.'/modules/Product/img/review/aging/wet-leaves.png'),
                            array('value'=>12,'label'=>langTranslate('product','review elements','Other notes - Forest floor','Forest floor')/*Подлесок*/,'image'=>BASE_URL.'/modules/Product/img/review/aging/forest-floor.png'),
                            array('value'=>13,'label'=>langTranslate('product','review elements','Other notes - Cedar','Cedar')/*Кедр*/,'image'=>BASE_URL.'/modules/Product/img/review/aging/cedar.png'),
                            array('value'=>14,'label'=>langTranslate('product','review elements','Other notes - Cigar box','Cigar box')/*Сигарная коробка*/,'image'=>BASE_URL.'/modules/Product/img/review/aging/cigar-box.png'),
                            array('value'=>15,'label'=>langTranslate('product','review elements','Other notes - Farmyard','Farmyard')/*Скотный двор*/,'image'=>BASE_URL.'/modules/Product/img/review/aging/farmyard.png'),


                        )
                ),
        ),
    'similarity_age_elements'=>array(
            array(
                    'caption'=>langTranslate('product','review elements','Age','Age'),//Возраст
                    'name'=>'similarity-age',
                    'manual-evaluation'=>true,
                    'list'=>'cols',
                    'values'=>array(
                            array('value'=>1,'label'=>langTranslate('product','review elements','Age - Too young','Too young')/*Незрелое*/),
                            array('value'=>2,'label'=>langTranslate('product','review elements','Age - Young','Young')/*Молодое*/),
                            array('value'=>3,'label'=>langTranslate('product','review elements','Age - Ready','Ready')/*Готовое*/),
                            array('value'=>4,'label'=>langTranslate('product','review elements','Age - Mature','Mature')/*Зрелое*/),
                            array('value'=>5,'label'=>langTranslate('product','review elements','Age - Too old','Too old')/*Старое*/),
                        )
                ),
        ),
    'recommendation_elements'=>array(
            array(
                    'caption'=>langTranslate('product','review elements','Sufficiency','Sufficiency'),//Самодостаточное
                    'name'=>'self-sufficiency',
                    'list'=>'cols',
                    'optional'=>true,
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Bool - No','No')/*Нет*/),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Bool - Yes','Yes')/*Да*/),
                        )
                ),
            array(
                    'caption'=>langTranslate('product','review elements','Gastronomy','Gastronomy'),//Гастрономическое
                    'name'=>'gastronomic',
                    'list'=>'cols',
                    'optional'=>true,
                    'values'=>array(
                            array('value'=>0,'label'=>langTranslate('product','review elements','Bool - No','No')/*Нет*/),
                            array('value'=>1,'label'=>langTranslate('product','review elements','Bool - Yes','Yes')/*Да*/),
                        )
                ),
            // array(
            //         'subelement-of'=>'gastronomic',
            //         'name'=>'gastronomic-recommendation',
            //         'list'=>'cols',
            //         'multichoice'=>true,
            //         'optional'=>true,
            //         'hide_empty_from_merge'=>true,
            //         'order_by_total_count_in_merge'=>true,
            //         'values'=>array(
            //                 array('value'=>1,'label'=>langTranslate('product','review elements','Gastronomy - Seafood','Seafood')/*Морепродукты*/),
            //                 array('value'=>2,'label'=>langTranslate('product','review elements','Gastronomy - Soft cheeses','Soft cheeses')/*Мягкие сыры*/),
            //                 array('value'=>3,'label'=>langTranslate('product','review elements','Gastronomy - Hard cheeses','Hard cheeses')/*Твердые сыры*/),
            //                 array('value'=>4,'label'=>langTranslate('product','review elements','Gastronomy - Meat','Meat')/*Мясо*/),
            //                 array('value'=>5,'label'=>langTranslate('product','review elements','Gastronomy - Poultry','Poultry')/*Птица*/),
            //             )
            //     ),
        ),

);