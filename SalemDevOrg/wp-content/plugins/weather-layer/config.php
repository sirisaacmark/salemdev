<?php
/**
	@Author Morgan Fabre
	Configuration par défaut de Weather Layer
*/

define ('LANGUAGE', 'en'); // Langue par défaut
define ('DEGREES', 'c'); // Unités par défaut des degrés 
define ('WINDSPEED', 'km/h'); // Unités par défaut de la vitesse du vent
define ('LAYER_TITLE_FORMAT', '%city% (%country%)'); // Formatage par défaut du titre du layer
define ('IMAGES_PATH', get_bloginfo('wpurl') . '/wp-content/plugins/weather-layer/icones/'); // Répertoire des images
define ('CACHE_DURATION', 14440); // 4 heures de cache
define ('NB_DAYS', 2); // Prévisions sur 2 jours max
define ('COLD_CLASS', 'coldDegrees');
define ('TEMPERATE_CLASS', 'temperateDegrees');
define ('HOT_CLASS', 'hotDegrees');
define ('OPENWEATHERMAP_APP_ID', '4956869188e90d5a8da462c16eb9e278');
define ('OPENWEATHERMAP_NB_HOURS_PER_DAY', 8);

$WL_degrees = array
(
	'Celsius'		=> 'c',
	'Fahrenheit'	=> 'f'
);

$WL_windspeed = array
(
	'km/h'		=>	'km/h',
	'm/s'		=>	'm/s',
	'Knots'		=>	'knots'
);

$WL_displays = array
(
	'horizontal',
	'vertical'
);

// Langues & mots traduits
$WL_languages = array
(
	'fr'	=>	array
	(
		'Langue'		=>	'Français',
		'Realtime'		=>	'En ce moment',
		'Today'			=>	'Aujourd\'hui',
		'Tomorrow'		=>	'Demain',
		'Day1'			=>	'Lundi',
		'Day2'			=>	'Mardi',
		'Day3'			=>	'Mercredi',
		'Day4'			=>	'Jeudi',
		'Day5'			=>	'Vendredi',
		'Day6'			=>	'Samedi',
		'Day0'			=>	'Dimanche',
		'Wind'			=>	'Vent',
		'Humidity'		=>	'Humidité',
		'Soleil'		=>	'Temps clair',
		'Soleil-partiel'=>	'Soleil partiel',
		'Pluie-neige'	=>	'Pluie neigeuse',
		'Undefined'		=>	'Indéterminé',
		'WhatsNew'		=>	'A découvrir',
		'Trends'		=>	'Les nouveautés'
	),
	
	'en'	=>	array
	(
		'Langue'		=>	'English',
		'Realtime'		=>	'Real time',
		'Day1'			=>	'Monday',
		'Day2'			=>	'Tuesday',
		'Day3'			=>	'Wednesday',
		'Day4'			=>	'Thursday',
		'Day5'			=>	'Friday',
		'Day6'			=>	'Saturday',
		'Day0'			=>	'Sunday',
		'Soleil'		=>	'Sunny',
		'Soleil-partiel'=>	'Partly sunny',
		'Nuages'		=>	'Cloudy',
		'Brouillard'	=>	'Fog',
		'Pluie'			=>	'Rainy',
		'Pluie-neige'	=>	'Sleet',
		'Neige'			=>	'Snowy',
		'Orages'		=>	'Thunderstorms',
		'WhatsNew'		=>	'What\'s new',
		'Trends'		=>	'Trends'
	),
	
	'de'	=>	array
	(
		'Langue'		=>	'Deutsche',
		'Realtime'		=>	'Echtzeit',
		'Today'			=>	'Heute',
		'Day1'			=>	'Montag',
		'Day2'			=>	'Dienstag',
		'Day3'			=>	'Mittwoch',
		'Day4'			=>	'Donnerstag',
		'Day5'			=>	'Freitag',
		'Day6'			=>	'Samstag',
		'Day0'			=>	'Sonntag',
		'Humidity'		=>	'Feuchtigkeit',
		'Soleil'		=>	'Löschen',
		'Soleil-partiel'=>	'Teilweise sonne',
		'Nuages'		=>	'Wolken',
		'Brouillard'	=>	'Nebel',
		'Pluie'			=>	'Regen',
		'Pluie-neige'	=>	'Schneeregen',
		'Neige'			=>	'Schnee',
		'Orages'		=>	'Gewitter',
		'Undefined'		=>	'Unbestimmt',
		'WhatsNew'		=>	'Zu entdecken',
		'Trends'		=>	'Trends'
	),
	
	'es'	=>	array
	(
		'Langue'		=>	'Español',
		'Realtime'		=>	'Tiempo real',
		'Today'			=>	'Hoy',
		'Tomorrow'		=>	'Mañana',
		'Day1'			=>	'Lunes',
		'Day2'			=>	'Mardes',
		'Day3'			=>	'Miércoles',
		'Day4'			=>	'Jueves',
		'Day5'			=>	'Viernes',
		'Day6'			=>	'Sábado',
		'Day0'			=>	'Domingo',
		'Wind'			=>	'Viento',
		'Humidity'		=>	'Humedad',
		'Soleil'		=>	'Borrar',
		'Soleil-partiel'=>	'Parcial dom',
		'Nuages'		=>	'Nubes',
		'Brouillard'	=>	'Niebla',
		'Pluie'			=>	'Lluvia',
		'Pluie-neige'	=>	'Aguanieve',
		'Neige'			=>	'Nieve',
		'Orages'		=>	'Tormentas',
		'Undefined'		=>	'Indeterminado',
		'WhatsNew'		=>	'Descubre',
		'Trends'		=>	'Nuevo'
	),
	
	'it'	=>	array
	(
		'Langue'		=>	'Italiano',
		'Realtime'		=>	'Tempo reale',
		'Today'			=>	'Oggi',
		'Tomorrow'		=>	'Domani',
		'Day1'			=>	'Lunedi',
		'Day2'			=>	'Martedì',
		'Day3'			=>	'Mercoledì',
		'Day4'			=>	'Giovedi',
		'Day5'			=>	'Venerdì',
		'Day6'			=>	'Sabato',
		'Day0'			=>	'Domenica',
		'Wind'			=>	'Vento',
		'Humidity'		=>	'Umidità',
		'Soleil'		=>	'Cancella',
		'Soleil-partiel'=>	'Parziale dom',
		'Nuages'		=>	'Nuvole',
		'Brouillard'	=>	'Nebbia',
		'Pluie'			=>	'Pioggia',
		'Pluie-neige'	=>	'Nevischio',
		'Neige'			=>	'Neve',
		'Orages'		=>	'Temporali',
		'Undefined'		=>	'Indeterminato',
		'WhatsNew'		=>	'Scoprire',
		'Trends'		=>	'Nuovo'
	),
	
	'ru'	=>	array
	(
		'Langue'		=>	'русский',
		'Realtime'		=>	'реальное время',
		'Today'			=>	'сегодня',
		'Tomorrow'		=>	'завтра',
		'Day1'			=>	'понедельник',
		'Day2'			=>	'вторник',
		'Day3'			=>	'среда',
		'Day4'			=>	'четверг',
		'Day5'			=>	'пятница',
		'Day6'			=>	'суббота',
		'Day0'			=>	'воскресенье',
		'Wind'			=>	'ветер',
		'Humidity'		=>	'влажность',
		'Soleil'		=>	'Очистить',
		'Soleil-partiel'=>	'частичное солнце',
		'Nuages'		=>	'Облака',
		'Brouillard'	=>	'туман',
		'Pluie'			=>	'дождь',
		'Pluie-neige'	=>	'дождь со снегом',
		'Neige'			=>	'снег',
		'Orages'		=>	'Грозы',
		'Undefined'		=>	'неопределенный',
		'WhatsNew'		=>	'открыть для себя',
		'Trends'		=>	'что нового'
	),		'jp'	=>	array	(		'Langue'		=>	'日本語',		'Realtime'		=>	'今',		'Today'			=>	'今日',		'Tomorrow'		=>	'明日',		'Day1'			=>	'月曜日',		'Day2'			=>	'火曜日',		'Day3'			=>	'水曜日',		'Day4'			=>	'木曜日',		'Day5'			=>	'金曜日',		'Day6'			=>	'土曜日',		'Day0'			=>	'日曜日',		'Wind'			=>	'風',		'Humidity'		=>	'湿度',		'Soleil'		=>	'晴れた天気',		'Nuages'		=>	'雲',		'Brouillard'	=>	'霧',		'Pluie'			=>	'雨',		'Neige'			=>	'雪',		'Orages'		=>	'雷雨',		'Soleil-partiel'=>	'部分太陽',		'Pluie-neige'	=>	'雪の降る雨',		'Undefined'		=>	'不特定',		'WhatsNew'		=>	'新しいです',		'Trends'		=>	'トレンド'	),
	
	'pl'	=>	array
	(
		'Langue'		=>	'Polski',
		'Realtime'		=>	'Obecnie',
		'Today'			=>	'Dzisiaj',
		'Tomorrow'		=>	'Jutro',
		'Day1'			=>	'Poniedziałek',
		'Day2'			=>	'Wtorek',
		'Day3'			=>	'Środa',
		'Day4'			=>	'Czwartek',
		'Day5'			=>	'Piątek',
		'Day6'			=>	'Sobota',
		'Day0'			=>	'Niedziela',
		'Soleil'		=>	'Słonecznie',
		'Soleil-partiel'=>	'Pogodnie',
		'Nuages'		=>	'Pochmurno',
		'Brouillard'	=>	'Mgła',
		'Pluie'			=>	'Deszcz',
		'Pluie-neige'	=>	'Śnieg z deszczem',
		'Neige'			=>	'Śnieg',
		'Orages'		=>	'Burze',
		'WhatsNew'		=>	'Co nowego',
		'Wind'			=>	'Wiatr',
		'Humidity'		=>	'Wilgotność',
		'Trends'		=>	'Trendy'
	)
);